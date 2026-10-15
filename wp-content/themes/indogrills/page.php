<?php get_header(); while (have_posts()) : the_post(); ?>
<div class="page-head"><div class="wrap wide">
  <p class="crumbs"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a> / <?php the_title(); ?></p>
  <h1><?php the_title(); ?></h1>
</div></div>
<div class="content"><div class="wrap wide page-body"><?php the_content(); ?></div></div>
<?php endwhile; get_footer(); ?>
