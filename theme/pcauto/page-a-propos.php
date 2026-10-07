<?php
/**
 * À propos : seules les sections vérifiables sont publiées.
 * En attente de confirmation (voir deploy/PENDIENTES-SEO.md) : histoire, équipe, chiffres, permis et affiliations.
 */
get_header(); ?>
<main id="contenu">
  <section class="page-hero" aria-labelledby="ap-title">
    <div class="container">
      <nav class="breadcrumb" aria-label="Fil d'Ariane"><a href="<?php echo esc_url(home_url('/')); ?>">Accueil</a><span aria-hidden="true">/</span><span aria-current="page">À propos</span></nav>
      <div class="page-hero__row">
        <h1 class="display page-h1" id="ap-title">À propos de PC Auto</h1>
        <div class="page-hero__aside"><p class="caption">PC Auto est un concessionnaire d’autos usagées situé à Granby et à Sainte-Eulalie, au Québec. Nous vendons des véhicules inspectés et garantis, nous achetons le vôtre et nous finançons sur place, en quatre langues.</p></div>
      </div>
    </div>
  </section>

  <section class="section section--tight" aria-labelledby="qui-title" style="padding-top:0">
    <div class="container steps-wrap">
      <h2 class="h-section" id="qui-title">Qui est <em>PC Auto ?</em></h2>
      <div class="prose">
        <p>PC Auto est un concessionnaire multimarque d’autos usagées avec deux succursales : <a href="<?php echo esc_url(pc_url('granby')); ?>">Granby</a> et <a href="<?php echo esc_url(pc_url('sainte-eulalie')); ?>">Sainte-Eulalie</a>. Notre équipe offre trois services au même endroit : la <a href="<?php echo esc_url(pc_url('acheter')); ?>">vente de véhicules d’occasion</a>, l’<a href="<?php echo esc_url(pc_url('vendre')); ?>">achat de votre véhicule</a> et le <a href="<?php echo esc_url(pc_url('financement')); ?>">financement auto usagé</a>.</p>
      </div>
    </div>
  </section>

  <section class="section section--tight" aria-labelledby="lang-title">
    <div class="container steps-wrap">
      <h2 class="h-section" id="lang-title">Un service en français, anglais, <em>espagnol et portugais</em></h2>
      <div class="prose">
        <p>Notre équipe vous accueille et vous répond dans votre langue, que vous soyez d’ici ou nouvellement arrivé au Québec.</p>
      </div>
    </div>
  </section>

  <section class="section section--tight" aria-labelledby="eng-title">
    <div class="container">
      <h2 class="h-section" id="eng-title">Nos engagements <em>envers nos clients</em></h2>
      <div class="cards-3">
        <article><h3>Inspection avant la vente</h3><p>Chaque véhicule est vérifié point par point avant d’être mis en vente. <a class="link-underline" href="<?php echo esc_url(pc_inv_url()); ?>">Voir comment nous inspectons</a>.</p></article>
        <article><h3>Prix affichés</h3><p>Tous nos prix sont visibles sur chaque fiche, en dollars canadiens, taxes et frais en sus.</p></article>
        <article><h3>Garantie</h3><p>Nous garantissons l’état de chacun de nos véhicules : révisés, réparés et prêts à l’emploi.</p></article>
      </div>
    </div>
  </section>

  <?php get_template_part('parts/section-locations', null, ['eyebrow' => '(02) — Succursales', 'h2' => 'Nos <em>succursales</em>']); ?>
</main>
<?php get_footer();
