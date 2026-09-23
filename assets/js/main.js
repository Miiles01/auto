/* PC Auto — scripts communs à toutes les pages */
(function () {
  "use strict";

  document.documentElement.classList.add("js");

  var PHONE_DISPLAY = "450 378-0888";
  var PHONE_TEL = "+14503780888";
  var WA_BASE = "https://api.whatsapp.com/send?phone=14503780888&text=";
  var reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  var finePointer = window.matchMedia("(hover: hover) and (pointer: fine)").matches;

  /* -- Icônes (trait fin, 24×24) ---------------------------------------- */
  var I = function (d, extra) {
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"' + (extra || "") + ">" + d + "</svg>";
  };
  var icons = {
    arrow: I('<path d="M5 12h14M13 6l6 6-6 6"/>'),
    arrowUpRight: I('<path d="M7 17 17 7M8 7h9v9"/>'),
    chevronLeft: I('<path d="m15 18-6-6 6-6"/>'),
    chevronRight: I('<path d="m9 18 6-6-6-6"/>'),
    check: I('<path d="M20 6 9 17l-5-5"/>'),
    plus: I('<path d="M12 5v14M5 12h14"/>'),
    close: I('<path d="M18 6 6 18M6 6l12 12"/>'),
    search: I('<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>'),
    expand: I('<path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/>'),
    pin: I('<path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>'),
    phone: I('<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/>'),
    mail: I('<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>'),
    clock: I('<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>'),
    whatsapp: '<svg viewBox="0 0 32 32" aria-hidden="true"><path fill="currentColor" d="M16.04 3C8.86 3 3.04 8.8 3.04 15.96c0 2.3.6 4.53 1.75 6.5L3 29l6.72-1.76a12.97 12.97 0 0 0 6.31 1.62h.01c7.17 0 13-5.8 13-12.96C29.04 8.8 23.2 3 16.04 3zm0 23.68h-.01a10.8 10.8 0 0 1-5.5-1.5l-.4-.23-3.99 1.04 1.07-3.88-.26-.4a10.7 10.7 0 0 1-1.65-5.75c0-5.94 4.85-10.78 10.8-10.78 5.95 0 10.79 4.84 10.79 10.78 0 5.95-4.85 10.72-10.85 10.72zm5.92-8.05c-.32-.16-1.92-.95-2.22-1.05-.3-.11-.51-.16-.73.16-.21.32-.84 1.05-1.03 1.27-.19.21-.38.24-.7.08-.32-.16-1.37-.5-2.6-1.6a9.8 9.8 0 0 1-1.8-2.24c-.19-.32-.02-.5.14-.65.14-.15.32-.38.48-.57.16-.19.21-.32.32-.54.1-.21.05-.4-.03-.56-.08-.16-.73-1.75-1-2.4-.26-.63-.53-.54-.73-.55h-.62c-.21 0-.56.08-.86.4-.3.32-1.13 1.1-1.13 2.7 0 1.58 1.16 3.12 1.32 3.33.16.21 2.28 3.48 5.53 4.88.77.33 1.37.53 1.84.68.78.25 1.48.21 2.04.13.62-.09 1.92-.79 2.19-1.54.27-.76.27-1.4.19-1.54-.08-.13-.29-.21-.61-.37z"/></svg>'
  };

  /* -- Formats ----------------------------------------------------------- */
  var nf = new Intl.NumberFormat("fr-CA");
  var fmtPrice = function (n) { return n ? nf.format(n) + " $" : "Prix sur demande"; };
  var fmtKm = function (n) { return nf.format(n) + " km"; };
  var BODY = { vus: "VUS", camion: "Camionnette", auto: "Berline et compacte", coupe: "Coupé sport" };
  var cars = (window.PC_CARS || []).slice();
  var carName = function (c) { return c.make + " " + c.model; };
  var carUrl = function (c) { return "vehicule.html?v=" + encodeURIComponent(c.slug); };
  var waLink = function (text) { return WA_BASE + encodeURIComponent(text || ""); };
  var esc = function (s) {
    return String(s).replace(/[&<>"']/g, function (ch) {
      return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[ch];
    });
  };
  var pad = function (n) { return (n < 10 ? "0" : "") + n; };

  var renderCard = function (c, i) {
    var second = c.thumbs[3] || c.thumbs[1] || c.thumbs[0];
    return (
      '<article class="car-card" data-cursor="Voir">' +
      '<div class="car-card__media">' +
      '<span class="car-card__index">(' + pad(i + 1) + ")</span>" +
      '<span class="chip car-card__loc">' + icons.pin + esc(c.location) + "</span>" +
      '<img src="' + c.thumbs[0] + '" alt="' + esc(carName(c) + " " + c.year + ", vue avant") + '" width="720" height="498" loading="lazy" decoding="async">' +
      '<img src="' + second + '" alt="" width="720" height="498" loading="lazy" decoding="async">' +
      "</div>" +
      '<h3 class="car-card__title"><a class="car-card__link" href="' + carUrl(c) + '">' + esc(carName(c)) + " <span>" + c.year + "</span></a></h3>" +
      '<p class="car-card__spec">' + fmtKm(c.km) + " · " + esc(c.transmission) + " · " + esc(c.drivetrain) + "</p>" +
      '<div class="car-card__foot"><span class="car-card__price">' + fmtPrice(c.price) + '</span><span class="car-card__more">Détails ' + icons.arrow + "</span></div>" +
      "</article>"
    );
  };

  var renderSoonCard = function () {
    return (
      '<article class="car-card car-card--soon">' +
      '<div class="car-card__media"><img src="assets/img/en-preparation.webp" alt="Véhicule sous une housse, en préparation" width="474" height="266" loading="lazy"><span class="soon-label">Bientôt en inventaire</span></div>' +
      '<h3 class="car-card__title">5 véhicules <span>en préparation</span></h3>' +
      '<p class="car-card__spec">Inspection et esthétique en cours. Écrivez-nous pour être le premier informé.</p>' +
      '<div class="car-card__foot"><a class="car-card__more car-card__link" href="' + waLink("Bonjour PC Auto, j'aimerais être informé(e) des prochains véhicules disponibles.") + '" target="_blank" rel="noopener">M’aviser ' + icons.arrow + "</a></div>" +
      "</article>"
    );
  };

  window.PC = {
    cars: cars, icons: icons, fmtPrice: fmtPrice, fmtKm: fmtKm, BODY: BODY,
    carName: carName, carUrl: carUrl, waLink: waLink, esc: esc, pad: pad,
    renderCard: renderCard, renderSoonCard: renderSoonCard,
    reduceMotion: reduceMotion, finePointer: finePointer,
    PHONE_DISPLAY: PHONE_DISPLAY, PHONE_TEL: PHONE_TEL
  };

  /* -- Injection des icônes déclaratives : <i data-icon="arrow"></i> ------- */
  var paintIcons = function (root) {
    (root || document).querySelectorAll("[data-icon]").forEach(function (el) {
      var k = el.getAttribute("data-icon");
      if (icons[k]) { el.outerHTML = icons[k]; }
    });
  };
  paintIcons();
  PC.paintIcons = paintIcons;

  /* -- Défilement doux (GSAP ScrollSmoother) ------------------------------ */
  var hasGsap = typeof window.gsap !== "undefined";
  var smoother = null;
  if (hasGsap) {
    gsap.registerPlugin.apply(gsap, [window.ScrollTrigger, window.ScrollSmoother, window.SplitText].filter(Boolean));
    if (!reduceMotion && window.ScrollSmoother && document.getElementById("smooth-wrapper")) {
      smoother = ScrollSmoother.create({
        wrapper: "#smooth-wrapper",
        content: "#smooth-content",
        smooth: 1.1,
        effects: true,
        smoothTouch: false,
        normalizeScroll: false
      });
    }
  }
  PC.smoother = smoother;
  PC.refresh = function () { if (window.ScrollTrigger) { ScrollTrigger.refresh(); } };
  PC.scrollTo = function (target) {
    var el = typeof target === "string" ? document.querySelector(target) : target;
    if (!el) { return; }
    if (smoother) { smoother.scrollTo(el, true, "top 80px"); }
    else { el.scrollIntoView({ behavior: reduceMotion ? "auto" : "smooth", block: "start" }); }
  };

  document.addEventListener("click", function (e) {
    var a = e.target.closest('a[href^="#"]');
    if (!a) { return; }
    var id = a.getAttribute("href");
    if (id.length < 2) { return; }
    var el = document.querySelector(id);
    if (!el) { return; }
    e.preventDefault();
    closeMenu();
    PC.scrollTo(el);
    history.replaceState(null, "", id);
  });

  /* -- En-tête ------------------------------------------------------------ */
  var header = document.querySelector(".site-header");
  var lastY = 0;
  var onScroll = function () {
    var y = window.scrollY || window.pageYOffset;
    if (!header) { return; }
    header.classList.toggle("is-scrolled", y > 24);
    var hide = y > 480 && y > lastY + 4 && !document.body.classList.contains("menu-open");
    if (y < lastY - 4 || y < 480) { header.classList.remove("is-hidden"); }
    else if (hide) { header.classList.add("is-hidden"); }
    lastY = y;
  };
  window.addEventListener("scroll", onScroll, { passive: true });
  onScroll();

  /* -- Menu mobile -------------------------------------------------------- */
  var toggle = document.querySelector(".menu-toggle");
  var menu = document.querySelector(".mobile-menu");
  function closeMenu() {
    if (!document.body.classList.contains("menu-open")) { return; }
    document.body.classList.remove("menu-open");
    if (toggle) { toggle.setAttribute("aria-expanded", "false"); toggle.setAttribute("aria-label", "Ouvrir le menu"); }
    if (menu) { menu.setAttribute("aria-hidden", "true"); }
    if (smoother) { smoother.paused(false); }
  }
  if (toggle && menu) {
    toggle.addEventListener("click", function () {
      var open = !document.body.classList.contains("menu-open");
      if (!open) { closeMenu(); return; }
      document.body.classList.add("menu-open");
      toggle.setAttribute("aria-expanded", "true");
      toggle.setAttribute("aria-label", "Fermer le menu");
      menu.setAttribute("aria-hidden", "false");
      header.classList.remove("is-hidden");
      if (smoother) { smoother.paused(true); }
      if (hasGsap && !reduceMotion) {
        gsap.fromTo(menu.querySelectorAll("nav a"), { yPercent: 110, opacity: 0 }, { yPercent: 0, opacity: 1, duration: 0.7, ease: "expo.out", stagger: 0.06, delay: 0.2 });
      }
    });
    menu.addEventListener("click", function (e) { if (e.target.closest("a")) { closeMenu(); } });
    document.addEventListener("keydown", function (e) { if (e.key === "Escape") { closeMenu(); } });
  }

  /* -- Année ------------------------------------------------------------- */
  document.querySelectorAll("[data-year]").forEach(function (el) { el.textContent = new Date().getFullYear(); });

  /* -- Formulaires → WhatsApp --------------------------------------------- */
  PC.fillVehicleSelect = function (select, selectedSlug) {
    if (!select) { return; }
    var html = '<option value="">Aucun en particulier</option>';
    cars.slice().sort(function (a, b) { return a.make.localeCompare(b.make) || b.year - a.year; }).forEach(function (c) {
      html += '<option value="' + esc(c.slug) + '"' + (c.slug === selectedSlug ? " selected" : "") + ">" + esc(carName(c) + " " + c.year + " — " + fmtPrice(c.price)) + "</option>";
    });
    select.innerHTML = html;
  };

  document.querySelectorAll("form[data-wa-form]").forEach(function (form) {
    PC.fillVehicleSelect(form.querySelector("select[name=vehicule]"), form.getAttribute("data-selected"));
    var status = form.querySelector(".form__status");
    var validate = function (field) {
      var input = field.querySelector("input, select, textarea");
      if (!input) { return true; }
      var ok = input.checkValidity();
      if (input.name === "telephone" && input.value.trim()) {
        ok = ok && input.value.replace(/\D/g, "").length >= 10;
      }
      field.classList.toggle("is-invalid", !ok);
      input.setAttribute("aria-invalid", ok ? "false" : "true");
      return ok;
    };
    form.querySelectorAll(".field").forEach(function (field) {
      var input = field.querySelector("input, select, textarea");
      if (!input) { return; }
      input.addEventListener("blur", function () { if (input.value) { validate(field); } });
      input.addEventListener("input", function () { if (field.classList.contains("is-invalid")) { validate(field); } });
    });
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      var fields = Array.prototype.slice.call(form.querySelectorAll(".field"));
      var bad = fields.filter(function (f) { return !validate(f); });
      if (bad.length) {
        var first = bad[0].querySelector("input, select, textarea");
        if (first) { first.focus(); }
        return;
      }
      var d = new FormData(form);
      var car = cars.filter(function (c) { return c.slug === d.get("vehicule"); })[0];
      var lines = [
        "Bonjour PC Auto,",
        "",
        "Nom : " + d.get("nom"),
        "Téléphone : " + d.get("telephone"),
        "Courriel : " + d.get("courriel")
      ];
      if (car) { lines.push("Véhicule : " + carName(car) + " " + car.year + " (stock " + car.stock + ", " + fmtPrice(car.price) + ")"); }
      if (d.get("sujet")) { lines.push("Sujet : " + d.get("sujet")); }
      if (d.get("message")) { lines.push("", String(d.get("message"))); }
      window.open(waLink(lines.join("\n")), "_blank", "noopener");
      if (status) {
        status.textContent = "Merci " + String(d.get("nom")).split(" ")[0] + " ! WhatsApp s’est ouvert avec votre message : il ne reste qu’à l’envoyer. Vous pouvez aussi nous appeler au " + PHONE_DISPLAY + ".";
        status.classList.add("is-visible");
      }
    });
  });

  /* -- Curseur « Voir » ------------------------------------------------------ */
  if (hasGsap && finePointer && !reduceMotion) {
    var cursor = document.createElement("div");
    cursor.className = "view-cursor";
    cursor.setAttribute("aria-hidden", "true");
    cursor.textContent = "Voir";
    document.body.appendChild(cursor);
    var xTo = gsap.quickTo(cursor, "x", { duration: 0.45, ease: "power3" });
    var yTo = gsap.quickTo(cursor, "y", { duration: 0.45, ease: "power3" });
    var shown = false;
    window.addEventListener("pointermove", function (e) {
      xTo(e.clientX); yTo(e.clientY);
      var over = e.target.closest && e.target.closest("[data-cursor]");
      if (over && !shown) {
        cursor.textContent = over.getAttribute("data-cursor");
        gsap.to(cursor, { scale: 1, opacity: 1, duration: 0.35, ease: "back.out(1.6)" }); shown = true;
      } else if (!over && shown) {
        gsap.to(cursor, { scale: 0, opacity: 0, duration: 0.25, ease: "power2.in" }); shown = false;
      }
    }, { passive: true });
    window.addEventListener("scroll", function () {
      if (shown) { gsap.to(cursor, { scale: 0, opacity: 0, duration: 0.2 }); shown = false; }
    }, { passive: true });
  }

  /* -- Apparitions au défilement ---------------------------------------------- */
  PC.reveal = function (root) {
    root = root || document;
    var items = root.querySelectorAll("[data-reveal]:not(.is-revealed)");
    if (!hasGsap || reduceMotion || !window.ScrollTrigger) {
      items.forEach(function (el) { el.style.opacity = 1; el.style.transform = "none"; el.classList.add("is-revealed"); });
      return;
    }
    ScrollTrigger.batch(items, {
      start: "top 88%",
      once: true,
      onEnter: function (batch) {
        batch.forEach(function (el) { el.classList.add("is-revealed"); });
        gsap.to(batch, { opacity: 1, y: 0, duration: 0.9, ease: "expo.out", stagger: 0.08, overwrite: true });
      }
    });
  };

  PC.splitLines = function (root) {
    root = root || document;
    var els = root.querySelectorAll("[data-split]");
    if (!hasGsap || reduceMotion || !window.SplitText) { return; }
    els.forEach(function (el) {
      var split = SplitText.create(el, { type: "lines", mask: "lines", linesClass: "split-line" });
      gsap.from(split.lines, {
        yPercent: 105, duration: 1.1, ease: "expo.out", stagger: 0.08,
        scrollTrigger: { trigger: el, start: "top 88%", once: true }
      });
    });
  };

  PC.revealPlates = function (root) {
    root = root || document;
    var plates = root.querySelectorAll(".plate--reveal");
    if (!hasGsap || reduceMotion || !window.ScrollTrigger) {
      plates.forEach(function (p) { p.style.clipPath = "none"; });
      return;
    }
    plates.forEach(function (p) {
      if (p.hasAttribute("data-hero")) { return; }
      gsap.to(p, {
        clipPath: "inset(0% 0 0 0)", duration: 1.2, ease: "expo.inOut",
        scrollTrigger: { trigger: p, start: "top 90%", once: true }
      });
    });
  };

  /* -- Préchargeur (une fois par session) ------------------------------------- */
  PC.runPreloader = function (onDone) {
    var pre = document.querySelector(".preloader");
    var seen = false;
    try { seen = sessionStorage.getItem("pc-preloaded") === "1"; } catch (err) { seen = false; }
    if (!pre || !hasGsap || reduceMotion || seen) {
      if (pre) { pre.remove(); }
      onDone(true);
      return;
    }
    try { sessionStorage.setItem("pc-preloaded", "1"); } catch (err) { /* stockage indisponible */ }
    var count = pre.querySelector(".preloader__count");
    var bar = pre.querySelector(".preloader__bar");
    var obj = { v: 0 };
    if (smoother) { smoother.paused(true); }
    gsap.timeline({
      onComplete: function () {
        pre.remove();
        if (smoother) { smoother.paused(false); }
      }
    })
      .to(obj, { v: 100, duration: 1.3, ease: "power2.inOut", onUpdate: function () { count.textContent = Math.round(obj.v); } }, 0)
      .to(bar, { scaleX: 1, duration: 1.3, ease: "power2.inOut" }, 0)
      .add(function () { onDone(false); }, 1.35)
      .to(pre, { yPercent: -100, duration: 0.9, ease: "expo.inOut" }, 1.35);
  };

  /* Initialisation commune à la fin du chargement de la page */
  PC.initCommon = function () {
    PC.reveal();
    PC.splitLines();
    PC.revealPlates();
    if (location.hash && location.hash.length > 1) {
      setTimeout(function () { PC.scrollTo(location.hash); }, 300);
    }
    window.addEventListener("load", PC.refresh);
  };
})();
