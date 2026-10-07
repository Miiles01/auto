        <!-- (05) Parcourir -->
        <section class="section browse" aria-labelledby="browse-title">
          <div class="container">
            <div class="section-head">
              <p class="eyebrow">(03) — Parcourir</p>
              <p class="section-head__note">VUS, berlines, camionnettes : chaque véhicule est inspecté, préparé et photographié sur notre terrain avant d'arriver ici.</p>
            </div>
            <h2 class="h-section" id="browse-title" style="margin-bottom:2rem">Parcourir l’inventaire <em>par catégorie</em></h2>
            <div class="browse__stage">
              <div class="browse__plate browse__plate--l" aria-hidden="true"><div class="plate"><?php pc_browse_plate(0, $args['branch'] ?? ''); ?></div></div>
              <div class="browse__list">
                <ul><?php pc_browse_list($args['branch'] ?? ''); ?></ul>
              </div>
              <div class="browse__plate browse__plate--r" aria-hidden="true"><div class="plate"><?php pc_browse_plate(1, $args['branch'] ?? ''); ?></div></div>
            </div>
          </div>
        </section>
