<?php
/** Témoignages : uniquement de vrais avis. La section se cache tant qu'il n'y en a aucun. */
if (!defined('ABSPATH')) { exit; }

add_action('init', function () {
    register_post_type('temoignage', [
        'labels' => ['name' => 'Témoignages', 'singular_name' => 'Témoignage', 'add_new_item' => 'Ajouter un témoignage', 'edit_item' => 'Modifier le témoignage', 'menu_name' => 'Témoignages', 'all_items' => 'Tous les témoignages'],
        'public' => false, 'show_ui' => true, 'menu_icon' => 'dashicons-format-quote', 'menu_position' => 6,
        'supports' => ['title', 'editor', 'thumbnail'],
    ]);
});

add_action('add_meta_boxes', function () {
    add_meta_box('pc_temoignage', 'Détails', function ($post) {
        wp_nonce_field('pc_tem', 'pc_tem_nonce');
        foreach (['pc_ville' => 'Ville', 'pc_vehicule_achete' => 'Véhicule acheté (ex. : Kia Sorento 2016)', 'pc_source' => 'Source (ex. : Avis Google)'] as $k => $l) {
            printf('<p><label>%s<br><input class="widefat" name="%s" value="%s"></label></p>', esc_html($l), esc_attr($k), esc_attr(get_post_meta($post->ID, $k, true)));
        }
        echo '<p class="description">Titre = nom du client (ex. : Sophie L.). Texte = l’avis, avec l’accord du client. Image à la une = photo (facultatif).</p>';
    }, 'temoignage', 'side');
});
add_action('save_post_temoignage', function ($id) {
    if (!isset($_POST['pc_tem_nonce']) || !wp_verify_nonce($_POST['pc_tem_nonce'], 'pc_tem') || !current_user_can('edit_post', $id)) { return; }
    foreach (['pc_ville', 'pc_vehicule_achete', 'pc_source'] as $k) { update_post_meta($id, $k, sanitize_text_field(wp_unslash($_POST[$k] ?? ''))); }
});

add_action('customize_register', function ($wp) {
    $wp->add_setting('pc_google_rating', ['default' => '', 'sanitize_callback' => 'sanitize_text_field']);
    $wp->add_control('pc_google_rating', ['section' => 'pc_options', 'type' => 'text', 'label' => 'Note Google (ex. : 4,8)', 'description' => 'Laisser vide tant que la note n’est pas confirmée.']);
    $wp->add_setting('pc_google_count', ['default' => '', 'sanitize_callback' => 'absint']);
    $wp->add_control('pc_google_count', ['section' => 'pc_options', 'type' => 'number', 'label' => 'Nombre d’avis Google']);
    $wp->add_setting('pc_google_date', ['default' => '', 'sanitize_callback' => 'sanitize_text_field']);
    $wp->add_control('pc_google_date', ['section' => 'pc_options', 'type' => 'text', 'label' => 'Date de la note (ex. : octobre 2026)']);
});

function pc_testimonials() {
    return get_posts(['post_type' => 'temoignage', 'post_status' => 'publish', 'posts_per_page' => 12, 'orderby' => 'date', 'order' => 'DESC']);
}
