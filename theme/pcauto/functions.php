<?php
/**
 * PC Auto — fonctions du thème.
 */
if (!defined('ABSPATH')) { exit; }

require get_theme_file_path('inc/helpers.php');
require get_theme_file_path('inc/product-fields.php');
require get_theme_file_path('inc/contact.php');
require get_theme_file_path('inc/customizer.php');
require get_theme_file_path('inc/testimonials.php');
require get_theme_file_path('inc/seo.php');

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('responsive-embeds');
    add_theme_support('woocommerce');
    add_image_size('pc-card', 720, 498, true);
    add_image_size('pc-main', 940, 650, true);
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('pc-fonts', 'https://fonts.googleapis.com/css2?family=Inter+Tight:wght@400;500;600&display=swap', [], null);
    $css = get_theme_file_path('assets/css/styles.css');
    wp_enqueue_style('pc-design', get_theme_file_uri('assets/css/styles.css'), [], file_exists($css) ? filemtime($css) : '1');
    wp_enqueue_style('pc-theme', get_stylesheet_uri(), ['pc-design'], filemtime(get_stylesheet_directory() . '/style.css'));
    if (class_exists('WooCommerce')) {
        $wc = get_theme_file_path('assets/css/woocommerce.css');
        wp_enqueue_style('pc-woo', get_theme_file_uri('assets/css/woocommerce.css'), ['pc-theme'], file_exists($wc) ? filemtime($wc) : '1');
    }
});

/* Titre des fiches véhicule : « Kia Sorento 2013 à Granby — 9 995 $ » */
add_filter('document_title_parts', function ($parts) {
    if (is_singular('product') && pc_is_vehicle(get_the_ID())) {
        $c = pc_car(get_the_ID());
        $parts['title'] = $c['name'] . ' ' . $c['year'] . ' à ' . $c['location'] . ' — ' . pc_price($c['price']);
    }
    return $parts;
});

/* À l'activation : permaliens propres */
add_action('after_switch_theme', function () {
    flush_rewrite_rules();
});

/* Ancienne adresse /inventaire/ → nouvelle page « Acheter une auto usagée » (redirection permanente) */
add_action('template_redirect', function () {
    $path = trim((string) wp_parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    if ($path === 'inventaire') {
        $qs = $_SERVER['QUERY_STRING'] ?? '';
        wp_safe_redirect(pc_inv_url($qs ? '?' . $qs : ''), 301);
        exit;
    }
}, 1);
