<?php get_header(); ?>
<div class="page-head"><div class="wrap wide">
  <p class="crumbs"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a> / Contact</p>
  <h1>Contact &amp; Request a Quote</h1>
  <p class="lead">Tell us what you need and our team will get back to you with a quotation.</p>
</div></div>
<div class="content"><div class="wrap two contact-grid">
  <div>
    <h2>Get in touch</h2>
    <p>Call, message or email us, or fill in the form. We respond to every enquiry.</p>
    <div class="ccards">
      <a class="ccard" href="tel:+919175694563"><?php echo indogrills_icon('phone'); ?><span><b>Call us</b>+91 91756 94563</span></a>
      <a class="ccard" href="https://wa.me/919175694563" target="_blank" rel="noopener"><svg class="ico" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21l1.6-4.6A8.5 8.5 0 1 1 8 19.6L3 21z"/><path d="M9 9.5c0 3 2.5 5.5 5.5 5.5l1.2-1.4-2-1-.8.7a4 4 0 0 1-1.7-1.7l.7-.8-1-2L9 9.5z"/></svg><span><b>WhatsApp</b>Chat with us</span></a>
      <a class="ccard" href="mailto:<?php echo esc_attr(get_option('admin_email')); ?>"><?php echo indogrills_icon('mail'); ?><span><b>Email</b><?php echo esc_html(get_option('admin_email')); ?></span></a>
      <div class="ccard"><?php echo indogrills_icon('pin'); ?><span><b>Location</b>Mumbai, Maharashtra, India</span></div>
    </div>

    <h3>For an accurate quote, please include</h3>
    <ul class="ticks">
      <li>Product type (grill, diffuser, damper, etc.)</li>
      <li>Sizes and quantity</li>
      <li>Material and finish (aluminium, GI, powder coat, anodised)</li>
      <li>Drawings or specifications, if available</li>
      <li>Delivery location and required timeline</li>
    </ul>

    <div class="cnote"><b>What happens next?</b><ol><li>We review your requirements.</li><li>We confirm sizes, finish and lead time.</li><li>You receive a clear quotation.</li></ol></div>
  </div>
  <div class="cform">
    <h2>Send your enquiry</h2>
    <p class="fsub">Fields marked * are required. We will get back to you with a quotation.</p>
    <?php indogrills_enquiry_form(); ?>
  </div>
</div></div>
<?php get_footer(); ?>
