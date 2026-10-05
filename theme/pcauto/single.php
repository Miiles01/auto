<?php
get_header();
while (have_posts()) : the_post();
    $id = get_the_ID();
    $cats = get_the_category($id);
    $elementor = pc_is_elementor($id);
    $related = $cats ? get_posts(['category__in' => wp_list_pluck($cats, 'term_id'), 'post__not_in' => [$id], 'posts_per_page' => 3, 'fields' => 'ids']) : [];
    if (count($related) < 3) { $related = array_unique(array_merge($related, get_posts(['post__not_in' => array_merge([$id], $related), 'posts_per_page' => 3 - count($related), 'fields' => 'ids']))); }
    $share = rawurlencode(get_permalink());
    $share_t = rawurlencode(get_the_title());
?>
<main id="contenu" class="pc-page">
<?php if ($elementor) : the_content(); else : ?>
  <article class="post">
    <div class="container post__container">
      <nav class="breadcrumb" aria-label="Fil d'Ariane"><a href="<?php echo esc_url(home_url('/')); ?>">Accueil</a><span aria-hidden="true">/</span><a href="<?php echo esc_url(pc_blog_url()); ?>">Blog</a><span aria-hidden="true">/</span><span aria-current="page"><?php the_title(); ?></span></nav>
      <header class="post__head">
        <div class="post-card__meta"><?php if ($cats) : ?><a class="chip" href="<?php echo esc_url(get_category_link($cats[0])); ?>"><?php echo esc_html($cats[0]->name); ?></a><?php endif; ?><time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('j F Y')); ?></time><span><?php echo (int) pc_read_time($id); ?> min de lecture</span></div>
        <h1 class="post__title display"><?php the_title(); ?></h1>
        <?php if (has_excerpt()) : ?><p class="post__lead"><?php echo esc_html(get_the_excerpt()); ?></p><?php endif; ?>
      </header>
      <?php if (has_post_thumbnail()) : ?><figure class="post__cover"><?php the_post_thumbnail('pc-main', ['alt' => '']); ?></figure><?php endif; ?>
      <div class="entry"><?php the_content(); ?></div>
      <footer class="post__foot">
        <p>Partager : <a class="link-underline" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share; ?>" target="_blank" rel="noopener">Facebook</a> · <a class="link-underline" href="https://api.whatsapp.com/send?text=<?php echo $share_t . '%20' . $share; ?>" target="_blank" rel="noopener">WhatsApp</a></p>
      </footer>
    </div>
  </article>
<?php endif; ?>

  <?php if ($related) : ?>
  <section class="section" aria-labelledby="rel-title">
    <div class="container">
      <div class="section-head"><div><p class="eyebrow" style="margin-bottom:1rem">À lire aussi</p><h2 class="h-section" id="rel-title">Autres <em>articles</em></h2></div><p class="section-head__note"><a class="link-underline" href="<?php echo esc_url(pc_blog_url()); ?>">Voir tous les articles</a></p></div>
      <div class="post-grid"><?php foreach ($related as $rid) { echo pc_post_card($rid); } ?></div>
    </div>
  </section>
  <?php endif; ?>

  <section class="band branch-switch" aria-labelledby="cta-title">
    <div class="container branch-switch__inner">
      <div><p class="eyebrow" style="margin-bottom:1rem">Votre prochain véhicule</p><h2 class="branch-switch__title display" id="cta-title">Visitez nos succursales</h2><p class="branch-switch__text">Granby et Sainte-Eulalie : chacune a son inventaire, ses prix affichés et un financement sur place.</p></div>
      <div class="branch-switch__links"><a class="btn btn--light btn--lg" href="<?php echo esc_url(home_url('/granby/')); ?>">Granby</a><a class="btn btn--light btn--lg" href="<?php echo esc_url(home_url('/sainte-eulalie/')); ?>">Sainte-Eulalie</a></div>
    </div>
  </section>
</main>
<?php endwhile;
get_footer();
