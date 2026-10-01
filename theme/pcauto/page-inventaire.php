<?php
get_header();
$f = [
    'type' => sanitize_key($_GET['type'] ?? ''),
    'q' => sanitize_text_field(wp_unslash($_GET['q'] ?? '')),
    'succursale' => sanitize_text_field(wp_unslash($_GET['succursale'] ?? '')),
    'prix' => absint($_GET['prix'] ?? 0),
    'tri' => sanitize_key($_GET['tri'] ?? 'recent'),
];
if (($_GET['motricite'] ?? '') === 'awd') { $f['type'] = 'awd'; }
if (!in_array($f['type'], ['', 'vus', 'auto', 'camion', 'awd'], true)) { $f['type'] = ''; }
if (!in_array($f['succursale'], ['', 'Granby', 'Sainte-Eulalie'], true)) { $f['succursale'] = ''; }
$ids = pc_ids($f, -1, $f['tri']);
$filtered = $f['type'] || $f['q'] || $f['succursale'] || $f['prix'];
$types = [
    '' => ['label' => 'Tous', 'href' => pc_inv_url()],
    'vus' => ['label' => 'VUS', 'href' => pc_inv_url('?type=vus')],
    'auto' => ['label' => 'Berlines et compactes', 'href' => pc_inv_url('?type=auto')],
    'camion' => ['label' => 'Camionnettes', 'href' => pc_inv_url('?type=camion')],
    'awd' => ['label' => '4x4 et intégrale', 'href' => pc_inv_url('?type=awd')],
];
foreach ($types as $k => &$t) { $t['n'] = count(pc_ids(['type' => $k])); }
unset($t);
?>
<main id="contenu">
        <section class="page-hero" aria-labelledby="inv-title">
          <div class="container">
            <nav class="breadcrumb" aria-label="Fil d'Ariane"><a href="<?php echo esc_url(home_url('/')); ?>">Accueil</a><span aria-hidden="true">/</span><span aria-current="page">Inventaire</span></nav>
            <div class="page-hero__row">
              <h1 class="display" id="inv-title">Inventaire</h1>
              <div class="page-hero__aside">
                <p class="caption"><?php echo pc_total(); ?> véhicules d'occasion inspectés et garantis, à Granby et à Sainte-Eulalie.</p>
              </div>
            </div>
          </div>
        </section>

        <section class="section" style="padding-top:0" aria-label="Résultats">
          <div class="container">
            <form class="inv-toolbar" role="search" method="get" action="<?php echo esc_url(pc_inv_url()); ?>">
              <?php if ($f['type']) : ?><input type="hidden" name="type" value="<?php echo esc_attr($f['type']); ?>"><?php endif; ?>
              <div class="field search-field">
                <label for="inv-q">Rechercher</label>
                <input id="inv-q" name="q" type="search" placeholder="Marque, modèle, année…" autocomplete="off" value="<?php echo esc_attr($f['q']); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
              </div>
              <div class="field">
                <label for="inv-loc">Succursale</label>
                <select id="inv-loc" name="succursale" onchange="this.form.submit()">
                  <option value="">Toutes</option>
                  <?php foreach (['Granby', 'Sainte-Eulalie'] as $l) : ?><option<?php selected($f['succursale'], $l); ?>><?php echo esc_html($l); ?></option><?php endforeach; ?>
                </select>
              </div>
              <div class="field">
                <label for="inv-prix">Prix maximum ($ CA)</label>
                <select id="inv-prix" name="prix" onchange="this.form.submit()">
                  <option value="">Tous les prix</option>
                  <?php foreach ([6000, 8000, 10000, 15000] as $p) : ?><option value="<?php echo $p; ?>"<?php selected((int) $f['prix'], $p); ?>><?php echo esc_html(number_format($p, 0, ',', "\u{00A0}")); ?>&nbsp;$ et moins</option><?php endforeach; ?>
                </select>
              </div>
              <div class="field">
                <label for="inv-sort">Trier par</label>
                <select id="inv-sort" name="tri" onchange="this.form.submit()">
                  <?php foreach (['recent' => 'Année : plus récent', 'prix-asc' => 'Prix : croissant', 'prix-desc' => 'Prix : décroissant', 'km' => 'Kilométrage : plus bas'] as $k => $l) : ?><option value="<?php echo $k; ?>"<?php selected($f['tri'], $k); ?>><?php echo esc_html($l); ?></option><?php endforeach; ?>
                </select>
              </div>
              <noscript><button class="btn btn--red" type="submit">Filtrer</button></noscript>
            </form>
            <div class="inv-bar">
              <div class="filters__pills" role="group" aria-label="Catégories">
                <?php foreach ($types as $k => $t) : ?>
                  <a class="pill" href="<?php echo esc_url($t['href']); ?>"<?php echo $f['type'] === $k ? ' aria-pressed="true"' : ''; ?>><?php echo esc_html($t['label']); ?> <span class="pill__count">(<?php echo (int) $t['n']; ?>)</span></a>
                <?php endforeach; ?>
              </div>
              <p class="inv-count"><?php echo count($ids); ?> <?php echo count($ids) > 1 ? 'véhicules' : 'véhicule'; ?></p>
            </div>
            <?php if ($ids) : ?>
            <div class="car-grid">
              <?php foreach ($ids as $i => $id) { echo pc_card($id, $i); } ?>
              <?php if (!$filtered) { echo pc_soon_card(); } ?>
            </div>
            <?php else : ?>
            <div class="inv-empty is-visible">
              <p>Aucun véhicule ne correspond à votre recherche.</p>
              <a class="btn btn--red" href="<?php echo esc_url(pc_inv_url()); ?>">Effacer les filtres</a>
            </div>
            <?php endif; ?>
          </div>
        </section>

        <section class="section section--dark section--tight" aria-labelledby="inv-cta">
          <div class="container">
            <div class="finance">
              <div>
                <p class="eyebrow" style="margin-bottom:1rem">Vous ne trouvez pas ?</p>
                <h2 class="h-section" id="inv-cta"><span><?php echo esc_html(pc_prep_words()); ?></span> <em>en préparation</em>. Dites-nous ce que vous cherchez.</h2>
              </div>
              <div class="finance__actions" style="justify-content:flex-end">
                <a class="btn btn--wa btn--lg" href="https://api.whatsapp.com/send?phone=14503780888&amp;text=Bonjour%20PC%20Auto%2C%20je%20cherche%20un%20v%C3%A9hicule%20%3A%20" target="_blank" rel="noopener"><svg viewBox="0 0 32 32" aria-hidden="true"><path fill="currentColor" d="M16.04 3C8.86 3 3.04 8.8 3.04 15.96c0 2.3.6 4.53 1.75 6.5L3 29l6.72-1.76a12.97 12.97 0 0 0 6.31 1.62h.01c7.17 0 13-5.8 13-12.96C29.04 8.8 23.2 3 16.04 3zm0 23.68h-.01a10.8 10.8 0 0 1-5.5-1.5l-.4-.23-3.99 1.04 1.07-3.88-.26-.4a10.7 10.7 0 0 1-1.65-5.75c0-5.94 4.85-10.78 10.8-10.78 5.95 0 10.79 4.84 10.79 10.78 0 5.95-4.85 10.72-10.85 10.72zm5.92-8.05c-.32-.16-1.92-.95-2.22-1.05-.3-.11-.51-.16-.73.16-.21.32-.84 1.05-1.03 1.27-.19.21-.38.24-.7.08-.32-.16-1.37-.5-2.6-1.6a9.8 9.8 0 0 1-1.8-2.24c-.19-.32-.02-.5.14-.65.14-.15.32-.38.48-.57.16-.19.21-.32.32-.54.1-.21.05-.4-.03-.56-.08-.16-.73-1.75-1-2.4-.26-.63-.53-.54-.73-.55h-.62c-.21 0-.56.08-.86.4-.3.32-1.13 1.1-1.13 2.7 0 1.58 1.16 3.12 1.32 3.33.16.21 2.28 3.48 5.53 4.88.77.33 1.37.53 1.84.68.78.25 1.48.21 2.04.13.62-.09 1.92-.79 2.19-1.54.27-.76.27-1.4.19-1.54-.08-.13-.29-.21-.61-.37z"/></svg>WhatsApp</a>
                <a class="btn btn--ghost-light btn--lg" href="tel:+14503780888"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/></svg>450 378-0888</a>
              </div>
            </div>
          </div>
        </section>
        <section class="section section--tight" id="temoignages" aria-labelledby="temoignages-title">
  <div class="container">
    <div class="section-head">
      <div>
        <p class="eyebrow" style="margin-bottom:1rem">Témoignages</p>
        <h2 class="h-section" id="temoignages-title">Ils roulent <em>en confiance</em></h2>
      </div>
      <div class="testi__head-side"><p class="section-head__note">Des clients de Granby, de Sainte-Eulalie et des environs racontent leur achat chez PC Auto.</p></div>
    </div>
    <ul class="testi__track" tabindex="0" aria-label="Témoignages de clients">
      <li class="testi">
        <svg class="testi__quote" viewBox="0 0 32 32" aria-hidden="true"><path fill="currentColor" d="M13 8C7.5 9.3 4 13.6 4 19.3V25h9v-9H8.4c.4-3 2.4-5.2 5.2-6zM28 8c-5.5 1.3-9 5.6-9 11.3V25h9v-9h-4.6c.4-3 2.4-5.2 5.2-6z"/></svg>
        <blockquote><p>Je cherchais un VUS familial sans me ruiner. On m&#39;a laissée faire l&#39;essai routier deux fois, sans pression, et le financement a été approuvé le jour même.</p></blockquote>
        <div class="testi__who">
          <img src="<?php echo esc_url(get_theme_file_uri('assets/img/avatars/sophie.webp')); ?>" alt="" width="240" height="240" loading="lazy">
          <p><b>Sophie L.</b><span>Kia Sorento 2016 · Granby</span></p>
        </div>
      </li>
      <li class="testi">
        <svg class="testi__quote" viewBox="0 0 32 32" aria-hidden="true"><path fill="currentColor" d="M13 8C7.5 9.3 4 13.6 4 19.3V25h9v-9H8.4c.4-3 2.4-5.2 5.2-6zM28 8c-5.5 1.3-9 5.6-9 11.3V25h9v-9h-4.6c.4-3 2.4-5.2 5.2-6z"/></svg>
        <blockquote><p>Ils ont évalué mon ancien camion et l&#39;ont repris sur place. Le Silverado était propre, inspecté et prêt à partir. Transaction simple du début à la fin.</p></blockquote>
        <div class="testi__who">
          <img src="<?php echo esc_url(get_theme_file_uri('assets/img/avatars/marc-andre.webp')); ?>" alt="" width="240" height="240" loading="lazy">
          <p><b>Marc-André T.</b><span>Chevrolet Silverado 2013 · Drummondville</span></p>
        </div>
      </li>
      <li class="testi">
        <svg class="testi__quote" viewBox="0 0 32 32" aria-hidden="true"><path fill="currentColor" d="M13 8C7.5 9.3 4 13.6 4 19.3V25h9v-9H8.4c.4-3 2.4-5.2 5.2-6zM28 8c-5.5 1.3-9 5.6-9 11.3V25h9v-9h-4.6c.4-3 2.4-5.2 5.2-6z"/></svg>
        <blockquote><p>Premier achat d&#39;auto, j&#39;étais nerveuse. Tout m&#39;a été expliqué clairement, les papiers comme la garantie. Trois mois plus tard, aucune mauvaise surprise.</p></blockquote>
        <div class="testi__who">
          <img src="<?php echo esc_url(get_theme_file_uri('assets/img/avatars/julie.webp')); ?>" alt="" width="240" height="240" loading="lazy">
          <p><b>Julie B.</b><span>Honda Civic 2014 · Sainte-Eulalie</span></p>
        </div>
      </li>
      <li class="testi">
        <svg class="testi__quote" viewBox="0 0 32 32" aria-hidden="true"><path fill="currentColor" d="M13 8C7.5 9.3 4 13.6 4 19.3V25h9v-9H8.4c.4-3 2.4-5.2 5.2-6zM28 8c-5.5 1.3-9 5.6-9 11.3V25h9v-9h-4.6c.4-3 2.4-5.2 5.2-6z"/></svg>
        <blockquote><p>Les photos sur le site correspondaient exactement au véhicule. Réponse sur WhatsApp en quelques minutes, un samedi en plus.</p></blockquote>
        <div class="testi__who">
          <img src="<?php echo esc_url(get_theme_file_uri('assets/img/avatars/thomas.webp')); ?>" alt="" width="240" height="240" loading="lazy">
          <p><b>Thomas G.</b><span>Jeep Wrangler 2012 · Saint-Hyacinthe</span></p>
        </div>
      </li>
      <li class="testi">
        <svg class="testi__quote" viewBox="0 0 32 32" aria-hidden="true"><path fill="currentColor" d="M13 8C7.5 9.3 4 13.6 4 19.3V25h9v-9H8.4c.4-3 2.4-5.2 5.2-6zM28 8c-5.5 1.3-9 5.6-9 11.3V25h9v-9h-4.6c.4-3 2.4-5.2 5.2-6z"/></svg>
        <blockquote><p>Un accueil chaleureux, et le service en espagnol pour ma mère qui m&#39;accompagnait. On s&#39;est sentis bien conseillés, pas seulement vendus.</p></blockquote>
        <div class="testi__who">
          <img src="<?php echo esc_url(get_theme_file_uri('assets/img/avatars/camille.webp')); ?>" alt="" width="240" height="240" loading="lazy">
          <p><b>Camille R.</b><span>Hyundai Elantra 2016 · Granby</span></p>
        </div>
      </li>
      <li class="testi">
        <svg class="testi__quote" viewBox="0 0 32 32" aria-hidden="true"><path fill="currentColor" d="M13 8C7.5 9.3 4 13.6 4 19.3V25h9v-9H8.4c.4-3 2.4-5.2 5.2-6zM28 8c-5.5 1.3-9 5.6-9 11.3V25h9v-9h-4.6c.4-3 2.4-5.2 5.2-6z"/></svg>
        <blockquote><p>Prix affiché, prix payé. Le financement directement chez eux m&#39;a évité un aller-retour à la banque. Je recommande sans hésiter.</p></blockquote>
        <div class="testi__who">
          <img src="<?php echo esc_url(get_theme_file_uri('assets/img/avatars/olivier.webp')); ?>" alt="" width="240" height="240" loading="lazy">
          <p><b>Olivier D.</b><span>Volkswagen Jetta 2014 · Bromont</span></p>
        </div>
      </li>
    </ul>
  </div>
</section>
      </main>
<?php get_footer();
