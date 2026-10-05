<?php
/**
 * Gabarit WooCommerce : boutique, fiche produit, catégories.
 * (Panier, caisse et compte utilisent page.php.)
 */
get_header(); ?>
<main id="contenu" class="pc-page pc-page--default pc-shop">
    <div class="container">
        <?php woocommerce_content(); ?>
    </div>
</main>
<?php get_footer();
