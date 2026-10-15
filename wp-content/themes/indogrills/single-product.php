<?php get_header(); while (have_posts()) : the_post();
$terms = get_the_terms(get_the_ID(), 'product_cat');
$term = $terms && !is_wp_error($terms) ? $terms[0] : null;
?>
<div class="page-head"><div class="wrap">
  <p class="crumbs"><a href="<?php echo esc_url(get_post_type_archive_link('product')); ?>">Products</a><?php if ($term) printf(' / <a href="%s">%s</a>', esc_url(get_term_link($term)), esc_html($term->name)); ?></p>
  <h1><?php the_title(); ?></h1>
</div></div>
<div class="content"><div class="wrap">
  <div class="product-single">
    <?php echo indogrills_product_gallery(get_the_ID()); ?>
    <div>
      <?php if ($term) echo '<span class="eyebrow dark">' . esc_html($term->name) . '</span>'; ?>
      <h2 style="margin-top:0">Product Description</h2>
      <?php the_content(); ?>
      <?php if ($term) echo '<p>' . esc_html(indogrills_category_blurb($term->name)) . '</p>'; ?>
      <table class="specs">
        <?php foreach (indogrills_spec_fields() as $k => $label) { $v = get_post_meta(get_the_ID(), $k, true); if ($v) printf('<tr><th>%s</th><td>%s</td></tr>', esc_html($label), esc_html($v)); } ?>
        <tr><th>Sizes</th><td>Standard and custom sizes</td></tr>
        <tr><th>Finish</th><td>Powder coated, anodised or mill finish</td></tr>
        <tr><th>Supply</th><td>Single orders to bulk project quantities</td></tr>
      </table>
      <a class="btn" href="#enquire">Request a Quote</a>
    </div>
  </div>

  <div class="pdetail">
    <div>
      <h2>Applications</h2>
      <ul class="ticks"><?php foreach (indogrills_category_apps($term ? $term->name : '') as $li) echo '<li>' . esc_html($li) . '</li>'; ?></ul>
    </div>
    <div>
      <h2>Made to Order in Mumbai</h2>
      <p><?php the_title(); ?> is manufactured by IndoGrills at our Mumbai facility to your required size, material and finish. We supply contractors, consultants and distributors in Mumbai, Navi Mumbai, Thane and across Maharashtra, from single pieces to full project quantities. <a href="<?php echo esc_url(home_url('/about/')); ?>">Learn more about IndoGrills</a>.</p>
    </div>
  </div>

  <?php if ($term) :
    $rel = new WP_Query(['post_type' => 'product', 'posts_per_page' => 4, 'post__not_in' => [get_the_ID()], 'orderby' => 'menu_order', 'order' => 'ASC',
      'tax_query' => [['taxonomy' => 'product_cat', 'terms' => $term->term_id]]]);
    if ($rel->have_posts()) : ?>
    <h2 style="margin-top:56px">Related Products</h2>
    <div class="grid"><?php while ($rel->have_posts()) { $rel->the_post(); indogrills_product_card(); } wp_reset_postdata(); ?></div>
  <?php endif; endif; ?>

  <div id="enquire" style="max-width:1000px;margin:56px auto 0">
    <h2>Enquire about <?php the_title(); ?></h2>
    <?php indogrills_enquiry_form(get_the_title()); ?>
  </div>
</div></div>
<?php endwhile; get_footer(); ?>
