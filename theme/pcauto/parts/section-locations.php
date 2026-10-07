        <?php /** Les deux succursales (accueil et contact). Args : h2 (HTML), eyebrow. */ ?>
        <section class="section" id="succursales" aria-labelledby="loc-title">
          <div class="container">
            <?php if (isset($_GET['achat'])) { echo pc_consult_notice(); } ?>
            <div class="section-head">
              <div>
                <p class="eyebrow" style="margin-bottom:1rem"><?php echo esc_html($args['eyebrow'] ?? '(09) — Succursales'); ?></p>
                <h2 class="h-section" id="loc-title"><?php echo $args['h2'] ?? 'Deux adresses, <em>une même équipe</em>'; ?></h2>
              </div>
              <p class="section-head__note">Chaque succursale a son propre inventaire. Appelez avant de passer : nous préparons le véhicule pour votre essai routier.</p>
            </div>
            <div class="locations">
              <article class="location">
                <div class="location__map">
                  <iframe title="Carte : PC Auto Granby, 1297 rue Principale" src="https://www.google.com/maps?q=1297+rue+Principale,+Granby,+QC+J2J+0M3&amp;output=embed&amp;z=15" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <div class="location__body">
                  <h3 class="location__name">PC Auto Granby</h3>
                  <p class="location__addr">1297, rue Principale<br>Granby (Québec) J2J 0M3</p>
                  <dl class="hours">
                    <dt>Lun. au ven.</dt><dd>8 h 00 – 18 h 00</dd>
                    <dt>Samedi</dt><dd>10 h 00 – 13 h 00, sur rendez-vous</dd>
                  </dl>
                  <div class="location__foot">
                    <a class="btn" href="https://www.google.com/maps/dir/?api=1&amp;destination=1297+rue+Principale,+Granby,+QC+J2J+0M3" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg>Itinéraire</a>
                    <a class="btn btn--ghost" href="<?php echo esc_url(home_url('/granby/')); ?>">Voir la succursale de Granby</a>
                  </div>
                </div>
              </article>
              <article class="location">
                <div class="location__map">
                  <iframe title="Carte : PC Auto Sainte-Eulalie, 315 rue des Bouleaux" src="https://www.google.com/maps?q=315+rue+des+Bouleaux,+Sainte-Eulalie,+QC+G0Z+1E0&amp;output=embed&amp;z=15" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <div class="location__body">
                  <h3 class="location__name">PC Auto Sainte-Eulalie</h3>
                  <p class="location__addr">315, rue des Bouleaux<br>Sainte-Eulalie (Québec) G0Z 1E0</p>
                  <dl class="hours">
                    <dt>Lun. au ven.</dt><dd>8 h 00 – 18 h 00</dd>
                    <dt>Samedi</dt><dd>10 h 00 – 13 h 00, sur rendez-vous</dd>
                  </dl>
                  <div class="location__foot">
                    <a class="btn" href="https://www.google.com/maps/dir/?api=1&amp;destination=315+rue+des+Bouleaux,+Sainte-Eulalie,+QC+G0Z+1E0" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg>Itinéraire</a>
                    <a class="btn btn--ghost" href="<?php echo esc_url(home_url('/sainte-eulalie/')); ?>">Voir la succursale de Sainte-Eulalie</a>
                  </div>
                </div>
              </article>
            </div>
          </div>
        </section>
