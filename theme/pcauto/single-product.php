<?php
/** Fiche produit : véhicule (gabarit PC Auto) ou produit ordinaire (gabarit WooCommerce). */
get_header();
if (!pc_is_vehicle(get_queried_object_id())) : ?>
<main id="contenu" class="pc-page pc-page--default pc-shop"><div class="container"><?php woocommerce_content(); ?></div></main>
<?php get_footer(); return; endif;
while (have_posts()) : the_post();
$c = pc_car(get_the_ID());
$full = $c['name'] . ' ' . $c['year'];
$price = pc_price($c['price']);
$reduced = $c['compare_at'] > $c['price'];
$n = count($c['gallery']);
$addr = ['Granby' => '1297, rue Principale, Granby (Québec) J2J 0M3', 'Sainte-Eulalie' => '315, rue des Bouleaux, Sainte-Eulalie (Québec) G0Z 1E0'][$c['location']] ?? '';
$interest = "Bonjour PC Auto, je suis intéressé(e) par le $full (stock {$c['stock']}, " . html_entity_decode($price) . ").";
$testDrive = "Bonjour PC Auto, j’aimerais réserver un essai routier pour le $full (stock {$c['stock']}) à votre succursale de {$c['location']}.";
$specs = [['Prix', $price], ['Kilométrage', pc_km($c['km'])], ['Année', $c['year']], ['Transmission', $c['transmission']], ['Moteur', $c['engine']], ['Motricité', $c['drivetrain']], ['Couleur', $c['color']], ['Catégorie', pc_body_label($c['body'])], ['Succursale', $c['location']], ['Numéro de stock', $c['stock']], ['Numéro de série (NIV)', $c['vin']]];
$same = function ($o) use ($c) { return $o['body'] === $c['body'] || ($c['body'] !== 'camion' && $o['body'] !== 'camion' && ($c['body'] === 'coupe' || $o['body'] === 'coupe')); };
$others = [];
foreach (pc_ids() as $oid) { if ($oid !== $c['id']) { $others[$oid] = pc_car($oid); } }
$dist = function ($a, $b) use ($others, $c) { return abs($others[$a]['price'] - $c['price']) <=> abs($others[$b]['price'] - $c['price']); };
$near = array_keys(array_filter($others, $same)); usort($near, $dist); $near = array_slice($near, 0, 4);
if (count($near) < 4) { $rest = array_diff(array_keys($others), $near); usort($rest, $dist); $near = array_merge($near, array_slice($rest, 0, 4 - count($near))); }
?>
<main id="contenu">
        <div class="vd">
          <div class="container">
            <nav class="breadcrumb" aria-label="Fil d'Ariane"><a href="<?php echo esc_url(home_url('/')); ?>">Accueil</a><span aria-hidden="true">/</span><a href="<?php echo esc_url(home_url('/inventaire/')); ?>">Inventaire</a><span aria-hidden="true">/</span><span aria-current="page"><?php echo esc_html($full); ?></span></nav>
            <header class="vd__head">
              <h1 class="vd__title display"><small><?php echo esc_html(pc_body_label($c['body']) . ' · ' . $c['year'] . ' · ' . $c['location']); ?></small><?php echo esc_html($c['name']); ?></h1>
              <div class="vd__price"><?php if ($c['reserved']) : ?><span class="vd__badge vd__badge--reserved">Réservé</span><?php elseif ($reduced) : ?><span class="vd__badge">Prix réduit</span><?php endif; ?><b class="tabular"><?php echo $price; ?></b><?php if ($reduced) : ?><s class="vd__was"><?php echo pc_price($c['compare_at']); ?></s><?php endif; ?><span>Prix en dollars canadiens, taxes et frais en sus.</span><span>Stock <?php echo esc_html($c['stock']); ?></span></div>
            </header>
            <div class="vd__layout">
              <div class="vd__main">
                <div class="gallery">
                  <div class="gallery__main"><?php echo pc_img($c['gallery'][0] ?? 0, 'pc-main', $full . ', photo 1 de ' . $n, ['loading' => 'eager', 'fetchpriority' => 'high']); ?></div>
                  <?php if ($n > 1) : ?>
                  <div class="gallery__thumbs">
                    <?php foreach ($c['gallery'] as $i => $att) : ?>
                    <a href="<?php echo esc_url(wp_get_attachment_url($att)); ?>" target="_blank" rel="noopener"><?php echo pc_img($att, 'pc-card', $full . ', photo ' . ($i + 1) . ' de ' . $n); ?></a>
                    <?php endforeach; ?>
                  </div>
                  <?php endif; ?>
                </div>
                <div class="vd__details">
                  <div><h2 class="vd__subhead">Caractéristiques</h2><table class="spec-table"><tbody>
                    <?php foreach ($specs as $s) : ?><tr><th scope="row"><?php echo esc_html($s[0]); ?></th><td><?php echo esc_html($s[1]); ?></td></tr><?php endforeach; ?>
                  </tbody></table></div>
                  <?php if ($c['options']) : ?>
                  <div><h2 class="vd__subhead">Équipements et options</h2><ul class="options">
                    <?php foreach ($c['options'] as $o) : ?><li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg><?php echo esc_html($o); ?></li><?php endforeach; ?>
                  </ul></div>
                  <?php endif; ?>
                </div>
              </div>
              <aside class="vd__panel">
                <dl class="key-specs">
                  <?php foreach ([['Kilométrage', pc_km($c['km'])], ['Transmission', $c['transmission']], ['Motricité', $c['drivetrain']], ['Moteur', $c['engine']]] as $s) : ?><div><dt><?php echo esc_html($s[0]); ?></dt><dd><?php echo esc_html($s[1]); ?></dd></div><?php endforeach; ?>
                </dl>
                <div class="panel panel--cta">
                  <h2>Ce véhicule vous intéresse ?</h2>
                  <?php if ($c['reserved']) : ?><p>Ce véhicule est actuellement réservé. Écrivez-nous pour être sur la liste d’attente ou découvrir un modèle similaire.</p><?php else : ?><p>Il se trouve à notre succursale de <?php echo esc_html($c['location']); ?>. Écrivez-nous ou appelez pour réserver votre essai routier.</p><?php endif; ?>
                  <div class="panel__actions">
                    <a class="btn btn--wa btn--lg" href="<?php echo esc_url(pc_wa($interest)); ?>" target="_blank" rel="noopener"><svg viewBox="0 0 32 32" aria-hidden="true"><path fill="currentColor" d="M16.04 3C8.86 3 3.04 8.8 3.04 15.96c0 2.3.6 4.53 1.75 6.5L3 29l6.72-1.76a12.97 12.97 0 0 0 6.31 1.62h.01c7.17 0 13-5.8 13-12.96C29.04 8.8 23.2 3 16.04 3zm0 23.68h-.01a10.8 10.8 0 0 1-5.5-1.5l-.4-.23-3.99 1.04 1.07-3.88-.26-.4a10.7 10.7 0 0 1-1.65-5.75c0-5.94 4.85-10.78 10.8-10.78 5.95 0 10.79 4.84 10.79 10.78 0 5.95-4.85 10.72-10.85 10.72zm5.92-8.05c-.32-.16-1.92-.95-2.22-1.05-.3-.11-.51-.16-.73.16-.21.32-.84 1.05-1.03 1.27-.19.21-.38.24-.7.08-.32-.16-1.37-.5-2.6-1.6a9.8 9.8 0 0 1-1.8-2.24c-.19-.32-.02-.5.14-.65.14-.15.32-.38.48-.57.16-.19.21-.32.32-.54.1-.21.05-.4-.03-.56-.08-.16-.73-1.75-1-2.4-.26-.63-.53-.54-.73-.55h-.62c-.21 0-.56.08-.86.4-.3.32-1.13 1.1-1.13 2.7 0 1.58 1.16 3.12 1.32 3.33.16.21 2.28 3.48 5.53 4.88.77.33 1.37.53 1.84.68.78.25 1.48.21 2.04.13.62-.09 1.92-.79 2.19-1.54.27-.76.27-1.4.19-1.54-.08-.13-.29-.21-.61-.37z"/></svg>Je suis intéressé(e)</a>
                    <a class="btn btn--red btn--lg" href="<?php echo esc_url(pc_wa($testDrive)); ?>" target="_blank" rel="noopener">Réserver un essai routier <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
                    <a class="btn btn--ghost-light btn--lg" href="tel:+14503780888"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/></svg>450 378-0888</a>
                  </div>
                </div>
              </aside>
            </div>
          </div>
        </div>
        <section class="section" id="similaires" aria-labelledby="sim-title">
          <div class="container">
            <div class="section-head">
              <div>
                <p class="eyebrow" style="margin-bottom:1rem">À voir aussi</p>
                <h2 class="h-section" id="sim-title">Véhicules <em>similaires</em></h2>
              </div>
              <p class="section-head__note"><a class="link-underline" href="<?php echo esc_url(home_url('/inventaire/')); ?>">Voir tout l'inventaire</a></p>
            </div>
            <div class="car-grid">
              <?php foreach ($near as $i => $oid) { echo pc_card($oid, $i); } ?>
            </div>
          </div>
        </section>
        <section class="section" id="contact" aria-labelledby="contact-title" style="padding-top:0">
          <div class="container">
            <div class="section-head">
              <div>
                <p class="eyebrow" style="margin-bottom:1rem">Nous écrire</p>
                <h2 class="h-section" id="contact-title">Une question sur <em>le <?php echo esc_html($c['name'] . ' ' . $c['year']); ?></em> ?</h2>
              </div>
              <p class="section-head__note">Réponse rapide en français, en anglais, en espagnol ou en portugais. Lun. au ven. 8 h – 18 h, sam. 10 h – 13 h sur rendez-vous.</p>
            </div>
            <div class="contact">
              <aside class="contact__aside">
                <div class="contact__direct">
                  <a href="tel:+14503780888"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/></svg><span><small>Appelez-nous</small><b>450 378-0888</b></span></a>
                  <a href="<?php echo esc_url(pc_wa($interest)); ?>" target="_blank" rel="noopener"><svg viewBox="0 0 32 32" aria-hidden="true"><path fill="currentColor" d="M16.04 3C8.86 3 3.04 8.8 3.04 15.96c0 2.3.6 4.53 1.75 6.5L3 29l6.72-1.76a12.97 12.97 0 0 0 6.31 1.62h.01c7.17 0 13-5.8 13-12.96C29.04 8.8 23.2 3 16.04 3zm0 23.68h-.01a10.8 10.8 0 0 1-5.5-1.5l-.4-.23-3.99 1.04 1.07-3.88-.26-.4a10.7 10.7 0 0 1-1.65-5.75c0-5.94 4.85-10.78 10.8-10.78 5.95 0 10.79 4.84 10.79 10.78 0 5.95-4.85 10.72-10.85 10.72zm5.92-8.05c-.32-.16-1.92-.95-2.22-1.05-.3-.11-.51-.16-.73.16-.21.32-.84 1.05-1.03 1.27-.19.21-.38.24-.7.08-.32-.16-1.37-.5-2.6-1.6a9.8 9.8 0 0 1-1.8-2.24c-.19-.32-.02-.5.14-.65.14-.15.32-.38.48-.57.16-.19.21-.32.32-.54.1-.21.05-.4-.03-.56-.08-.16-.73-1.75-1-2.4-.26-.63-.53-.54-.73-.55h-.62c-.21 0-.56.08-.86.4-.3.32-1.13 1.1-1.13 2.7 0 1.58 1.16 3.12 1.32 3.33.16.21 2.28 3.48 5.53 4.88.77.33 1.37.53 1.84.68.78.25 1.48.21 2.04.13.62-.09 1.92-.79 2.19-1.54.27-.76.27-1.4.19-1.54-.08-.13-.29-.21-.61-.37z"/></svg><span><small>WhatsApp</small><b>Écrivez-nous en direct</b></span></a>
                </div>
                <dl class="hours"><dt>Succursale</dt><dd><?php echo esc_html($c['location']); ?></dd><dt>Adresse</dt><dd><?php echo esc_html($addr); ?></dd><dt>Lun. au ven.</dt><dd>8 h 00 – 18 h 00</dd><dt>Samedi</dt><dd>10 h 00 – 13 h 00, sur rendez-vous</dd></dl>
              </aside>
              <?php get_template_part('parts/contact-form', null, ['selected' => $c['id']]); ?>
            </div>
          </div>
        </section>
</main>
<?php endwhile;
get_footer();
