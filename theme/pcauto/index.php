<?php get_header(); ?>
<main id="contenu" class="pc-page pc-page--default">
<div class="container">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <article style="margin-bottom:3rem"><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><?php the_excerpt(); ?></article>
<?php endwhile; else : ?><p>Aucun contenu.</p><?php endif; ?>
</div>
</main>
<?php get_footer();
