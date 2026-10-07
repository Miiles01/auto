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
      <nav class="breadcrumb" aria-label="Fil d'Ariane"><a href="<?php echo esc_url(home_url('/')); ?>">Accueil</a><span aria-hidden="true">/</span><span aria-current="page">Acheter une auto usagée</span></nav>
      <div class="page-hero__row">
        <h1 class="display page-h1" id="inv-title">Acheter une auto usagée au Québec</h1>
        <div class="page-hero__aside"><p class="caption"><?php echo esc_html(pc_inventory_summary()); ?> VUS, berlines, compactes et camionnettes.</p></div>
      </div>
    </div>
  </section>

        <section class="section" style="padding-top:0" aria-labelledby="inv-h2">
          <div class="container">
            <div class="section-head">
              <div>
                <p class="eyebrow" style="margin-bottom:1rem">(01) — Inventaire</p>
                <h2 class="h-section" id="inv-h2">Nos autos usagées <em>à vendre</em></h2>
              </div>
              <p class="section-head__note">Chaque fiche indique la marque, le modèle, l’année, le kilométrage, le prix et la succursale où se trouve le véhicule.</p>
            </div>
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

  <!-- Financement -->
  <section class="section section--tight" aria-labelledby="pay-title">
    <div class="container steps-wrap">
      <h2 class="h-section" id="pay-title">Payez votre auto <em>par versements mensuels</em></h2>
      <div class="prose">
        <p>Vous n’avez pas à payer votre véhicule comptant : le financement se règle directement à la succursale, avec des démarches simples et rapides. Choisissez le véhicule, nous vous expliquons les options pour le payer par versements.</p>
        <p><a class="btn btn--red btn--lg" href="<?php echo esc_url(pc_url('financement')); ?>">Découvrir le financement auto usagé <?php echo pc_icon('arrow'); ?></a></p>
      </div>
    </div>
  </section>

  <!-- Inspection -->
  <section class="section section--tight" aria-labelledby="insp-title">
    <div class="container">
      <h2 class="h-section" id="insp-title">Comment nos véhicules <em>sont-ils inspectés ?</em></h2>
      <p class="lead-p">Avant d’être mis en vente, chaque véhicule passe entre les mains de notre équipe. Voici ce que nous vérifions.</p>
      <div class="cards-3 cards-5">
        <article><h3>Moteur et transmission</h3><p>Démarrage, fuites, niveaux et passage des vitesses vérifiés sur la route.</p></article>
        <article><h3>Freins et pneus</h3><p>Freins ABS, usure des pneus, suspension et direction.</p></article>
        <article><h3>Habitacle</h3><p>Climatisation, sièges chauffants, caméra de recul, Bluetooth et commandes au volant.</p></article>
        <article><h3>Carrosserie et historique</h3><p>État de la carrosserie et numéro de série (NIV) affiché sur chaque fiche.</p></article>
        <article><h3>Motricité 4x4 et intégrale</h3><p>Engagement du 4x4 ou de la traction intégrale testé avant la vente.</p></article>
      </div>
    </div>
  </section>

  <!-- Garantie -->
  <section class="section section--tight" aria-labelledby="gar-title">
    <div class="container steps-wrap">
      <h2 class="h-section" id="gar-title">Quelle garantie couvre <em>nos autos usagées ?</em></h2>
      <div class="prose">
        <p>Nous garantissons l’état de chacun de nos véhicules : révisés, réparés et prêts à l’emploi. Demandez à la succursale les modalités de la garantie du véhicule qui vous intéresse.</p>
      </div>
    </div>
  </section>

  <!-- Comment acheter -->
  <section class="section section--tight" aria-labelledby="how-title">
    <div class="container">
      <h2 class="h-section" id="how-title">Comment acheter une auto usagée <em>chez PC Auto ?</em></h2>
      <p class="lead-p">Trois étapes : choisir, essayer, puis financer ou payer.</p>
      <ol class="cards-3 steps">
        <li><h3>Choisir votre véhicule</h3><p>Parcourez l’inventaire, comparez les prix affichés et repérez la succursale où se trouve le véhicule.</p></li>
        <li><h3>Faire un essai routier</h3><p>Appelez ou écrivez-nous : nous préparons le véhicule pour votre essai routier à Granby ou à Sainte-Eulalie.</p></li>
        <li><h3>Financer ou payer</h3><p>Réglez l’achat comptant ou finalisez le financement sur place avec notre équipe.</p></li>
      </ol>
    </div>
  </section>

  <!-- FAQ -->
  <section class="section section--tight" aria-labelledby="faq-title">
    <div class="container steps-wrap">
      <h2 class="h-section" id="faq-title">Questions fréquentes <em>sur l’achat d’une auto usagée</em></h2>
      <?php pc_faq([
        ['Que faut-il vérifier avant d’acheter une auto usagée ?', '<ul><li>L’historique du véhicule et le numéro de série (NIV).</li><li>L’inspection : moteur, freins, pneus, carrosserie et habitacle.</li><li>Le kilométrage par rapport à l’âge du véhicule.</li><li>La garantie offerte.</li><li>Le prix total, taxes et frais compris.</li></ul>'],
        ['Vaut-il mieux acheter d’un concessionnaire ou d’un particulier ?', '<div class="table-wrap"><table><thead><tr><th></th><th>Concessionnaire</th><th>Particulier</th></tr></thead><tbody><tr><th>Garantie</th><td>Le commerçant garantit l’état du véhicule.</td><td>Peu ou pas de recours.</td></tr><tr><th>Financement</th><td>Possible sur place.</td><td>À organiser soi-même.</td></tr><tr><th>Démarches</th><td>Prises en charge par l’équipe.</td><td>À faire soi-même.</td></tr><tr><th>Risque</th><td>Véhicule inspecté avant la vente.</td><td>Plus élevé : état souvent inconnu.</td></tr></tbody></table></div><p>Pour connaître vos droits comme consommateur, consultez <a href="https://www.opc.gouv.qc.ca" target="_blank" rel="noopener">l’Office de la protection du consommateur</a>.</p>'],
        ['Les prix affichés incluent-ils tous les frais ?', '<p>Non : les prix sont affichés en dollars canadiens, taxes et frais en sus. L’équipe de la succursale vous confirme le prix total avant la signature.</p>'],
        ['Puis-je faire un essai routier ?', '<p>Oui. Écrivez-nous ou appelez la succursale où se trouve le véhicule : nous le préparons pour votre essai routier. Sur chaque fiche, un bouton permet de le réserver.</p>'],
      ]); ?>
    </div>
  </section>

  <!-- Cierre -->
  <section class="band branch-switch" aria-labelledby="close-title">
    <div class="container branch-switch__inner">
      <div>
        <h2 class="branch-switch__title display" id="close-title">Achetez votre voiture d’occasion chez PC Auto</h2>
        <p class="branch-switch__text">Visitez la succursale de Granby ou celle de Sainte-Eulalie : chacune a son inventaire et un financement sur place.</p>
      </div>
      <div class="branch-switch__links">
        <a class="btn btn--light btn--lg" href="<?php echo esc_url(pc_url('granby')); ?>">Granby</a>
        <a class="btn btn--light btn--lg" href="<?php echo esc_url(pc_url('sainte-eulalie')); ?>">Sainte-Eulalie</a>
      </div>
    </div>
  </section>
</main>
<?php get_footer();
