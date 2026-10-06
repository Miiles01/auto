<?php
if (!defined('ABSPATH')) { exit; }

add_action('customize_register', function ($wp) {
    $wp->add_section('pc_options', ['title' => 'PC Auto', 'priority' => 30]);

    $wp->add_setting('pc_prep_count', ['default' => 5, 'sanitize_callback' => 'absint']);
    $wp->add_control('pc_prep_count', [
        'section' => 'pc_options', 'type' => 'number',
        'label' => 'Véhicules en préparation',
        'description' => 'Nombre affiché sur la carte « Bientôt en inventaire ». 0 la masque.',
    ]);

    $wp->add_setting('pc_online_purchase', ['default' => false, 'sanitize_callback' => 'rest_sanitize_boolean']);
    $wp->add_control('pc_online_purchase', [
        'section' => 'pc_options', 'type' => 'checkbox',
        'label' => 'Permettre l’achat en ligne des véhicules (panier WooCommerce)',
        'description' => 'Désactivé par défaut : les véhicules affichent leur prix et invitent à nous contacter.',
    ]);

    $wp->add_setting('pc_contact_email', ['default' => '', 'sanitize_callback' => 'sanitize_email']);
    $wp->add_control('pc_contact_email', [
        'section' => 'pc_options', 'type' => 'email',
        'label' => 'Courriel de réception du formulaire',
        'description' => 'Laissez vide pour utiliser le courriel de l’administrateur du site.',
    ]);
});
