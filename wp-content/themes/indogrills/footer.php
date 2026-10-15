<section class="foot-cta">
  <div class="wrap">
    <div>
      <h2>Have a project in mind?</h2>
      <p>Send us your sizes, quantity and finish. We'll respond with a quotation.</p>
    </div>
    <a class="btn" href="<?php echo esc_url(home_url('/contact/')); ?>">Request a Quote</a>
  </div>
</section>
<footer class="site-footer">
  <div class="wrap">
    <div class="cols">
      <div>
        <img class="flogo" src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/logo-light.svg'); ?>" alt="IndoGrills">
        <p>Manufacturer of AC grills, diffusers and louvres for residential, commercial and industrial HVAC projects.</p>
      </div>
      <div>
        <h4>Quick Links</h4>
        <?php wp_nav_menu(['theme_location' => 'primary', 'container' => false, 'fallback_cb' => 'indogrills_fallback_menu']); ?>
      </div>
      <div>
        <h4>Products</h4>
        <ul>
          <?php $cats = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);
          if ($cats && !is_wp_error($cats)) foreach ($cats as $c) printf('<li><a href="%s">%s</a></li>', esc_url(get_term_link($c)), esc_html($c->name)); ?>
        </ul>
      </div>
      <div>
        <h4>Contact</h4>
        <ul class="fcontact">
          <li><?php echo indogrills_icon('mail'); ?><a href="mailto:<?php echo esc_attr(get_option('admin_email')); ?>"><?php echo esc_html(get_option('admin_email')); ?></a></li>
          <li><?php echo indogrills_icon('phone'); ?><a href="tel:+919175694563">+91 91756 94563</a></li>
          <li><?php echo indogrills_icon('pin'); ?><span>Mumbai, Maharashtra, India</span></li>
        </ul>
      </div>
    </div>
    <div class="copy">&copy; <?php echo date('Y'); ?> IndoGrills. All rights reserved.</div>
  </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
