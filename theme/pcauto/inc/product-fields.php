<?php
/**
 * Les véhicules sont des PRODUITS WooCommerce.
 *  - Prix, photos, UGS (= numéro de stock), catégories : champs natifs de WooCommerce.
 *  - Année, kilométrage, succursale, etc. : boîte « Détails du véhicule » ci-dessous.
 * Un produit est un véhicule s'il a une année (méta pc_year). Les autres produits
 * (accessoires…) restent dans la boutique /shop/.
 */
if (!defined('ABSPATH')) { exit; }

function pc_fields() {
    return [
        'year' => ['Année', 'number'], 'km' => ['Kilométrage', 'number'],
        'transmission' => ['Transmission', 'text'], 'engine' => ['Moteur', 'text'],
        'drivetrain' => ['Motricité (FWD, RWD, AWD, 4x4)', 'text'], 'color' => ['Couleur', 'text'],
        'vin' => ['Numéro de série (NIV)', 'text'],
    ];
}

add_action('add_meta_boxes', function () {
    add_meta_box('pc_vehicle', 'Détails du véhicule (laisser l’année vide pour un produit ordinaire)', 'pc_metabox', 'product', 'normal', 'high');
});

function pc_metabox($post) {
    wp_nonce_field('pc_save', 'pc_meta_nonce');
    $v = function ($k, $d = '') use ($post) { $x = get_post_meta($post->ID, 'pc_' . $k, true); return $x === '' ? $d : $x; };
    echo '<p style="color:#666">Titre du produit = « Marque Modèle » (ex. : Kia Sorento). Le prix, les photos, l’UGS (numéro de stock) et la catégorie se règlent avec les champs habituels de WooCommerce.</p>';
    echo '<table class="form-table"><tbody>';
    foreach (pc_fields() as $k => $f) {
        printf('<tr><th><label for="pc_%1$s">%2$s</label></th><td><input class="regular-text" type="%3$s" id="pc_%1$s" name="pc_%1$s" value="%4$s"></td></tr>', esc_attr($k), esc_html($f[0]), esc_attr($f[1]), esc_attr($v($k)));
    }
    echo '<tr><th><label for="pc_location">Succursale</label></th><td><select id="pc_location" name="pc_location">';
    foreach (['Granby', 'Sainte-Eulalie'] as $l) { printf('<option%s>%s</option>', selected($v('location', 'Granby'), $l, false), esc_html($l)); }
    echo '</select></td></tr><tr><th><label for="pc_body">Type de véhicule</label></th><td><select id="pc_body" name="pc_body">';
    foreach (['vus' => 'VUS', 'auto' => 'Berline et compacte', 'coupe' => 'Coupé sport', 'camion' => 'Camionnette'] as $k => $l) { printf('<option value="%s"%s>%s</option>', $k, selected($v('body', 'auto'), $k, false), esc_html($l)); }
    echo '</select></td></tr>';
    printf('<tr><th>Statut</th><td><label><input type="checkbox" name="pc_reserved" value="1"%s> Réservé</label> &nbsp; <label><input type="checkbox" name="pc_featured" value="1"%s> En vedette (affiché en premier sur l’accueil)</label></td></tr>', checked($v('reserved'), '1', false), checked($v('featured'), '1', false));
    printf('<tr><th><label for="pc_options">Équipements et options</label></th><td><textarea class="large-text" rows="6" id="pc_options" name="pc_options">%s</textarea><p class="description">Une option par ligne.</p></td></tr>', esc_textarea($v('options')));
    echo '</tbody></table>';
}

add_action('save_post_product', function ($id) {
    if (!isset($_POST['pc_meta_nonce']) || !wp_verify_nonce($_POST['pc_meta_nonce'], 'pc_save')) { return; }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return; }
    if (!current_user_can('edit_post', $id)) { return; }
    foreach (pc_fields() as $k => $f) {
        $raw = wp_unslash($_POST['pc_' . $k] ?? '');
        if ($f[1] === 'number') { $raw = $raw === '' ? '' : (string) absint($raw); } else { $raw = sanitize_text_field($raw); }
        if ($raw === '') { delete_post_meta($id, 'pc_' . $k); } else { update_post_meta($id, 'pc_' . $k, $raw); }
    }
    $loc = sanitize_text_field(wp_unslash($_POST['pc_location'] ?? 'Granby'));
    update_post_meta($id, 'pc_location', in_array($loc, ['Granby', 'Sainte-Eulalie'], true) ? $loc : 'Granby');
    $body = sanitize_key($_POST['pc_body'] ?? 'auto');
    update_post_meta($id, 'pc_body', in_array($body, ['vus', 'auto', 'coupe', 'camion'], true) ? $body : 'auto');
    update_post_meta($id, 'pc_reserved', empty($_POST['pc_reserved']) ? '0' : '1');
    update_post_meta($id, 'pc_featured', empty($_POST['pc_featured']) ? '0' : '1');
    update_post_meta($id, 'pc_options', sanitize_textarea_field(wp_unslash($_POST['pc_options'] ?? '')));
});

/* La boutique /shop/ ne montre pas les véhicules (ils ont leurs pages Inventaire / succursales). */
add_action('woocommerce_product_query', function ($q) {
    $meta = (array) $q->get('meta_query');
    $meta[] = ['key' => 'pc_year', 'compare' => 'NOT EXISTS'];
    $q->set('meta_query', $meta);
});

/* Les véhicules ne s'achètent pas en ligne, sauf si le réglage du thème l'autorise. */
add_filter('woocommerce_is_purchasable', function ($ok, $product) {
    if (get_post_meta($product->get_id(), 'pc_year', true) !== '' && !get_theme_mod('pc_online_purchase', false)) { return false; }
    return $ok;
}, 10, 2);
