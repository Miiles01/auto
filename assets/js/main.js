/* Vaporax — interacciones y animaciones (GSAP 3.13) */
(() => {
  const root = document.documentElement;
  const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const hasGsap = typeof window.gsap !== 'undefined';
  const WHATSAPP = '14503780888';

  if (hasGsap) gsap.registerPlugin(ScrollTrigger, ScrollSmoother);

  /* ---------- ScrollSmoother (solo landing) ---------- */
  let smoother = null;
  if (hasGsap && !reduced) {
    smoother = ScrollSmoother.create({
      wrapper: '#smooth-wrapper',
      content: '#smooth-content',
      smooth: 1.1,
      effects: false,
      smoothTouch: false,
    });
  }
  const scrollY = () => (smoother ? smoother.scrollTop() : window.scrollY);

  /* ---------- Anclas internas ---------- */
  const header = document.querySelector('[data-header]');
  document.addEventListener('click', (e) => {
    const link = e.target.closest('a[href^="#"]');
    if (!link) return;
    const id = link.getAttribute('href');
    const target = id === '#top' ? document.body : document.querySelector(id);
    if (!target) return;
    e.preventDefault();
    closeMenu();
    if (smoother) smoother.scrollTo(id === '#top' ? 0 : target, true, 'top 88px');
    else if (id === '#top') window.scrollTo({ top: 0, behavior: reduced ? 'auto' : 'smooth' });
    else window.scrollTo({ top: target.getBoundingClientRect().top + window.scrollY - 88, behavior: reduced ? 'auto' : 'smooth' });
  });

  /* ---------- Header: baja con la barra de estado y se vuelve sólido ---------- */
  const hero = document.querySelector('.hero');
  const updateHeader = () => {
    const minY = window.innerWidth < 768 ? 12 : 16;
    const y = Math.max(minY, headerTopPx() - scrollY());
    header.style.setProperty('--header-y', y + 'px');
    const heroBottom = hero.offsetTop + hero.offsetHeight;
    header.classList.toggle('is-solid', scrollY() > heroBottom - 90);
  };
  // --header-top usa calc(); lo medimos una vez con un elemento de prueba
  let cachedHeaderTop = null;
  const headerTopPx = () => {
    if (cachedHeaderTop !== null) return cachedHeaderTop;
    const probe = document.createElement('div');
    probe.style.cssText = 'position:absolute;visibility:hidden;height:var(--header-top)';
    header.appendChild(probe);
    cachedHeaderTop = probe.offsetHeight;
    probe.remove();
    return cachedHeaderTop;
  };
  window.addEventListener('resize', () => { cachedHeaderTop = null; updateHeader(); });
  if (hasGsap) gsap.ticker.add(updateHeader); else window.addEventListener('scroll', updateHeader, { passive: true });
  updateHeader();

  /* ---------- Píldora de la navegación ---------- */
  const nav = document.querySelector('.site-header__nav');
  const pill = nav.querySelector('.site-header__pill');
  const navLinks = [...nav.querySelectorAll('a')];
  const movePill = (link) => {
    navLinks.forEach((a) => a.classList.toggle('is-active', a === link));
    pill.style.width = link.offsetWidth + 'px';
    pill.style.transform = `translateX(${link.offsetLeft}px)`;
  };
  movePill(navLinks[0]);
  if (document.fonts) document.fonts.ready.then(() => movePill(nav.querySelector('a.is-active') || navLinks[0]));
  window.addEventListener('resize', () => movePill(nav.querySelector('a.is-active') || navLinks[0]));
  if (hasGsap) {
    navLinks.forEach((a, i) => {
      const id = a.getAttribute('href');
      if (id === '#top') return;
      ScrollTrigger.create({
        trigger: id, start: 'top 45%', end: 'bottom 45%',
        onToggle: (self) => { if (self.isActive) movePill(a); else if (self.direction < 0 && i === 1) movePill(navLinks[0]); },
      });
    });
  }

  /* ---------- Menú móvil (MOSS) ---------- */
  const burger = document.querySelector('.burger');
  const panel = document.getElementById('menu-panel');
  document.querySelectorAll('.menu-panel__item').forEach((a) => {
    const word = a.dataset.label;
    const half = Array(4).fill(`<span>${word}</span>`).join('');
    a.insertAdjacentHTML('beforeend', `<span class="menu-panel__band" aria-hidden="true"><span class="menu-panel__track">${half}${half}</span></span>`);
  });
  function toggleMenu(force) {
    const open = root.classList.toggle('menu-open', force);
    burger.setAttribute('aria-expanded', open);
    burger.setAttribute('aria-label', open ? 'Fermer le menu' : 'Ouvrir le menu');
    panel.setAttribute('aria-hidden', !open);
    if (smoother) smoother.paused(open);
  }
  function closeMenu() { if (root.classList.contains('menu-open')) toggleMenu(false); }
  burger.addEventListener('click', () => toggleMenu());
  document.querySelector('[data-menu-close]').addEventListener('click', () => toggleMenu(false));
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') { closeMenu(); closeHotspots(); } });

  /* ---------- Rail del hero ---------- */
  const rail = document.querySelector('[data-rail]');
  const railList = rail.querySelector('[data-rail-list]');
  const railCards = [...railList.children];
  const railThumb = rail.querySelector('[data-rail-thumb]');
  const railIndex = rail.querySelector('[data-rail-index]');
  let railI = 0;
  const showRail = (i) => {
    railI = i;
    const visible = window.innerWidth >= 1024 ? 2 : 1;
    const shift = Math.min(i, railCards.length - visible);
    const step = railCards[0].offsetWidth + parseFloat(getComputedStyle(railList).columnGap || 12);
    railList.style.transform = `translateX(${-shift * step}px)`;
    railThumb.style.transform = `translateX(${i * 100}%)`;
    railIndex.textContent = String(i + 1).padStart(2, '0');
    railCards.forEach((c, k) => c.classList.toggle('is-dim', visible > 1 && k !== i));
  };
  let railTimer = null;
  const startRail = () => {
    if (reduced) return;
    clearInterval(railTimer);
    railTimer = setInterval(() => showRail((railI + 1) % railCards.length), 4500);
  };
  rail.addEventListener('mouseenter', () => clearInterval(railTimer));
  rail.addEventListener('mouseleave', startRail);
  railCards.forEach((c, k) => c.addEventListener('focus', () => showRail(k)));
  window.addEventListener('resize', () => showRail(railI));
  showRail(0);

  /* ---------- Acordeón del proceso ---------- */
  const steps = [...document.querySelectorAll('[data-steps] .step')];
  steps.forEach((step) => {
    step.querySelector('.step__head').addEventListener('click', () => {
      const open = !step.classList.contains('is-open');
      steps.forEach((s) => {
        const on = s === step && open;
        s.classList.toggle('is-open', on);
        s.querySelector('.step__head').setAttribute('aria-expanded', on);
      });
      if (hasGsap) setTimeout(() => ScrollTrigger.refresh(), 750);
    });
  });

  /* ---------- Hotspots ---------- */
  const hotspots = [...document.querySelectorAll('.hotspot')];
  function closeHotspots(except) { hotspots.forEach((h) => h !== except && h.classList.remove('is-open')); }
  hotspots.forEach((h) => h.addEventListener('click', (e) => {
    e.stopPropagation();
    closeHotspots(h);
    h.classList.toggle('is-open');
  }));
  document.addEventListener('click', () => closeHotspots());

  /* ---------- Formulario → WhatsApp ---------- */
  const form = document.querySelector('[data-request-form]');
  const note = form.querySelector('[data-form-note]');
  const noteDefault = note.textContent;
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const data = new FormData(form);
    const nom = (data.get('nom') || '').trim();
    const tel = (data.get('tel') || '').trim();
    form.nom.classList.toggle('is-invalid', !nom);
    form.tel.classList.toggle('is-invalid', tel.replace(/\D/g, '').length < 10);
    if (!nom || tel.replace(/\D/g, '').length < 10) {
      note.textContent = 'Indiquez votre nom et un numéro de téléphone valide';
      note.classList.add('is-error');
      return;
    }
    note.textContent = noteDefault;
    note.classList.remove('is-error');
    const lines = [
      'Bonjour Vaporax, j’aimerais réserver un rendez-vous.',
      `Nom : ${nom}`,
      `Téléphone : ${tel}`,
      data.get('vehicule') ? `Véhicule : ${data.get('vehicule').trim()}` : '',
      `Service : ${data.get('service')}`,
    ].filter(Boolean);
    window.open(`https://api.whatsapp.com/send?phone=${WHATSAPP}&text=${encodeURIComponent(lines.join('\n'))}`, '_blank', 'noopener');
  });
  form.querySelectorAll('input').forEach((i) => i.addEventListener('input', () => i.classList.remove('is-invalid')));

  /* ---------- Marquesina continua ---------- */
  document.querySelectorAll('[data-marquee]').forEach((track) => {
    const list = track.firstElementChild;
    const clone = list.cloneNode(true);
    clone.setAttribute('aria-hidden', 'true');
    track.appendChild(clone);
  });

  /* ---------- Sin GSAP o movimiento reducido: todo visible ---------- */
  if (!hasGsap || reduced) {
    root.classList.add('is-loaded');
    root.classList.remove('js');
    startRail();
    return;
  }

  /* ---------- Loader + entrada del hero ---------- */
  const heroIntro = () => {
    const tl = gsap.timeline({ onComplete: startRail });
    tl.fromTo('[data-hero-media] > img', { scale: 1.14 }, { scale: 1, duration: 2.2, ease: 'expo.out' }, 0)
      .to('[data-hero-line]', { y: 0, yPercent: 0, duration: 1.2, ease: 'expo.out', stagger: 0.12 }, 0.15)
      .from('.hero__chips li', { opacity: 0, y: 12, duration: 0.8, ease: 'power3.out', stagger: 0.06 }, 0.6)
      .from('.site-header > *', { opacity: 0, y: -12, duration: 0.8, ease: 'power3.out', stagger: 0.06 }, 0.3)
      .from('.hero-rail', { opacity: 0, y: 24, duration: 1, ease: 'power3.out' }, 0.7)
      .from('.hero__cta-sm', { opacity: 0, y: 16, duration: 0.8, ease: 'power3.out' }, 0.8)
      .to('[data-reveal-late]', { opacity: 1, duration: 0.8, stagger: 0.2 }, 1.2);
  };
  gsap.set('[data-hero-line]', { y: 0, yPercent: 110 });

  let seen = false;
  try { seen = sessionStorage.getItem('vx-loaded') === '1'; } catch (err) { /* stockage bloqué */ }
  const preloader = document.querySelector('.preloader');
  if (seen) {
    root.classList.add('is-loaded');
    heroIntro();
  } else {
    if (smoother) smoother.paused(true);
    const counter = preloader.querySelector('.preloader__count');
    const state = { v: 0 };
    gsap.timeline({
      onComplete: () => {
        root.classList.add('is-loaded');
        if (smoother) smoother.paused(false);
        try { sessionStorage.setItem('vx-loaded', '1'); } catch (err) { /* rien */ }
      },
    })
      .from('.preloader__center > *', { opacity: 0, y: 14, duration: 0.7, ease: 'power3.out', stagger: 0.08 })
      .from('.preloader__wordmark', { yPercent: 40, opacity: 0, duration: 1.2, ease: 'expo.out' }, 0)
      .to(state, { v: 100, duration: 1.5, ease: 'power2.inOut', onUpdate: () => { counter.textContent = String(Math.round(state.v)).padStart(3, '0'); } }, 0.2)
      .to('.preloader__bar', { scaleX: 1, duration: 1.5, ease: 'power2.inOut' }, 0.2)
      .to('.preloader__center', { opacity: 0, y: -20, duration: 0.5, ease: 'power2.in' }, '+=0.15')
      .to(preloader, { yPercent: -100, duration: 1, ease: 'expo.inOut' }, '-=0.2')
      .add(heroIntro, '-=0.55');
  }

  /* ---------- Parallax del hero y fotos ---------- */
  gsap.to('[data-hero-media]', {
    yPercent: 16, ease: 'none',
    scrollTrigger: { trigger: '.hero', start: 'top top', end: 'bottom top', scrub: true },
  });
  document.querySelectorAll('[data-parallax]').forEach((el) => {
    gsap.fromTo(el, { yPercent: -6 }, {
      yPercent: 6, ease: 'none',
      scrollTrigger: { trigger: el.parentElement, start: 'top bottom', end: 'bottom top', scrub: true },
    });
  });

  /* ---------- Títulos: líneas que suben ---------- */
  document.querySelectorAll('[data-split]').forEach((title) => {
    const lines = [...title.children];
    lines.forEach((line) => {
      const wrap = document.createElement('span');
      wrap.style.cssText = 'display:block;overflow:hidden;padding-bottom:.08em;margin-bottom:-.08em';
      line.replaceWith(wrap);
      wrap.appendChild(line);
    });
    gsap.from(lines, {
      yPercent: 110, duration: 1.1, ease: 'expo.out', stagger: 0.1,
      scrollTrigger: { trigger: title, start: 'top 85%', once: true },
    });
  });

  /* ---------- Declaración: palabras que se rellenan con el scroll ---------- */
  const statement = document.querySelector('[data-statement]');
  const words = [];
  statement.childNodes.forEach((node) => {
    const strong = node.nodeName === 'STRONG';
    const text = node.textContent;
    const frag = document.createDocumentFragment();
    text.split(/(\s+)/).forEach((part) => {
      if (!part) return;
      if (/^\s+$/.test(part)) { frag.appendChild(document.createTextNode(part)); return; }
      const w = document.createElement('span');
      w.className = 'word' + (strong ? ' is-strong' : '');
      w.textContent = part;
      words.push(w);
      frag.appendChild(w);
    });
    if (strong) { node.textContent = ''; node.appendChild(frag); } else { node.replaceWith(frag); }
  });
  const ink = getComputedStyle(root).getPropertyValue('--ink').trim();
  const muted = getComputedStyle(root).getPropertyValue('--muted').trim();
  const tlWords = gsap.timeline({
    scrollTrigger: { trigger: statement, start: 'top 80%', end: 'bottom 45%', scrub: 0.6 },
  });
  words.forEach((w, i) => {
    tlWords.to(w, { color: w.classList.contains('is-strong') ? ink : muted, duration: 1, ease: 'none' }, i * 0.35);
  });

  /* ---------- About fijado mientras Services sube encima (escritorio) ---------- */
  ScrollTrigger.matchMedia({
    '(min-width: 1024px)': () => {
      ScrollTrigger.create({
        trigger: '[data-about-pin]',
        start: 'bottom bottom',
        endTrigger: '.services',
        end: 'top top',
        pin: true,
        pinSpacing: false,
      });
    },
  });

  /* ---------- Odómetros ---------- */
  document.querySelectorAll('[data-odometer]').forEach((el) => {
    const digits = el.dataset.odometer.split('');
    el.textContent = '';
    el.setAttribute('aria-label', el.dataset.odometer);
    const cols = digits.map((d) => {
      const wrap = document.createElement('span');
      wrap.className = 'odo';
      wrap.setAttribute('aria-hidden', 'true');
      const col = document.createElement('span');
      col.className = 'odo__col';
      const n = +d;
      const seq = [];
      for (let k = 0; k <= 10 + n; k++) seq.push(`<span>${k % 10}</span>`);
      col.innerHTML = seq.join('');
      wrap.appendChild(col);
      el.appendChild(wrap);
      return { col, steps: 10 + n };
    });
    ScrollTrigger.create({
      trigger: el, start: 'top 90%', once: true,
      onEnter: () => cols.forEach(({ col, steps: s }, k) => {
        gsap.fromTo(col, { yPercent: 0 }, { yPercent: -(s / (s + 1)) * 100, duration: 1.8 + k * 0.25, ease: 'expo.out' });
      }),
    });
  });

  /* ---------- Aparición de tarjetas ---------- */
  ScrollTrigger.batch('[data-reveal]', {
    start: 'top 88%',
    once: true,
    onEnter: (batch) => gsap.to(batch, { opacity: 1, y: 0, duration: 1, ease: 'power3.out', stagger: 0.1 }),
  });

  /* ---------- Precio del duo ---------- */
  gsap.from('.duo__amount', {
    yPercent: 30, opacity: 0, duration: 1.2, ease: 'expo.out',
    scrollTrigger: { trigger: '.duo__price', start: 'top 85%', once: true },
  });

  window.addEventListener('load', () => ScrollTrigger.refresh());
})();
