<?php
/**
 * Landing de succursale : héros, inventaire propre à la succursale, financement,
 * visite (carte et horaires), témoignages, contact, lien vers l'autre succursale.
 * Usage : get_template_part('parts/succursale', null, ['key' => 'granby']);
 */
$branches = pc_branches();
$b = $branches[$args['key']];
$other = $branches[$b['other']];
$name = $b['name'];

$type = sanitize_key($_GET['type'] ?? '');
if (!in_array($type, ['', 'vus', 'auto', 'camion', 'awd'], true)) { $type = ''; }

$all = pc_ids(['succursale' => $name], -1, 'home');
$ids = pc_ids(['succursale' => $name, 'type' => $type], -1, 'recent');
$other_count = count(pc_ids(['succursale' => $other['name']]));

$types = [
    '' => 'Tous', 'vus' => 'VUS', 'auto' => 'Berlines et compactes', 'camion' => 'Camionnettes', 'awd' => '4x4 et intégrale',
];
$counts = [];
foreach ($types as $k => $l) { $counts[$k] = count(pc_ids(['succursale' => $name, 'type' => $k])); }

$hero_att = 0;
foreach ($all as $cid) { $g = pc_car($cid)['gallery']; if (!empty($g[3])) { $hero_att = $g[3]; break; } }
$hero_url = $hero_att ? wp_get_attachment_image_url($hero_att, 'pc-main') : get_theme_file_uri('assets/video/hero-poster.webp');

$directions = 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode($b['query']);
$map = 'https://www.google.com/maps?q=' . rawurlencode($b['query']) . '&output=embed&z=15';
$base = get_permalink();
?>
<main id="contenu">

  <!-- (01) Héros -->
  <section class="hero hero--branch" aria-labelledby="hero-title">
    <div class="hero__media" aria-hidden="true" style="background-image:url('<?php echo esc_url($hero_url); ?>')"><span class="hero__overlay"></span></div>
    <div class="container hero__inner">
      <h1 class="hero__kicker" id="hero-title">Concessionnaire d’autos usagées à <?php echo esc_html($name); ?></h1>
      <p class="hero__title display"><span class="hero__line"><span>PC Auto</span></span><span class="hero__line"><span><?php echo esc_html($name); ?></span></span></p>
      <p class="hero__lead">Les véhicules de notre succursale de <?php echo esc_html($name); ?> sont inspectés, garantis et financés sur place. Passez nous voir au <?php echo esc_html($b['street']); ?> ou appelez avant de venir : nous préparons votre essai routier.</p>
      <div class="hero__actions">
        <a class="btn btn--red btn--lg" href="#inventaire">Voir les <?php echo count($all); ?> véhicules <?php echo pc_icon('arrow'); ?></a>
        <a class="btn btn--ghost-light btn--lg" href="<?php echo esc_url($directions); ?>" target="_blank" rel="noopener"><?php echo pc_icon('pin'); ?>Itinéraire</a>
      </div>
      <div class="hero__stats">
        <span><b><?php echo count($all); ?></b>véhicules à <?php echo esc_html($name); ?></span>
        <span><b>8 h – 18 h</b>du lundi au vendredi</span>
        <span><b>100 %</b>financement sur place</span>
      </div>
    </div>
  </section>

  <!-- (02) Inventaire de la succursale -->
  <section class="section" id="inventaire" aria-labelledby="inv-title">
    <div class="container">
      <div class="section-head">
        <div>
          <p class="eyebrow" style="margin-bottom:1rem">(01) — Inventaire de <?php echo esc_html($name); ?></p>
          <h2 class="h-section" id="inv-title">Autos usagées à vendre <em>à <?php echo esc_html($name); ?></em></h2>
        </div>
        <p class="section-head__note">Tous nos prix sont affichés. Chaque véhicule ci-dessous se trouve à la succursale de <?php echo esc_html($name); ?>.</p>
      </div>
      <div class="filters">
        <div class="filters__pills" role="group" aria-label="Catégories">
          <?php foreach ($types as $k => $l) : if ($k !== '' && !$counts[$k]) { continue; } ?>
            <a class="pill" href="<?php echo esc_url(($k ? add_query_arg('type', $k, $base) : $base) . '#inventaire'); ?>"<?php echo $type === $k ? ' aria-pressed="true"' : ''; ?>><?php echo esc_html($l); ?> <span class="pill__count">(<?php echo (int) $counts[$k]; ?>)</span></a>
          <?php endforeach; ?>
        </div>
        <p class="filters__meta">Triés du plus récent au plus ancien</p>
      </div>
      <?php if ($ids) : ?>
        <div class="car-grid">
          <?php foreach ($ids as $i => $id) { echo pc_card($id, $i); } ?>
        </div>
      <?php else : ?>
        <div class="inv-empty is-visible"><p>Aucun véhicule dans cette catégorie pour le moment.</p><a class="btn btn--red" href="<?php echo esc_url($base . '#inventaire'); ?>">Voir tous les véhicules de <?php echo esc_html($name); ?></a></div>
      <?php endif; ?>
      <div class="center-cta">
        <a class="btn btn--ghost btn--lg" href="<?php echo esc_url(pc_inv_url()); ?>">Voir l’inventaire des deux succursales <?php echo pc_icon('arrow'); ?></a>
      </div>
    </div>
  </section>

  <!-- Parcourir par catégorie + inspection (déplacés depuis l'accueil) -->
  <?php get_template_part('parts/section-browse', null, ['branch' => $name]); ?>
  <?php get_template_part('parts/section-inspection'); ?>

  <!-- Financement : teaser vers la page dédiée -->
  <section class="section section--dark section--tight" aria-labelledby="fin-title">
    <div class="container">
      <div class="finance">
        <div>
          <p class="eyebrow" style="margin-bottom:1rem">Financement</p>
          <h2 class="h-section" id="fin-title">Financement auto usagé <em>à <?php echo esc_html($name); ?></em></h2>
          <p style="margin-top:1rem;max-width:34rem;opacity:.85">Financez votre véhicule directement à la succursale de <?php echo esc_html($name); ?>, avec des démarches simples et rapides.</p>
        </div>
        <div class="finance__actions" style="justify-content:flex-end">
          <a class="btn btn--red btn--lg" href="<?php echo esc_url(pc_url('financement')); ?>">Voir le financement auto usagé <?php echo pc_icon('arrow'); ?></a>
          <a class="btn btn--ghost-light btn--lg" href="tel:+14503780888"><?php echo pc_icon('phone'); ?>450 378-0888</a>
        </div>
      </div>
    </div>
  </section>

  <!-- (04) Nous rendre visite -->
  <section class="section" id="visite" aria-labelledby="visit-title">
    <div class="container">
      <div class="section-head">
        <div>
          <p class="eyebrow" style="margin-bottom:1rem">(03) — Nous rendre visite</p>
          <h2 class="h-section" id="visit-title">PC Auto <em><?php echo esc_html($name); ?></em></h2>
        </div>
        <p class="section-head__note">Appelez avant de passer : nous préparons le véhicule qui vous intéresse pour votre essai routier.</p>
      </div>
      <div class="locations locations--single">
        <article class="location">
          <div class="location__map"><iframe title="Carte : PC Auto <?php echo esc_attr($name); ?>, <?php echo esc_attr($b['street']); ?>" src="<?php echo esc_url($map); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></div>
          <div class="location__body">
            <h3 class="location__name">PC Auto <?php echo esc_html($name); ?></h3>
            <p class="location__addr"><?php echo esc_html($b['street']); ?><br><?php echo esc_html($b['city']); ?></p>
            <dl class="hours"><?php foreach ($b['hours'] as $h) : ?><dt><?php echo esc_html($h[0]); ?></dt><dd><?php echo esc_html($h[1]); ?></dd><?php endforeach; ?></dl>
            <div class="location__foot">
              <a class="btn btn--red" href="<?php echo esc_url($directions); ?>" target="_blank" rel="noopener"><?php echo pc_icon('pin'); ?>Itinéraire</a>
              <a class="btn" href="tel:+14503780888"><?php echo pc_icon('phone'); ?>450 378-0888</a>
              <a class="btn btn--wa" href="<?php echo esc_url(pc_wa('Bonjour PC Auto ' . $name . ', ')); ?>" target="_blank" rel="noopener"><?php echo pc_icon('whatsapp'); ?>WhatsApp</a>
            </div>
          </div>
        </article>
      </div>
    </div>
  </section>

  <!-- (05) Témoignages -->
  <?php get_template_part('parts/temoignages', null, ['eyebrow' => '(04) — Témoignages']); ?>

  <!-- (06) Contact -->
  <section class="section" id="contact" aria-labelledby="contact-title" style="padding-top:0">
    <div class="container">
      <div class="section-head">
        <div>
          <p class="eyebrow" style="margin-bottom:1rem">(05) — Nous écrire</p>
          <h2 class="h-section" id="contact-title">Écrivez à la succursale de <em><?php echo esc_html($name); ?></em></h2>
        </div>
        <p class="section-head__note">Réponse rapide en français, en anglais, en espagnol ou en portugais.</p>
      </div>
      <div class="contact">
        <aside class="contact__aside">
          <div class="contact__direct">
            <a href="tel:+14503780888"><?php echo pc_icon('phone'); ?><span><small>Appelez-nous</small><b>450 378-0888</b></span></a>
            <a href="<?php echo esc_url(pc_wa('Bonjour PC Auto ' . $name . ', ')); ?>" target="_blank" rel="noopener"><?php echo pc_icon('whatsapp'); ?><span><small>WhatsApp</small><b>Écrivez-nous en direct</b></span></a>
          </div>
          <dl class="hours"><?php foreach ($b['hours'] as $h) : ?><dt><?php echo esc_html($h[0]); ?></dt><dd><?php echo esc_html($h[1]); ?></dd><?php endforeach; ?></dl>
        </aside>
        <?php get_template_part('parts/contact-form', null, ['succursale' => $name]); ?>
      </div>
    </div>
  </section>

  <!-- (07) L'autre succursale -->
  <section class="band branch-switch" aria-labelledby="other-title">
    <div class="container branch-switch__inner">
      <div>
        <p class="eyebrow" style="margin-bottom:1rem">Notre autre succursale</p>
        <h2 class="branch-switch__title display" id="other-title">PC Auto <?php echo esc_html($other['name']); ?></h2>
        <p class="branch-switch__text"><?php echo esc_html($other['street'] . ', ' . $other['city']); ?> · <?php echo (int) $other_count; ?> véhicules en inventaire</p>
      </div>
      <a class="btn btn--light btn--lg" href="<?php echo esc_url($other['url']); ?>">Voir la succursale de <?php echo esc_html($other['name']); ?> <?php echo pc_icon('arrow'); ?></a>
    </div>
  </section>

</main>
