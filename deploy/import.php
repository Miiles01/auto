<?php
// wp eval-file import.php — importe les véhicules (fiches + photos dans la médiathèque)
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
$dir = getenv('HOME') . '/pc-import';
$cars = json_decode(file_get_contents("$dir/vehicles.json"), true);
$root = term_exists('Véhicules', 'product_cat') ?: wp_insert_term('Véhicules', 'product_cat', ['slug' => 'vehicules']);
$root = is_array($root) ? (int) $root['term_id'] : (int) $root;
$bodyTerms = [];
foreach (['vus' => 'VUS', 'auto' => 'Berlines et compactes', 'coupe' => 'Berlines et compactes', 'camion' => 'Camionnettes'] as $k => $label) {
    if (!isset($bodyTerms[$label])) {
        $t = term_exists($label, 'product_cat', $root) ?: wp_insert_term($label, 'product_cat', ['parent' => $root]);
        $bodyTerms[$label] = is_array($t) ? (int) $t['term_id'] : (int) $t;
    }
    $bodyTerms[$k] = $bodyTerms[$label];
}
foreach ($cars as $c) {
    if (wc_get_product_id_by_sku($c['stock'])) { WP_CLI::log("déjà là : {$c['slug']}"); continue; }
    $p = new WC_Product_Simple();
    $p->set_name($c['title']);
    $p->set_slug($c['slug']);
    $p->set_status('publish');
    $p->set_regular_price((string) $c['price']);
    $p->set_sku($c['stock']);
    $p->set_manage_stock(false);
    $p->set_stock_status('instock');
    $p->set_category_ids([$root, $bodyTerms[$c['body']]]);
    $id = $p->save();
    $ids = [];
    foreach ($c['images'] as $n => $img) {
        $tmp = wp_tempnam($img);
        copy("$dir/$img", $tmp);
        $att = media_handle_sideload(['name' => basename($img), 'tmp_name' => $tmp], $id, $c['title'] . ' ' . $c['year'] . ', photo ' . ($n + 1));
        if (is_wp_error($att)) { WP_CLI::warning($att->get_error_message()); @unlink($tmp); continue; }
        update_post_meta($att, '_wp_attachment_image_alt', $c['title'] . ' ' . $c['year']);
        $ids[] = $att;
    }
    if ($ids) { $p->set_image_id($ids[0]); $p->set_gallery_image_ids(array_slice($ids, 1)); }
    $p->save();
    foreach (['year', 'km', 'transmission', 'engine', 'drivetrain', 'color', 'vin', 'location', 'body'] as $k) { update_post_meta($id, 'pc_' . $k, (string) $c[$k]); }
    update_post_meta($id, 'pc_options', implode("\n", $c['options']));
    update_post_meta($id, 'pc_featured', (string) $c['featured']);
    update_post_meta($id, 'pc_reserved', (string) $c['reserved']);
    WP_CLI::success("{$c['slug']} : " . count($ids) . ' photos');
}
