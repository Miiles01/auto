<?php
/** Section « Ce que disent nos clients » : n'apparaît que s'il existe de vrais témoignages. */
$items = pc_testimonials();
if (!$items) { return; }
$rating = get_theme_mod('pc_google_rating'); $count = (int) get_theme_mod('pc_google_count'); $date = get_theme_mod('pc_google_date');
?>
<section class="section section--tight" id="temoignages" aria-labelledby="temoignages-title">
  <div class="container">
    <div class="section-head">
      <div>
        <p class="eyebrow" style="margin-bottom:1rem"><?php echo esc_html($args['eyebrow'] ?? 'Témoignages'); ?></p>
        <h2 class="h-section" id="temoignages-title">Ce que disent <em>nos clients</em></h2>
      </div>
      <div class="testi__head-side"><p class="section-head__note"><?php echo $rating && $count ? esc_html('Note Google : ' . $rating . ' / 5 sur ' . $count . ' avis' . ($date ? ' (' . $date . ')' : '') . '.') : 'Des clients de Granby, de Sainte-Eulalie et des environs racontent leur achat chez PC Auto.'; ?></p></div>
    </div>
    <ul class="testi__track" tabindex="0" aria-label="Témoignages de clients">
      <?php foreach ($items as $t) : $v = get_post_meta($t->ID, 'pc_vehicule_achete', true); $ville = get_post_meta($t->ID, 'pc_ville', true); $src = get_post_meta($t->ID, 'pc_source', true); ?>
      <li class="testi">
        <blockquote><p><?php echo esc_html(wp_strip_all_tags($t->post_content)); ?></p></blockquote>
        <div class="testi__who">
          <?php if (has_post_thumbnail($t)) { echo get_the_post_thumbnail($t, [120, 120], ['alt' => '', 'loading' => 'lazy']); } ?>
          <p><b><?php echo esc_html($t->post_title); ?></b><span><?php echo esc_html(trim(implode(' · ', array_filter([$v, $ville, $src])))); ?></span></p>
        </div>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
