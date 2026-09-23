/* PC Auto — page Inventaire : recherche, filtres et tri synchronisés avec l'adresse */
(function () {
  "use strict";
  var PC = window.PC;
  var cars = PC.cars;
  var motion = typeof window.gsap !== "undefined" && !PC.reduceMotion;

  var TYPES = [
    { key: "", label: "Tous", test: function () { return true; } },
    { key: "vus", label: "VUS", test: function (c) { return c.body === "vus"; } },
    { key: "auto", label: "Berlines et compactes", test: function (c) { return c.body === "auto" || c.body === "coupe"; } },
    { key: "camion", label: "Camionnettes", test: function (c) { return c.body === "camion"; } },
    { key: "awd", label: "4x4 et intégrale", test: function (c) { return /AWD|4x4/i.test(c.drivetrain); } }
  ];

  var form = document.querySelector("[data-inv-form]");
  var grid = document.querySelector("[data-inv-grid]");
  var typesEl = document.querySelector("[data-inv-types]");
  var countEl = document.querySelector("[data-inv-count]");
  var emptyEl = document.querySelector("[data-inv-empty]");
  var resetEl = document.querySelector("[data-inv-reset]");

  document.querySelectorAll("[data-count-total]").forEach(function (el) { el.textContent = cars.length; });

  /* État initial depuis l'adresse */
  var params = new URLSearchParams(location.search);
  var state = {
    type: params.get("type") || (params.get("motricite") === "awd" ? "awd" : ""),
    q: params.get("q") || "",
    succursale: params.get("succursale") || "",
    prix: params.get("prix") || "",
    tri: params.get("tri") || "recent"
  };
  if (!TYPES.some(function (t) { return t.key === state.type; })) { state.type = ""; }
  form.q.value = state.q;
  form.succursale.value = state.succursale;
  form.prix.value = state.prix;
  form.tri.value = state.tri;

  typesEl.innerHTML = TYPES.map(function (t) {
    var n = cars.filter(t.test).length;
    return '<button class="pill" type="button" data-type="' + t.key + '" aria-pressed="' + (t.key === state.type) + '">' + t.label + ' <span class="pill__count">(' + n + ")</span></button>";
  }).join("");

  var norm = function (s) { return String(s).toLowerCase().normalize("NFD").replace(/[̀-ͯ]/g, ""); };

  var syncUrl = function () {
    var p = new URLSearchParams();
    Object.keys(state).forEach(function (k) {
      if (state[k] && !(k === "tri" && state[k] === "recent")) { p.set(k, state[k]); }
    });
    var qs = p.toString();
    history.replaceState(null, "", location.pathname + (qs ? "?" + qs : ""));
  };

  var render = function (animate) {
    var type = TYPES.filter(function (t) { return t.key === state.type; })[0] || TYPES[0];
    var words = norm(state.q).split(/\s+/).filter(Boolean);
    var list = cars.filter(function (c) {
      if (!type.test(c)) { return false; }
      if (state.succursale && c.location !== state.succursale) { return false; }
      if (state.prix && c.price > +state.prix) { return false; }
      if (words.length) {
        var hay = norm([c.make, c.model, c.year, c.color, c.stock, c.transmission, c.drivetrain, PC.BODY[c.body]].join(" "));
        return words.every(function (w) { return hay.indexOf(w) > -1; });
      }
      return true;
    });
    var sorters = {
      recent: function (a, b) { return b.year - a.year || a.price - b.price; },
      "prix-asc": function (a, b) { return a.price - b.price; },
      "prix-desc": function (a, b) { return b.price - a.price; },
      km: function (a, b) { return a.km - b.km; }
    };
    list.sort(sorters[state.tri] || sorters.recent);

    grid.innerHTML = list.map(PC.renderCard).join("");
    countEl.textContent = list.length + (list.length > 1 ? " véhicules" : " véhicule");
    emptyEl.classList.toggle("is-visible", list.length === 0);
    var dirty = state.type || state.q || state.succursale || state.prix;
    resetEl.hidden = !dirty;
    typesEl.querySelectorAll("button").forEach(function (b) { b.setAttribute("aria-pressed", String(b.getAttribute("data-type") === state.type)); });
    if (animate && motion) {
      gsap.fromTo(grid.children, { opacity: 0, y: 20 }, { opacity: 1, y: 0, duration: 0.55, ease: "expo.out", stagger: 0.035 });
    }
    PC.refresh();
  };

  var update = function (animate) { syncUrl(); render(animate); };

  typesEl.addEventListener("click", function (e) {
    var b = e.target.closest("button");
    if (!b) { return; }
    state.type = b.getAttribute("data-type");
    update(true);
  });
  var timer;
  form.q.addEventListener("input", function () {
    clearTimeout(timer);
    timer = setTimeout(function () { state.q = form.q.value.trim(); update(true); }, 180);
  });
  ["succursale", "prix", "tri"].forEach(function (name) {
    form[name].addEventListener("change", function () { state[name] = form[name].value; update(true); });
  });
  resetEl.addEventListener("click", function () {
    state = { type: "", q: "", succursale: "", prix: "", tri: state.tri };
    form.q.value = ""; form.succursale.value = ""; form.prix.value = "";
    update(true);
  });

  render(false);
  PC.initCommon();
})();
