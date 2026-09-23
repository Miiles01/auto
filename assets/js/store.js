/* PC Auto — magasin de données de la démo d'administration.
   L'inventaire de base vient de data.js ; les modifications faites dans /admin
   sont enregistrées dans le navigateur (localStorage) et appliquées au site
   public. Chargé après data.js et avant main.js sur toutes les pages. */
(function () {
  "use strict";

  var KEY = "pc-admin-v1";
  var base = (window.PC_CARS || []).map(function (c) {
    var copy = JSON.parse(JSON.stringify(c));
    copy.status = "online";
    copy.featured = false;
    copy.compareAt = 0;
    copy.priceHistory = [];
    copy.createdAt = "2026-09-01T12:00:00.000Z";
    copy.updatedAt = copy.createdAt;
    return copy;
  });

  var defaults = function () {
    return {
      cars: base.map(function (c) { return JSON.parse(JSON.stringify(c)); }),
      leads: [],
      log: [],
      settings: { prepCount: 5, defaultRate: 9.99 }
    };
  };

  var state;
  try { state = JSON.parse(localStorage.getItem(KEY) || "null"); } catch (err) { state = null; }
  if (!state || !Array.isArray(state.cars)) { state = defaults(); }
  state.leads = state.leads || [];
  state.log = state.log || [];
  state.settings = Object.assign(defaults().settings, state.settings || {});

  var persist = function () {
    try {
      localStorage.setItem(KEY, JSON.stringify(state));
      return true;
    } catch (err) {
      return false; /* quota dépassé : les photos sont trop lourdes */
    }
  };
  var now = function () { return new Date().toISOString(); };
  var uid = function () { return Date.now().toString(36) + Math.random().toString(36).slice(2, 7); };
  var clone = function (o) { return JSON.parse(JSON.stringify(o)); };
  var slugify = function (s) {
    return String(s).toLowerCase().normalize("NFD").replace(/[̀-ͯ]/g, "")
      .replace(/[^a-z0-9]+/g, "-").replace(/^-+|-+$/g, "");
  };
  var addLog = function (text, carId) {
    state.log.unshift({ id: uid(), at: now(), text: text, carId: carId || null });
    state.log = state.log.slice(0, 80);
  };

  var Store = {
    KEY: KEY,
    all: function () { return clone(state.cars); },
    get: function (id) {
      var c = state.cars.filter(function (x) { return x.id === +id; })[0];
      return c ? clone(c) : null;
    },
    nextId: function () { return state.cars.reduce(function (m, c) { return Math.max(m, c.id); }, 0) + 1; },
    nextStock: function () {
      var max = state.cars.reduce(function (m, c) {
        var n = parseInt(String(c.stock).replace(/\D/g, ""), 10);
        return isNaN(n) ? m : Math.max(m, n);
      }, 600);
      return "PC" + (max + 1);
    },
    slugFor: function (c) { return slugify(c.make + "-" + c.model + "-" + c.year + "-" + c.id); },
    /* Crée ou met à jour un véhicule. Retourne false si le stockage est plein. */
    save: function (car) {
      var existing = state.cars.filter(function (x) { return x.id === car.id; })[0];
      car.updatedAt = now();
      car.slug = Store.slugFor(car);
      /* les photos d'origine ont une miniature légère dans autos/thumb/ ; les photos importées servent telles quelles */
      car.thumbs = car.images.map(function (p) { return /^assets\/img\/autos\/[^/]+$/.test(p) ? p.replace("autos/", "autos/thumb/") : p; });
      if (existing) {
        if (existing.price !== car.price) {
          car.priceHistory = (existing.priceHistory || []).concat([{ at: now(), from: existing.price, to: car.price }]);
          if (car.price < existing.price) { car.compareAt = Math.max(existing.compareAt || 0, existing.price); }
          else if (car.price >= (existing.compareAt || 0)) { car.compareAt = 0; }
          addLog("Prix modifié : " + car.make + " " + car.model + " " + car.year + " (" + existing.price + " → " + car.price + " $)", car.id);
        }
        if (existing.status !== car.status) {
          addLog("Statut « " + Store.STATUS[car.status] + " » : " + car.make + " " + car.model + " " + car.year, car.id);
        }
        if (existing.price === car.price && existing.status === car.status) {
          addLog("Fiche modifiée : " + car.make + " " + car.model + " " + car.year, car.id);
        }
        state.cars = state.cars.map(function (x) { return x.id === car.id ? car : x; });
      } else {
        car.createdAt = now();
        car.priceHistory = car.priceHistory || [];
        state.cars.push(car);
        addLog("Véhicule ajouté : " + car.make + " " + car.model + " " + car.year, car.id);
      }
      return persist();
    },
    remove: function (id) {
      var c = Store.get(id);
      state.cars = state.cars.filter(function (x) { return x.id !== +id; });
      if (c) { addLog("Véhicule supprimé : " + c.make + " " + c.model + " " + c.year); }
      return persist();
    },
    leads: function () { return clone(state.leads); },
    addLead: function (lead) {
      lead.id = uid();
      lead.at = now();
      lead.status = "new";
      state.leads.unshift(lead);
      state.leads = state.leads.slice(0, 200);
      return persist();
    },
    updateLead: function (id, patch) {
      state.leads = state.leads.map(function (l) { return l.id === id ? Object.assign(l, patch) : l; });
      return persist();
    },
    removeLead: function (id) {
      state.leads = state.leads.filter(function (l) { return l.id !== id; });
      return persist();
    },
    log: function () { return clone(state.log); },
    settings: function () { return clone(state.settings); },
    saveSettings: function (s) {
      state.settings = Object.assign(state.settings, s);
      addLog("Réglages mis à jour");
      return persist();
    },
    reset: function () {
      state = defaults();
      try { localStorage.removeItem(KEY); } catch (err) { /* stockage indisponible */ }
    },
    usage: function () {
      var bytes = 0;
      try { bytes = (localStorage.getItem(KEY) || "").length; } catch (err) { bytes = 0; }
      return bytes;
    },
    STATUS: { online: "En ligne", reserved: "Réservé", sold: "Vendu", draft: "Brouillon" }
  };

  window.PCStore = Store;

  /* Le site public ne montre que les véhicules en ligne ou réservés */
  window.PC_CARS = state.cars.filter(function (c) { return c.status === "online" || c.status === "reserved"; });
  window.PC_SETTINGS = clone(state.settings);
})();
