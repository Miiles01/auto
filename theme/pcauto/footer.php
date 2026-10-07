<footer class="footer">
        <div class="container">
          <div class="footer__top">
            <div class="footer__cta">
              <h2>Votre prochain véhicule<br>vous attend</h2>
              <a class="btn btn--light btn--lg" href="<?php echo esc_url(pc_inv_url('')); ?>">Voir l'inventaire <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
            </div>
            <div class="footer__col">
              <h3>Explorer</h3>
              <ul>
                <li><a class="link-underline" href="<?php echo esc_url(pc_inv_url('')); ?>">Inventaire</a></li>
                <li><a class="link-underline" href="<?php echo esc_url(pc_inv_url('?type=vus')); ?>">VUS</a></li>
                <li><a class="link-underline" href="<?php echo esc_url(pc_inv_url('?type=auto')); ?>">Berlines et compactes</a></li>
                <li><a class="link-underline" href="<?php echo esc_url(pc_inv_url('?type=camion')); ?>">Camionnettes</a></li>
              </ul>
            </div>
            <div class="footer__col">
              <h3>Services</h3>
              <ul>
                <li><a class="link-underline" href="<?php echo esc_url(pc_url('financement')); ?>">Financement</a></li>
                <li><a class="link-underline" href="<?php echo esc_url(pc_url('vendre')); ?>">Vendre mon auto</a></li>
                <li><a class="link-underline" href="<?php echo esc_url(pc_url('acheter')); ?>">Acheter une auto usagée</a></li>
                <li><a class="link-underline" href="<?php echo esc_url(pc_url('contact')); ?>">Nous écrire</a></li>
              </ul>
            </div>
            <div class="footer__col">
              <h3>Nous joindre</h3>
              <ul>
                <li><a class="link-underline" href="tel:+14503780888">450 378-0888</a></li>
                <li><a class="link-underline" href="https://api.whatsapp.com/send?phone=14503780888&amp;text=" target="_blank" rel="noopener">WhatsApp</a></li>
                <li><a class="link-underline" href="<?php echo esc_url(home_url('/granby/')); ?>">Granby — 1297, rue Principale</a></li>
                <li><a class="link-underline" href="<?php echo esc_url(home_url('/sainte-eulalie/')); ?>">Sainte-Eulalie — 315, rue des Bouleaux</a></li>
                <li><a class="link-underline" href="<?php echo esc_url(pc_url('apropos')); ?>">À propos</a></li>
                <li><a class="link-underline" href="<?php echo esc_url(pc_blog_url()); ?>">Blog</a></li>
              </ul>
            </div>
          </div>
          <div class="footer__legal">
            <span>© <?php echo esc_html(wp_date('Y')); ?> PC Auto. Tous droits réservés.</span>
            <span>Lun. au ven. 8 h – 18 h · Sam. 10 h – 13 h (sur rendez-vous)</span>
            <span>Prix en dollars canadiens, taxes et frais en sus.</span>
          </div>
          <p class="footer__word" aria-hidden="true"><span>P</span><span>C</span><span>&nbsp;</span><span>A</span><span>u</span><span>t</span><span>o</span></p>
        </div>
      </footer>
<?php wp_footer(); ?>
</body>
</html>
