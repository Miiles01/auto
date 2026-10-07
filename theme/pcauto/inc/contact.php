<?php
if (!defined('ABSPATH')) { exit; }

add_action('admin_post_nopriv_pc_contact', 'pc_handle_contact');
add_action('admin_post_pc_contact', 'pc_handle_contact');

function pc_handle_contact() {
    $back = wp_get_referer() ?: home_url('/');
    $back = remove_query_arg(['envoye', 'erreur'], $back);
    $go = function ($flag) use ($back) {
        wp_safe_redirect(add_query_arg($flag, '1', $back) . '#contact');
        exit;
    };

    if (!isset($_POST['pc_nonce']) || !wp_verify_nonce($_POST['pc_nonce'], 'pc_contact')) { $go('erreur'); }
    if (!empty($_POST['website'])) { $go('envoye'); } // pot de miel : on fait semblant

    $nom = sanitize_text_field(wp_unslash($_POST['nom'] ?? ''));
    $tel = sanitize_text_field(wp_unslash($_POST['telephone'] ?? ''));
    $mail = sanitize_email(wp_unslash($_POST['courriel'] ?? ''));
    $sujet = sanitize_text_field(wp_unslash($_POST['sujet'] ?? ''));
    $msg = sanitize_textarea_field(wp_unslash($_POST['message'] ?? ''));
    $vid = absint($_POST['vehicule'] ?? 0);
    $type = in_array($_POST['type'] ?? '', ['contact', 'vente', 'financement'], true) ? $_POST['type'] : 'contact';
    $marque = sanitize_text_field(wp_unslash($_POST['marque'] ?? ''));
    $modele = sanitize_text_field(wp_unslash($_POST['modele'] ?? ''));
    $annee = absint($_POST['annee'] ?? 0);
    $km = absint($_POST['km'] ?? 0);
    $suc = sanitize_text_field(wp_unslash($_POST['succursale'] ?? ''));
    if (!in_array($suc, ['Granby', 'Sainte-Eulalie'], true)) { $suc = ''; }

    if ($nom === '' || strlen(preg_replace('/\D/', '', $tel)) < 10 || !is_email($mail)) { $go('erreur'); }

    $lines = ['Bonjour PC Auto,', '', "Nom : $nom", "Téléphone : $tel", "Courriel : $mail"];
    if ($vid && get_post_type($vid) === 'product' && pc_is_vehicle($vid)) {
        $c = pc_car($vid);
        $lines[] = 'Véhicule : ' . $c['name'] . ' ' . $c['year'] . ' (stock ' . $c['stock'] . ', ' . html_entity_decode(pc_price($c['price'])) . ') ' . get_permalink($vid);
    }
    if ($suc !== '') { $lines[] = "Succursale : $suc"; }
    if ($type === 'vente') { $lines[] = "Véhicule à vendre : $marque $modele $annee, " . number_format($km, 0, ',', ' ') . ' km'; }
    if ($sujet !== '') { $lines[] = "Sujet : $sujet"; }
    if ($msg !== '') { $lines[] = ''; $lines[] = $msg; }

    $to = get_theme_mod('pc_contact_email') ?: get_option('admin_email');
    $headers = ['Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . $nom . ' <' . $mail . '>'];
    $ok = wp_mail($to, '[PC Auto' . ($suc ? ' ' . $suc : '') . '] ' . ($sujet ?: 'Demande d’information') . ' — ' . $nom, implode("\n", $lines), $headers);

    $go($ok ? 'envoye' : 'erreur');
}
