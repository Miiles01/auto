<?php
/**
 * Gabarit WooCommerce : boutique, fiche produit, catégories.
 * (Panier, caisse et compte utilisent page.php.)
 */
// Les véhicules ont leur propre gabarit (WooCommerce donne priorité à ce fichier sur single-product.php).
if (is_singular('product') && pc_is_vehicle(get_queried_object_id())) {
    require get_theme_file_path('single-product.php');
    return;
}
get_header(); ?>
<main id="contenu" class="pc-page pc-page--default pc-shop">
    <div class="container">
        <?php woocommerce_content(); ?>
    </div>
</main>
<?php get_footer();
