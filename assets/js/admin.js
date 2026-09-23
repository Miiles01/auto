/* PC Auto — panneau d'administration (démo).
   Routes : #/tableau, #/inventaire, #/vehicule/nouveau, #/vehicule/:id, #/demandes, #/reglages */
(function () {
  "use strict";
  const S = window.PCStore;

  /* -- Icônes (trait fin) ----------------------------------------------------- */
  const svg = (d) => `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${d}</svg>`;
  const I = {
    grid: svg('<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>'),
    car: svg('<path d="M5 17h14M3 13l2-5.5A2 2 0 0 1 6.9 6h10.2a2 2 0 0 1 1.9 1.5L21 13v4a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1v-1H6v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-4zM3 13h18"/><circle cx="7" cy="13.5" r=".5"/><circle cx="17" cy="13.5" r=".5"/>'),
    plus: svg('<path d="M12 5v14M5 12h14"/>'),
    inbox: svg('<path d="M3 13h5l1.5 2.5h5L16 13h5"/><path d="M5.5 5h13L21 13v5a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-5z"/>'),
    settings: svg('<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/>'),
    external: svg('<path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>'),
    logout: svg('<path d="M15 4h4a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-4M10 17l5-5-5-5M15 12H3"/>'),
    info: svg('<circle cx="12" cy="12" r="9"/><path d="M12 8h.01M11 12h1v4h1"/>'),
    menu: svg('<path d="M4 7h16M4 12h16M4 17h16"/>'),
    close: svg('<path d="M18 6 6 18M6 6l12 12"/>'),
    search: svg('<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>'),
    edit: svg('<path d="M4 20h4L19 9a2.8 2.8 0 0 0-4-4L4 16v4z"/><path d="m13.5 6.5 4 4"/>'),
    eye: svg('<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>'),
    copy: svg('<rect x="8" y="8" width="12" height="12" rx="2"/><path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2"/>'),
    trash: svg('<path d="M4 7h16M10 11v6M14 11v6M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-12M9 7V4h6v3"/>'),
    star: svg('<path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2L12 17.3 6.4 20.2l1.1-6.2L3 9.6l6.2-.9z"/>'),
    upload: svg('<path d="M12 16V4M7 9l5-5 5 5M4 16v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3"/>'),
    sort: svg('<path d="m7 15 5 5 5-5M7 9l5-5 5 5"/>'),
    chevron: svg('<path d="m6 9 6 6 6-6"/>'),
    check: svg('<path d="M20 6 9 17l-5-5"/>'),
    phone: svg('<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/>'),
    message: svg('<path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1-4.6A8 8 0 1 1 21 12z"/>'),
    image: svg('<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="m21 16-5-5-9 9"/>'),
    alert: svg('<path d="M12 9v4M12 17h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/>'),
    download: svg('<path d="M12 4v12M7 11l5 5 5-5M4 20h16"/>'),
    refresh: svg('<path d="M20 11a8 8 0 1 0-2.3 5.7M20 4v7h-7"/>'),
    back: svg('<path d="M19 12H5M11 18l-6-6 6-6"/>'),
    tag: svg('<path d="M3 12V4a1 1 0 0 1 1-1h8l9 9-9 9z"/><circle cx="7.5" cy="7.5" r="1.5"/>'),
    dollar: svg('<path d="M12 2v20M17 6H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>'),
    gauge: svg('<path d="M12 14l4-4M3.3 17a9 9 0 1 1 17.4 0"/>'),
    cover: svg('<path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2L12 17.3 6.4 20.2l1.1-6.2L3 9.6l6.2-.9z"/>')
  };
  const paintIcons = (root) => root.querySelectorAll("[data-icon]").forEach((el) => { if (I[el.dataset.icon]) { el.outerHTML = I[el.dataset.icon]; } });

  /* -- Utilitaires ------------------------------------------------------------ */
  const nf = new Intl.NumberFormat("fr-CA");
  const money = (n) => nf.format(Math.round(n || 0)) + " $";
  const km = (n) => nf.format(n || 0) + " km";
  const esc = (s) => String(s == null ? "" : s).replace(/[&<>"']/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c]));
  const src = (p) => (!p ? "" : /^(data:|https?:|blob:)/.test(p) ? p : "../" + p);
  const BODY = { vus: "VUS", auto: "Berline et compacte", coupe: "Coupé sport", camion: "Camionnette" };
  const LEAD = { new: "Nouvelle", contacted: "Contactée", booked: "Essai planifié", closed: "Vendu", lost: "Perdue" };
  const isPublic = (c) => c.status === "online" || c.status === "reserved";
  const siteUrl = (c) => "../vehicule.html?v=" + encodeURIComponent(c.slug);
  const date = (iso) => new Date(iso).toLocaleDateString("fr-CA", { day: "numeric", month: "short", year: "numeric" });
  const ago = (iso) => {
    const s = (Date.now() - new Date(iso).getTime()) / 1000;
    if (s < 60) { return "à l'instant"; }
    if (s < 3600) { return "il y a " + Math.floor(s / 60) + " min"; }
    if (s < 86400) { return "il y a " + Math.floor(s / 3600) + " h"; }
    if (s < 86400 * 7) { return "il y a " + Math.floor(s / 86400) + " j"; }
    return date(iso);
  };
  const $ = (sel, root) => (root || document).querySelector(sel);
  const $$ = (sel, root) => Array.from((root || document).querySelectorAll(sel));

  const view = $("#view");
  const titleEl = $("#title");
  const crumbEl = $("#crumb");
  const actionsEl = $("#top-actions");

  /* -- Notifications ----------------------------------------------------------- */
  const toast = (msg, opts = {}) => {
    const t = document.createElement("div");
    t.className = "toast" + (opts.error ? " toast--error" : "");
    t.setAttribute("role", "status");
    t.innerHTML = (opts.error ? I.alert : I.check) + `<span>${esc(msg)}</span>` + (opts.href ? `<a href="${opts.href}" target="_blank" rel="noopener">${esc(opts.link || "Voir")}</a>` : "");
    $("#toasts").appendChild(t);
    setTimeout(() => { t.style.transition = "opacity .3s"; t.style.opacity = "0"; setTimeout(() => t.remove(), 300); }, opts.error ? 6000 : 4200);
  };

  const dialog = $("#confirm");
  const confirmBox = (title, text, ok = "Confirmer") => new Promise((resolve) => {
    $("#confirm-title").textContent = title;
    $("#confirm-text").textContent = text;
    $("#confirm-ok").textContent = ok;
    const done = (v) => { dialog.close(); $("#confirm-ok").onclick = null; $("#confirm-cancel").onclick = null; resolve(v); };
    $("#confirm-ok").onclick = () => done(true);
    $("#confirm-cancel").onclick = () => done(false);
    dialog.oncancel = () => done(false);
    dialog.showModal();
  });

  const saveFailed = () => toast("Espace de stockage du navigateur plein : retirez quelques photos ou réinitialisez la démo.", { error: true });

  /* -- Navigation --------------------------------------------------------------- */
  let dirty = false;
  let current = "";
  const setHead = (title, crumb, actions = "") => {
    titleEl.textContent = title;
    crumbEl.textContent = crumb;
    actionsEl.innerHTML = actions;
    paintIcons(actionsEl);
    document.title = title + " | Administration PC Auto";
  };
  const counts = () => {
    const leadsNew = S.leads().filter((l) => l.status === "new").length;
    const el = $("[data-count-leads]");
    el.textContent = leadsNew;
    el.hidden = !leadsNew;
    $("[data-count-cars]").textContent = S.all().filter(isPublic).length;
  };

  const routes = {
    tableau: renderDashboard,
    inventaire: renderInventory,
    vehicule: renderEditor,
    demandes: renderLeads,
    reglages: renderSettings
  };

  const route = async () => {
    const hash = location.hash.replace(/^#\/?/, "") || "tableau";
    if (dirty && hash !== current) {
      const leave = await confirmBox("Quitter sans enregistrer ?", "Les modifications de cette fiche seront perdues.", "Quitter");
      if (!leave) { history.replaceState(null, "", "#/" + current); return; }
      dirty = false;
    }
    current = hash;
    const [name, param] = hash.split("/");
    const fn = routes[name] || renderDashboard;
    $$(".side__link[data-route]").forEach((a) => {
      const r = a.dataset.route;
      const on = r === name || (r === "nouveau" && name === "vehicule" && param === "nouveau") || (r === "inventaire" && name === "vehicule" && param !== "nouveau");
      if (on) { a.setAttribute("aria-current", "page"); } else { a.removeAttribute("aria-current"); }
    });
    document.getElementById("app").classList.remove("nav-open");
    fn(param);
    paintIcons(view);
    counts();
    view.focus({ preventScroll: true });
    window.scrollTo(0, 0);
  };
  window.addEventListener("hashchange", route);
  window.addEventListener("beforeunload", (e) => { if (dirty) { e.preventDefault(); e.returnValue = ""; } });

  /* ==========================================================================
     Tableau de bord
     ========================================================================== */
  function renderDashboard() {
    setHead("Tableau de bord", "Administration", `<a class="btn btn--red" href="#/vehicule/nouveau"><i data-icon="plus"></i><span>Ajouter un véhicule</span></a>`);
    const cars = S.all();
    const pub = cars.filter(isPublic);
    const reserved = cars.filter((c) => c.status === "reserved").length;
    const drafts = cars.filter((c) => c.status === "draft").length;
    const sold = cars.filter((c) => c.status === "sold").length;
    const value = pub.reduce((s, c) => s + c.price, 0);
    const avg = pub.length ? value / pub.length : 0;
    const avgKm = pub.length ? pub.reduce((s, c) => s + c.km, 0) / pub.length : 0;
    const leads = S.leads();
    const newLeads = leads.filter((l) => l.status === "new").length;

    const group = (key, labels) => {
      const m = {};
      pub.forEach((c) => { const k = labels ? labels(c) : c[key]; m[k] = (m[k] || 0) + 1; });
      return Object.entries(m).sort((a, b) => b[1] - a[1]);
    };
    const bars = (rows, red) => {
      const max = Math.max(1, ...rows.map((r) => r[1]));
      return rows.map(([k, v]) => `<div class="bar"><span>${esc(k)}</span><span class="bar__track"><span class="bar__fill${red ? " bar__fill--red" : ""}" style="width:${(v / max) * 100}%"></span></span><b>${v}</b></div>`).join("");
    };
    const toFix = cars.filter((c) => c.status !== "sold" && (c.images.length < 6 || c.options.length < 5 || !c.vin))
      .slice(0, 5);
    const log = S.log().slice(0, 8);

    view.innerHTML = `
      <section class="kpis" aria-label="Indicateurs">
        <div class="card kpi"><span class="kpi__label"><i data-icon="car"></i>Véhicules en ligne</span><span class="kpi__value">${pub.length}</span><span class="kpi__sub">${reserved} réservé${reserved > 1 ? "s" : ""} · ${drafts} brouillon${drafts > 1 ? "s" : ""} · ${sold} vendu${sold > 1 ? "s" : ""}</span></div>
        <div class="card kpi"><span class="kpi__label"><i data-icon="dollar"></i>Valeur de l'inventaire</span><span class="kpi__value">${money(value)}</span><span class="kpi__sub">Prix affichés, en $ CA</span></div>
        <div class="card kpi"><span class="kpi__label"><i data-icon="tag"></i>Prix moyen</span><span class="kpi__value">${money(avg)}</span><span class="kpi__sub">Kilométrage moyen : ${km(Math.round(avgKm))}</span></div>
        <a class="card kpi kpi--accent" href="#/demandes"><span class="kpi__label"><i data-icon="inbox"></i>Nouvelles demandes</span><span class="kpi__value">${newLeads}</span><span class="kpi__sub">${leads.length} demande${leads.length > 1 ? "s" : ""} au total · Voir</span></a>
      </section>

      <div class="grid-3">
        <section class="card">
          <div class="card__head"><div><h2>Répartition de l'inventaire en ligne</h2><p>Par catégorie et par succursale</p></div><a class="btn btn--ghost btn--sm" href="#/inventaire">Gérer</a></div>
          <div class="card__body grid-2">
            <div class="bars">${bars(group(null, (c) => (c.body === "coupe" ? "Berline et compacte" : BODY[c.body])))}</div>
            <div class="bars">${bars(group("location"), true)}</div>
          </div>
        </section>
        <section class="card">
          <div class="card__head"><div><h2>Dernières demandes</h2><p>Formulaire et boutons du site</p></div></div>
          <div class="card__body">${leads.length ? `<ul class="mini-list">${leads.slice(0, 5).map((l) => `
            <li><span class="avatar" style="background:var(--fg)">${esc((l.nom || "V").slice(0, 1))}</span><span><b>${esc(l.nom)}</b><small>${esc(l.source)}${l.vehicule ? " · " + esc(l.vehicule) : ""}</small></span><span class="end"><span class="status status--${l.status}">${LEAD[l.status]}</span></span></li>`).join("")}</ul>`
            : `<div class="empty">${I.inbox}<p>Aucune demande pour l'instant. Elles apparaîtront ici dès qu'un client écrira depuis le site.</p></div>`}</div>
        </section>
      </div>

      <div class="grid-2">
        <section class="card">
          <div class="card__head"><div><h2>Activité récente</h2><p>Modifications faites dans l'administration</p></div></div>
          <div class="card__body">${log.length ? `<ul class="activity">${log.map((e) => `<li><i></i><span>${esc(e.text)}</span><time datetime="${e.at}">${ago(e.at)}</time></li>`).join("")}</ul>`
            : `<div class="empty">${I.refresh}<p>Rien pour l'instant. Modifiez un prix ou ajoutez un véhicule pour voir l'historique.</p></div>`}</div>
        </section>
        <section class="card">
          <div class="card__head"><div><h2>Fiches à compléter</h2><p>Moins de 6 photos, moins de 5 options ou NIV manquant</p></div></div>
          <div class="card__body">${toFix.length ? `<ul class="mini-list">${toFix.map((c) => `
            <li><img src="${src(c.thumbs[0])}" alt=""><span><b>${esc(c.make + " " + c.model + " " + c.year)}</b><small>${c.images.length} photo${c.images.length > 1 ? "s" : ""} · ${c.options.length} option${c.options.length > 1 ? "s" : ""}${c.vin ? "" : " · NIV manquant"}</small></span><a class="btn btn--ghost btn--sm end" href="#/vehicule/${c.id}">Compléter</a></li>`).join("")}</ul>`
            : `<div class="empty">${I.check}<p>Toutes les fiches sont complètes.</p></div>`}</div>
        </section>
      </div>`;
  }

  /* ==========================================================================
     Inventaire
     ========================================================================== */
  const inv = { q: "", status: "", body: "", loc: "", sort: "updatedAt", dir: -1, selected: new Set() };

  function renderInventory() {
    setHead("Inventaire", "Administration / Inventaire", `<a class="btn btn--red" href="#/vehicule/nouveau"><i data-icon="plus"></i><span>Ajouter un véhicule</span></a>`);
    view.innerHTML = `
      <section class="card">
        <div class="toolbar">
          <label class="search"><span class="sr-only">Rechercher</span>${I.search}<input class="input" type="search" id="inv-q" placeholder="Marque, modèle, stock, NIV…" value="${esc(inv.q)}"></label>
          <label><span class="sr-only">Statut</span><select class="select" id="inv-status">
            <option value="">Tous les statuts</option>${Object.entries(S.STATUS).map(([k, v]) => `<option value="${k}"${inv.status === k ? " selected" : ""}>${v}</option>`).join("")}</select></label>
          <label><span class="sr-only">Catégorie</span><select class="select" id="inv-body">
            <option value="">Toutes catégories</option>${Object.entries(BODY).map(([k, v]) => `<option value="${k}"${inv.body === k ? " selected" : ""}>${v}</option>`).join("")}</select></label>
          <label><span class="sr-only">Succursale</span><select class="select" id="inv-loc">
            <option value="">Toutes succursales</option><option${inv.loc === "Granby" ? " selected" : ""}>Granby</option><option${inv.loc === "Sainte-Eulalie" ? " selected" : ""}>Sainte-Eulalie</option></select></label>
        </div>
        <div class="bulk" id="bulk" role="region" aria-label="Actions groupées">
          <b id="bulk-count"></b>
          <label><span class="sr-only">Nouveau statut</span><select class="select" id="bulk-status"><option value="">Changer le statut…</option>${Object.entries(S.STATUS).map(([k, v]) => `<option value="${k}">${v}</option>`).join("")}</select></label>
          <label style="display:flex;gap:.375rem;align-items:center">Ajuster les prix <input class="input" id="bulk-pct" type="number" step="1" min="-50" max="50" value="-5" style="width:4.75rem;min-height:2rem;color:var(--fg)"> %</label>
          <button class="btn btn--ghost btn--sm" type="button" id="bulk-apply">Appliquer</button>
          <button class="btn btn--ghost btn--sm" type="button" id="bulk-delete" style="margin-left:auto">${I.trash}Supprimer</button>
        </div>
        <div class="table-wrap"><table class="table" id="inv-table">
          <thead><tr>
            <th class="col-check"><input type="checkbox" class="check" id="check-all" aria-label="Tout sélectionner"></th>
            <th><button type="button" data-sort="name">Véhicule ${I.sort}</button></th>
            <th>Catégorie</th>
            <th class="num"><button type="button" data-sort="km">Kilométrage ${I.sort}</button></th>
            <th>Succursale</th>
            <th class="num"><button type="button" data-sort="price">Prix ${I.sort}</button></th>
            <th>Statut</th>
            <th>Vedette</th>
            <th><button type="button" data-sort="updatedAt">Mis à jour ${I.sort}</button></th>
            <th class="num"><span class="sr-only">Actions</span></th>
          </tr></thead>
          <tbody id="inv-body-rows"></tbody>
        </table></div>
        <div class="table-foot" id="inv-foot"></div>
      </section>`;

    const rows = $("#inv-body-rows");
    const draw = () => {
      const words = inv.q.toLowerCase().normalize("NFD").replace(/[̀-ͯ]/g, "").split(/\s+/).filter(Boolean);
      let list = S.all().filter((c) => {
        if (inv.status && c.status !== inv.status) { return false; }
        if (inv.body && c.body !== inv.body) { return false; }
        if (inv.loc && c.location !== inv.loc) { return false; }
        const hay = [c.make, c.model, c.year, c.stock, c.vin, c.color].join(" ").toLowerCase().normalize("NFD").replace(/[̀-ͯ]/g, "");
        return words.every((w) => hay.includes(w));
      });
      const key = { name: (c) => (c.make + c.model).toLowerCase(), km: (c) => c.km, price: (c) => c.price, updatedAt: (c) => c.updatedAt }[inv.sort];
      list.sort((a, b) => (key(a) > key(b) ? 1 : key(a) < key(b) ? -1 : 0) * inv.dir);
      $$("#inv-table th button[data-sort]").forEach((b) => {
        if (b.dataset.sort === inv.sort) { b.setAttribute("aria-sort", inv.dir > 0 ? "ascending" : "descending"); } else { b.removeAttribute("aria-sort"); }
      });

      rows.innerHTML = list.length ? list.map((c) => `
        <tr data-id="${c.id}" class="${inv.selected.has(c.id) ? "is-selected" : ""}">
          <td><input type="checkbox" class="check" data-select aria-label="Sélectionner ${esc(c.make + " " + c.model)}"${inv.selected.has(c.id) ? " checked" : ""}></td>
          <td><a class="veh" href="#/vehicule/${c.id}"><img src="${src(c.thumbs[0])}" alt="" loading="lazy"><span><b>${esc(c.make + " " + c.model)} ${c.year}</b><small>${esc(c.stock)} · ${c.images.length} photo${c.images.length > 1 ? "s" : ""}</small></span></a></td>
          <td>${BODY[c.body] || ""}</td>
          <td class="num">${km(c.km)}</td>
          <td>${esc(c.location)}</td>
          <td class="num"><label class="sr-only" for="p-${c.id}">Prix</label><input class="price-edit" id="p-${c.id}" data-price type="text" inputmode="numeric" value="${nf.format(c.price)}">${c.compareAt > c.price ? `<span class="price-was">${money(c.compareAt)}</span>` : ""}</td>
          <td><label class="sr-only" for="s-${c.id}">Statut</label><select class="status status--${c.status} status-select" id="s-${c.id}" data-status>${Object.entries(S.STATUS).map(([k, v]) => `<option value="${k}"${c.status === k ? " selected" : ""}>${v}</option>`).join("")}</select></td>
          <td><button class="star" type="button" data-feature aria-pressed="${!!c.featured}" aria-label="${c.featured ? "Retirer de la vedette" : "Mettre en vedette"}">${I.star}</button></td>
          <td class="muted" style="white-space:nowrap">${ago(c.updatedAt)}</td>
          <td><div class="row-actions">
            <a class="icon-btn" href="#/vehicule/${c.id}" aria-label="Modifier">${I.edit}</a>
            ${isPublic(c) ? `<a class="icon-btn" href="${siteUrl(c)}" target="_blank" rel="noopener" aria-label="Voir sur le site">${I.eye}</a>` : `<span class="icon-btn" aria-hidden="true" style="opacity:.3">${I.eye}</span>`}
            <button class="icon-btn" type="button" data-duplicate aria-label="Dupliquer">${I.copy}</button>
            <button class="icon-btn icon-btn--danger" type="button" data-delete aria-label="Supprimer">${I.trash}</button>
          </div></td>
        </tr>`).join("")
        : `<tr><td colspan="10"><div class="empty">${I.search}<p>Aucun véhicule ne correspond à ces filtres.</p></div></td></tr>`;

      const value = list.filter(isPublic).reduce((s, c) => s + c.price, 0);
      $("#inv-foot").innerHTML = `<span>${list.length} véhicule${list.length > 1 ? "s" : ""} affiché${list.length > 1 ? "s" : ""}</span><span>Valeur en ligne : <b class="tabular" style="color:var(--fg)">${money(value)}</b></span>`;
      const n = inv.selected.size;
      $("#bulk").classList.toggle("is-visible", n > 0);
      $("#bulk-count").textContent = n + " sélectionné" + (n > 1 ? "s" : "");
      $("#check-all").checked = list.length > 0 && list.every((c) => inv.selected.has(c.id));
      rows.dataset.ids = list.map((c) => c.id).join(",");
    };

    const commitPrice = (input) => {
      const id = +input.closest("tr").dataset.id;
      const car = S.get(id);
      const val = parseInt(input.value.replace(/\D/g, ""), 10);
      if (!val || val < 100) { input.value = nf.format(car.price); toast("Prix invalide.", { error: true }); return; }
      if (val === car.price) { input.value = nf.format(val); return; }
      const before = car.price;
      car.price = val;
      if (!S.save(car)) { saveFailed(); return; }
      toast(`${car.make} ${car.model} : ${money(before)} → ${money(val)}`, isPublic(car) ? { href: siteUrl(S.get(id)), link: "Voir sur le site" } : {});
      draw();
    };

    $("#inv-q").addEventListener("input", (e) => { inv.q = e.target.value; draw(); });
    $("#inv-status").addEventListener("change", (e) => { inv.status = e.target.value; draw(); });
    $("#inv-body").addEventListener("change", (e) => { inv.body = e.target.value; draw(); });
    $("#inv-loc").addEventListener("change", (e) => { inv.loc = e.target.value; draw(); });
    $$("#inv-table th button[data-sort]").forEach((b) => b.addEventListener("click", () => {
      if (inv.sort === b.dataset.sort) { inv.dir *= -1; } else { inv.sort = b.dataset.sort; inv.dir = b.dataset.sort === "name" ? 1 : -1; }
      draw();
    }));
    $("#check-all").addEventListener("change", (e) => {
      const ids = (rows.dataset.ids || "").split(",").filter(Boolean).map(Number);
      ids.forEach((id) => (e.target.checked ? inv.selected.add(id) : inv.selected.delete(id)));
      draw();
    });

    rows.addEventListener("change", (e) => {
      const tr = e.target.closest("tr");
      if (!tr) { return; }
      const id = +tr.dataset.id;
      if (e.target.matches("[data-select]")) {
        if (e.target.checked) { inv.selected.add(id); } else { inv.selected.delete(id); }
        draw();
      }
      if (e.target.matches("[data-status]")) {
        const car = S.get(id);
        car.status = e.target.value;
        if (!S.save(car)) { saveFailed(); return; }
        toast(`${car.make} ${car.model} : ${S.STATUS[car.status]}`);
        draw(); counts();
      }
    });
    rows.addEventListener("keydown", (e) => {
      if (e.target.matches("[data-price]") && e.key === "Enter") { e.preventDefault(); e.target.blur(); }
      if (e.target.matches("[data-price]") && e.key === "Escape") { e.target.value = nf.format(S.get(+e.target.closest("tr").dataset.id).price); e.target.blur(); }
    });
    rows.addEventListener("focusout", (e) => { if (e.target.matches("[data-price]")) { commitPrice(e.target); } });
    rows.addEventListener("click", async (e) => {
      const tr = e.target.closest("tr");
      if (!tr || !tr.dataset.id) { return; }
      const id = +tr.dataset.id;
      const car = S.get(id);
      if (e.target.closest("[data-feature]")) {
        car.featured = !car.featured;
        if (!S.save(car)) { saveFailed(); return; }
        toast(car.featured ? "Mis en vedette sur l'accueil" : "Retiré de la vedette");
        draw();
      }
      if (e.target.closest("[data-duplicate]")) {
        const copy = Object.assign(car, { id: S.nextId(), stock: S.nextStock(), status: "draft", featured: false, compareAt: 0, priceHistory: [], vin: "" });
        if (!S.save(copy)) { saveFailed(); return; }
        toast("Copie créée en brouillon");
        location.hash = "#/vehicule/" + copy.id;
      }
      if (e.target.closest("[data-delete]")) {
        const ok = await confirmBox("Supprimer ce véhicule ?", `${car.make} ${car.model} ${car.year} (${car.stock}) sera retiré de l'inventaire et du site.`, "Supprimer");
        if (!ok) { return; }
        S.remove(id);
        inv.selected.delete(id);
        toast("Véhicule supprimé");
        draw(); counts();
      }
    });

    $("#bulk-apply").addEventListener("click", () => {
      const status = $("#bulk-status").value;
      const pct = parseFloat($("#bulk-pct").value);
      const ids = Array.from(inv.selected);
      let okAll = true;
      ids.forEach((id) => {
        const car = S.get(id);
        if (!car) { return; }
        if (status) { car.status = status; }
        if (pct && !isNaN(pct)) { car.price = Math.max(100, Math.round((car.price * (1 + pct / 100)) / 5) * 5); }
        okAll = S.save(car) && okAll;
      });
      if (!okAll) { saveFailed(); }
      toast(`${ids.length} véhicule${ids.length > 1 ? "s" : ""} mis à jour`);
      inv.selected.clear();
      draw(); counts();
    });
    $("#bulk-delete").addEventListener("click", async () => {
      const ids = Array.from(inv.selected);
      const ok = await confirmBox(`Supprimer ${ids.length} véhicule${ids.length > 1 ? "s" : ""} ?`, "Ils seront retirés de l'inventaire et du site.", "Supprimer");
      if (!ok) { return; }
      ids.forEach((id) => S.remove(id));
      inv.selected.clear();
      toast("Véhicules supprimés");
      draw(); counts();
    });

    draw();
  }

  /* ==========================================================================
     Éditeur de véhicule
     ========================================================================== */
  const COMMON_OPTIONS = () => {
    const freq = {};
    S.all().forEach((c) => c.options.forEach((o) => { freq[o] = (freq[o] || 0) + 1; }));
    return Object.keys(freq).sort((a, b) => freq[b] - freq[a] || a.localeCompare(b));
  };
  const MAKES = ["Acura", "Audi", "BMW", "Buick", "Chevrolet", "Chrysler", "Dodge", "Ford", "GMC", "Honda", "Hyundai", "Jeep", "Kia", "Lexus", "Mazda", "Mercedes-Benz", "Mitsubishi", "Nissan", "Ram", "Scion", "Subaru", "Toyota", "Volkswagen", "Volvo"];

  /* Réduit une photo à 1000 px de large et la convertit en WebP (ou JPEG) */
  const compress = (file) => new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onerror = reject;
    reader.onload = () => {
      const img = new Image();
      img.onerror = reject;
      img.onload = () => {
        const scale = Math.min(1, 1000 / img.width);
        const canvas = document.createElement("canvas");
        canvas.width = Math.round(img.width * scale);
        canvas.height = Math.round(img.height * scale);
        canvas.getContext("2d").drawImage(img, 0, 0, canvas.width, canvas.height);
        let out = canvas.toDataURL("image/webp", 0.68);
        if (out.indexOf("data:image/webp") !== 0) { out = canvas.toDataURL("image/jpeg", 0.75); }
        resolve(out);
      };
      img.src = reader.result;
    };
    reader.readAsDataURL(file);
  });

  function renderEditor(param) {
    const isNew = param === "nouveau" || !param;
    const existing = isNew ? null : S.get(param);
    if (!isNew && !existing) {
      setHead("Véhicule introuvable", "Administration / Inventaire");
      view.innerHTML = `<div class="card"><div class="empty">${I.alert}<p>Ce véhicule n'existe plus.</p><a class="btn" href="#/inventaire">Retour à l'inventaire</a></div></div>`;
      return;
    }
    const car = existing || {
      id: S.nextId(), stock: S.nextStock(), make: "", model: "", year: new Date().getFullYear() - 5, body: "vus",
      price: 0, compareAt: 0, km: 0, transmission: "Automatique", engine: "", drivetrain: "FWD", color: "",
      location: "Granby", vin: "", options: [], images: [], thumbs: [], status: "online", featured: false, priceHistory: []
    };
    const title = isNew ? "Ajouter un véhicule" : `${car.make} ${car.model} ${car.year}`;
    setHead(title, "Administration / Inventaire / " + (isNew ? "Nouveau" : car.stock),
      `<a class="btn btn--ghost" href="#/inventaire"><i data-icon="back"></i><span>Inventaire</span></a>` +
      (!isNew && isPublic(car) ? `<a class="btn btn--ghost" href="${siteUrl(car)}" target="_blank" rel="noopener"><i data-icon="eye"></i><span>Voir sur le site</span></a>` : ""));

    const allOptions = Array.from(new Set(COMMON_OPTIONS().concat(car.options)));
    const seg = (name, values, cur) => `<div class="seg" role="radiogroup">${values.map(([v, l]) => `<label><input type="radio" name="${name}" value="${esc(v)}"${String(cur) === String(v) ? " checked" : ""}><span>${esc(l)}</span></label>`).join("")}</div>`;

    view.innerHTML = `
      <form class="editor" id="editor" novalidate>
        <div class="editor__main">
          <section class="card">
            <div class="card__head"><div><h2>Informations</h2><p>Ce qui s'affiche comme titre sur le site</p></div></div>
            <div class="card__body form-grid form-grid--3">
              <div class="field"><label for="f-make">Marque *</label><input class="input" id="f-make" name="make" list="makes" value="${esc(car.make)}" required><datalist id="makes">${MAKES.map((m) => `<option value="${m}">`).join("")}</datalist></div>
              <div class="field"><label for="f-model">Modèle *</label><input class="input" id="f-model" name="model" value="${esc(car.model)}" required placeholder="Ex. : Civic"></div>
              <div class="field"><label for="f-year">Année *</label><input class="input" id="f-year" name="year" type="number" min="1990" max="${new Date().getFullYear() + 1}" value="${car.year}" required></div>
              <div class="field full"><span class="label">Catégorie</span>${seg("body", Object.entries(BODY), car.body)}</div>
            </div>
          </section>

          <section class="card">
            <div class="card__head"><div><h2>Prix et publication</h2><p>Une baisse de prix affiche « Prix réduit » et l'ancien prix barré</p></div></div>
            <div class="card__body form-grid">
              <div class="field"><label for="f-price">Prix de vente *</label><div class="input-affix"><input class="input tabular" id="f-price" name="price" type="text" inputmode="numeric" value="${car.price ? nf.format(car.price) : ""}" required placeholder="12 995"><span>$ CA</span></div></div>
              <div class="field"><label for="f-compare">Prix avant réduction</label><div class="input-affix"><input class="input tabular" id="f-compare" name="compareAt" type="text" inputmode="numeric" value="${car.compareAt ? nf.format(car.compareAt) : ""}" placeholder="Facultatif"><span>$ CA</span></div><span class="hint">Rempli automatiquement quand vous baissez le prix.</span></div>
              <div class="field full"><span class="label">Statut</span>${seg("status", Object.entries(S.STATUS), car.status)}<span class="hint">« Vendu » et « Brouillon » retirent le véhicule du site ; « Réservé » l'affiche avec une étiquette.</span></div>
              <div class="field"><span class="label">Succursale</span>${seg("location", [["Granby", "Granby"], ["Sainte-Eulalie", "Sainte-Eulalie"]], car.location)}</div>
              <div class="field"><label for="f-stock">Numéro de stock</label><input class="input" id="f-stock" name="stock" value="${esc(car.stock)}"></div>
              <label class="check full"><input type="checkbox" name="featured"${car.featured ? " checked" : ""}> Mettre en vedette sur la page d'accueil</label>
            </div>
          </section>

          <section class="card">
            <div class="card__head"><div><h2>Caractéristiques</h2></div></div>
            <div class="card__body form-grid form-grid--3">
              <div class="field"><label for="f-km">Kilométrage *</label><div class="input-affix"><input class="input tabular" id="f-km" name="km" type="text" inputmode="numeric" value="${car.km ? nf.format(car.km) : ""}" required><span>km</span></div></div>
              <div class="field"><label for="f-engine">Moteur</label><input class="input" id="f-engine" name="engine" value="${esc(car.engine)}" placeholder="Ex. : 2.0 L"></div>
              <div class="field"><label for="f-color">Couleur</label><input class="input" id="f-color" name="color" value="${esc(car.color)}" placeholder="Ex. : Gris"></div>
              <div class="field"><span class="label">Transmission</span>${seg("transmission", [["Automatique", "Automatique"], ["Manuelle", "Manuelle"]], car.transmission)}</div>
              <div class="field"><span class="label">Motricité</span>${seg("drivetrain", [["FWD", "FWD"], ["RWD", "RWD"], ["AWD", "AWD"], ["4x4", "4x4"]], car.drivetrain)}</div>
              <div class="field"><label for="f-vin">Numéro de série (NIV)</label><input class="input" id="f-vin" name="vin" value="${esc(car.vin)}" maxlength="17" style="text-transform:none;font-family:ui-monospace,Menlo,monospace" placeholder="17 caractères"><span class="hint" id="vin-hint"></span></div>
            </div>
          </section>

          <section class="card">
            <div class="card__head"><div><h2>Équipements et options</h2><p>Touchez pour ajouter ou retirer</p></div><span class="muted" id="opt-count" style="font-size:.8125rem"></span></div>
            <div class="card__body">
              <div class="chips" id="chips">${allOptions.map((o) => `<button type="button" class="chip-opt" aria-pressed="${car.options.includes(o)}" data-opt="${esc(o)}">${I.check}${esc(o)}</button>`).join("")}</div>
              <div class="chip-add"><label class="sr-only" for="new-opt">Nouvelle option</label><input class="input" id="new-opt" placeholder="Autre option (ex. : Attache-remorque)"><button class="btn btn--ghost" type="button" id="add-opt">${I.plus}Ajouter</button></div>
            </div>
          </section>

          <section class="card">
            <div class="card__head"><div><h2>Photos</h2><p>La première photo sert de couverture. Glissez pour réordonner.</p></div><span class="muted" id="photo-count" style="font-size:.8125rem"></span></div>
            <div class="card__body">
              <label class="dropzone" id="dropzone">
                ${I.upload}<b>Glissez vos photos ici ou cliquez pour choisir</b><small>JPG, PNG ou WebP · 12 photos max · réduites automatiquement</small>
                <input type="file" id="files" accept="image/*" multiple class="sr-only">
              </label>
              <div class="photos" id="photos"></div>
            </div>
          </section>
        </div>

        <aside class="editor__side">
          <section>
            <p class="muted" style="font-size:.8125rem;margin-bottom:.5rem">Aperçu sur le site</p>
            <div class="preview-card" id="preview"></div>
          </section>
          ${!isNew ? `<section class="card">
            <div class="card__head"><div><h2>Historique des prix</h2></div></div>
            <div class="card__body">${car.priceHistory && car.priceHistory.length ? `<ul class="history">${car.priceHistory.slice().reverse().map((h) => `<li><span>${date(h.at)}</span><span class="${h.to < h.from ? "down" : "up"} tabular">${money(h.from)} → ${money(h.to)}</span></li>`).join("")}</ul>` : `<p class="muted" style="font-size:.8125rem">Aucun changement de prix depuis la mise en ligne.</p>`}</div>
          </section>
          <section class="card"><div class="card__body" style="display:grid;gap:.75rem">
            <dl class="dl" style="grid-template-columns:5.5rem 1fr;font-size:.8125rem"><dt>Créé</dt><dd>${date(car.createdAt)}</dd><dt>Modifié</dt><dd>${ago(car.updatedAt)}</dd></dl>
            <button class="btn btn--danger" type="button" id="delete-car">${I.trash}Supprimer ce véhicule</button>
          </div></section>` : ""}
        </aside>

        <div class="editor-actions" style="grid-column:1/-1">
          <span class="spacer" id="dirty-flag"></span>
          <a class="btn btn--ghost" href="#/inventaire">Annuler</a>
          <button class="btn btn--red" type="submit">${I.check}${isNew ? "Publier le véhicule" : "Enregistrer"}</button>
        </div>
      </form>`;

    const form = $("#editor");
    let photos = car.images.slice();
    let options = car.options.slice();
    const markDirty = () => { dirty = true; $("#dirty-flag").innerHTML = `${I.info}Modifications non enregistrées`; };
    const num = (v) => parseInt(String(v).replace(/\D/g, ""), 10) || 0;
    const val = (name) => { const el = form.elements[name]; return el ? (el.type === "checkbox" ? el.checked : el.value) : ""; };

    const collect = () => ({
      ...car,
      make: val("make").trim(), model: val("model").trim(), year: num(val("year")), body: val("body"),
      price: num(val("price")), compareAt: num(val("compareAt")), status: val("status"), location: val("location"),
      stock: val("stock").trim() || car.stock, featured: val("featured"), km: num(val("km")),
      engine: val("engine").trim(), color: val("color").trim(), transmission: val("transmission"), drivetrain: val("drivetrain"),
      vin: val("vin").trim().toUpperCase(), options: options.slice(), images: photos.slice()
    });

    const drawPreview = () => {
      const c = collect();
      const reduced = c.compareAt > c.price && c.price;
      $("#preview").innerHTML = `
        <div class="preview-card__media">${c.images[0] ? `<img src="${src(c.images[0])}" alt="">` : `${I.image}`}
          <span class="preview-card__loc">${esc(c.location)}</span>
          ${c.status === "reserved" ? `<span class="preview-card__badge preview-card__badge--dark">Réservé</span>` : reduced ? `<span class="preview-card__badge">Prix réduit</span>` : ""}
        </div>
        <div class="preview-card__body">
          <p class="preview-card__title">${esc(c.make || "Marque")} ${esc(c.model || "Modèle")} <span>${c.year || ""}</span></p>
          <p class="preview-card__spec">${km(c.km)} · ${esc(c.transmission)} · ${esc(c.drivetrain)}</p>
          <p class="preview-card__foot"><span class="preview-card__price">${c.price ? money(c.price) : "— $"}</span>${reduced ? `<s class="preview-card__was">${money(c.compareAt)}</s>` : ""}<span class="status status--${c.status}" style="margin-left:auto">${S.STATUS[c.status]}</span></p>
        </div>`;
    };

    const drawPhotos = () => {
      $("#photos").innerHTML = photos.map((p, i) => `
        <figure class="photo" draggable="true" data-i="${i}">
          <img src="${src(p)}" alt="Photo ${i + 1}">
          ${i === 0 ? `<span class="photo__cover">Couverture</span>` : ""}
          <span class="photo__num">${i + 1}</span>
          <div class="photo__tools">
            ${i > 0 ? `<button type="button" data-cover aria-label="Utiliser comme couverture">${I.cover}</button>` : ""}
            <button type="button" data-remove aria-label="Retirer la photo">${I.trash}</button>
          </div>
        </figure>`).join("");
      $("#photo-count").textContent = photos.length + " / 12";
    };
    const drawOptCount = () => { $("#opt-count").textContent = options.length + " sélectionnée" + (options.length > 1 ? "s" : ""); };
    const vinHint = () => {
      const v = val("vin").trim();
      $("#vin-hint").textContent = v ? (v.length === 17 ? "Format valide" : v.length + " / 17 caractères") : "";
    };

    form.addEventListener("input", (e) => {
      markDirty();
      if (e.target.name === "vin") { vinHint(); }
      if (e.target.name === "price" && existing && num(e.target.value) < existing.price && num(e.target.value) > 0 && !num(val("compareAt"))) {
        form.elements.compareAt.value = nf.format(existing.price);
      }
      drawPreview();
    });
    form.addEventListener("change", () => { markDirty(); drawPreview(); });
    ["price", "compareAt", "km"].forEach((n) => form.elements[n].addEventListener("blur", (e) => { const v = num(e.target.value); e.target.value = v ? nf.format(v) : ""; }));

    $("#chips").addEventListener("click", (e) => {
      const b = e.target.closest("[data-opt]");
      if (!b) { return; }
      const o = b.dataset.opt;
      const on = b.getAttribute("aria-pressed") !== "true";
      b.setAttribute("aria-pressed", String(on));
      options = on ? options.concat([o]) : options.filter((x) => x !== o);
      markDirty(); drawOptCount();
    });
    const addOpt = () => {
      const input = $("#new-opt");
      const o = input.value.trim().replace(/^\w/, (c) => c.toUpperCase());
      if (!o) { return; }
      if (!options.includes(o)) {
        options.push(o);
        if (!$(`[data-opt="${CSS.escape(o)}"]`)) {
          $("#chips").insertAdjacentHTML("beforeend", `<button type="button" class="chip-opt" aria-pressed="true" data-opt="${esc(o)}">${I.check}${esc(o)}</button>`);
        } else { $(`[data-opt="${CSS.escape(o)}"]`).setAttribute("aria-pressed", "true"); }
      }
      input.value = "";
      markDirty(); drawOptCount();
    };
    $("#add-opt").addEventListener("click", addOpt);
    $("#new-opt").addEventListener("keydown", (e) => { if (e.key === "Enter") { e.preventDefault(); addOpt(); } });

    const addFiles = async (files) => {
      const list = Array.from(files).filter((f) => f.type.indexOf("image/") === 0);
      const room = 12 - photos.length;
      if (!list.length) { return; }
      if (room <= 0) { toast("Maximum 12 photos par véhicule.", { error: true }); return; }
      const dz = $("#dropzone");
      dz.style.opacity = ".6";
      try {
        for (const f of list.slice(0, room)) { photos.push(await compress(f)); }
      } catch (err) { toast("Impossible de lire une des images.", { error: true }); }
      dz.style.opacity = "";
      if (list.length > room) { toast("Seules les " + room + " premières photos ont été ajoutées (12 max).", { error: true }); }
      markDirty(); drawPhotos(); drawPreview();
    };
    $("#files").addEventListener("change", (e) => { addFiles(e.target.files); e.target.value = ""; });
    const dz = $("#dropzone");
    ["dragenter", "dragover"].forEach((t) => dz.addEventListener(t, (e) => { if (e.dataTransfer.types.includes("Files")) { e.preventDefault(); dz.classList.add("is-over"); } }));
    ["dragleave", "drop"].forEach((t) => dz.addEventListener(t, () => dz.classList.remove("is-over")));
    dz.addEventListener("drop", (e) => { if (e.dataTransfer.files.length) { e.preventDefault(); addFiles(e.dataTransfer.files); } });

    /* Réordonner les photos par glisser-déposer */
    let dragFrom = null;
    const grid = $("#photos");
    grid.addEventListener("dragstart", (e) => { const f = e.target.closest(".photo"); if (!f) { return; } dragFrom = +f.dataset.i; f.classList.add("is-dragging"); e.dataTransfer.effectAllowed = "move"; });
    grid.addEventListener("dragend", () => { dragFrom = null; $$(".photo", grid).forEach((p) => p.classList.remove("is-dragging", "is-target")); });
    grid.addEventListener("dragover", (e) => { const f = e.target.closest(".photo"); if (dragFrom === null || !f) { return; } e.preventDefault(); $$(".photo", grid).forEach((p) => p.classList.toggle("is-target", p === f)); });
    grid.addEventListener("drop", (e) => {
      const f = e.target.closest(".photo");
      if (dragFrom === null || !f) { return; }
      e.preventDefault();
      const to = +f.dataset.i;
      const [moved] = photos.splice(dragFrom, 1);
      photos.splice(to, 0, moved);
      dragFrom = null;
      markDirty(); drawPhotos(); drawPreview();
    });
    grid.addEventListener("click", (e) => {
      const f = e.target.closest(".photo");
      if (!f) { return; }
      const i = +f.dataset.i;
      if (e.target.closest("[data-remove]")) { photos.splice(i, 1); }
      else if (e.target.closest("[data-cover]")) { photos.unshift(photos.splice(i, 1)[0]); }
      else { return; }
      markDirty(); drawPhotos(); drawPreview();
    });

    const del = $("#delete-car");
    if (del) {
      del.addEventListener("click", async () => {
        const ok = await confirmBox("Supprimer ce véhicule ?", `${car.make} ${car.model} ${car.year} sera retiré de l'inventaire et du site.`, "Supprimer");
        if (!ok) { return; }
        S.remove(car.id);
        dirty = false;
        toast("Véhicule supprimé");
        location.hash = "#/inventaire";
      });
    }

    form.addEventListener("submit", (e) => {
      e.preventDefault();
      const c = collect();
      const errors = [];
      const flag = (name, bad) => { const el = form.elements[name]; if (el && el.classList) { el.classList.toggle("input--error", bad); } if (bad) { errors.push(name); } };
      flag("make", !c.make);
      flag("model", !c.model);
      flag("year", c.year < 1990 || c.year > new Date().getFullYear() + 1);
      flag("price", c.price < 100);
      flag("km", !val("km"));
      if (c.compareAt && c.compareAt <= c.price) { c.compareAt = 0; }
      const needsPhoto = c.status === "online" || c.status === "reserved";
      if (needsPhoto && !photos.length) { errors.push("photos"); $("#dropzone").style.borderColor = "var(--accent)"; } else { $("#dropzone").style.borderColor = ""; }
      if (errors.length) {
        toast(errors.includes("photos") && errors.length === 1 ? "Ajoutez au moins une photo pour publier (ou enregistrez en brouillon)." : "Vérifiez les champs en rouge.", { error: true });
        const first = errors[0] === "photos" ? $("#dropzone") : form.elements[errors[0]];
        if (first) { first.scrollIntoView({ behavior: "smooth", block: "center" }); if (first.focus) { first.focus({ preventScroll: true }); } }
        return;
      }
      if (!S.save(c)) { saveFailed(); return; }
      dirty = false;
      const saved = S.get(c.id);
      toast(isNew ? "Véhicule publié" : "Modifications enregistrées", isPublic(saved) ? { href: siteUrl(saved), link: "Voir sur le site" } : {});
      location.hash = "#/inventaire";
    });

    drawPhotos(); drawPreview(); drawOptCount(); vinHint();
  }

  /* ==========================================================================
     Demandes
     ========================================================================== */
  const leadsState = { q: "", status: "" };
  function renderLeads() {
    setHead("Demandes", "Administration / Demandes");
    view.innerHTML = `
      <section class="card">
        <div class="toolbar">
          <label class="search"><span class="sr-only">Rechercher</span>${I.search}<input class="input" type="search" id="lead-q" placeholder="Nom, téléphone, véhicule…" value="${esc(leadsState.q)}"></label>
          <label><span class="sr-only">Statut</span><select class="select" id="lead-status"><option value="">Tous les statuts</option>${Object.entries(LEAD).map(([k, v]) => `<option value="${k}"${leadsState.status === k ? " selected" : ""}>${v}</option>`).join("")}</select></label>
        </div>
        <div class="table-wrap"><table class="table">
          <thead><tr><th>Reçue</th><th>Client</th><th>Source</th><th>Véhicule</th><th>Statut</th><th class="num"><span class="sr-only">Actions</span></th></tr></thead>
          <tbody id="lead-rows"></tbody>
        </table></div>
      </section>
      <p class="muted" style="font-size:.8125rem">Les demandes arrivent du formulaire « Nous écrire » et des boutons « Je suis intéressé(e) », « Réserver un essai routier » et « Appeler » des fiches véhicules.</p>`;

    const draw = () => {
      const q = leadsState.q.toLowerCase();
      const list = S.leads().filter((l) => (!leadsState.status || l.status === leadsState.status) &&
        [l.nom, l.telephone, l.courriel, l.vehicule, l.source].join(" ").toLowerCase().includes(q));
      $("#lead-rows").innerHTML = list.length ? list.map((l) => `
        <tr data-id="${l.id}">
          <td class="muted" style="white-space:nowrap">${ago(l.at)}</td>
          <td class="lead-cell"><b>${esc(l.nom)}</b><small>${esc([l.telephone, l.courriel].filter(Boolean).join(" · ") || "Coordonnées non fournies")}</small></td>
          <td>${esc(l.source)}</td>
          <td>${l.vehicule ? esc(l.vehicule) : `<span class="muted">—</span>`}</td>
          <td><label class="sr-only" for="ls-${l.id}">Statut</label><select class="status status--${l.status} status-select" id="ls-${l.id}" data-lead-status>${Object.entries(LEAD).map(([k, v]) => `<option value="${k}"${l.status === k ? " selected" : ""}>${v}</option>`).join("")}</select></td>
          <td><div class="row-actions"><button class="icon-btn" type="button" data-open aria-label="Ouvrir la demande">${I.eye}</button><button class="icon-btn icon-btn--danger" type="button" data-del aria-label="Supprimer la demande">${I.trash}</button></div></td>
        </tr>`).join("")
        : `<tr><td colspan="6"><div class="empty">${I.inbox}<p>${S.leads().length ? "Aucune demande ne correspond." : "Aucune demande pour l'instant. Essayez le formulaire du site : la demande apparaîtra ici."}</p>${S.leads().length ? "" : `<a class="btn btn--ghost" href="../index.html#contact" target="_blank" rel="noopener">${I.external}Ouvrir le formulaire</a>`}</div></td></tr>`;
    };
    $("#lead-q").addEventListener("input", (e) => { leadsState.q = e.target.value; draw(); });
    $("#lead-status").addEventListener("change", (e) => { leadsState.status = e.target.value; draw(); });
    $("#lead-rows").addEventListener("change", (e) => {
      if (!e.target.matches("[data-lead-status]")) { return; }
      S.updateLead(e.target.closest("tr").dataset.id, { status: e.target.value });
      toast("Statut : " + LEAD[e.target.value]);
      draw(); counts();
    });
    $("#lead-rows").addEventListener("click", async (e) => {
      const tr = e.target.closest("tr[data-id]");
      if (!tr) { return; }
      const id = tr.dataset.id;
      if (e.target.closest("[data-open]")) { openLead(id, draw); }
      if (e.target.closest("[data-del]")) {
        const ok = await confirmBox("Supprimer cette demande ?", "Elle sera retirée de la liste.", "Supprimer");
        if (ok) { S.removeLead(id); draw(); counts(); }
      }
    });
    draw();
  }

  const drawer = $("#drawer");
  const closeDrawer = () => { drawer.classList.remove("is-open"); drawer.setAttribute("aria-hidden", "true"); $("#drawer-backdrop").classList.remove("is-open"); };
  $$("[data-close-drawer]").forEach((b) => b.addEventListener("click", closeDrawer));
  document.addEventListener("keydown", (e) => { if (e.key === "Escape" && drawer.classList.contains("is-open")) { closeDrawer(); } });

  function openLead(id, redraw) {
    const l = S.leads().filter((x) => x.id === id)[0];
    if (!l) { return; }
    const car = l.carId ? S.get(l.carId) : null;
    const digits = (l.telephone || "").replace(/\D/g, "");
    $("#drawer-title").textContent = l.nom;
    $("#drawer-body").innerHTML = `
      <dl class="dl">
        <dt>Reçue</dt><dd>${new Date(l.at).toLocaleString("fr-CA", { dateStyle: "long", timeStyle: "short" })}</dd>
        <dt>Source</dt><dd>${esc(l.source)}</dd>
        <dt>Téléphone</dt><dd>${esc(l.telephone || "—")}</dd>
        <dt>Courriel</dt><dd>${esc(l.courriel || "—")}</dd>
        <dt>Sujet</dt><dd>${esc(l.sujet || "—")}</dd>
        <dt>Véhicule</dt><dd>${car ? `<a href="#/vehicule/${car.id}" style="text-decoration:underline">${esc(l.vehicule)}</a>` : esc(l.vehicule || "—")}</dd>
        ${l.message ? `<dt>Message</dt><dd>${esc(l.message)}</dd>` : ""}
      </dl>
      <div class="field"><label for="d-status">Statut du suivi</label><select class="select" id="d-status">${Object.entries(LEAD).map(([k, v]) => `<option value="${k}"${l.status === k ? " selected" : ""}>${v}</option>`).join("")}</select></div>
      <div class="field"><label for="d-note">Note interne</label><textarea class="textarea" id="d-note" placeholder="Ex. : rappeler samedi pour l'essai routier">${esc(l.note || "")}</textarea></div>
      <div style="display:flex;flex-wrap:wrap;gap:.5rem">
        <button class="btn" type="button" id="d-save">${I.check}Enregistrer</button>
        ${digits ? `<a class="btn btn--ghost" href="tel:+1${digits.slice(-10)}">${I.phone}Appeler</a><a class="btn btn--ghost" href="https://api.whatsapp.com/send?phone=1${digits.slice(-10)}" target="_blank" rel="noopener">${I.message}WhatsApp</a>` : ""}
        ${l.courriel ? `<a class="btn btn--ghost" href="mailto:${esc(l.courriel)}">Courriel</a>` : ""}
      </div>`;
    $("#d-save").addEventListener("click", () => {
      S.updateLead(id, { status: $("#d-status").value, note: $("#d-note").value });
      toast("Demande mise à jour");
      closeDrawer(); redraw(); counts();
    });
    drawer.classList.add("is-open");
    drawer.setAttribute("aria-hidden", "false");
    $("#drawer-backdrop").classList.add("is-open");
    $("#d-status").focus();
  }

  /* ==========================================================================
     Réglages
     ========================================================================== */
  function renderSettings() {
    setHead("Réglages", "Administration / Réglages");
    const s = S.settings();
    const used = S.usage();
    const quota = 5 * 1024 * 1024;
    view.innerHTML = `
      <div class="grid-2">
        <section class="card">
          <div class="card__head"><div><h2>Boutique</h2><p>Ce qui s'affiche sur le site public</p></div></div>
          <form class="card__body" id="settings-form">
            <div class="settings-row"><div><b>Véhicules en préparation</b><p>Nombre affiché sur la carte « Bientôt en inventaire ». 0 la masque.</p></div><input class="input" type="number" min="0" max="50" name="prepCount" value="${s.prepCount}" aria-label="Véhicules en préparation"></div>
            <div class="settings-row"><div><b>Taux par défaut du calculateur</b><p>Taux annuel proposé dans « Estimez vos paiements ».</p></div><div class="input-affix"><input class="input" type="number" min="0" max="30" step="0.01" name="defaultRate" value="${s.defaultRate}" aria-label="Taux par défaut"><span>%</span></div></div>
            <div style="display:flex;justify-content:flex-end;padding-top:1rem"><button class="btn btn--red" type="submit">${I.check}Enregistrer</button></div>
          </form>
        </section>
        <section class="card">
          <div class="card__head"><div><h2>Accès de démonstration</h2><p>Remplacer par une vraie authentification en production</p></div></div>
          <div class="card__body" style="display:grid;gap:1rem">
            <dl class="creds"><dt>Identifiant</dt><dd>admin@pcauto.ca</dd><dt>Mot de passe</dt><dd>demo-pcauto</dd><dt>Adresse</dt><dd>/admin</dd></dl>
            <div class="demo-note">${I.info}<span>Dans cette démo, tout est enregistré dans le navigateur. Pour une gestion partagée entre plusieurs personnes, il faudra brancher une base de données (PHP + SQLite sur Hostinger).</span></div>
          </div>
        </section>
      </div>
      <section class="card">
        <div class="card__head"><div><h2>Données de la démo</h2><p>Stockage du navigateur utilisé par les photos et l'inventaire</p></div></div>
        <div class="card__body">
          <div class="settings-row"><div><b>Espace utilisé</b><p>${(used / 1024 / 1024).toFixed(2).replace(".", ",")} Mo sur environ 5 Mo</p><div class="meter"><span style="width:${Math.min(100, (used / quota) * 100)}%"></span></div></div></div>
          <div class="settings-row"><div><b>Exporter l'inventaire</b><p>Fichier JSON avec tous les véhicules, prêt à remplacer le contenu de data.js.</p></div><button class="btn btn--ghost" type="button" id="export">${I.download}Exporter</button></div>
          <div class="settings-row"><div><b>Réinitialiser la démo</b><p>Rétablit l'inventaire d'origine et efface les demandes et l'historique.</p></div><button class="btn btn--danger" type="button" id="reset">${I.refresh}Réinitialiser</button></div>
        </div>
      </section>`;

    $("#settings-form").addEventListener("submit", (e) => {
      e.preventDefault();
      const f = e.target;
      const prep = Math.max(0, Math.min(50, parseInt(f.prepCount.value, 10) || 0));
      const rate = Math.max(0, Math.min(30, parseFloat(f.defaultRate.value) || 0));
      if (!S.saveSettings({ prepCount: prep, defaultRate: rate })) { saveFailed(); return; }
      toast("Réglages enregistrés", { href: "../index.html", link: "Voir le site" });
    });
    $("#export").addEventListener("click", () => {
      const cars = S.all().map((c) => { const o = Object.assign({}, c); return o; });
      const blob = new Blob([JSON.stringify(cars, null, 1)], { type: "application/json" });
      const a = document.createElement("a");
      a.href = URL.createObjectURL(blob);
      a.download = "pc-auto-inventaire-" + new Date().toISOString().slice(0, 10) + ".json";
      document.body.appendChild(a); a.click(); a.remove();
      setTimeout(() => URL.revokeObjectURL(a.href), 1000);
      toast("Inventaire exporté");
    });
    $("#reset").addEventListener("click", async () => {
      const ok = await confirmBox("Réinitialiser la démo ?", "L'inventaire d'origine sera rétabli ; les véhicules ajoutés, les demandes et l'historique seront effacés.", "Réinitialiser");
      if (!ok) { return; }
      S.reset();
      location.hash = "#/tableau";
      location.reload();
    });
  }

  /* -- Coquille : navigation mobile, déconnexion ----------------------------------- */
  paintIcons(document);
  const app = $("#app");
  $("#menu-btn").addEventListener("click", () => {
    const open = !app.classList.contains("nav-open");
    app.classList.toggle("nav-open", open);
    $("#menu-btn").setAttribute("aria-expanded", String(open));
  });
  $("#side-backdrop").addEventListener("click", () => app.classList.remove("nav-open"));
  $("#logout").addEventListener("click", async () => {
    if (dirty && !(await confirmBox("Se déconnecter ?", "Les modifications non enregistrées seront perdues.", "Se déconnecter"))) { return; }
    dirty = false;
    try { sessionStorage.removeItem("pc-admin"); } catch (err) { /* stockage indisponible */ }
    location.href = "index.html";
  });

  route();
})();
