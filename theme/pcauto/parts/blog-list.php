<?php
/** Liste d'articles (page Blog, catégories, étiquettes, recherche). */
$is_blog = is_home();
$title = $is_blog ? 'Blog' : wp_strip_all_tags(get_the_archive_title());
$cats = get_categories(['hide_empty' => true]);
$current = is_category() ? get_queried_object_id() : 0;
?>
<main id="contenu">
  <section class="page-hero" aria-labelledby="blog-title">
    <div class="container">
      <nav class="breadcrumb" aria-label="Fil d'Ariane"><a href="<?php echo esc_url(home_url('/')); ?>">Accueil</a><span aria-hidden="true">/</span><?php if ($is_blog) : ?><span aria-current="page">Blog</span><?php else : ?><a href="<?php echo esc_url(pc_blog_url()); ?>">Blog</a><span aria-hidden="true">/</span><span aria-current="page"><?php echo esc_html($title); ?></span><?php endif; ?></nav>
      <div class="page-hero__row">
        <h1 class="display" id="blog-title"><?php echo esc_html($title); ?></h1>
        <div class="page-hero__aside"><p class="caption">Conseils d’achat, financement, entretien et nouvelles de PC Auto, à Granby et Sainte-Eulalie.</p></div>
      </div>
    </div>
  </section>

  <section class="section" style="padding-top:0" aria-label="Articles">
    <div class="container">
      <?php if (count($cats) > 0) : ?>
      <div class="filters">
        <div class="filters__pills" role="group" aria-label="Catégories">
          <a class="pill" href="<?php echo esc_url(pc_blog_url()); ?>"<?php echo $is_blog ? ' aria-pressed="true"' : ''; ?>>Tous</a>
          <?php foreach ($cats as $c) : ?><a class="pill" href="<?php echo esc_url(get_category_link($c)); ?>"<?php echo $current === $c->term_id ? ' aria-pressed="true"' : ''; ?>><?php echo esc_html($c->name); ?> <span class="pill__count">(<?php echo (int) $c->count; ?>)</span></a><?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <?php if (have_posts()) : ?>
        <div class="post-grid">
          <?php while (have_posts()) : the_post(); echo pc_post_card(get_the_ID()); endwhile; ?>
        </div>
        <?php the_posts_pagination(['mid_size' => 1, 'prev_text' => '← Précédent', 'next_text' => 'Suivant →', 'screen_reader_text' => 'Pagination des articles']); ?>
      <?php else : ?>
        <div class="inv-empty is-visible"><p>Aucun article pour le moment.</p><a class="btn btn--red" href="<?php echo esc_url(pc_inv_url()); ?>">Voir l’inventaire</a></div>
      <?php endif; ?>
    </div>
  </section>
</main>
