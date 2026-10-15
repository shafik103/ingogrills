<?php get_header();
$cur = get_queried_object();
$is_cat = is_tax('product_cat');
global $wp_query;
?>
<div class="page-head"><div class="wrap">
  <p class="crumbs"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a> / <?php if ($is_cat) : ?><a href="<?php echo esc_url(get_post_type_archive_link('product')); ?>">Products</a> / <?php echo esc_html($cur->name); else : ?>Products<?php endif; ?></p>
  <h1><?php echo $is_cat ? esc_html($cur->name) : 'Our Products'; ?></h1>
  <p class="lead"><?php echo $is_cat ? esc_html(indogrills_category_blurb($cur->name)) : 'Grills, diffusers, dampers and ventilation products, manufactured to your size and finish.'; ?></p>
</div></div>
<div class="content"><div class="wrap">
  <?php $cats = get_terms(['taxonomy' => 'product_cat']); if ($cats && !is_wp_error($cats)) : ?>
  <div class="filters">
    <a href="<?php echo esc_url(get_post_type_archive_link('product')); ?>" class="<?php echo $is_cat ? '' : 'on'; ?>">All</a>
    <?php foreach ($cats as $c) printf('<a href="%s" class="%s">%s <span>%d</span></a>', esc_url(get_term_link($c)), is_tax('product_cat', $c->term_id) ? 'on' : '', esc_html($c->name), $c->count); ?>
  </div>
  <?php endif; ?>
  <p class="count"><?php echo (int) $wp_query->found_posts; ?> products</p>
  <div class="grid">
    <?php if (have_posts()) { while (have_posts()) { the_post(); indogrills_product_card(); } } else echo '<p>No products found.</p>'; ?>
  </div>
  <?php the_posts_pagination(['mid_size' => 1, 'prev_text' => '&larr; Prev', 'next_text' => 'Next &rarr;', 'screen_reader_text' => 'Products navigation']); ?>
</div></div>
<?php get_footer(); ?>
