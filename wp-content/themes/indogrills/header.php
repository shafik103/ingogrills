<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="topbar">
  <div class="wrap">
    <span>Manufacturer of AC Grills &amp; Air Distribution Products</span>
    <span class="tb-links">
      <a href="mailto:<?php echo esc_attr(get_option('admin_email')); ?>"><?php echo indogrills_icon('mail'); ?><?php echo esc_html(get_option('admin_email')); ?></a>
      <a href="tel:+919175694563"><?php echo indogrills_icon('phone'); ?>+91 91756 94563</a>
    </span>
  </div>
</div>
<header class="site-header">
  <div class="wrap">
    <a class="logo" href="<?php echo esc_url(home_url('/')); ?>"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/logo.svg'); ?>" alt="IndoGrills - Air Distribution Products"></a>
    <button class="nav-toggle" aria-label="Menu" onclick="document.querySelector('.nav').classList.toggle('open')">&#9776;</button>
    <nav class="nav">
      <?php wp_nav_menu(['theme_location' => 'primary', 'container' => false, 'fallback_cb' => 'indogrills_fallback_menu']); ?>
      <a class="btn nav-cta" href="<?php echo esc_url(home_url('/contact/')); ?>">Get a Quote</a>
    </nav>
  </div>
</header>
