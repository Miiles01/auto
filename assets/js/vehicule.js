/* PC Auto — fiche véhicule : galerie, visionneuse, calculateur de paiements */
(function () {
  "use strict";
  var PC = window.PC;
  var esc = PC.esc;
  var icons = PC.icons;
  var motion = typeof window.gsap !== "undefined" && !PC.reduceMotion;

  var slug = new URLSearchParams(location.search).get("v");
  var car = PC.cars.filter(function (c) { return c.slug === slug; })[0];
  var body = document.querySelector("[data-vd-body]");
  var ADDR = {
    "Granby": "1297, rue Principale, Granby (Québec) J2J 0M3",
    "Sainte-Eulalie": "315, rue des Bouleaux, Sainte-Eulalie (Québec) G0Z 1E0"
  };

  if (!car) {
    document.querySelector("[data-vd]").innerHTML =
      '<div class="vd-missing"><h1>Ce véhicule n’est plus disponible</h1><p class="lead" style="margin-bottom:2rem">Il a peut-être déjà trouvé preneur. Jetez un œil au reste de l’inventaire.</p><a class="btn btn--red btn--lg" href="inventaire.html">Voir l’inventaire ' + icons.arrow + "</a></div>";
    document.querySelector("[data-vd-similar-wrap]").remove();
    PC.initCommon();
    return;
  }

  var name = PC.carName(car);
  var full = name + " " + car.year;
  var price = PC.fmtPrice(car.price);
  var interest = "Bonjour PC Auto, je suis intéressé(e) par le " + full + " (stock " + car.stock + ", " + price + ").";
  var testDrive = "Bonjour PC Auto, j’aimerais réserver un essai routier pour le " + full + " (stock " + car.stock + ") à votre succursale de " + car.location + ".";

  /* Métadonnées */
  document.title = full + " à " + car.location + " — " + price + " | PC Auto";
  var md = document.querySelector('meta[name="description"]');
  if (md) { md.setAttribute("content", full + " d’occasion, " + PC.fmtKm(car.km) + ", " + car.transmission.toLowerCase() + ", " + car.drivetrain + ". " + price + " chez PC Auto à " + car.location + ". Financement sur place."); }
  var ld = document.createElement("script");
  ld.type = "application/ld+json";
  ld.textContent = JSON.stringify({
    "@context": "https://schema.org", "@type": "Car",
    name: full, brand: { "@type": "Brand", name: car.make }, model: car.model,
    vehicleModelDate: String(car.year), vehicleIdentificationNumber: car.vin, color: car.color,
    mileageFromOdometer: { "@type": "QuantitativeValue", value: car.km, unitCode: "KMT" },
    vehicleTransmission: car.transmission, driveWheelConfiguration: car.drivetrain,
    image: car.images.slice(0, 5).map(function (p) { return location.origin + "/" + p; }),
    offers: { "@type": "Offer", price: car.price, priceCurrency: "CAD", availability: "https://schema.org/InStock", seller: { "@type": "AutoDealer", name: "PC Auto" } }
  });
  document.head.appendChild(ld);
  document.querySelector("[data-vd-crumb]").textContent = full;
  document.querySelector("[data-vd-name]").textContent = "le " + full;
  document.querySelector("[data-vd-wa-link]").href = PC.waLink(interest);

  /* Rendu */
  var specs = [
    ["Prix", price], ["Kilométrage", PC.fmtKm(car.km)], ["Année", car.year], ["Transmission", car.transmission],
    ["Moteur", car.engine], ["Motricité", car.drivetrain], ["Couleur", car.color], ["Catégorie", PC.BODY[car.body]],
    ["Succursale", car.location], ["Numéro de stock", car.stock], ["Numéro de série (NIV)", car.vin]
  ];
  var n = car.images.length;
  body.innerHTML =
    '<header class="vd__head">' +
      '<h1 class="vd__title display"><small>' + esc(PC.BODY[car.body]) + " · " + car.year + " · " + esc(car.location) + "</small>" + esc(name) + "</h1>" +
      '<div class="vd__price"><b class="tabular">' + price + "</b><span>Taxes et frais en sus · Stock " + esc(car.stock) + "</span></div>" +
    "</header>" +
    '<div class="vd__layout">' +
      '<div class="vd__main">' +
      '<div class="gallery">' +
        '<div class="gallery__main" data-g-main role="button" tabindex="0" aria-label="Agrandir la photo">' +
          '<img data-g-img src="' + car.images[0] + '" alt="' + esc(full) + ', photo 1 de ' + n + '" width="940" height="650" fetchpriority="high">' +
          '<button class="gallery__nav gallery__nav--prev" type="button" data-g-prev aria-label="Photo précédente">' + icons.chevronLeft + "</button>" +
          '<button class="gallery__nav gallery__nav--next" type="button" data-g-next aria-label="Photo suivante">' + icons.chevronRight + "</button>" +
          '<span class="gallery__counter" data-g-count>1 / ' + n + "</span>" +
          '<span class="gallery__zoom" aria-hidden="true">' + icons.expand + "Agrandir</span>" +
        "</div>" +
        '<div class="gallery__thumbs" data-g-thumbs>' + car.thumbs.map(function (t, i) {
          return '<button type="button" aria-label="Photo ' + (i + 1) + '"' + (i === 0 ? ' aria-current="true"' : "") + ' data-g-i="' + i + '"><img src="' + t + '" alt="" loading="lazy" width="720" height="498"></button>';
        }).join("") + "</div>" +
      "</div>" +
      '<div class="vd__details">' +
        '<div data-reveal><h2 class="vd__subhead">Caractéristiques</h2><table class="spec-table"><tbody>' +
          specs.map(function (s) { return '<tr><th scope="row">' + s[0] + "</th><td>" + esc(s[1]) + "</td></tr>"; }).join("") +
        "</tbody></table></div>" +
        '<div data-reveal><h2 class="vd__subhead">Équipements et options</h2><ul class="options">' +
          car.options.map(function (o) { return "<li>" + icons.check + esc(o) + "</li>"; }).join("") +
        "</ul></div>" +
      "</div>" +
      "</div>" +
      '<aside class="vd__panel">' +
        '<dl class="key-specs">' +
          [["Kilométrage", PC.fmtKm(car.km)], ["Transmission", car.transmission], ["Motricité", car.drivetrain], ["Moteur", car.engine]].map(function (s) {
            return "<div><dt>" + s[0] + "</dt><dd>" + esc(s[1]) + "</dd></div>";
          }).join("") +
        "</dl>" +
        '<div class="panel panel--cta">' +
          "<h2>Ce véhicule vous intéresse ?</h2>" +
          "<p>Il se trouve à notre succursale de " + esc(car.location) + ". Écrivez-nous ou appelez pour réserver votre essai routier.</p>" +
          '<div class="panel__actions">' +
            '<a class="btn btn--wa btn--lg" href="' + PC.waLink(interest) + '" target="_blank" rel="noopener">' + icons.whatsapp + "Je suis intéressé(e)</a>" +
            '<a class="btn btn--red btn--lg" href="' + PC.waLink(testDrive) + '" target="_blank" rel="noopener">Réserver un essai routier ' + icons.arrow + "</a>" +
            '<a class="btn btn--ghost-light btn--lg" href="tel:+14503780888">' + icons.phone + "450 378-0888</a>" +
          "</div>" +
        "</div>" +
        '<div class="panel">' +
          "<h2>Estimez vos paiements</h2>" +
          '<div class="calc" data-calc>' +
            '<div class="calc__row"><label for="c-down"><span>Mise de fonds</span><output data-c-down-out></output></label><input id="c-down" type="range" min="0" max="' + Math.round(car.price * 0.5 / 250) * 250 + '" step="250" value="' + Math.round(car.price * 0.1 / 250) * 250 + '"></div>' +
            '<div class="calc__row"><span style="font-size:.875rem">Terme</span><div class="calc__terms" role="group" aria-label="Terme du financement">' +
              [36, 48, 60, 72].map(function (t) { return '<button class="pill" type="button" data-term="' + t + '" aria-pressed="' + (t === 60) + '">' + t + " mois</button>"; }).join("") +
            "</div></div>" +
            '<div class="calc__row"><label for="c-rate"><span>Taux annuel estimé</span><output data-c-rate-out></output></label><input id="c-rate" type="range" min="2.99" max="24.99" step="0.5" value="9.99"></div>' +
            '<div class="calc__result"><span>Paiement par semaine<br><small style="color:var(--fg-muted)" data-c-month></small></span><b data-c-week></b></div>' +
            '<p class="calc__note">Estimation à titre indicatif seulement, taxes et frais en sus. Le taux réel dépend de votre dossier de crédit. <a href="#contact" class="link-underline" style="color:var(--accent-strong)">Demandez votre approbation</a>.</p>' +
          "</div>" +
        "</div>" +
      "</aside>" +
    "</div>";

  /* Où voir le véhicule */
  document.querySelector("[data-vd-where]").innerHTML =
    "<dt>Succursale</dt><dd>" + esc(car.location) + "</dd><dt>Adresse</dt><dd>" + esc(ADDR[car.location]) + "</dd>" +
    "<dt>Lun. au ven.</dt><dd>8 h 00 – 18 h 00</dd><dt>Samedi</dt><dd>10 h 00 – 13 h 00, sur rendez-vous</dd>";

  /* Formulaire : véhicule présélectionné */
  var sel = document.getElementById("f-vehicule");
  if (sel) { sel.value = car.slug; }

  /* -- Galerie ------------------------------------------------------------ */
  var cur = 0;
  var mainImg = body.querySelector("[data-g-img]");
  var countEl = body.querySelector("[data-g-count]");
  var thumbs = body.querySelectorAll("[data-g-thumbs] button");
  var lb = document.querySelector("[data-lightbox]");
  var lbImg = lb.querySelector("[data-lb-img]");
  var lbCount = lb.querySelector("[data-lb-count]");
  var lastFocus = null;

  var show = function (i) {
    cur = (i + n) % n;
    mainImg.src = car.images[cur];
    mainImg.alt = full + ", photo " + (cur + 1) + " de " + n;
    countEl.textContent = (cur + 1) + " / " + n;
    thumbs.forEach(function (t, k) {
      if (k === cur) { t.setAttribute("aria-current", "true"); } else { t.removeAttribute("aria-current"); }
    });
    var th = thumbs[cur];
    th.parentElement.scrollTo({ left: th.offsetLeft - th.parentElement.clientWidth / 2 + th.clientWidth / 2, behavior: "smooth" });
    if (lb.classList.contains("is-open")) {
      lbImg.src = car.images[cur]; lbImg.alt = mainImg.alt; lbCount.textContent = (cur + 1) + " / " + n;
    }
    if (motion) { gsap.fromTo(mainImg, { opacity: 0.4 }, { opacity: 1, duration: 0.4, ease: "power2.out" }); }
    var next = new Image(); next.src = car.images[(cur + 1) % n];
  };
  body.querySelector("[data-g-prev]").addEventListener("click", function (e) { e.stopPropagation(); show(cur - 1); });
  body.querySelector("[data-g-next]").addEventListener("click", function (e) { e.stopPropagation(); show(cur + 1); });
  thumbs.forEach(function (t) { t.addEventListener("click", function () { show(+t.getAttribute("data-g-i")); }); });

  var openLb = function () {
    lastFocus = document.activeElement;
    lb.classList.add("is-open");
    lbImg.src = car.images[cur]; lbImg.alt = mainImg.alt; lbCount.textContent = (cur + 1) + " / " + n;
    if (PC.smoother) { PC.smoother.paused(true); }
    document.documentElement.style.overflow = "hidden";
    lb.querySelector("[data-lb-close]").focus();
  };
  var closeLb = function () {
    lb.classList.remove("is-open");
    if (PC.smoother) { PC.smoother.paused(false); }
    document.documentElement.style.overflow = "";
    if (lastFocus) { lastFocus.focus(); }
  };
  var main = body.querySelector("[data-g-main]");
  main.addEventListener("click", openLb);
  main.addEventListener("keydown", function (e) { if (e.key === "Enter" || e.key === " ") { e.preventDefault(); openLb(); } });
  lb.querySelector("[data-lb-close]").addEventListener("click", closeLb);
  lb.querySelector("[data-lb-prev]").addEventListener("click", function () { show(cur - 1); });
  lb.querySelector("[data-lb-next]").addEventListener("click", function () { show(cur + 1); });
  lb.addEventListener("click", function (e) { if (e.target === lb) { closeLb(); } });
  document.addEventListener("keydown", function (e) {
    var open = lb.classList.contains("is-open");
    if (open && e.key === "Escape") { closeLb(); }
    if (open && e.key === "Tab") {
      var f = lb.querySelectorAll("button");
      if (e.shiftKey && document.activeElement === f[0]) { e.preventDefault(); f[f.length - 1].focus(); }
      else if (!e.shiftKey && document.activeElement === f[f.length - 1]) { e.preventDefault(); f[0].focus(); }
    }
    if (open || document.activeElement === main) {
      if (e.key === "ArrowLeft") { show(cur - 1); }
      if (e.key === "ArrowRight") { show(cur + 1); }
    }
  });
  /* Balayage tactile */
  var sx = null;
  [main, lb].forEach(function (el) {
    el.addEventListener("touchstart", function (e) { sx = e.touches[0].clientX; }, { passive: true });
    el.addEventListener("touchend", function (e) {
      if (sx === null) { return; }
      var dx = e.changedTouches[0].clientX - sx;
      if (Math.abs(dx) > 40) { show(dx < 0 ? cur + 1 : cur - 1); }
      sx = null;
    }, { passive: true });
  });

  /* -- Calculateur -------------------------------------------------------- */
  var down = document.getElementById("c-down");
  var rate = document.getElementById("c-rate");
  var term = 60;
  var nf = new Intl.NumberFormat("fr-CA", { minimumFractionDigits: 0, maximumFractionDigits: 0 });
  var nf2 = new Intl.NumberFormat("fr-CA", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  var calc = function () {
    var principal = Math.max(car.price - +down.value, 0);
    var r = +rate.value / 100 / 12;
    var m = r ? principal * r / (1 - Math.pow(1 + r, -term)) : principal / term;
    var w = m * 12 / 52;
    document.querySelector("[data-c-down-out]").textContent = nf.format(+down.value) + " $";
    document.querySelector("[data-c-rate-out]").textContent = nf2.format(+rate.value) + " %";
    document.querySelector("[data-c-week]").textContent = nf.format(Math.round(w)) + " $";
    document.querySelector("[data-c-month]").textContent = "soit environ " + nf.format(Math.round(m)) + " $ par mois";
  };
  down.addEventListener("input", calc);
  rate.addEventListener("input", calc);
  body.querySelectorAll("[data-term]").forEach(function (b) {
    b.addEventListener("click", function () {
      term = +b.getAttribute("data-term");
      body.querySelectorAll("[data-term]").forEach(function (x) { x.setAttribute("aria-pressed", String(x === b)); });
      calc();
    });
  });
  calc();

  /* -- Similaires ----------------------------------------------------------- */
  var same = function (c) { return c.body === car.body || (car.body !== "camion" && c.body !== "camion" && (car.body === "coupe" || c.body === "coupe")); };
  var similar = PC.cars.filter(function (c) { return c.slug !== car.slug && same(c); })
    .sort(function (a, b) { return Math.abs(a.price - car.price) - Math.abs(b.price - car.price); })
    .slice(0, 4);
  if (similar.length < 4) {
    similar = similar.concat(PC.cars.filter(function (c) { return c.slug !== car.slug && similar.indexOf(c) < 0; })
      .sort(function (a, b) { return Math.abs(a.price - car.price) - Math.abs(b.price - car.price); })
      .slice(0, 4 - similar.length));
  }
  document.querySelector("[data-vd-similar]").innerHTML = similar.map(PC.renderCard).join("");

  if (motion) {
    gsap.from(".vd__title, .vd__price", { y: 40, opacity: 0, duration: 1.1, ease: "expo.out", stagger: 0.08 });
    gsap.from(".gallery, .vd__panel > *", { y: 30, opacity: 0, duration: 1, ease: "expo.out", stagger: 0.08, delay: 0.15 });
  }
  PC.initCommon();
})();
