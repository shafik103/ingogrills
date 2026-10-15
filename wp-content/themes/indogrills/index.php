<?php get_header(); ?>
<div class="page-head"><div class="wrap"><h1><?php echo is_singular() ? esc_html(get_the_title()) : esc_html(get_the_archive_title() ?: 'News'); ?></h1></div></div>
<div class="content"><div class="wrap">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
  <article>
    <?php if (!is_singular()) : ?><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><?php endif; ?>
    <?php is_singular() ? the_content() : the_excerpt(); ?>
  </article>
<?php endwhile; the_posts_pagination(['prev_text' => '&larr; Prev', 'next_text' => 'Next &rarr;']); else : ?><p>Nothing found.</p><?php endif; ?>
</div></div>
<?php get_footer(); ?>
