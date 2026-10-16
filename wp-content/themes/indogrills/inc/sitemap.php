<?php
if (!defined('ABSPATH')) exit;

/*
 * XML sitemap at /sitemap.xml (pages, products with images, categories) and a working /robots.txt.
 * Handled early in parse_request so no permalink flush is needed. The core wp-sitemap.xml is turned off
 * (it redirects here) so there is a single sitemap for search engines.
 */
add_filter('wp_sitemaps_enabled', '__return_false');

function ig_sitemap_url($loc, $lastmod = '', $freq = '', $prio = '', $images = []) {
    $x = "  <url>\n    <loc>" . esc_url($loc) . "</loc>\n";
    if ($lastmod) $x .= '    <lastmod>' . esc_html(gmdate('c', strtotime($lastmod))) . "</lastmod>\n";
    if ($freq) $x .= "    <changefreq>$freq</changefreq>\n";
    if ($prio) $x .= "    <priority>$prio</priority>\n";
    foreach (array_unique(array_filter($images)) as $img) {
        $x .= '    <image:image><image:loc>' . esc_url($img) . "</image:loc></image:image>\n";
    }
    return $x . "  </url>\n";
}

function ig_sitemap_xml() {
    $out = '';
    $front = (int) get_option('page_on_front');
    $home_mod = $front ? get_post_field('post_modified_gmt', $front) : '';

    $out .= ig_sitemap_url(home_url('/'), $home_mod, 'weekly', '1.0');

    // Pages (about, contact, ...) - skip the front page itself and anything noindex/draft
    foreach (get_posts(['post_type' => 'page', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'menu_order title', 'order' => 'ASC']) as $p) {
        if ($p->ID === $front || $p->ID === (int) get_option('wp_page_for_privacy_policy')) continue;
        $prio = in_array($p->post_name, ['about', 'contact'], true) ? '0.8' : '0.6';
        $out .= ig_sitemap_url(get_permalink($p), $p->post_modified_gmt, 'monthly', $prio);
    }

    // Products archive
    $latest = get_posts(['post_type' => 'product', 'numberposts' => 1, 'orderby' => 'modified', 'order' => 'DESC']);
    $out .= ig_sitemap_url(get_post_type_archive_link('product'), $latest ? $latest[0]->post_modified_gmt : '', 'weekly', '0.9');

    // Categories
    foreach (get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true]) as $t) {
        $newest = get_posts(['post_type' => 'product', 'numberposts' => 1, 'orderby' => 'modified', 'order' => 'DESC',
            'tax_query' => [['taxonomy' => 'product_cat', 'terms' => $t->term_id]]]);
        $out .= ig_sitemap_url(get_term_link($t), $newest ? $newest[0]->post_modified_gmt : '', 'weekly', '0.8');
    }

    // Products with their images
    foreach (get_posts(['post_type' => 'product', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'menu_order', 'order' => 'ASC']) as $p) {
        $out .= ig_sitemap_url(get_permalink($p), $p->post_modified_gmt, 'monthly', '0.7', ig_image_urls($p->ID));
    }

    return '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
        . '<?xml-stylesheet type="text/xsl" href="' . esc_url(get_template_directory_uri() . '/assets/sitemap.xsl') . '"?>' . "\n"
        . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n"
        . $out . "</urlset>\n";
}

add_action('parse_request', function () {
    $base = trailingslashit((string) parse_url(home_url(), PHP_URL_PATH));
    $path = strtok($_SERVER['REQUEST_URI'] ?? '', '?');
    if ($path === $base . 'sitemap.xml') {
        status_header(200);
        header('Content-Type: application/xml; charset=UTF-8');
        header('X-Robots-Tag: noindex, follow');
        echo ig_sitemap_xml();
        exit;
    }
    if (in_array($path, [$base . 'wp-sitemap.xml', $base . 'sitemap_index.xml'], true)) {
        wp_redirect(home_url('/sitemap.xml'), 301);
        exit;
    }
    if ($path === $base . 'robots.txt') {
        status_header(200);
        do_robots();
        exit;
    }
});

add_filter('robots_txt', function ($output) {
    return rtrim($output) . "\n\nSitemap: " . home_url('/sitemap.xml') . "\n";
}, 20);
