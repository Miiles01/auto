<?php
// wp eval-file pages.php — pages du site (architecture SEO), portée, blog, réglages de base. Sans contenu d'exemple.
// Les gabarits sont choisis automatiquement d'après l'adresse (page-<slug>.php).
$mk = function ($title, $slug, $old = '') {
    $p = get_page_by_path($slug);
    if (!$p && $old && ($o = get_page_by_path($old))) { wp_update_post(['ID' => $o->ID, 'post_title' => $title, 'post_name' => $slug]); return $o->ID; }
    if ($p) { return $p->ID; }
    return wp_insert_post(['post_type' => 'page', 'post_status' => 'publish', 'post_title' => $title, 'post_name' => $slug]);
};
$home = $mk('Accueil', 'accueil');
$mk('Acheter une auto usagée', 'acheter-une-auto-usagee', 'inventaire');
$mk('Vendre mon auto', 'vendre-mon-auto');
$mk('Financement auto usagé', 'financement-auto-usage');
$mk('À propos', 'a-propos');
$mk('Contact', 'contact');
$mk('Granby', 'granby');
$mk('Sainte-Eulalie', 'sainte-eulalie');
$blog = $mk('Blog', 'blog');
update_option('show_on_front', 'page'); update_option('page_on_front', $home); update_option('page_for_posts', $blog);
update_option('blogname', 'PC Auto');
update_option('blogdescription', 'Concessionnaire d’autos usagées à Granby et Sainte-Eulalie');
update_option('permalink_structure', '/%postname%/');
flush_rewrite_rules();
echo "pages ok\n";
