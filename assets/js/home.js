/* PC Auto — page d'accueil */
(function () {
  "use strict";
  var PC = window.PC;
  var cars = PC.cars;
  var hasGsap = typeof window.gsap !== "undefined";
  var motion = hasGsap && !PC.reduceMotion;

  var byYear = cars.slice().sort(function (a, b) { return (b.featured ? 1 : 0) - (a.featured ? 1 : 0) || b.year - a.year || b.id - a.id; });
  var isAwd = function (c) { return /AWD|4x4/i.test(c.drivetrain); };
  var CATS = [
    { key: "all", label: "Tous les véhicules", href: "inventaire.html", test: function () { return true; } },
    { key: "vus", label: "VUS", href: "inventaire.html?type=vus", test: function (c) { return c.body === "vus"; } },
    { key: "auto", label: "Berlines et compactes", href: "inventaire.html?type=auto", test: function (c) { return c.body === "auto" || c.body === "coupe"; } },
    { key: "camion", label: "Camionnettes", href: "inventaire.html?type=camion", test: function (c) { return c.body === "camion"; } },
    { key: "awd", label: "4x4 et intégrale", href: "inventaire.html?motricite=awd", test: isAwd },
    { key: "budget", label: "Moins de 8 000 $", href: "inventaire.html?prix=8000", test: function (c) { return c.price < 8000; } }
  ];
  CATS.forEach(function (cat) { cat.cars = byYear.filter(cat.test); });

  /* Compteurs */
  document.querySelectorAll("[data-count-total]").forEach(function (el) { el.textContent = cars.length; });

  /* Nouveaux arrivages : les deux dernières entrées de l'inventaire */
  var arrivals = document.querySelector("[data-new-arrivals]");
  if (arrivals) {
    arrivals.innerHTML = cars.slice().sort(function (a, b) { return b.id - a.id; }).slice(0, 2).map(function (c) {
      return '<a href="' + PC.carUrl(c) + '"><span class="plate plate--reveal" data-hero><img src="' + c.thumbs[0] + '" alt="' + PC.esc(PC.carName(c) + " " + c.year) + '" width="720" height="498"></span>' +
        '<span class="caption" data-hero-cap>' + PC.esc(PC.carName(c)) + " " + c.year + "<br>" + PC.priceHTML(c.price) + "</span></a>";
    }).join("");
  }

  /* -- Parcourir : liste + bande rouge + plaques ---------------------------- */
  var list = document.querySelector("[data-browse-list]");
  var band = document.querySelector("[data-browse-band]");
  var plateL = document.querySelector('[data-browse-plate="l"]');
  var plateR = document.querySelector('[data-browse-plate="r"]');
  if (list) {
    var lHtml = "", rHtml = "";
    CATS.forEach(function (cat, i) {
      var li = document.createElement("li");
      li.innerHTML = '<a href="' + cat.href + '" data-cat="' + i + '">' + cat.label + "<sup>" + cat.cars.length + "</sup></a>";
      list.appendChild(li);
      var a = cat.cars[0], b = cat.cars[1] || cat.cars[0];
      lHtml += '<img data-cat-img="' + i + '" src="' + a.thumbs[0] + '" alt="" loading="lazy">';
      rHtml += '<img data-cat-img="' + i + '" src="' + (b.thumbs[4] || b.thumbs[1]) + '" alt="" loading="lazy">';
    });
    plateL.innerHTML = lHtml;
    plateR.innerHTML = rHtml;
    var links = list.querySelectorAll("a");
    var setActive = function (i) {
      links.forEach(function (l, k) { l.classList.toggle("is-active", k === i); });
      var li = links[i].parentElement;
      band.style.transform = "translateY(" + li.offsetTop + "px)";
      band.style.height = li.offsetHeight + "px";
      document.querySelectorAll("[data-cat-img]").forEach(function (img) {
        img.classList.toggle("is-on", +img.getAttribute("data-cat-img") === i);
      });
    };
    var resting = 1;
    links.forEach(function (l, i) {
      l.addEventListener("mouseenter", function () { setActive(i); });
      l.addEventListener("focus", function () { setActive(i); });
    });
    list.addEventListener("mouseleave", function () { setActive(resting); });
    setActive(resting);
    window.addEventListener("resize", function () {
      var cur = 0;
      links.forEach(function (l, k) { if (l.classList.contains("is-active")) { cur = k; } });
      setActive(cur);
    });
  }

  /* -- Inventaire en vedette ------------------------------------------------ */
  var grid = document.querySelector("[data-featured-grid]");
  var pills = document.querySelector("[data-featured-filters]");
  var FEATURED = CATS.slice(0, 5);
  var renderFeatured = function (i, animate) {
    var cat = FEATURED[i];
    var list = cat.cars.slice(0, cat.key === "all" ? 7 : 8);
    var html = list.map(PC.renderCard).join("");
    if (cat.key === "all") { html += PC.renderSoonCard(); }
    grid.innerHTML = html;
    if (animate && motion) {
      gsap.fromTo(grid.children, { opacity: 0, y: 24 }, { opacity: 1, y: 0, duration: 0.6, ease: "expo.out", stagger: 0.05 });
    }
    PC.refresh();
  };
  if (grid && pills) {
    pills.innerHTML = FEATURED.map(function (cat, i) {
      var label = cat.key === "all" ? "Tous" : cat.label;
      return '<button class="pill" type="button" aria-pressed="' + (i === 0) + '" data-i="' + i + '">' + label + ' <span class="pill__count">(' + cat.cars.length + ")</span></button>";
    }).join("");
    pills.addEventListener("click", function (e) {
      var b = e.target.closest("button");
      if (!b) { return; }
      pills.querySelectorAll("button").forEach(function (x) { x.setAttribute("aria-pressed", String(x === b)); });
      renderFeatured(+b.getAttribute("data-i"), true);
    });
    renderFeatured(0, false);
  }

  /* -- Services : image liée à la ligne survolée ---------------------------- */
  var services = document.querySelectorAll("[data-service]");
  services.forEach(function (s) {
    var on = function () {
      var i = s.getAttribute("data-service");
      services.forEach(function (x) { x.classList.toggle("is-active", x === s); });
      document.querySelectorAll("[data-service-img]").forEach(function (img) {
        img.classList.toggle("is-on", img.getAttribute("data-service-img") === i);
      });
    };
    s.addEventListener("mouseenter", on);
    s.addEventListener("focus", on);
  });

  /* Liens qui préremplissent le sujet du formulaire */
  document.addEventListener("click", function (e) {
    var a = e.target.closest("[data-subject]");
    if (!a) { return; }
    var sel = document.getElementById("f-sujet");
    if (sel) { sel.value = a.getAttribute("data-subject"); }
  });

  /* -- Points d'inspection -------------------------------------------------- */
  var plate = document.querySelector("[data-inspect]");
  if (plate) {
    var spots = plate.querySelectorAll(".hotspot");
    var closeAll = function () {
      spots.forEach(function (h) { h.classList.remove("is-open"); h.querySelector("button").setAttribute("aria-expanded", "false"); });
      plate.classList.remove("is-focus");
    };
    spots.forEach(function (h) {
      var btn = h.querySelector("button");
      btn.addEventListener("click", function (e) {
        e.stopPropagation();
        var open = !h.classList.contains("is-open");
        closeAll();
        if (open) {
          h.classList.add("is-open");
          btn.setAttribute("aria-expanded", "true");
          plate.style.setProperty("--fx", h.offsetLeft + "px");
          plate.style.setProperty("--fy", h.offsetTop + "px");
          plate.classList.add("is-focus");
        }
      });
    });
    document.addEventListener("click", function (e) { if (!e.target.closest(".hotspot")) { closeAll(); } });
    document.addEventListener("keydown", function (e) { if (e.key === "Escape") { closeAll(); } });
  }

  /* -- Manifeste : les mots s'allument au défilement ----------------------- */
  var manifesto = document.querySelector("[data-manifesto]");
  if (manifesto && motion && window.ScrollTrigger) {
    var walker = document.createTreeWalker(manifesto, NodeFilter.SHOW_TEXT);
    var nodes = [];
    while (walker.nextNode()) { if (walker.currentNode.nodeValue.trim()) { nodes.push(walker.currentNode); } }
    nodes.forEach(function (n) {
      var frag = document.createDocumentFragment();
      n.nodeValue.split(/(\s+)/).forEach(function (part) {
        if (!part) { return; }
        if (/^\s+$/.test(part)) { frag.appendChild(document.createTextNode(part)); return; }
        var sp = document.createElement("span");
        sp.className = "word"; sp.textContent = part;
        frag.appendChild(sp);
      });
      n.parentNode.replaceChild(frag, n);
    });
    var words = manifesto.querySelectorAll(".word, .manifesto__img");
    gsap.set(manifesto.querySelectorAll(".manifesto__img"), { opacity: 0.28 });
    gsap.to(words, {
      opacity: 1, stagger: 0.1, ease: "none",
      scrollTrigger: { trigger: manifesto, start: "top 80%", end: "bottom 45%", scrub: true }
    });
    gsap.from(manifesto.querySelectorAll(".manifesto__img"), {
      width: 0, duration: 1, ease: "expo.out",
      scrollTrigger: { trigger: manifesto, start: "top 75%", once: true }
    });
  }

  /* -- Pied de page : lettres qui montent ------------------------------------ */
  if (motion && window.ScrollTrigger) {
    gsap.from(".footer__word span", {
      yPercent: 100, duration: 1.2, ease: "expo.out", stagger: 0.05,
      scrollTrigger: { trigger: ".footer__word", start: "top 95%", once: true }
    });
  }

  /* -- Intro du héros (après le préchargeur) ---------------------------------- */
  var heroIntro = function (instant) {
    var plates = document.querySelectorAll(".hero .plate--reveal");
    var caps = document.querySelectorAll("[data-hero-cap]");
    var lines = document.querySelectorAll("[data-hero-line]");
    if (!motion) {
      plates.forEach(function (p) { p.style.clipPath = "none"; });
      return;
    }
    var tl = gsap.timeline({ delay: instant ? 0.1 : 0 });
    tl.from(lines[0], { xPercent: -18, opacity: 0, duration: 1.3, ease: "expo.out" }, 0)
      .from(lines[1], { xPercent: 14, opacity: 0, duration: 1.3, ease: "expo.out" }, 0.12)
      .to(plates, { clipPath: "inset(0% 0 0 0)", duration: 1.2, ease: "expo.inOut", stagger: 0.08 }, 0.25)
      .from(caps, { y: 18, opacity: 0, duration: 0.9, ease: "expo.out", stagger: 0.06 }, 0.55);
  };

  PC.runPreloader(heroIntro);
  PC.initCommon();
})();
