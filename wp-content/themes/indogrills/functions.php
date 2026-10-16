<?php
if (!defined('ABSPATH')) exit;
require_once get_template_directory() . '/inc/seo.php';
require_once get_template_directory() . '/inc/mail.php';

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'gallery', 'caption']);
    register_nav_menu('primary', 'Primary Menu');
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('indogrills', get_stylesheet_uri(), [], filemtime(get_stylesheet_directory() . '/style.css'));
});

add_action('wp_head', function () {
    $u = get_template_directory_uri() . '/assets/img/';
    echo '<link rel="icon" href="' . esc_url($u . 'IndoGrills-Favicon.svg') . '" type="image/svg+xml">' . "\n";
    echo '<link rel="icon" href="' . esc_url($u . 'favicon.ico') . '" sizes="any">' . "\n";
    echo '<link rel="apple-touch-icon" href="' . esc_url($u . 'apple-touch-icon.png') . '">' . "\n";
    echo '<meta name="theme-color" content="#15385F">' . "\n";
});

/* Products */
add_action('init', function () {
    register_post_type('product', [
        'labels' => ['name' => 'Products', 'singular_name' => 'Product', 'add_new_item' => 'Add New Product'],
        'public' => true, 'has_archive' => 'products', 'rewrite' => ['slug' => 'products'],
        'menu_icon' => 'dashicons-grid-view', 'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
    ]);
    register_taxonomy('product_cat', 'product', [
        'labels' => ['name' => 'Product Categories', 'singular_name' => 'Product Category'],
        'hierarchical' => true, 'public' => true, 'show_admin_column' => true,
        'rewrite' => ['slug' => 'product-category'],
    ]);
    register_post_type('enquiry', [
        'labels' => ['name' => 'Enquiries', 'singular_name' => 'Enquiry'],
        'public' => false, 'show_ui' => true, 'menu_icon' => 'dashicons-email-alt',
        'supports' => ['title', 'editor'], 'capability_type' => 'post', 'capabilities' => ['create_posts' => 'do_not_allow'], 'map_meta_cap' => true,
    ]);
});

function indogrills_spec_fields() {
    return ['_ig_material' => 'Material', '_ig_sizes' => 'Available Sizes', '_ig_finish' => 'Finish / Colours', '_ig_use' => 'Application'];
}

add_action('add_meta_boxes', function () {
    add_meta_box('ig_specs', 'Product Specifications', function ($post) {
        wp_nonce_field('ig_specs', 'ig_specs_nonce');
        foreach (indogrills_spec_fields() as $k => $label) {
            printf('<p><label><strong>%s</strong><br><input type="text" name="%s" value="%s" style="width:100%%"></label></p>',
                esc_html($label), esc_attr($k), esc_attr(get_post_meta($post->ID, $k, true)));
        }
    }, 'product');
});

add_action('save_post_product', function ($id) {
    if (!isset($_POST['ig_specs_nonce']) || !wp_verify_nonce($_POST['ig_specs_nonce'], 'ig_specs') || !current_user_can('edit_post', $id)) return;
    foreach (indogrills_spec_fields() as $k => $_) {
        update_post_meta($id, $k, sanitize_text_field($_POST[$k] ?? ''));
    }
});

add_action('pre_get_posts', function ($q) {
    if (!is_admin() && $q->is_main_query() && (is_post_type_archive('product') || is_tax('product_cat'))) {
        $q->set('posts_per_page', 12); $q->set('orderby', 'menu_order'); $q->set('order', 'ASC');
    }
});

/* Enquiry form */
function indogrills_handle_enquiry() {
    $back = wp_get_referer() ?: home_url('/contact/');
    $back = remove_query_arg('enquiry', $back);
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'ig_enquiry') || !empty($_POST['website'])) {
        wp_safe_redirect(add_query_arg('enquiry', 'error', $back)); exit;
    }
    $name = sanitize_text_field($_POST['name'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $company = sanitize_text_field($_POST['company'] ?? '');
    $product = sanitize_text_field($_POST['product'] ?? '');
    $msg = sanitize_textarea_field($_POST['message'] ?? '');
    if (!$name || !is_email($email) || !$msg) {
        wp_safe_redirect(add_query_arg('enquiry', 'error', $back)); exit;
    }
    $body = "Name: $name\nCompany: $company\nEmail: $email\nPhone: $phone\nProduct: $product\n\n$msg";
    wp_insert_post(['post_type' => 'enquiry', 'post_status' => 'publish', 'post_title' => "$name - " . ($product ?: 'General'), 'post_content' => $body]);
    wp_mail(indogrills_contact_email(), "New enquiry from $name", $body, ["Reply-To: $name <$email>"]);
    wp_safe_redirect(add_query_arg('enquiry', 'sent', $back)); exit;
}
add_action('admin_post_nopriv_ig_enquiry', 'indogrills_handle_enquiry');
add_action('admin_post_ig_enquiry', 'indogrills_handle_enquiry');

function indogrills_enquiry_form($product = '') {
    $s = $_GET['enquiry'] ?? '';
    if ($s === 'sent') echo '<div class="notice ok">Thank you! Your enquiry has been sent. We will contact you shortly.</div>';
    if ($s === 'error') echo '<div class="notice err">Please fill in your name, a valid email and a message.</div>';
    ?>
    <form class="form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="ig_enquiry">
        <?php wp_nonce_field('ig_enquiry'); ?>
        <input class="hp" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
        <label>Name *<input name="name" required></label>
        <label>Company<input name="company"></label>
        <label>Email *<input type="email" name="email" required></label>
        <label>Phone<input name="phone"></label>
        <label>Product of interest<input name="product" value="<?php echo esc_attr($product); ?>"></label>
        <label>Requirements (sizes, quantity, finish) *<textarea name="message" rows="5" required></textarea></label>
        <p><button class="btn" type="submit">Request a Quote</button></p>
    </form>
    <?php
}

function indogrills_placeholder($cat = '') {
    $s = '<svg class="ph" viewBox="0 0 120 120" fill="none" stroke="#15385F" stroke-width="5" stroke-linecap="round" stroke-linejoin="round">';
    switch ($cat) {
        case 'Diffusers':
            $s .= '<rect x="14" y="14" width="92" height="92" rx="6"/><rect x="34" y="34" width="52" height="52" rx="4"/><rect x="50" y="50" width="20" height="20" rx="3" stroke="#FF9733"/>'; break;
        case 'Dampers':
            $s .= '<rect x="14" y="24" width="92" height="72" rx="6"/><path d="M26 84 L94 36" stroke="#FF9733"/><circle cx="60" cy="60" r="5" fill="#15385F"/>'; break;
        case 'Disk Valves':
            $s .= '<circle cx="60" cy="60" r="46"/><circle cx="60" cy="60" r="26" stroke="#FF9733"/><circle cx="60" cy="60" r="6" fill="#15385F"/>'; break;
        case 'Jet Nozzles':
            $s .= '<circle cx="60" cy="60" r="46"/><circle cx="60" cy="60" r="28"/><path d="M60 14v18M60 88v18M14 60h18M88 60h18" stroke="#FF9733"/>'; break;
        case 'Louvers':
            $s .= '<rect x="14" y="14" width="92" height="92" rx="6"/><path d="M26 34 L94 28M26 52 L94 46M26 70 L94 64M26 88 L94 82" stroke="#FF9733"/>'; break;
        case 'Accessories & Specials':
            $s .= '<circle cx="60" cy="60" r="16" stroke="#FF9733"/><path d="M60 14v18M60 88v18M14 60h18M88 60h18M27 27l13 13M80 80l13 13M93 27L80 40M40 80L27 93"/>'; break;
        default:
            $s .= '<rect x="14" y="14" width="92" height="92" rx="6"/><path d="M28 36h64M28 52h64M28 68h64" /><path d="M28 84h64" stroke="#FF9733"/>';
    }
    return $s . '</svg>';
}

function indogrills_product_card() {
    $t = get_the_terms(get_the_ID(), 'product_cat');
    $cat = $t && !is_wp_error($t) ? $t[0]->name : '';
    echo '<a class="card gal" href="' . esc_url(get_permalink()) . '"><div class="thumb">';
    echo has_post_thumbnail() ? get_the_post_thumbnail(null, 'medium_large') : indogrills_placeholder($cat);
    echo '<span class="view">View Details</span></div><div class="body"><h3>' . esc_html(get_the_title()) . '</h3><p class="meta">' . esc_html($cat) . '</p></div></a>';
}

function indogrills_category_blurb($name) {
    $b = [
        'Grills' => 'Grills are fitted at the outlet or inlet of an air duct to supply conditioned air into a room or to draw return air back to the unit. They are available in different blade types and angles, in standard and custom sizes, with powder-coated or anodised finishes.',
        'Diffusers' => 'Diffusers distribute supply air evenly across a space to maintain comfort without draughts. They suit ceilings in offices, retail, hospitality and healthcare, and are available in slot, square, round and tile formats.',
        'Dampers' => 'Dampers regulate or shut off airflow inside ducting so the system can be balanced and controlled. Manual, gravity and motorised options are available for different duct sizes and shapes.',
        'Disk Valves' => 'Disk valves are adjustable ventilation valves for exhaust in toilets and kitchens. The centre disk is turned to set the airflow.',
        'Jet Nozzles' => 'Jet nozzles deliver air over long throws, which suits large halls, atriums and high-ceiling spaces. Adjustable sizes are available.',
        'Louvers' => 'Louvers allow ventilation and air intake or exhaust while keeping out weather and debris. Used on walls, plant rooms and building facades.',
    ];
    return $b[$name] ?? 'Manufactured to your specification. Contact us with your size, material and finish requirements.';
}

function indogrills_fallback_menu() {
    echo '<ul>';
    foreach ([['Home', home_url('/')], ['About Us', home_url('/about/')], ['Products', get_post_type_archive_link('product')], ['Contact', home_url('/contact/')]] as $i) {
        printf('<li><a href="%s">%s</a></li>', esc_url($i[1]), esc_html($i[0]));
    }
    echo '</ul>';
}


function indogrills_icon($name) {
    $p = [
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
        'phone' => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/>',
        'pin' => '<path d="M12 21s7-6.2 7-11a7 7 0 0 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/>',
    ];
    return '<svg class="ico" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $p[$name] . '</svg>';
}

/* Product gallery (extra images) */
add_action('add_meta_boxes', function () {
    add_meta_box('ig_gallery', 'Product Gallery (extra angles)', function ($post) {
        wp_nonce_field('ig_gallery', 'ig_gallery_nonce');
        $ids = get_post_meta($post->ID, '_ig_gallery', true);
        echo '<input type="hidden" id="ig_gallery_ids" name="ig_gallery_ids" value="' . esc_attr($ids) . '">';
        echo '<div id="ig_gallery_preview" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:10px">';
        foreach (array_filter(explode(',', (string) $ids)) as $id) echo wp_get_attachment_image((int) $id, [80, 80], false, ['style' => 'border:1px solid #ccd']);
        echo '</div><button type="button" class="button" id="ig_gallery_btn">Add / change gallery images</button> <button type="button" class="button-link" id="ig_gallery_clear" style="margin-left:10px">Clear</button>';
        echo '<p class="description">Use the Featured Image for the main photo. Add more angles here.</p>';
    }, 'product', 'side');
});
add_action('admin_enqueue_scripts', function ($hook) {
    if (!in_array($hook, ['post.php', 'post-new.php'], true) || get_post_type() !== 'product') return;
    wp_enqueue_media();
    wp_enqueue_script('ig-gallery-admin', get_template_directory_uri() . '/assets/gallery-admin.js', ['jquery'], '1.0', true);
});
add_action('save_post_product', function ($id) {
    if (!isset($_POST['ig_gallery_nonce']) || !wp_verify_nonce($_POST['ig_gallery_nonce'], 'ig_gallery') || !current_user_can('edit_post', $id)) return;
    $ids = array_filter(array_map('absint', explode(',', $_POST['ig_gallery_ids'] ?? '')));
    update_post_meta($id, '_ig_gallery', implode(',', $ids));
});

function indogrills_product_gallery($id) {
    $ids = [];
    if (has_post_thumbnail($id)) $ids[] = get_post_thumbnail_id($id);
    foreach (array_filter(explode(',', (string) get_post_meta($id, '_ig_gallery', true))) as $g) $ids[] = (int) $g;
    $ids = array_values(array_unique($ids));
    if (!$ids) return '<div class="img">' . indogrills_placeholder() . '</div>';
    $out = '<div class="pgal"><div class="pgal-main"><img id="pgal-main" src="' . esc_url(wp_get_attachment_image_url($ids[0], 'large')) . '" alt="' . esc_attr(get_the_title($id)) . '">';
    if (count($ids) > 1) $out .= '<button type="button" class="pgal-nav prev" aria-label="Previous">&#8249;</button><button type="button" class="pgal-nav next" aria-label="Next">&#8250;</button>';
    $out .= '</div>';
    if (count($ids) > 1) {
        $out .= '<div class="pgal-thumbs">';
        foreach ($ids as $i => $a) $out .= '<button type="button" class="pgal-t' . ($i ? '' : ' on') . '" data-src="' . esc_url(wp_get_attachment_image_url($a, 'large')) . '"><img src="' . esc_url(wp_get_attachment_image_url($a, 'thumbnail')) . '" alt=""></button>';
        $out .= '</div>';
    }
    return $out . '</div>';
}

add_action('wp_footer', function () {
    if (!is_singular('product')) return;
    echo "<script>(function(){var m=document.getElementById('pgal-main');if(!m)return;var t=[].slice.call(document.querySelectorAll('.pgal-t')),c=0;function go(i){c=(i+t.length)%t.length;m.src=t[c].dataset.src;t.forEach(function(x,k){x.classList.toggle('on',k===c)});}t.forEach(function(x,i){x.addEventListener('click',function(){go(i)})});var p=document.querySelector('.pgal-nav.prev'),n=document.querySelector('.pgal-nav.next');if(p)p.onclick=function(){go(c-1)};if(n)n.onclick=function(){go(c+1)};})();</script>";
});

function indogrills_category_apps($name) {
    $a = [
        'Grills' => ['Return air and supply air openings in ducted AC systems', 'Offices, apartments, hotels and hospitals', 'Doors, walls and ceilings where airflow must pass'],
        'Diffusers' => ['False ceilings in offices, malls and showrooms', 'Hotels, restaurants and hospitality interiors', 'Hospitals, clean areas and laboratories'],
        'Dampers' => ['Balancing airflow in supply and return ducts', 'Isolating duct branches for maintenance', 'Automated control with motorised actuators'],
        'Disk Valves' => ['Toilet and bathroom exhaust', 'Kitchens and pantries', 'Small rooms needing adjustable ventilation'],
        'Jet Nozzles' => ['Auditoriums, atriums and large halls', 'Showrooms and high-ceiling spaces', 'Spot cooling in industrial areas'],
        'Louvers' => ['Plant rooms and generator rooms', 'External walls and building facades', 'Fresh-air intake and exhaust openings'],
    ];
    return $a[$name] ?? ['HVAC and ventilation projects', 'Residential, commercial and industrial buildings', 'Custom requirements on request'];
}

/* Public contact email: shown on the site and used for enquiry notifications. */
function indogrills_contact_email() { return 'info@indogrills.com'; }
