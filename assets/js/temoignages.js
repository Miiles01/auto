/* PC Auto — composant « Témoignages » (accueil et inventaire).
   DÉMO : ces témoignages sont des exemples. Avant la mise en ligne, les
   remplacer par de vrais avis de clients (avec leur accord), par exemple
   repris de la fiche Google de l'entreprise. */
(function () {
  "use strict";

  var TEMOIGNAGES = [
    { nom: "Sophie L.", ville: "Granby", vehicule: "Kia Sorento 2016", photo: "sophie",
      texte: "Je cherchais un VUS familial sans me ruiner. On m'a laissée faire l'essai routier deux fois, sans pression, et le financement a été approuvé le jour même." },
    { nom: "Marc-André T.", ville: "Drummondville", vehicule: "Chevrolet Silverado 2013", photo: "marc-andre",
      texte: "Ils ont évalué mon ancien camion et l'ont repris sur place. Le Silverado était propre, inspecté et prêt à partir. Transaction simple du début à la fin." },
    { nom: "Julie B.", ville: "Sainte-Eulalie", vehicule: "Honda Civic 2014", photo: "julie",
      texte: "Premier achat d'auto, j'étais nerveuse. Tout m'a été expliqué clairement, les papiers comme la garantie. Trois mois plus tard, aucune mauvaise surprise." },
    { nom: "Thomas G.", ville: "Saint-Hyacinthe", vehicule: "Jeep Wrangler 2012", photo: "thomas",
      texte: "Les photos sur le site correspondaient exactement au véhicule. Réponse sur WhatsApp en quelques minutes, un samedi en plus." },
    { nom: "Camille R.", ville: "Granby", vehicule: "Hyundai Elantra 2016", photo: "camille",
      texte: "Un accueil chaleureux, et le service en espagnol pour ma mère qui m'accompagnait. On s'est sentis bien conseillés, pas seulement vendus." },
    { nom: "Olivier D.", ville: "Bromont", vehicule: "Volkswagen Jetta 2014", photo: "olivier",
      texte: "Prix affiché, prix payé. Le financement directement chez eux m'a évité un aller-retour à la banque. Je recommande sans hésiter." }
  ];

  var esc = function (s) {
    return String(s).replace(/[&<>"']/g, function (c) { return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c]; });
  };
  var chevron = function (dir) {
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="' + (dir === "prev" ? "m15 18-6-6 6-6" : "m9 18 6-6-6-6") + '"/></svg>';
  };

  document.querySelectorAll("[data-temoignages]").forEach(function (root) {
    var eyebrow = root.getAttribute("data-eyebrow") || "Témoignages";
    root.innerHTML =
      '<div class="container">' +
        '<div class="section-head">' +
          '<div><p class="eyebrow" style="margin-bottom:1rem">' + esc(eyebrow) + "</p>" +
          '<h2 class="h-section" id="' + root.id + '-title">Ils roulent <em>en confiance</em></h2></div>' +
          '<div class="testi__head-side"><p class="section-head__note">Des clients de Granby, de Sainte-Eulalie et des environs racontent leur achat chez PC Auto.</p>' +
          '<div class="testi__nav"><button class="testi__btn" type="button" data-testi-prev aria-label="Témoignages précédents">' + chevron("prev") + "</button>" +
          '<button class="testi__btn" type="button" data-testi-next aria-label="Témoignages suivants">' + chevron("next") + "</button></div></div>" +
        "</div>" +
        '<ul class="testi__track" data-testi-track tabindex="0" aria-label="Témoignages de clients">' +
          TEMOIGNAGES.map(function (t) {
            return '<li class="testi" data-reveal>' +
              '<svg class="testi__quote" viewBox="0 0 32 32" aria-hidden="true"><path fill="currentColor" d="M13 8C7.5 9.3 4 13.6 4 19.3V25h9v-9H8.4c.4-3 2.4-5.2 5.2-6zM28 8c-5.5 1.3-9 5.6-9 11.3V25h9v-9h-4.6c.4-3 2.4-5.2 5.2-6z"/></svg>' +
              "<blockquote><p>" + esc(t.texte) + "</p></blockquote>" +
              '<div class="testi__who">' +
                '<img src="assets/img/avatars/' + t.photo + '.webp" alt="" width="240" height="240" loading="lazy">' +
                "<p><b>" + esc(t.nom) + "</b><span>" + esc(t.vehicule) + " · " + esc(t.ville) + "</span></p>" +
              "</div>" +
            "</li>";
          }).join("") +
        "</ul>" +
      "</div>";

    var track = root.querySelector("[data-testi-track]");
    var prev = root.querySelector("[data-testi-prev]");
    var next = root.querySelector("[data-testi-next]");
    var step = function () {
      var card = track.querySelector(".testi");
      return card ? card.getBoundingClientRect().width + parseFloat(getComputedStyle(track).columnGap || 0) : track.clientWidth;
    };
    var sync = function () {
      prev.disabled = track.scrollLeft <= 4;
      next.disabled = track.scrollLeft + track.clientWidth >= track.scrollWidth - 4;
    };
    prev.addEventListener("click", function () { track.scrollBy({ left: -step(), behavior: "smooth" }); });
    next.addEventListener("click", function () { track.scrollBy({ left: step(), behavior: "smooth" }); });
    track.addEventListener("scroll", sync, { passive: true });
    track.addEventListener("keydown", function (e) {
      if (e.key === "ArrowRight") { e.preventDefault(); next.click(); }
      if (e.key === "ArrowLeft") { e.preventDefault(); prev.click(); }
    });
    window.addEventListener("resize", sync);
    sync();
  });
})();
