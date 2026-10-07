<?php
/** Financement auto usagé : structure H1/H2/H3 définie par le spécialiste SEO. */
get_header();
$pc_fin_ids = pc_ids([], 6, 'home'); ?>
<main id="contenu">
  <section class="page-hero" aria-labelledby="f-title">
    <div class="container">
      <nav class="breadcrumb" aria-label="Fil d'Ariane"><a href="<?php echo esc_url(home_url('/')); ?>">Accueil</a><span aria-hidden="true">/</span><span aria-current="page">Financement auto usagé</span></nav>
      <div class="page-hero__row">
        <h1 class="display page-h1" id="f-title">Financement auto usagé simple et rapide pour votre voiture</h1>
        <div class="page-hero__aside"><p class="caption">Financez votre véhicule usagé directement à la succursale de Granby ou de Sainte-Eulalie, sans aller-retour entre la banque et le concessionnaire.</p></div>
      </div>
    </div>
  </section>

  <section class="section section--tight" aria-labelledby="qui-title" style="padding-top:0">
    <div class="container steps-wrap">
      <h2 class="h-section" id="qui-title">Qui peut obtenir un financement auto <em>chez PC Auto ?</em></h2>
      <div class="prose">
        <p>Chaque dossier est étudié individuellement. Parlez-nous de votre situation : notre équipe vous explique les options de financement pour le véhicule de votre choix.</p>
        <p><a class="btn btn--red" href="#demande">Faire ma demande <?php echo pc_icon('arrow'); ?></a> <a class="btn" href="tel:+14503780888"><?php echo pc_icon('phone'); ?>450 378-0888</a></p>
      </div>
    </div>
  </section>

  <section class="section section--tight" id="demande" aria-labelledby="dem-title">
    <div class="container">
      <div class="section-head">
        <div><p class="eyebrow" style="margin-bottom:1rem">(01) — Demande</p><h2 class="h-section" id="dem-title">Faites votre demande <em>de financement</em></h2></div>
        <p class="section-head__note">Un court formulaire suffit pour commencer. Nous vous répondons rapidement.</p>
      </div>
      <div class="contact">
        <aside class="contact__aside">
          <div class="contact__direct">
            <a href="tel:+14503780888"><?php echo pc_icon('phone'); ?><span><small>Appelez-nous</small><b>450 378-0888</b></span></a>
            <a href="<?php echo esc_url(pc_wa('Bonjour PC Auto, j’aimerais des informations sur le financement.')); ?>" target="_blank" rel="noopener"><?php echo pc_icon('whatsapp'); ?><span><small>WhatsApp</small><b>Écrivez-nous en direct</b></span></a>
          </div>
        </aside>
        <?php get_template_part('parts/contact-form', null, ['type' => 'financement']); ?>
      </div>
    </div>
  </section>

  <?php if ($pc_fin_ids) : ?>
  <section class="section section--tight" aria-labelledby="veh-title">
    <div class="container">
      <div class="section-head">
        <div><p class="eyebrow" style="margin-bottom:1rem">(02) — Inventaire</p><h2 class="h-section" id="veh-title">Trouvez le véhicule qui convient <em>à votre situation financière</em></h2></div>
        <p class="section-head__note"><?php echo esc_html(pc_inventory_summary()); ?></p>
      </div>
      <div class="car-grid"><?php foreach ($pc_fin_ids as $i => $cid) { echo pc_card($cid, $i); } ?></div>
      <div class="center-cta"><a class="btn btn--ghost btn--lg" href="<?php echo esc_url(pc_inv_url()); ?>">Voir tous les véhicules <?php echo pc_icon('arrow'); ?></a></div>
    </div>
  </section>
  <?php endif; ?>

  <section class="section section--tight" aria-labelledby="how-title">
    <div class="container">
      <h2 class="h-section" id="how-title">Comment fonctionne <em>le financement auto ?</em></h2>
      <ol class="cards-3 steps cards-4">
        <li><h3>Choisir un véhicule</h3><p>Parcourez l’inventaire et repérez le véhicule qui vous convient.</p></li>
        <li><h3>Faire la demande</h3><p>Remplissez le formulaire ou passez à la succursale : nous ouvrons votre dossier.</p></li>
        <li><h3>Recevoir la réponse</h3><p>Notre équipe vous présente les options de financement disponibles pour votre dossier.</p></li>
        <li><h3>Signer et prendre la route</h3><p>Une fois le financement convenu, nous finalisons la transaction et vous remettons les clés.</p></li>
      </ol>
    </div>
  </section>

  <section class="section section--tight" aria-labelledby="taux-title">
    <div class="container steps-wrap">
      <h2 class="h-section" id="taux-title">Quel est le taux d’intérêt <em>d’un prêt auto usagé ?</em></h2>
      <div class="prose">
        <p>Le taux dépend de votre situation et de la durée du prêt. Nous vous présentons les conditions avant que vous vous engagiez.</p>
      </div>
      <div class="cards-3">
        <article><h3>Ce qui détermine votre taux</h3><p>Votre historique de crédit, la durée du prêt, la mise de fonds et le véhicule choisi.</p></article>
        <article><h3>Durée du prêt et mensualités</h3><p>Plus la durée est longue, plus la mensualité baisse, mais plus le coût total augmente. Nous cherchons avec vous l’équilibre qui convient.</p></article>
      </div>
    </div>
  </section>

  <section class="section section--tight" aria-labelledby="docs-title">
    <div class="container steps-wrap">
      <h2 class="h-section" id="docs-title">Quels documents faut-il <em>pour une demande de financement ?</em></h2>
      <div class="prose">
        <p>Habituellement, une pièce d’identité, une preuve de revenu et une preuve d’adresse. Notre équipe vous confirme la liste exacte selon votre dossier.</p>
      </div>
    </div>
  </section>

<?php get_template_part('parts/section-financing'); ?>

  <section class="section section--tight">
    <div class="container center-cta" style="flex-direction:column;align-items:center;text-align:center;gap:1rem">
      <p class="lead-p" style="margin:0">Questions sur le crédit auto et le financement ?</p>
      <a class="btn btn--red btn--lg" href="<?php echo esc_url(pc_url('contact')); ?>">Parlez à notre équipe <?php echo pc_icon('arrow'); ?></a>
    </div>
  </section>
</main>
<?php get_footer();
