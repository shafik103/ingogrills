<?php
if (!defined('ABSPATH')) exit;

const IG_BRAND = 'IndoGrills';
const IG_PHONE = '+919175694563';

function ig_clip($s, $len) {
    $s = trim(preg_replace('/\s+/', ' ', wp_strip_all_tags($s)));
    if (mb_strlen($s) <= $len) return $s;
    $s = preg_replace('/\s+\S*$/u', '', mb_substr($s, 0, $len - 1));
    return rtrim($s, " ,.;:-") . '…';
}

function ig_product_cat($id) {
    $t = get_the_terms($id, 'product_cat');
    return $t && !is_wp_error($t) ? $t[0] : null;
}

function ig_pname($id) { return html_entity_decode(wp_strip_all_tags(get_post_field('post_title', $id)), ENT_QUOTES, 'UTF-8'); }

function ig_product_title($id) {
    $custom = trim((string) get_post_meta($id, '_ig_seo_title', true));
    if ($custom !== '') return $custom;
    $n = ig_pname($id);
    foreach (["$n Manufacturer in Mumbai | " . IG_BRAND, "$n Manufacturer | " . IG_BRAND, "$n | " . IG_BRAND] as $c) {
        if (mb_strlen($c) <= 60) return $c;
    }
    return $n . ' | ' . IG_BRAND;
}

function ig_product_desc($id) {
    $custom = trim((string) get_post_meta($id, '_ig_seo_desc', true));
    if ($custom !== '') return ig_clip($custom, 160);
    $n = ig_pname($id);
    $spec = rtrim(trim(wp_strip_all_tags(get_the_excerpt($id) ?: get_post_field('post_content', $id))), '. ');
    $lead = (strcasecmp($spec, $n) === 0 || $spec === '') ? $n : "$n: $spec";
    $tail = 'Made to order by IndoGrills, HVAC manufacturer in Mumbai. Custom sizes, bulk supply. Get a quote.';
    $d = $lead . '. ' . $tail;
    if (mb_strlen($d) > 160) $d = $lead . '. Made to order by IndoGrills, Mumbai. Custom sizes. Get a quote.';
    return ig_clip($d, 160);
}

function ig_page_default_title($id) {
    $custom = trim((string) get_post_meta($id, '_ig_seo_title', true));
    if ($custom !== '') return $custom;
    $slug = get_post_field('post_name', $id);
    if ($slug === 'about') return 'About IndoGrills | AC Grill & Air Distribution Manufacturer';
    if ($slug === 'contact') return 'Contact & Request a Quote | ' . IG_BRAND;
    return ig_pname($id) . ' | ' . IG_BRAND;
}

function ig_page_default_desc($id) {
    $custom = trim((string) get_post_meta($id, '_ig_seo_desc', true));
    if ($custom !== '') return ig_clip($custom, 160);
    $e = get_post_field('post_excerpt', $id);
    if (!$e) $e = wp_strip_all_tags(strip_shortcodes(get_post_field('post_content', $id)));
    return ig_clip($e, 160);
}

function ig_page_title() {
    if (is_front_page()) return IG_BRAND . ' | AC Grills, Diffusers & Dampers Manufacturer in Mumbai';
    if (is_singular('product')) return ig_product_title(get_queried_object_id());
    if (is_page()) return ig_page_default_title(get_queried_object_id());
    if (is_search()) return 'Search results | ' . IG_BRAND;
    if (is_404()) return 'Page not found | ' . IG_BRAND;
    if (is_post_type_archive('product')) return 'AC Grills, Diffusers, Dampers & HVAC Products | ' . IG_BRAND . ' Mumbai';
    if (is_tax('product_cat')) {
        $n = get_queried_object()->name;
        $t = "$n Manufacturer in Mumbai | " . IG_BRAND;
        return mb_strlen($t) <= 60 ? $t : "$n | " . IG_BRAND;
    }
    return '';
}

add_filter('pre_get_document_title', function ($t) {
    $x = ig_page_title();
    return $x !== '' ? $x : $t;
});

function ig_meta_description() {
    if (is_front_page()) return 'IndoGrills: Mumbai manufacturer of AC grills, diffusers, dampers, disk valves and louvers (louvres). 10+ years experience. Custom sizes. Get a quote.';
    if (is_singular('product')) return ig_product_desc(get_queried_object_id());
    if (is_post_type_archive('product')) return 'Browse 50+ HVAC air distribution products from IndoGrills, Mumbai: AC grills, diffusers, dampers, disk valves, jet nozzles and louvers made to your size.';
    if (is_tax('product_cat')) {
        $o = get_queried_object();
        $first = preg_split('/(?<=\.)\s/', indogrills_category_blurb($o->name))[0];
        $first = rtrim(ig_clip($first, 95), '.…');
        return ig_clip($o->name . ': ' . $first . '. Made by ' . IG_BRAND . ', Mumbai. Get a quote.', 160);
    }
    if (is_page()) return ig_page_default_desc(get_queried_object_id());
    return '';
}

function ig_image_urls($id) {
    $ids = [];
    if (has_post_thumbnail($id)) $ids[] = get_post_thumbnail_id($id);
    foreach (array_filter(explode(',', (string) get_post_meta($id, '_ig_gallery', true))) as $g) $ids[] = (int) $g;
    $out = [];
    foreach ($ids as $a) { $u = wp_get_attachment_image_url($a, 'large'); if ($u) $out[] = $u; }
    return $out;
}

function ig_org() {
    return [
        '@type' => 'LocalBusiness',
        '@id' => home_url('/#organization'),
        'name' => IG_BRAND,
        'url' => home_url('/'),
        'logo' => get_template_directory_uri() . '/assets/img/logo.svg',
        'image' => get_template_directory_uri() . '/assets/img/IndoGrills-Horizontal.svg',
        'description' => 'Manufacturer of AC grills, diffusers, dampers and other HVAC air distribution products in Mumbai.',
        'telephone' => IG_PHONE,
        'email' => indogrills_contact_email(),
        'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Mumbai', 'addressRegion' => 'Maharashtra', 'addressCountry' => 'IN'],
        'areaServed' => ['Mumbai', 'Navi Mumbai', 'Thane', 'Maharashtra', 'India'],
    ];
}

function ig_jsonld($data) {
    echo '<script type="application/ld+json">' . wp_json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</script>\n";
}

add_action('wp_head', function () {
    if (is_admin()) return;
    $desc = ig_meta_description();
    if ($desc) echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";

    $title = wp_get_document_title();
    $url = is_singular() ? get_permalink() : (is_tax() ? get_term_link(get_queried_object()) : (is_post_type_archive() ? get_post_type_archive_link(get_post_type()) : home_url('/')));
    $img = '';
    if (is_singular('product')) { $i = ig_image_urls(get_queried_object_id()); $img = $i[0] ?? ''; }
    echo '<meta property="og:site_name" content="' . esc_attr(IG_BRAND) . '">' . "\n";
    echo '<meta property="og:locale" content="en_IN">' . "\n";
    echo '<meta property="og:type" content="' . (is_singular('product') ? 'product' : 'website') . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    if ($desc) echo '<meta property="og:description" content="' . esc_attr($desc) . '">' . "\n";
    if (!is_wp_error($url)) echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
    if ($img) echo '<meta property="og:image" content="' . esc_url($img) . '">' . "\n";
    echo '<meta name="twitter:card" content="' . ($img ? 'summary_large_image' : 'summary') . '">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
    if ($desc) echo '<meta name="twitter:description" content="' . esc_attr($desc) . '">' . "\n";
    if ($img) echo '<meta name="twitter:image" content="' . esc_url($img) . '">' . "\n";

    // Organisation / local business (every page)
    ig_jsonld(array_merge(['@context' => 'https://schema.org'], ig_org()));

    if (is_front_page()) {
        $faq = [
            ['Can you manufacture grills to custom sizes?', 'Yes. Send your drawing or size list and we will quote to your specification.'],
            ['Which materials and finishes are available?', 'Aluminium, GI and MS, with powder coating, anodising or mill finish in standard and RAL colours.'],
            ['Do you supply bulk and project orders?', 'Yes. We supply single orders as well as large project and distributor quantities.'],
            ['How do I get a quotation?', 'Use the quote form on our contact page with your sizes, quantity and finish, and we will respond promptly.'],
        ];
        ig_jsonld(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array_map(fn($q) => ['@type' => 'Question', 'name' => $q[0], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $q[1]]], $faq)]);
    }
    if (is_page() && !is_front_page()) {
        $pid = get_queried_object_id();
        $type = is_page('about') ? 'AboutPage' : (is_page('contact') ? 'ContactPage' : 'WebPage');
        ig_jsonld(['@context' => 'https://schema.org', '@type' => $type, 'name' => wp_get_document_title(), 'description' => ig_page_default_desc($pid), 'url' => get_permalink($pid), 'isPartOf' => ['@id' => home_url('/#website')], 'about' => ['@id' => home_url('/#organization')]]);
        ig_jsonld(['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => home_url('/')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => ig_pname($pid), 'item' => get_permalink($pid)]]]);
    }
    if (is_front_page()) {
        ig_jsonld(['@context' => 'https://schema.org', '@type' => 'WebSite', '@id' => home_url('/#website'), 'name' => IG_BRAND, 'url' => home_url('/'), 'publisher' => ['@id' => home_url('/#organization')]]);
    }

    if (is_singular('product')) {
        $id = get_queried_object_id();
        $cat = ig_product_cat($id);
        $imgs = ig_image_urls($id);
        $p = [
            '@context' => 'https://schema.org', '@type' => 'Product',
            'name' => ig_pname($id), 'description' => ig_product_desc($id),
            'sku' => 'IG-' . $id, 'mpn' => 'IG-' . $id, 'url' => get_permalink($id),
            'brand' => ['@type' => 'Brand', 'name' => IG_BRAND],
            'manufacturer' => ['@id' => home_url('/#organization')],
        ];
        if ($imgs) $p['image'] = $imgs;
        if ($cat) $p['category'] = $cat->name;
        ig_jsonld($p);

        $crumbs = [['Home', home_url('/')], ['Products', get_post_type_archive_link('product')]];
        if ($cat) $crumbs[] = [$cat->name, get_term_link($cat)];
        $crumbs[] = [ig_pname($id), get_permalink($id)];
        $items = [];
        foreach ($crumbs as $i => $c) $items[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $c[0], 'item' => $c[1]];
        ig_jsonld(['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $items]);
    }
    if (is_tax('product_cat') || is_post_type_archive('product')) {
        global $wp_query;
        $list = [];
        foreach ($wp_query->posts as $i => $po) $list[] = ['@type' => 'ListItem', 'position' => $i + 1, 'url' => get_permalink($po)];
        ig_jsonld(['@context' => 'https://schema.org', '@type' => 'CollectionPage', 'name' => wp_get_document_title(), 'mainEntity' => ['@type' => 'ItemList', 'itemListElement' => $list]]);
    }
}, 5);

// Keep WP's own description-less canonical, but noindex thin pages
add_filter('wp_robots', function ($r) {
    if (is_search() || is_404()) { $r['noindex'] = true; $r['follow'] = true; }
    else { $r['max-image-preview'] = 'large'; }
    return $r;
});

// Image alt text fallback for product images
add_filter('wp_get_attachment_image_attributes', function ($attr, $att) {
    if (empty($attr['alt'])) {
        $parent = $att->post_parent ? get_the_title($att->post_parent) : '';
        $attr['alt'] = $parent ? $parent . ' - ' . IG_BRAND . ' Mumbai' : $att->post_title;
    }
    return $attr;
}, 10, 2);

// SEO meta box on products and pages
add_action('add_meta_boxes', function () {
    foreach (['product', 'page'] as $pt) {
        add_meta_box('ig_seo', 'SEO (Google result)', function ($post) {
            wp_nonce_field('ig_seo', 'ig_seo_nonce');
            $isp = $post->post_type === 'product';
            $dt = $isp ? ig_product_title($post->ID) : ig_page_default_title($post->ID);
            $dd = $isp ? ig_product_desc($post->ID) : ig_page_default_desc($post->ID);
            $t = get_post_meta($post->ID, '_ig_seo_title', true);
            $d = get_post_meta($post->ID, '_ig_seo_desc', true);
            echo '<p><strong>SEO title</strong> (max 60 characters)<br><input type="text" name="ig_seo_title" value="' . esc_attr($t) . '" style="width:100%" maxlength="70" placeholder="' . esc_attr($dt) . '"></p>';
            echo '<p><strong>Meta description</strong> (max 160 characters)<br><textarea name="ig_seo_desc" rows="3" style="width:100%" maxlength="170" placeholder="' . esc_attr($dd) . '">' . esc_textarea($d) . '</textarea></p>';
            echo '<p class="description">Leave blank to use the automatic title and description shown in grey.</p>';
        }, $pt, 'normal', 'default');
    }
});
add_action('save_post', function ($id) {
    if (!in_array(get_post_type($id), ['product', 'page'], true)) return;
    if (!isset($_POST['ig_seo_nonce']) || !wp_verify_nonce($_POST['ig_seo_nonce'], 'ig_seo') || !current_user_can('edit_post', $id)) return;
    update_post_meta($id, '_ig_seo_title', sanitize_text_field($_POST['ig_seo_title'] ?? ''));
    update_post_meta($id, '_ig_seo_desc', sanitize_textarea_field($_POST['ig_seo_desc'] ?? ''));
});
