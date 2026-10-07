<?php
/** Contact : structure H1/H2/H3 définie par le spécialiste SEO. */
get_header(); ?>
<main id="contenu">
  <section class="page-hero" aria-labelledby="c-title">
    <div class="container">
      <nav class="breadcrumb" aria-label="Fil d'Ariane"><a href="<?php echo esc_url(home_url('/')); ?>">Accueil</a><span aria-hidden="true">/</span><span aria-current="page">Contact</span></nav>
      <div class="page-hero__row">
        <h1 class="display page-h1" id="c-title">Contactez PC Auto</h1>
        <div class="page-hero__aside"><p class="caption">Par téléphone au 450 378-0888, par WhatsApp ou avec le formulaire : nous vous répondons en français, en anglais, en espagnol et en portugais.</p></div>
      </div>
    </div>
  </section>

  <section class="section section--tight" id="contact" aria-labelledby="form-title" style="padding-top:0">
    <div class="container">
      <div class="section-head">
        <div><p class="eyebrow" style="margin-bottom:1rem">(01) — Nous écrire</p><h2 class="h-section" id="form-title">Formulaire <em>de contact</em></h2></div>
        <p class="section-head__note">Choisissez le sujet : acheter, vendre ou financer. Nous vous répondons rapidement.</p>
      </div>
      <div class="contact">
        <aside class="contact__aside">
          <div class="contact__direct">
            <a href="tel:+14503780888"><?php echo pc_icon('phone'); ?><span><small>Appelez-nous</small><b>450 378-0888</b></span></a>
            <a href="<?php echo esc_url(pc_wa('Bonjour PC Auto, ')); ?>" target="_blank" rel="noopener"><?php echo pc_icon('whatsapp'); ?><span><small>WhatsApp</small><b>Écrivez-nous en direct</b></span></a>
          </div>
          <dl class="hours"><dt>Lundi au vendredi</dt><dd>8 h 00 – 18 h 00</dd><dt>Samedi</dt><dd>10 h 00 – 13 h 00 (sur rendez-vous)</dd><dt>Dimanche</dt><dd>Fermé</dd></dl>
        </aside>
        <?php get_template_part('parts/contact-form', null, ['type' => 'contact']); ?>
      </div>
    </div>
  </section>

  <?php get_template_part('parts/section-locations', null, ['eyebrow' => '(02) — Succursales', 'h2' => 'Nos <em>succursales</em>']); ?>

  <section class="band branch-switch" aria-labelledby="visit-title">
    <div class="container branch-switch__inner">
      <div>
        <h2 class="branch-switch__title display" id="visit-title">Venez nous rendre visite, votre tranquillité d’esprit commence ici.</h2>
        <p class="branch-switch__text">Nous voulons que vous vous sentiez comme chez vous pendant que nous prenons soin de votre voiture.</p>
      </div>
      <div class="branch-switch__links">
        <a class="btn btn--light btn--lg" href="<?php echo esc_url(pc_url('granby')); ?>">Granby</a>
        <a class="btn btn--light btn--lg" href="<?php echo esc_url(pc_url('sainte-eulalie')); ?>">Sainte-Eulalie</a>
      </div>
    </div>
  </section>
</main>
<?php get_footer();
