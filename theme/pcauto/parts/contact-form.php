<?php
/**
 * Formulaire multi-usage.
 * Arguments : type = contact | vente | financement ; selected = ID d'un véhicule (fiche) ; succursale = nom (page locale).
 * Le champ « véhicule » n'apparaît que sur la fiche d'un véhicule.
 */
$type = in_array($args['type'] ?? 'contact', ['contact', 'vente', 'financement'], true) ? ($args['type'] ?? 'contact') : 'contact';
$selected = (int) ($args['selected'] ?? 0);
$branch = $args['succursale'] ?? '';
$sujets = ['Acheter une auto usagée', 'Vendre mon auto', 'Financement auto usagé', 'Autre question'];
$labels = ['contact' => 'Envoyer le message', 'vente' => 'Obtenir mon offre', 'financement' => 'Faire ma demande'];
$id = 'f' . wp_unique_id();
?>
<form class="form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
  <input type="hidden" name="action" value="pc_contact">
  <input type="hidden" name="type" value="<?php echo esc_attr($type); ?>">
  <?php if ($branch) : ?><input type="hidden" name="succursale" value="<?php echo esc_attr($branch); ?>"><?php endif; ?>
  <?php wp_nonce_field('pc_contact', 'pc_nonce'); ?>
  <div class="pc-hp" aria-hidden="true"><label>Ne pas remplir<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

  <div class="field">
    <label for="<?php echo $id; ?>-nom">Nom et prénom <span class="req" aria-hidden="true">*</span></label>
    <input id="<?php echo $id; ?>-nom" name="nom" type="text" autocomplete="name" required>
  </div>
  <div class="field">
    <label for="<?php echo $id; ?>-tel">Téléphone <span class="req" aria-hidden="true">*</span></label>
    <input id="<?php echo $id; ?>-tel" name="telephone" type="tel" inputmode="tel" autocomplete="tel" required placeholder="450 000-0000">
  </div>
  <div class="field">
    <label for="<?php echo $id; ?>-mail">Courriel <span class="req" aria-hidden="true">*</span></label>
    <input id="<?php echo $id; ?>-mail" name="courriel" type="email" autocomplete="email" required>
  </div>

  <?php if ($type === 'contact') : ?>
    <div class="field">
      <label for="<?php echo $id; ?>-sujet">Sujet</label>
      <select id="<?php echo $id; ?>-sujet" name="sujet">
        <?php foreach ($sujets as $s) : ?><option<?php selected($selected ? 'Acheter une auto usagée' : '', $s); ?>><?php echo esc_html($s); ?></option><?php endforeach; ?>
      </select>
    </div>
    <?php if (!$branch) : ?>
    <div class="field">
      <label for="<?php echo $id; ?>-suc">Succursale</label>
      <select id="<?php echo $id; ?>-suc" name="succursale">
        <option value="">Peu importe</option>
        <?php foreach (pc_branches() as $b) : ?><option><?php echo esc_html($b['name']); ?></option><?php endforeach; ?>
      </select>
    </div>
    <?php endif; ?>
  <?php endif; ?>

  <?php if ($type === 'vente') : ?>
    <input type="hidden" name="sujet" value="Vendre mon auto">
    <div class="field"><label for="<?php echo $id; ?>-marque">Marque <span class="req" aria-hidden="true">*</span></label><input id="<?php echo $id; ?>-marque" name="marque" type="text" required></div>
    <div class="field"><label for="<?php echo $id; ?>-modele">Modèle <span class="req" aria-hidden="true">*</span></label><input id="<?php echo $id; ?>-modele" name="modele" type="text" required></div>
    <div class="field"><label for="<?php echo $id; ?>-annee">Année <span class="req" aria-hidden="true">*</span></label><input id="<?php echo $id; ?>-annee" name="annee" type="number" inputmode="numeric" min="1980" max="2100" required></div>
    <div class="field"><label for="<?php echo $id; ?>-km">Kilométrage <span class="req" aria-hidden="true">*</span></label><input id="<?php echo $id; ?>-km" name="km" type="number" inputmode="numeric" min="0" required></div>
  <?php endif; ?>

  <?php if ($type === 'financement') : ?>
    <input type="hidden" name="sujet" value="Financement auto usagé">
  <?php endif; ?>

  <?php if ($selected) : ?>
  <div class="field field--full">
    <label for="<?php echo $id; ?>-veh">Véhicule qui vous intéresse</label>
    <select id="<?php echo $id; ?>-veh" name="vehicule">
      <option value="">Aucun en particulier</option>
      <?php foreach (pc_ids($branch ? ['succursale' => $branch] : [], -1, 'recent') as $vid) : $vc = pc_car($vid); ?>
        <option value="<?php echo (int) $vid; ?>"<?php selected($selected, $vid); ?>><?php echo esc_html($vc['name'] . ' ' . $vc['year'] . ' — ' . pc_price($vc['price'])); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <?php endif; ?>

  <div class="field field--full">
    <label for="<?php echo $id; ?>-msg">Message</label>
    <textarea id="<?php echo $id; ?>-msg" name="message" rows="4"></textarea>
  </div>
  <div class="form__foot">
    <p class="form__note">Nous vous répondons rapidement, en français, en anglais, en espagnol ou en portugais. Vous pouvez aussi nous appeler au 450 378-0888.</p>
    <button class="btn btn--red btn--lg" type="submit"><?php echo esc_html($labels[$type]); ?> <?php echo pc_icon('arrow'); ?></button>
  </div>
  <?php if (isset($_GET['envoye'])) : ?><p class="form__status form__status--ok is-visible" role="status">Merci ! Votre message a bien été envoyé. Nous vous répondons rapidement.</p><?php endif; ?>
  <?php if (isset($_GET['erreur'])) : ?><p class="form__status form__status--error is-visible" role="alert">Le message n’a pas pu être envoyé. Vérifiez les champs ou appelez-nous au 450 378-0888.</p><?php endif; ?>
</form>
