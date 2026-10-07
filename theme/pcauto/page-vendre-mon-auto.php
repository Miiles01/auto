<?php
/** Vendre mon auto : structure H1/H2/H3 définie par le spécialiste SEO. */
get_header(); ?>
<main id="contenu">
  <section class="page-hero" aria-labelledby="v-title">
    <div class="container">
      <nav class="breadcrumb" aria-label="Fil d'Ariane"><a href="<?php echo esc_url(home_url('/')); ?>">Accueil</a><span aria-hidden="true">/</span><span aria-current="page">Vendre mon auto</span></nav>
      <div class="page-hero__row">
        <h1 class="display page-h1" id="v-title">Vendre mon auto usagée au Québec</h1>
        <div class="page-hero__aside"><p class="caption">PC Auto achète votre véhicule, avec ou sans échange. Décrivez-le-nous et recevez une évaluation de notre équipe.</p></div>
      </div>
    </div>
  </section>

  <section class="section section--tight" id="offre" aria-labelledby="offre-title" style="padding-top:0">
    <div class="container">
      <div class="section-head">
        <div><p class="eyebrow" style="margin-bottom:1rem">(01) — Votre offre</p><h2 class="h-section" id="offre-title">Obtenez une offre <em>pour votre véhicule</em></h2></div>
        <p class="section-head__note">Indiquez la marque, le modèle, l’année et le kilométrage : nous vous répondons rapidement.</p>
      </div>
      <div class="contact">
        <aside class="contact__aside">
          <div class="contact__direct">
            <a href="tel:+14503780888"><?php echo pc_icon('phone'); ?><span><small>Appelez-nous</small><b>450 378-0888</b></span></a>
            <a href="<?php echo esc_url(pc_wa('Bonjour PC Auto, j’aimerais faire évaluer mon véhicule.')); ?>" target="_blank" rel="noopener"><?php echo pc_icon('whatsapp'); ?><span><small>WhatsApp</small><b>Écrivez-nous en direct</b></span></a>
          </div>
        </aside>
        <?php get_template_part('parts/contact-form', null, ['type' => 'vente']); ?>
      </div>
    </div>
  </section>

  <section class="section section--tight" aria-labelledby="steps-title">
    <div class="container">
      <h2 class="h-section" id="steps-title">Comment vendre son auto <em>à PC Auto ?</em></h2>
      <p class="lead-p">Trois étapes simples, de la description de votre véhicule au paiement.</p>
      <ol class="cards-3 steps">
        <li><h3>Décrivez votre véhicule</h3><p>Marque, modèle, année et kilométrage : remplissez le formulaire ou appelez-nous.</p></li>
        <li><h3>Recevez notre évaluation</h3><p>Notre équipe examine et évalue votre véhicule, puis vous fait une offre.</p></li>
        <li><h3>Recevez votre paiement</h3><p>Si l’offre vous convient, nous concluons la transaction avec vous à la succursale.</p></li>
      </ol>
    </div>
  </section>

  <section class="section section--tight" aria-labelledby="val-title">
    <div class="container steps-wrap">
      <h2 class="h-section" id="val-title">Quelle est la valeur <em>de mon véhicule usagé ?</em></h2>
      <div class="prose">
        <p>La valeur d’un véhicule usagé dépend de plusieurs facteurs :</p>
        <ul>
          <li><strong>L’année</strong> et le modèle du véhicule.</li>
          <li><strong>Le kilométrage</strong> au compteur.</li>
          <li><strong>L’état général</strong> : mécanique, carrosserie et habitacle.</li>
          <li><strong>L’historique</strong> : entretien, accidents, propriétaires précédents.</li>
        </ul>
        <p>Notre équipe examine ces éléments avec vous pour établir une offre.</p>
      </div>
    </div>
  </section>

  <section class="section section--tight" aria-labelledby="cmp-title">
    <div class="container steps-wrap">
      <h2 class="h-section" id="cmp-title">Vendre à un particulier ou à un garage : <em>que choisir ?</em></h2>
      <div class="table-wrap"><table>
        <thead><tr><th></th><th>Vendre à PC Auto</th><th>Vendre à un particulier</th></tr></thead>
        <tbody>
          <tr><th>Délai</th><td>Une évaluation et une offre rapides.</td><td>Peut prendre des semaines.</td></tr>
          <tr><th>Démarches</th><td>Prises en charge avec notre équipe.</td><td>Annonce, visites et négociation à gérer.</td></tr>
          <tr><th>Risque</th><td>Transaction conclue avec un commerçant établi.</td><td>Plus élevé : acheteurs inconnus, rendez-vous à organiser.</td></tr>
        </tbody>
      </table></div>
    </div>
  </section>

  <section class="section section--tight" aria-labelledby="docs-title">
    <div class="container steps-wrap">
      <h2 class="h-section" id="docs-title">Quels documents faut-il pour vendre <em>son auto au Québec ?</em></h2>
      <div class="prose">
        <p>Munissez-vous au minimum du certificat d’immatriculation du véhicule et d’une pièce d’identité valide. Notre équipe vous indique les autres documents nécessaires selon votre situation. Pour les règles de transfert de propriété, consultez le site de la <a href="https://saaq.gouv.qc.ca" target="_blank" rel="noopener">SAAQ</a>.</p>
      </div>
    </div>
  </section>

  <section class="section section--tight" aria-labelledby="faq-title">
    <div class="container steps-wrap">
      <h2 class="h-section" id="faq-title">Questions fréquentes <em>sur la vente d’une auto</em></h2>
      <?php pc_faq([
        ['Pourquoi vendre votre auto à un garage comme PC Auto ?', '<p>Parce que la transaction est plus simple : une évaluation, une offre et des démarches prises en charge, sans avoir à organiser des visites avec des inconnus. Nous achetons votre véhicule avec ou sans échange.</p>'],
        ['Puis-je acheter un autre véhicule en même temps ?', '<p>Oui, vous pouvez vendre votre auto et choisir un véhicule de notre <a href="' . esc_url(pc_url('acheter')) . '">inventaire</a> lors de la même visite. Demandez-nous les détails.</p>'],
      ]); ?>
    </div>
  </section>

  <section class="band branch-switch" aria-labelledby="adv-title">
    <div class="container branch-switch__inner">
      <div>
        <h2 class="branch-switch__title display" id="adv-title">Les avantages de vendre sa voiture chez un concessionnaire professionnel</h2>
        <p class="branch-switch__text">Une équipe qui évalue, qui explique et qui s’occupe des démarches, à Granby et à Sainte-Eulalie.</p>
      </div>
      <a class="btn btn--light btn--lg" href="#offre">Obtenir mon offre <?php echo pc_icon('arrow'); ?></a>
    </div>
  </section>
</main>
<?php get_footer();
