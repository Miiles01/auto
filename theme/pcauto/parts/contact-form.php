<?php $selected = (int) ($args['selected'] ?? 0); $branch = $args['succursale'] ?? ''; ?>
<form class="form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
  <input type="hidden" name="action" value="pc_contact">
  <?php if ($branch) : ?><input type="hidden" name="succursale" value="<?php echo esc_attr($branch); ?>"><?php endif; ?>
  <?php wp_nonce_field('pc_contact', 'pc_nonce'); ?>
  <div class="pc-hp" aria-hidden="true"><label>Ne pas remplir<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
                <div class="field">
                  <label for="f-nom">Nom et prénom <span class="req" aria-hidden="true">*</span></label>
                  <input id="f-nom" name="nom" type="text" autocomplete="name" required>
                </div>
                <div class="field">
                  <label for="f-tel">Téléphone <span class="req" aria-hidden="true">*</span></label>
                  <input id="f-tel" name="telephone" type="tel" inputmode="tel" autocomplete="tel" required placeholder="450 000-0000">
                </div>
                <div class="field">
                  <label for="f-mail">Courriel <span class="req" aria-hidden="true">*</span></label>
                  <input id="f-mail" name="courriel" type="email" autocomplete="email" required>
                </div>
                <div class="field">
                  <label for="f-sujet">Sujet</label>
                  <select id="f-sujet" name="sujet">
                    <option value="Information sur un véhicule">Information sur un véhicule</option>
                    <option value="Demande de financement">Demande de financement</option>
                    <option value="Faire évaluer mon véhicule">Faire évaluer mon véhicule</option>
                    <option value="Réserver un essai routier">Réserver un essai routier</option>
                    <option value="Question sur la garantie">Question sur la garantie</option>
                  </select>
                </div>
                <div class="field field--full">
                  <label for="f-vehicule">Véhicule qui vous intéresse</label>
                  <select id="f-vehicule" name="vehicule">
          <option value="">Aucun en particulier</option>
          <?php foreach (pc_ids($branch ? ['succursale' => $branch] : [], -1, 'recent') as $vid) : $vc = pc_car($vid); ?>
            <option value="<?php echo (int) $vid; ?>"<?php selected($selected, $vid); ?>><?php echo esc_html($vc['name'] . ' ' . $vc['year'] . ' — ' . pc_price($vc['price'])); ?></option>
          <?php endforeach; ?>
        </select>
                </div>
                <div class="field field--full">
                  <label for="f-msg">Message</label>
                  <textarea id="f-msg" name="message" rows="4"></textarea>
                </div>
                <div class="form__foot">
                  <p class="form__note">Nous vous répondons rapidement. Vous pouvez aussi nous appeler au 450 378-0888.</p>
                  <button class="btn btn--red btn--lg" type="submit">Demander de l'information <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
                </div>
                <?php if (isset($_GET['envoye'])) : ?><p class="form__status form__status--ok is-visible" role="status">Merci ! Votre message a bien été envoyé. Nous vous répondons rapidement.</p><?php endif; ?>
  <?php if (isset($_GET['erreur'])) : ?><p class="form__status form__status--error is-visible" role="alert">Le message n’a pas pu être envoyé. Vérifiez les champs ou appelez-nous au 450 378-0888.</p><?php endif; ?>
</form>
