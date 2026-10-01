<?php get_header(); ?>
<main id="contenu" class="pc-page<?php echo pc_is_elementor(get_the_ID()) ? '' : ' pc-page--default'; ?>">
<?php while (have_posts()) : the_post();
    if (pc_is_elementor(get_the_ID())) { the_content(); }
    else { ?>
    <div class="container">
        <h1 class="display" style="font-size:clamp(3rem,8vw,6rem);color:var(--accent);margin-bottom:2rem"><?php the_title(); ?></h1>
        <div class="entry"><?php the_content(); ?></div>
    </div>
<?php } endwhile; ?>
</main>
<?php get_footer();
