<?php
if (!defined('ABSPATH')) { exit; }

const PC_WA_PHONE = '14503780888';

function pc_icon($name) {
    static $icons = null;
    if ($icons === null) { $icons = require get_theme_file_path('inc/icons.php'); }
    return $icons[$name] ?? '';
}

function pc_price($n) {
    return $n ? number_format((float) $n, 0, ',', "\u{00A0}") . "\u{00A0}$" : 'Prix sur demande';
}
function pc_km($n) { return number_format((int) $n, 0, ',', "\u{00A0}") . ' km'; }
function pc_wa($text = '') { return 'https://api.whatsapp.com/send?phone=' . PC_WA_PHONE . '&text=' . rawurlencode($text); }
function pc_url($key) {
    $p = ['acheter' => '/acheter-une-auto-usagee/', 'vendre' => '/vendre-mon-auto/', 'financement' => '/financement-auto-usage/',
          'apropos' => '/a-propos/', 'contact' => '/contact/', 'granby' => '/granby/', 'sainte-eulalie' => '/sainte-eulalie/'];
    return $key === 'blog' ? pc_blog_url() : home_url($p[$key] ?? '/');
}
/** Page « Acheter » = inventaire complet. */
function pc_inv_url($qs = '') { return pc_url('acheter') . $qs; }
function pc_body_label($k) {
    $l = ['vus' => 'VUS', 'camion' => 'Camionnette', 'auto' => 'Berline et compacte', 'coupe' => 'Coupé sport'];
    return $l[$k] ?? '';
}
function pc_prep_count() { return (int) get_theme_mod('pc_prep_count', 5); }
function pc_total() { return count(pc_ids()); }
function pc_is_vehicle($id) { return get_post_meta($id, 'pc_year', true) !== ''; }

function pc_is_elementor($id) {
    if (!$id || !class_exists('\Elementor\Plugin')) { return false; }
    $doc = \Elementor\Plugin::$instance->documents->get($id);
    return $doc && $doc->is_built_with_elementor();
}

/** Toutes les données d'un véhicule (produit WooCommerce) dans un tableau. */
function pc_car($id) {
    $m = function ($k, $d = '') use ($id) {
        $v = get_post_meta($id, 'pc_' . $k, true);
        return ($v === '' || $v === false) ? $d : $v;
    };
    $gallery = [];
    if ($thumb = (int) get_post_thumbnail_id($id)) { $gallery[] = $thumb; }
    foreach (explode(',', (string) get_post_meta($id, '_product_image_gallery', true)) as $g) { if ((int) $g) { $gallery[] = (int) $g; } }
    $options = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $m('options')))));
    $price = (float) get_post_meta($id, '_price', true);
    $regular = (float) get_post_meta($id, '_regular_price', true);
    $sale = get_post_meta($id, '_sale_price', true);
    return [
        'id' => $id,
        'name' => get_the_title($id),
        'url' => get_permalink($id),
        'year' => (int) $m('year', 0),
        'price' => (int) round($price),
        'compare_at' => ($sale !== '' && $regular > $price) ? (int) round($regular) : 0,
        'km' => (int) $m('km', 0),
        'transmission' => $m('transmission'),
        'engine' => $m('engine'),
        'drivetrain' => $m('drivetrain'),
        'color' => $m('color'),
        'stock' => (string) get_post_meta($id, '_sku', true),
        'vin' => $m('vin'),
        'location' => $m('location', 'Granby'),
        'body' => $m('body', 'auto'),
        'reserved' => $m('reserved') === '1',
        'featured' => $m('featured') === '1',
        'options' => $options,
        'gallery' => array_values(array_unique($gallery)),
    ];
}

function pc_img($att, $size, $alt = '', $attrs = []) {
    if (!$att) { return ''; }
    return wp_get_attachment_image($att, $size, false, array_merge(['alt' => $alt, 'loading' => 'lazy', 'decoding' => 'async'], $attrs));
}

/** Arguments WP_Query pour l'inventaire (filtres de l'URL ou de l'accueil). */
function pc_query_args($f = [], $per = -1, $order = 'recent') {
    $meta = [
        'relation' => 'AND',
        'pc_year_c' => ['key' => 'pc_year', 'type' => 'NUMERIC', 'compare' => 'EXISTS'],
        'pc_price_c' => ['key' => '_price', 'type' => 'NUMERIC', 'compare' => 'EXISTS'],
        'pc_km_c' => ['key' => 'pc_km', 'type' => 'NUMERIC', 'compare' => 'EXISTS'],
        'pc_feat_c' => ['key' => 'pc_featured', 'compare' => 'EXISTS'],
    ];
    $type = $f['type'] ?? '';
    if ($type === 'vus' || $type === 'camion') { $meta[] = ['key' => 'pc_body', 'value' => $type]; }
    if ($type === 'auto') { $meta[] = ['key' => 'pc_body', 'value' => ['auto', 'coupe'], 'compare' => 'IN']; }
    if ($type === 'awd') { $meta[] = ['key' => 'pc_drivetrain', 'value' => 'AWD|4x4', 'compare' => 'REGEXP']; }
    if (!empty($f['succursale'])) { $meta[] = ['key' => 'pc_location', 'value' => $f['succursale']]; }
    if (!empty($f['prix'])) { $meta[] = ['key' => '_price', 'value' => (int) $f['prix'], 'type' => 'NUMERIC', 'compare' => '<=']; }
    if (!empty($f['prix_lt'])) { $meta[] = ['key' => '_price', 'value' => (int) $f['prix_lt'], 'type' => 'NUMERIC', 'compare' => '<']; }

    $words = [];
    foreach (preg_split('/\s+/', trim((string) ($f['q'] ?? ''))) as $w) {
        if ($w === '') { continue; }
        if (preg_match('/^(19|20)\d{2}$/', $w)) { $meta[] = ['key' => 'pc_year', 'value' => (int) $w, 'type' => 'NUMERIC']; }
        else { $words[] = $w; }
    }

    $orders = [
        'recent' => ['pc_year_c' => 'DESC', 'pc_price_c' => 'ASC'],
        'prix-asc' => ['pc_price_c' => 'ASC'],
        'prix-desc' => ['pc_price_c' => 'DESC'],
        'km' => ['pc_km_c' => 'ASC'],
        'home' => ['pc_feat_c' => 'DESC', 'pc_year_c' => 'DESC', 'ID' => 'DESC'],
    ];
    $args = [
        'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => $per,
        'meta_query' => $meta, 'orderby' => $orders[$order] ?? $orders['recent'],
        'no_found_rows' => true, 'fields' => 'ids',
    ];
    if ($words) { $args['s'] = implode(' ', $words); }
    return $args;
}
function pc_ids($f = [], $per = -1, $order = 'recent') { return get_posts(pc_query_args($f, $per, $order)); }

/** Catégories (liste « Parcourir » et pastilles). $branch = nom de succursale pour limiter à une succursale. */
function pc_cats($branch = '') {
    $b = $branch ? pc_branch_by_name($branch) : null;
    $link = function ($type, $qs) use ($b) { return $b ? $b['url'] . ($type ? '?type=' . $type . '#inventaire' : '#inventaire') : pc_inv_url($qs); };
    $cats = [
        ['key' => 'all', 'label' => 'Tous les véhicules', 'href' => $link('', ''), 'f' => []],
        ['key' => 'vus', 'label' => 'VUS', 'href' => $link('vus', '?type=vus'), 'f' => ['type' => 'vus']],
        ['key' => 'auto', 'label' => 'Berlines et compactes', 'href' => $link('auto', '?type=auto'), 'f' => ['type' => 'auto']],
        ['key' => 'camion', 'label' => 'Camionnettes', 'href' => $link('camion', '?type=camion'), 'f' => ['type' => 'camion']],
        ['key' => 'awd', 'label' => '4x4 et intégrale', 'href' => $link('awd', '?type=awd'), 'f' => ['type' => 'awd']],
        ['key' => 'budget', 'label' => 'Moins de 8 000 $', 'href' => pc_inv_url('?prix=8000' . ($b ? '&succursale=' . rawurlencode($branch) : '')), 'f' => ['prix_lt' => 8000]],
    ];
    if ($branch) { foreach ($cats as &$c) { $c['f']['succursale'] = $branch; } unset($c); }
    return $cats;
}

function pc_card($id, $i = 0) {
    $c = pc_car($id);
    $g = $c['gallery'];
    $first = $g[0] ?? 0;
    $second = $g[3] ?? ($g[1] ?? $first);
    $reduced = $c['compare_at'] && $c['compare_at'] > $c['price'];
    $badge = $c['reserved'] ? '<span class="car-card__badge car-card__badge--reserved">Réservé</span>' : ($reduced ? '<span class="car-card__badge">Prix réduit</span>' : '');
    ob_start(); ?>
<article class="car-card<?php echo $c['reserved'] ? ' is-reserved' : ''; ?>">
  <div class="car-card__media">
    <span class="car-card__index">(<?php echo str_pad($i + 1, 2, '0', STR_PAD_LEFT); ?>)</span>
    <span class="chip car-card__loc"><?php echo pc_icon('pin') . esc_html($c['location']); ?></span><?php echo $badge; ?>
    <?php echo pc_img($first, 'pc-card', $c['name'] . ' ' . $c['year'] . ', vue avant'); ?>
    <?php echo $second ? pc_img($second, 'pc-card', '') : ''; ?>
  </div>
  <p class="car-card__title"><a class="car-card__link" href="<?php echo esc_url($c['url']); ?>"><?php echo esc_html($c['name']); ?> <span><?php echo (int) $c['year']; ?></span></a></p>
  <p class="car-card__spec"><?php echo esc_html(pc_km($c['km']) . ' · ' . $c['transmission'] . ' · ' . $c['drivetrain']); ?></p>
  <div class="car-card__foot"><span class="car-card__price"><span><?php echo pc_price($c['price']); ?></span><?php if ($reduced) : ?> <s class="car-card__was"><span><?php echo pc_price($c['compare_at']); ?></span></s><?php endif; ?></span><span class="car-card__more">Détails <?php echo pc_icon('arrow'); ?></span></div>
</article>
<?php
    return ob_get_clean();
}

function pc_soon_card() {
    $prep = pc_prep_count();
    if ($prep < 1) { return ''; }
    ob_start(); ?>
<article class="car-card car-card--soon">
  <div class="car-card__media"><img src="<?php echo esc_url(get_theme_file_uri('assets/img/en-preparation.webp')); ?>" alt="Véhicule sous une housse, en préparation" width="474" height="266" loading="lazy"><span class="soon-label">Bientôt en inventaire</span></div>
  <p class="car-card__title"><?php echo $prep . ($prep > 1 ? ' véhicules' : ' véhicule'); ?> <span>en préparation</span></p>
  <p class="car-card__spec">Inspection et esthétique en cours. Écrivez-nous pour être le premier informé.</p>
  <div class="car-card__foot"><a class="car-card__more car-card__link" href="<?php echo esc_url(pc_wa("Bonjour PC Auto, j'aimerais être informé(e) des prochains véhicules disponibles.")); ?>" target="_blank" rel="noopener">M’aviser <?php echo pc_icon('arrow'); ?></a></div>
</article>
<?php
    return ob_get_clean();
}

function pc_prep_words() {
    $w = ['Aucun véhicule n’est', 'Un véhicule est', 'Deux véhicules sont', 'Trois véhicules sont', 'Quatre véhicules sont', 'Cinq véhicules sont', 'Six véhicules sont', 'Sept véhicules sont', 'Huit véhicules sont', 'Neuf véhicules sont', 'Dix véhicules sont'];
    $n = pc_prep_count();
    return $w[$n] ?? $n . ' véhicules sont';
}

/* ---- Accueil ---------------------------------------------------------- */
function pc_home_pills() {
    foreach (array_slice(pc_cats(), 0, 5) as $i => $c) {
        $n = count(pc_ids($c['f']));
        printf('<a class="pill" href="%s"%s>%s <span class="pill__count">(%d)</span></a>', esc_url($c['href']), $i === 0 ? ' aria-pressed="true"' : '', $c['key'] === 'all' ? 'Tous' : esc_html($c['label']), $n);
    }
}
function pc_home_grid() {
    foreach (pc_ids([], 7, 'home') as $i => $id) { echo pc_card($id, $i); }
    echo pc_soon_card();
}
function pc_browse_list($branch = '') {
    foreach (pc_cats($branch) as $c) {
        printf('<li><a href="%s">%s<sup>%d</sup></a></li>', esc_url($c['href']), esc_html($c['label']), count(pc_ids($c['f'])));
    }
}
/** side 0 = plaque gauche, 1 = plaque droite ; la catégorie n°2 est visible au repos. */
function pc_browse_plate($side, $branch = '') {
    foreach (pc_cats($branch) as $i => $c) {
        $ids = pc_ids($c['f'], 2, 'home');
        if (!$ids) { continue; }
        $car = pc_car($side === 0 ? $ids[0] : ($ids[1] ?? $ids[0]));
        $g = $car['gallery'];
        $att = $side === 0 ? ($g[0] ?? 0) : ($g[4] ?? ($g[1] ?? ($g[0] ?? 0)));
        echo pc_img($att, 'pc-card', '', $i === 1 ? ['class' => 'is-on'] : []);
    }
}

/* ---- Succursales -------------------------------------------------------- */
function pc_branches() {
    $hours = [['Lun. au ven.', '8 h 00 – 18 h 00'], ['Samedi', '10 h 00 – 13 h 00, sur rendez-vous'], ['Dimanche', 'Fermé']];
    return [
        'granby' => [
            'key' => 'granby', 'name' => 'Granby', 'other' => 'sainte-eulalie', 'url' => home_url('/granby/'),
            'street' => '1297, rue Principale', 'city' => 'Granby (Québec) J2J 0M3',
            'query' => '1297 rue Principale, Granby, QC J2J 0M3', 'hours' => $hours,
        ],
        'sainte-eulalie' => [
            'key' => 'sainte-eulalie', 'name' => 'Sainte-Eulalie', 'other' => 'granby', 'url' => home_url('/sainte-eulalie/'),
            'street' => '315, rue des Bouleaux', 'city' => 'Sainte-Eulalie (Québec) G0Z 1E0',
            'query' => '315 rue des Bouleaux, Sainte-Eulalie, QC G0Z 1E0', 'hours' => $hours,
        ],
    ];
}
function pc_branch_by_name($name) {
    foreach (pc_branches() as $b) { if ($b['name'] === $name) { return $b; } }
    return null;
}

/* ---- Blog ---------------------------------------------------------------- */
function pc_blog_url() {
    $id = (int) get_option('page_for_posts');
    return $id ? get_permalink($id) : home_url('/blog/');
}
function pc_read_time($post_id) {
    $words = str_word_count(wp_strip_all_tags(get_post_field('post_content', $post_id)));
    return max(1, (int) ceil($words / 200));
}
function pc_post_card($id = null) {
    $id = $id ?: get_the_ID();
    $cats = get_the_category($id);
    ob_start(); ?>
<article class="post-card">
  <a class="post-card__media" href="<?php echo esc_url(get_permalink($id)); ?>" tabindex="-1" aria-hidden="true">
    <?php if (has_post_thumbnail($id)) { echo get_the_post_thumbnail($id, 'pc-card', ['loading' => 'lazy', 'alt' => '']); } else { ?><span class="post-card__ph"><?php echo esc_html(get_bloginfo('name')); ?></span><?php } ?>
  </a>
  <div class="post-card__meta"><?php if ($cats) : ?><a class="chip" href="<?php echo esc_url(get_category_link($cats[0])); ?>"><?php echo esc_html($cats[0]->name); ?></a><?php endif; ?><time datetime="<?php echo esc_attr(get_the_date('c', $id)); ?>"><?php echo esc_html(get_the_date('j F Y', $id)); ?></time></div>
  <h3 class="post-card__title"><a href="<?php echo esc_url(get_permalink($id)); ?>"><?php echo esc_html(get_the_title($id)); ?></a></h3>
  <p class="post-card__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt($id), 24)); ?></p>
  <a class="car-card__more post-card__more" href="<?php echo esc_url(get_permalink($id)); ?>">Lire l’article <?php echo pc_icon('arrow'); ?></a>
</article>
<?php
    return ob_get_clean();
}

/** Aviso « consultez la succursale » (boutique en mode catalogue : aucun achat en ligne). */
function pc_consult_notice($branch_name = '') {
    $one = $branch_name ? pc_branch_by_name($branch_name) : null;
    ob_start(); ?>
<div class="pc-consult woocommerce-info" role="status">
  <p><strong>Les achats ne se font pas en ligne.</strong> <?php echo $one ? 'Consultez la succursale de ' . esc_html($one['name']) . ' :' : 'Consultez la succursale de votre choix :'; ?></p>
  <p class="pc-consult__links">
    <?php foreach (pc_branches() as $b) : if ($one && $b['key'] !== $one['key']) { continue; } ?>
      <a class="btn btn--red" href="<?php echo esc_url($b['url']); ?>">Succursale de <?php echo esc_html($b['name']); ?></a>
    <?php endforeach; ?>
    <a class="btn" href="tel:+14503780888">450 378-0888</a>
  </p>
</div>
<?php
    return ob_get_clean();
}

/** Bloc de questions fréquentes : chaque question est un H3 (structure demandée par le SEO). */
function pc_faq($items) {
    echo '<div class="faq">';
    foreach ($items as $it) {
        echo '<div class="faq__item"><h3>' . esc_html($it[0]) . '</h3><div class="faq__a">' . wp_kses_post($it[1]) . '</div></div>';
    }
    echo '</div>';
}

/** Phrase de résumé de l'inventaire : nombre, fourchette de prix, succursales. */
function pc_inventory_summary() {
    $ids = pc_ids();
    if (!$ids) { return 'Nos véhicules sont à voir à Granby et à Sainte-Eulalie.'; }
    $prices = array_filter(array_map(function ($id) { return (int) get_post_meta($id, '_price', true); }, $ids));
    $n = count($ids);
    $txt = $n . ' véhicules en inventaire';
    if ($prices) { $txt .= ', de ' . pc_price(min($prices)) . ' à ' . pc_price(max($prices)); }
    return html_entity_decode($txt . ', à voir à Granby et à Sainte-Eulalie.');
}
