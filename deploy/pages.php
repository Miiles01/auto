<?php
// wp eval-file pages.php — pages, portée, blog, réglages de base (sans contenu d'exemple)
$mk = function ($title, $slug) {
    $e = get_page_by_path($slug);
    if ($e) { return $e->ID; }
    return wp_insert_post(['post_type' => 'page', 'post_status' => 'publish', 'post_title' => $title, 'post_name' => $slug]);
};
$home = $mk('Accueil', 'accueil'); $mk('Inventaire', 'inventaire'); $mk('Granby', 'granby'); $mk('Sainte-Eulalie', 'sainte-eulalie');
$blog = $mk('Blog', 'blog');
update_option('show_on_front', 'page'); update_option('page_on_front', $home); update_option('page_for_posts', $blog);
update_option('blogname', 'PC Auto');
update_option('blogdescription', 'Véhicules d’occasion à Granby et Sainte-Eulalie');
update_option('permalink_structure', '/%postname%/');
flush_rewrite_rules();
echo "pages ok\n";
