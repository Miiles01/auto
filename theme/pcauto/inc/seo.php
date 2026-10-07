<?php
/**
 * SEO de base : titres, descriptions, Open Graph et données structurées (AutoDealer).
 * Se désactive tout seul si un plugin SEO (Yoast, Rank Math, AIOSEO, SEOPress) est actif.
 * Les valeurs par défaut suivent le document « Análisis KW & Arquitectura Web » du spécialiste SEO ;
 * chaque page, article ou produit peut les remplacer avec la boîte « SEO ».
 */
if (!defined('ABSPATH')) { exit; }

function pc_seo_plugin_active() {
    return defined('WPSEO_VERSION') || class_exists('RankMath') || defined('AIOSEO_VERSION') || defined('SEOPRESS_VERSION');
}

function pc_seo_defaults() {
    return [
        'home' => ['Concessionnaire d’autos usagées au Québec | PC Auto', 'PC Auto, concessionnaire multimarque d’autos usagées à Granby et Sainte-Eulalie. Véhicules inspectés et garantis, financement sur place, service en quatre langues.'],
        'acheter-une-auto-usagee' => ['Acheter une auto usagée au Québec | PC Auto', 'Achetez une auto usagée inspectée et garantie chez PC Auto : VUS, berlines et camionnettes, prix affichés, à Granby et à Sainte-Eulalie.'],
        'vendre-mon-auto' => ['Vendre mon auto usagée au Québec | PC Auto', 'Vendez votre auto usagée à PC Auto : décrivez votre véhicule, recevez notre évaluation et une offre, avec ou sans échange.'],
        'financement-auto-usage' => ['Financement auto usagé | PC Auto', 'Financement auto usagé simple et rapide, directement chez PC Auto à Granby et Sainte-Eulalie. Faites votre demande en ligne.'],
        'a-propos' => ['À propos de PC Auto | Concessionnaire à Granby', 'PC Auto, concessionnaire d’autos usagées à Granby et Sainte-Eulalie : service en français, anglais, espagnol et portugais.'],
        'contact' => ['Contact | PC Auto Granby et Sainte-Eulalie', 'Contactez PC Auto par téléphone au 450 378-0888, par WhatsApp ou en ligne. Deux succursales : Granby et Sainte-Eulalie.'],
        'granby' => ['Concessionnaire auto usagé à Granby | PC Auto', 'PC Auto Granby, concessionnaire d’occasion au 1297, rue Principale : autos usagées inspectées, financement sur place. Appelez le 450 378-0888.'],
        'sainte-eulalie' => ['Concessionnaire auto usagé à Sainte-Eulalie | PC Auto', 'PC Auto Sainte-Eulalie, concessionnaire d’occasion au 315, rue des Bouleaux : autos usagées inspectées, financement sur place. Appelez le 450 378-0888.'],
        'blog' => ['Blog : guides et conseils d’achat | PC Auto', 'Guides et conseils pour acheter, vendre et financer une auto usagée au Québec.'],
    ];
}

/** [titre, description] pour la page courante. */
function pc_seo_current() {
    $d = pc_seo_defaults();
    $key = null;
    if (is_front_page()) { $key = 'home'; }
    elseif (is_home()) { $key = 'blog'; }
    elseif (is_page()) { $key = get_post_field('post_name', get_queried_object_id()); }
    $title = $key && isset($d[$key]) ? $d[$key][0] : '';
    $desc = $key && isset($d[$key]) ? $d[$key][1] : '';
    if (is_singular()) {
        $id = get_queried_object_id();
        if ($t = get_post_meta($id, 'pc_seo_title', true)) { $title = $t; }
        if ($x = get_post_meta($id, 'pc_seo_desc', true)) { $desc = $x; }
        if ($desc === '' && is_singular('product') && pc_is_vehicle($id)) {
            $c = pc_car($id);
            $desc = $c['name'] . ' ' . $c['year'] . ' d’occasion, ' . pc_km($c['km']) . ', ' . strtolower($c['transmission']) . ', ' . $c['drivetrain'] . '. ' . html_entity_decode(pc_price($c['price'])) . ' chez PC Auto à ' . $c['location'] . '.';
        }
        if ($desc === '' && is_singular('post')) { $desc = wp_trim_words(get_the_excerpt($id), 28); }
    }
    return [$title, $desc];
}

add_action('after_setup_theme', function () {
    if (pc_seo_plugin_active()) { return; }

    add_filter('pre_get_document_title', function ($t) {
        $cur = pc_seo_current();
        return $cur[0] ?: $t;
    }, 20);

    add_action('wp_head', function () {
        $cur = pc_seo_current();
        $title = $cur[0] ?: wp_get_document_title();
        $desc = $cur[1];
        if ($desc) { echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n"; }
        echo '<meta property="og:locale" content="fr_CA">' . "\n";
        echo '<meta property="og:site_name" content="PC Auto">' . "\n";
        echo '<meta property="og:type" content="' . (is_singular('post') ? 'article' : 'website') . '">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
        if ($desc) { echo '<meta property="og:description" content="' . esc_attr($desc) . '">' . "\n"; }
        echo '<meta property="og:url" content="' . esc_url(is_singular() ? get_permalink() : home_url(add_query_arg([]))) . '">' . "\n";
        $img = '';
        if (is_singular() && has_post_thumbnail()) { $img = get_the_post_thumbnail_url(null, 'pc-main'); }
        if (!$img) { $img = get_theme_file_uri('assets/video/hero-poster.webp'); }
        echo '<meta property="og:image" content="' . esc_url($img) . '">' . "\n";
        if (is_search() || isset($_GET['achat']) || isset($_GET['envoye']) || isset($_GET['erreur'])) { echo '<meta name="robots" content="noindex,follow">' . "\n"; }
    }, 1);

    add_action('wp_head', 'pc_seo_schema', 5);
});

/** Données structurées : concessionnaire (accueil, contact, à propos) ou succursale (pages locales). */
function pc_seo_schema() {
    $hours = [
        ['@type' => 'OpeningHoursSpecification', 'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'], 'opens' => '08:00', 'closes' => '18:00'],
        ['@type' => 'OpeningHoursSpecification', 'dayOfWeek' => 'Saturday', 'opens' => '10:00', 'closes' => '13:00'],
    ];
    $addr = [
        'granby' => ['streetAddress' => '1297, rue Principale', 'addressLocality' => 'Granby', 'postalCode' => 'J2J 0M3'],
        'sainte-eulalie' => ['streetAddress' => '315, rue des Bouleaux', 'addressLocality' => 'Sainte-Eulalie', 'postalCode' => 'G0Z 1E0'],
    ];
    $postal = function ($k) use ($addr) { return array_merge(['@type' => 'PostalAddress', 'addressRegion' => 'QC', 'addressCountry' => 'CA'], $addr[$k]); };
    $slug = is_page() ? get_post_field('post_name', get_queried_object_id()) : '';
    $data = null;
    if (isset($addr[$slug])) {
        $data = ['@context' => 'https://schema.org', '@type' => 'AutoDealer', 'name' => 'PC Auto ' . $addr[$slug]['addressLocality'], 'url' => get_permalink(), 'telephone' => '+1-450-378-0888', 'knowsLanguage' => ['fr', 'en', 'es', 'pt'], 'address' => $postal($slug), 'openingHoursSpecification' => $hours, 'parentOrganization' => ['@type' => 'Organization', 'name' => 'PC Auto', 'url' => home_url('/')]];
    } elseif (is_front_page() || in_array($slug, ['contact', 'a-propos'], true)) {
        $data = ['@context' => 'https://schema.org', '@type' => 'AutoDealer', 'name' => 'PC Auto', 'url' => home_url('/'), 'telephone' => '+1-450-378-0888', 'knowsLanguage' => ['fr', 'en', 'es', 'pt'], 'address' => $postal('granby'), 'openingHoursSpecification' => $hours,
            'department' => [
                ['@type' => 'AutoDealer', 'name' => 'PC Auto Granby', 'url' => pc_url('granby'), 'telephone' => '+1-450-378-0888', 'address' => $postal('granby')],
                ['@type' => 'AutoDealer', 'name' => 'PC Auto Sainte-Eulalie', 'url' => pc_url('sainte-eulalie'), 'telephone' => '+1-450-378-0888', 'address' => $postal('sainte-eulalie')],
            ]];
    } elseif (is_singular('product') && pc_is_vehicle(get_queried_object_id())) {
        $c = pc_car(get_queried_object_id());
        $data = ['@context' => 'https://schema.org', '@type' => 'Car', 'name' => $c['name'] . ' ' . $c['year'], 'model' => $c['name'], 'vehicleModelDate' => (string) $c['year'], 'vehicleIdentificationNumber' => $c['vin'], 'color' => $c['color'],
            'mileageFromOdometer' => ['@type' => 'QuantitativeValue', 'value' => $c['km'], 'unitCode' => 'KMT'], 'vehicleTransmission' => $c['transmission'], 'driveWheelConfiguration' => $c['drivetrain'],
            'offers' => ['@type' => 'Offer', 'price' => $c['price'], 'priceCurrency' => 'CAD', 'availability' => $c['reserved'] ? 'https://schema.org/LimitedAvailability' : 'https://schema.org/InStock', 'seller' => ['@type' => 'AutoDealer', 'name' => 'PC Auto ' . $c['location']]]];
        if ($c['gallery']) { $data['image'] = array_values(array_filter(array_map('wp_get_attachment_url', array_slice($c['gallery'], 0, 5)))); }
    }
    if ($data) { echo '<script type="application/ld+json">' . wp_json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n"; }
}

/* ---- Boîte « SEO » (titre et description personnalisés) ---- */
add_action('add_meta_boxes', function () {
    if (pc_seo_plugin_active()) { return; }
    foreach (['page', 'post', 'product'] as $t) {
        add_meta_box('pc_seo', 'SEO (titre et description)', function ($post) {
            wp_nonce_field('pc_seo_save', 'pc_seo_nonce');
            $d = pc_seo_defaults()[$post->post_name] ?? ['', ''];
            printf('<p><label>Titre SEO<br><input class="widefat" name="pc_seo_title" value="%s" placeholder="%s"></label></p>', esc_attr(get_post_meta($post->ID, 'pc_seo_title', true)), esc_attr($d[0]));
            printf('<p><label>Description (≈ 155 caractères)<br><textarea class="widefat" rows="3" name="pc_seo_desc" placeholder="%s">%s</textarea></label></p>', esc_attr($d[1]), esc_textarea(get_post_meta($post->ID, 'pc_seo_desc', true)));
            echo '<p class="description">Laisser vide pour utiliser la valeur proposée.</p>';
        }, $t, 'normal', 'default');
    }
});
add_action('save_post', function ($id) {
    if (!isset($_POST['pc_seo_nonce']) || !wp_verify_nonce($_POST['pc_seo_nonce'], 'pc_seo_save') || !current_user_can('edit_post', $id)) { return; }
    foreach (['pc_seo_title', 'pc_seo_desc'] as $k) {
        $v = sanitize_text_field(wp_unslash($_POST[$k] ?? ''));
        if ($v === '') { delete_post_meta($id, $k); } else { update_post_meta($id, $k, $v); }
    }
});
