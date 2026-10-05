<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#contenu">Aller au contenu</a>

  <header class="site-header">
    <div class="site-header__inner">
      <a class="brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="PC Auto, accueil">
        <img src="<?php echo esc_url(get_theme_file_uri('assets/img/logo-pcauto.webp')); ?>" alt="PC Auto" width="480" height="417">
      </a>
      <nav class="nav" aria-label="Navigation principale">
        <div class="nav__drop">
          <a href="<?php echo esc_url(home_url('/#succursales')); ?>" aria-haspopup="true">Succursales</a>
          <div class="nav__menu">
            <?php foreach (pc_branches() as $b) : ?><a href="<?php echo esc_url($b['url']); ?>"><b><?php echo esc_html($b['name']); ?></b><span><?php echo esc_html($b['street']); ?></span></a><?php endforeach; ?>
          </div>
        </div>
        <a href="<?php echo esc_url(home_url('/#financement')); ?>">Financement</a>
        <a href="<?php echo esc_url(home_url('/#services')); ?>">Services</a>
        <a href="<?php echo esc_url(pc_blog_url()); ?>">Blog</a>
      </nav>
      <div class="header-utils">
        <a class="tel" href="tel:+14503780888"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/></svg><span>450 378-0888</span></a>
        <a class="btn btn--red" href="<?php echo esc_url(home_url('/inventaire/')); ?>">Voir l'inventaire</a>
        <a class="menu-toggle" href="#mobile-menu" aria-label="Ouvrir le menu"><span></span></a>
      </div>
    </div>
  </header>

  <div class="mobile-menu" id="mobile-menu">
    <a class="menu-toggle menu-toggle--close" href="#contenu" aria-label="Fermer le menu"><span></span></a>
    <nav aria-label="Navigation mobile">
      <a href="<?php echo esc_url(home_url('/inventaire/')); ?>">Inventaire</a>
      <a href="<?php echo esc_url(home_url('/granby/')); ?>">Granby</a>
      <a href="<?php echo esc_url(home_url('/sainte-eulalie/')); ?>">Sainte-Eulalie</a>
      <a href="<?php echo esc_url(home_url('/#financement')); ?>">Financement</a>
      <a href="<?php echo esc_url(pc_blog_url()); ?>">Blog</a>
    </nav>
    <div class="mobile-menu__foot">
      <a class="btn btn--light btn--lg" href="tel:+14503780888"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/></svg>Appeler le 450 378-0888</a>
      <p>Lun. au ven. 8 h – 18 h · Sam. 10 h – 13 h (sur rendez-vous)</p>
    </div>
  </div>

  <a class="wa-float" href="https://api.whatsapp.com/send?phone=14503780888&amp;text=" target="_blank" rel="noopener" aria-label="Écrivez-nous sur WhatsApp">
    <svg viewBox="0 0 32 32" aria-hidden="true"><path fill="currentColor" d="M16.04 3C8.86 3 3.04 8.8 3.04 15.96c0 2.3.6 4.53 1.75 6.5L3 29l6.72-1.76a12.97 12.97 0 0 0 6.31 1.62h.01c7.17 0 13-5.8 13-12.96C29.04 8.8 23.2 3 16.04 3zm0 23.68h-.01a10.8 10.8 0 0 1-5.5-1.5l-.4-.23-3.99 1.04 1.07-3.88-.26-.4a10.7 10.7 0 0 1-1.65-5.75c0-5.94 4.85-10.78 10.8-10.78 5.95 0 10.79 4.84 10.79 10.78 0 5.95-4.85 10.72-10.85 10.72zm5.92-8.05c-.32-.16-1.92-.95-2.22-1.05-.3-.11-.51-.16-.73.16-.21.32-.84 1.05-1.03 1.27-.19.21-.38.24-.7.08-.32-.16-1.37-.5-2.6-1.6a9.8 9.8 0 0 1-1.8-2.24c-.19-.32-.02-.5.14-.65.14-.15.32-.38.48-.57.16-.19.21-.32.32-.54.1-.21.05-.4-.03-.56-.08-.16-.73-1.75-1-2.4-.26-.63-.53-.54-.73-.55h-.62c-.21 0-.56.08-.86.4-.3.32-1.13 1.1-1.13 2.7 0 1.58 1.16 3.12 1.32 3.33.16.21 2.28 3.48 5.53 4.88.77.33 1.37.53 1.84.68.78.25 1.48.21 2.04.13.62-.09 1.92-.79 2.19-1.54.27-.76.27-1.4.19-1.54-.08-.13-.29-.21-.61-.37z"/></svg>
    <span class="wa-float__label">Écrivez-nous</span>
  </a>

