<?php get_header();
$contact = esc_url(home_url('/contact/'));
$prod = esc_url(get_post_type_archive_link('product'));
?>
<section class="hero">
  <div class="wrap hero-grid">
    <div>
      <span class="eyebrow">Air Distribution Products Manufacturer</span>
      <h1>Precision-made <span>AC Grills</span> for every HVAC project</h1>
      <p>Return air grills, supply grills, diffusers and louvres, manufactured in custom sizes and finishes for HVAC contractors, consultants and distributors.</p>
      <a class="btn" href="<?php echo $contact; ?>">Request a Quote</a>
      <a class="btn alt" href="<?php echo $prod; ?>">Browse Products</a>
    </div>
    <div class="hero-art" aria-hidden="true">
      <svg viewBox="0 0 160 160"><rect width="160" height="160" rx="28" fill="rgba(255,255,255,.08)"/><g><rect x="25" y="30" width="110" height="12" rx="6" fill="#fff"/><rect x="25" y="54" width="110" height="12" rx="6" fill="#fff"/><rect x="25" y="78" width="110" height="12" rx="6" fill="#fff"/><rect x="25" y="102" width="110" height="12" rx="6" fill="#FF9733"/><rect x="25" y="126" width="60" height="12" rx="6" fill="#fff"/></g></svg>
    </div>
  </div>
</section>

<section class="strip">
  <div class="wrap strip-grid">
    <div><strong>Custom Sizes</strong><span>Made to drawing &amp; specification</span></div>
    <div><strong>Multiple Materials</strong><span>Aluminium, GI and more</span></div>
    <div><strong>Powder Coat &amp; Anodised</strong><span>Standard and RAL colours</span></div>
    <div><strong>Project &amp; Bulk Supply</strong><span>Dependable lead times</span></div>
  </div>
</section>

<section class="block">
  <div class="wrap two intro">
    <div>
      <span class="eyebrow dark">About Us</span>
      <h2>Your manufacturing partner for air distribution</h2>
      <p>IndoGrills designs and manufactures air distribution products used in residential, commercial and industrial air-conditioning systems. We work directly with contractors, MEP consultants and distributors to deliver grills that fit the drawing, the ceiling and the budget.</p>
      <ul class="ticks">
        <li>In-house fabrication and finishing</li>
        <li>Consistent quality checks on every batch</li>
        <li>Technical support from enquiry to delivery</li>
      </ul>
      <a class="btn" href="<?php echo esc_url(home_url('/about/')); ?>">More About Us</a>
    </div>
    <div class="intro-panel">
      <h3>Product Range at a Glance</h3>
      <table class="specs">
        <tr><th>Products</th><td>Return &amp; supply grills, diffusers, louvres</td></tr>
        <tr><th>Materials</th><td>Aluminium, GI, MS</td></tr>
        <tr><th>Finishes</th><td>Powder coat, anodised, mill finish</td></tr>
        <tr><th>Sizes</th><td>Standard and fully custom</td></tr>
        <tr><th>Supply</th><td>Single orders to large projects</td></tr>
      </table>
    </div>
  </div>
</section>

<section class="block alt">
  <div class="wrap">
    <span class="eyebrow dark center">Categories</span>
    <h2 class="sec-title">Explore Our Product Range</h2>
    <div class="grid">
      <?php
      $cats = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);
      if ($cats && !is_wp_error($cats)) foreach ($cats as $c) {
          $first = get_posts(['post_type' => 'product', 'numberposts' => 1, 'orderby' => 'menu_order', 'order' => 'ASC', 'tax_query' => [['taxonomy' => 'product_cat', 'terms' => $c->term_id]], 'meta_key' => '_thumbnail_id']);
      $img = $first ? get_the_post_thumbnail($first[0], 'medium_large') : indogrills_placeholder($c->name);
      printf('<a class="card cat" href="%s"><div class="thumb">%s</div><div class="body"><h3>%s</h3><p class="meta">%d product%s &rarr;</p></div></a>',
              esc_url(get_term_link($c)), $img, esc_html($c->name), $c->count, $c->count == 1 ? '' : 's');
      }
      ?>
    </div>
  </div>
</section>

<section class="block">
  <div class="wrap">
    <span class="eyebrow dark center">Featured</span>
    <h2 class="sec-title">Popular Products</h2>
    <div class="grid">
      <?php
      $q = new WP_Query(['post_type' => 'product', 'posts_per_page' => 4, 'orderby' => 'menu_order', 'order' => 'ASC']);
      if ($q->have_posts()) { while ($q->have_posts()) { $q->the_post(); indogrills_product_card(); } wp_reset_postdata(); }
      ?>
    </div>
    <p style="text-align:center;margin-top:30px"><a class="btn" href="<?php echo $prod; ?>">View All Products</a></p>
  </div>
</section>

<section class="block dark">
  <div class="wrap">
    <span class="eyebrow center">How We Work</span>
    <h2 class="sec-title">From Enquiry to Delivery</h2>
    <div class="steps">
      <div class="step"><b>1</b><h3>Share Requirements</h3><p>Send drawings, sizes, quantity and finish.</p></div>
      <div class="step"><b>2</b><h3>Quotation</h3><p>Receive a clear, itemised quote.</p></div>
      <div class="step"><b>3</b><h3>Manufacturing</h3><p>Fabrication, finishing and quality checks.</p></div>
      <div class="step"><b>4</b><h3>Delivery</h3><p>Packed and dispatched to your site.</p></div>
    </div>
  </div>
</section>

<section class="block">
  <div class="wrap">
    <span class="eyebrow dark center">Applications</span>
    <h2 class="sec-title">Industries We Serve</h2>
    <div class="features">
      <div class="feature"><div class="ic">&#8962;</div><h3>Residential</h3><p>Apartments, villas and housing projects.</p></div>
      <div class="feature"><div class="ic">&#9635;</div><h3>Commercial</h3><p>Offices, malls, hotels and hospitality.</p></div>
      <div class="feature"><div class="ic">&#9881;</div><h3>Industrial</h3><p>Factories, warehouses and plants.</p></div>
      <div class="feature"><div class="ic">&#10010;</div><h3>Healthcare &amp; Education</h3><p>Hospitals, clinics, schools and campuses.</p></div>
    </div>
  </div>
</section>

<section class="block alt">
  <div class="wrap faq">
    <h2 class="sec-title">Frequently Asked Questions</h2>
    <details><summary>Can you manufacture grills to custom sizes?</summary><p>Yes. Send your drawing or size list and we will quote to your specification.</p></details>
    <details><summary>Which materials and finishes are available?</summary><p>Aluminium, GI and MS, with powder coating, anodising or mill finish in standard and RAL colours.</p></details>
    <details><summary>Do you supply bulk and project orders?</summary><p>Yes. We supply single orders as well as large project and distributor quantities.</p></details>
    <details><summary>How do I get a quotation?</summary><p>Use the <a href="<?php echo $contact; ?>">quote form</a> with your sizes, quantity and finish, and we will respond promptly.</p></details>
  </div>
</section>

<section class="cta">
  <div class="wrap">
    <h2>Ready to discuss your project?</h2>
    <p>Send us your requirements and get a quotation from our team.</p>
    <a class="btn" href="<?php echo $contact; ?>">Request a Quote</a>
  </div>
</section>
<?php get_footer(); ?>
