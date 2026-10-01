<?php
if (!defined('ABSPATH')) { exit; }

function pc_register_cpt() {
    register_post_type('vehicule', [
        'labels' => [
            'name' => 'Véhicules', 'singular_name' => 'Véhicule', 'add_new' => 'Ajouter un véhicule',
            'add_new_item' => 'Ajouter un véhicule', 'edit_item' => 'Modifier le véhicule', 'new_item' => 'Nouveau véhicule',
            'view_item' => 'Voir le véhicule', 'search_items' => 'Rechercher un véhicule', 'all_items' => 'Tous les véhicules',
            'not_found' => 'Aucun véhicule', 'menu_name' => 'Véhicules',
        ],
        'public' => true, 'has_archive' => false, 'menu_icon' => 'dashicons-car', 'menu_position' => 5,
        'supports' => ['title'], 'rewrite' => ['slug' => 'vehicule', 'with_front' => false],
    ]);
}
add_action('init', 'pc_register_cpt');

/* ---- Champs --------------------------------------------------------------- */
function pc_fields() {
    return [
        'year' => ['Année', 'number'], 'price' => ['Prix ($ CA)', 'number'], 'compare_at' => ['Ancien prix (si prix réduit)', 'number'],
        'km' => ['Kilométrage', 'number'], 'transmission' => ['Transmission', 'text'], 'engine' => ['Moteur', 'text'],
        'drivetrain' => ['Motricité (FWD, RWD, AWD, 4x4)', 'text'], 'color' => ['Couleur', 'text'],
        'stock' => ['Numéro de stock', 'text'], 'vin' => ['Numéro de série (NIV)', 'text'],
    ];
}

add_action('add_meta_boxes', function () {
    add_meta_box('pc_vehicle', 'Détails du véhicule', 'pc_metabox', 'vehicule', 'normal', 'high');
});

function pc_metabox($post) {
    wp_nonce_field('pc_save', 'pc_meta_nonce');
    $v = function ($k, $d = '') use ($post) { $x = get_post_meta($post->ID, 'pc_' . $k, true); return $x === '' ? $d : $x; };
    echo '<p style="color:#666">Le titre de la fiche est « Marque Modèle » (ex. : Kia Sorento). L’année se saisit ci-dessous.</p>';
    echo '<table class="form-table"><tbody>';
    foreach (pc_fields() as $k => $f) {
        printf('<tr><th><label for="pc_%1$s">%2$s</label></th><td><input class="regular-text" type="%3$s" id="pc_%1$s" name="pc_%1$s" value="%4$s"></td></tr>', esc_attr($k), esc_html($f[0]), esc_attr($f[1]), esc_attr($v($k)));
    }
    echo '<tr><th><label for="pc_location">Succursale</label></th><td><select id="pc_location" name="pc_location">';
    foreach (['Granby', 'Sainte-Eulalie'] as $l) { printf('<option%s>%s</option>', selected($v('location', 'Granby'), $l, false), esc_html($l)); }
    echo '</select></td></tr><tr><th><label for="pc_body">Catégorie</label></th><td><select id="pc_body" name="pc_body">';
    foreach (['vus' => 'VUS', 'auto' => 'Berline et compacte', 'coupe' => 'Coupé sport', 'camion' => 'Camionnette'] as $k => $l) { printf('<option value="%s"%s>%s</option>', $k, selected($v('body', 'auto'), $k, false), esc_html($l)); }
    echo '</select></td></tr>';
    printf('<tr><th>Statut</th><td><label><input type="checkbox" name="pc_reserved" value="1"%s> Réservé</label> &nbsp; <label><input type="checkbox" name="pc_featured" value="1"%s> En vedette (affiché en premier sur l’accueil)</label></td></tr>', checked($v('reserved'), '1', false), checked($v('featured'), '1', false));
    printf('<tr><th><label for="pc_options">Équipements et options</label></th><td><textarea class="large-text" rows="6" id="pc_options" name="pc_options">%s</textarea><p class="description">Une option par ligne.</p></td></tr>', esc_textarea($v('options')));
    echo '</tbody></table>';

    $ids = array_filter(array_map('absint', explode(',', (string) $v('gallery'))));
    echo '<h4>Photos</h4><p class="description">La première photo est la photo principale. Pour changer l’ordre, retirez puis rajoutez les photos.</p>';
    printf('<input type="hidden" id="pc_gallery" name="pc_gallery" value="%s"><div id="pc_gallery_preview" style="display:flex;flex-wrap:wrap;gap:6px;margin:8px 0">', esc_attr(implode(',', $ids)));
    foreach ($ids as $id) { echo '<span data-id="' . $id . '" style="position:relative">' . wp_get_attachment_image($id, [90, 62], false, ['style' => 'display:block;width:90px;height:62px;object-fit:cover']) . '<a href="#" class="pc-rm" style="position:absolute;top:0;right:0;background:#d63638;color:#fff;padding:0 5px;text-decoration:none">×</a></span>'; }
    echo '</div><button type="button" class="button" id="pc_gallery_pick">Ajouter des photos</button>';
    ?>
    <script>
    jQuery(function ($) {
        var input = $('#pc_gallery'), box = $('#pc_gallery_preview');
        var sync = function () { input.val(box.children().map(function () { return $(this).data('id'); }).get().join(',')); };
        box.on('click', '.pc-rm', function (e) { e.preventDefault(); $(this).parent().remove(); sync(); });
        $('#pc_gallery_pick').on('click', function (e) {
            e.preventDefault();
            var frame = wp.media({ title: 'Photos du véhicule', multiple: true, library: { type: 'image' } });
            frame.on('select', function () {
                frame.state().get('selection').each(function (a) {
                    var m = a.toJSON(), url = (m.sizes && m.sizes.thumbnail) ? m.sizes.thumbnail.url : m.url;
                    box.append('<span data-id="' + m.id + '" style="position:relative"><img src="' + url + '" style="display:block;width:90px;height:62px;object-fit:cover"><a href="#" class="pc-rm" style="position:absolute;top:0;right:0;background:#d63638;color:#fff;padding:0 5px;text-decoration:none">×</a></span>');
                });
                sync();
            });
            frame.open();
        });
    });
    </script>
    <?php
}

add_action('admin_enqueue_scripts', function ($hook) {
    if (in_array($hook, ['post.php', 'post-new.php'], true) && get_post_type() === 'vehicule') { wp_enqueue_media(); }
});

add_action('save_post_vehicule', function ($id) {
    if (!isset($_POST['pc_meta_nonce']) || !wp_verify_nonce($_POST['pc_meta_nonce'], 'pc_save')) { return; }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return; }
    if (!current_user_can('edit_post', $id)) { return; }
    foreach (pc_fields() as $k => $f) {
        $raw = wp_unslash($_POST['pc_' . $k] ?? '');
        update_post_meta($id, 'pc_' . $k, $f[1] === 'number' ? (string) absint($raw) : sanitize_text_field($raw));
    }
    $loc = sanitize_text_field(wp_unslash($_POST['pc_location'] ?? 'Granby'));
    update_post_meta($id, 'pc_location', in_array($loc, ['Granby', 'Sainte-Eulalie'], true) ? $loc : 'Granby');
    $body = sanitize_key($_POST['pc_body'] ?? 'auto');
    update_post_meta($id, 'pc_body', in_array($body, ['vus', 'auto', 'coupe', 'camion'], true) ? $body : 'auto');
    update_post_meta($id, 'pc_reserved', empty($_POST['pc_reserved']) ? '0' : '1');
    update_post_meta($id, 'pc_featured', empty($_POST['pc_featured']) ? '0' : '1');
    update_post_meta($id, 'pc_options', sanitize_textarea_field(wp_unslash($_POST['pc_options'] ?? '')));
    update_post_meta($id, 'pc_gallery', implode(',', array_filter(array_map('absint', explode(',', wp_unslash($_POST['pc_gallery'] ?? ''))))));
});

/* ---- Colonnes de la liste ---------------------------------------------------- */
add_filter('manage_vehicule_posts_columns', function ($c) {
    return ['cb' => $c['cb'], 'pc_thumb' => '', 'title' => 'Véhicule', 'pc_year' => 'Année', 'pc_price' => 'Prix', 'pc_loc' => 'Succursale', 'date' => $c['date']];
});
add_action('manage_vehicule_posts_custom_column', function ($col, $id) {
    $c = pc_car($id);
    if ($col === 'pc_thumb') { echo $c['gallery'] ? wp_get_attachment_image($c['gallery'][0], [60, 42]) : ''; }
    if ($col === 'pc_year') { echo (int) $c['year']; }
    if ($col === 'pc_price') { echo esc_html(pc_price($c['price'])); }
    if ($col === 'pc_loc') { echo esc_html($c['location']); }
}, 10, 2);
