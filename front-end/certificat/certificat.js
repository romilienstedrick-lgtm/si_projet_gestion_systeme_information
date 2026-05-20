let apprenants = [];
let apprenantsFiltres = [];
let selectedId = null;
let currentTab = 'apprenants';

// =============================================
// HISTORIQUE (localStorage)
// =============================================
const HIST_KEY = 'js_cert_historique';

function getHistorique() {
  try { return JSON.parse(localStorage.getItem(HIST_KEY)) || []; }
  catch(e) { return []; }
}

function saveHistorique(hist) {
  localStorage.setItem(HIST_KEY, JSON.stringify(hist));
}

function enregistrerImpression(a) {
  const hist = getHistorique();
  const entry = {
    id: a.id,
    nom: a.nom,
    prenom: a.prenom,
    formation: a.formation,
    session: a.nom_session || '',
    date: new Date().toISOString()
  };
  // On ajoute en tête
  hist.unshift(entry);
  // Conserver max 500 entrées
  saveHistorique(hist.slice(0, 500));
  updateHistBadge();
  renderList(); // refresh pour mettre le badge vert
}

function isPrinted(id) {
  const hist = getHistorique();
  return hist.some(h => h.id == id);
}

function getPrintCount(id) {
  return getHistorique().filter(h => h.id == id).length;
}

function updateHistBadge() {
  const hist = getHistorique();
  const ids = [...new Set(hist.map(h => h.id))];
  document.getElementById('histCount').textContent = ids.length;
}

function clearHistorique() {
  if (!confirm('Effacer tout l\'historique d\'impression ?')) return;
  localStorage.removeItem(HIST_KEY);
  updateHistBadge();
  renderHistorique('');
  renderList();
}

// =============================================
// SWITCH ONGLETS
// =============================================
function switchTab(tab) {
  currentTab = tab;
  document.getElementById('tabApprenants').classList.toggle('active', tab === 'apprenants');
  document.getElementById('tabHistorique').classList.toggle('active', tab === 'historique');
  document.getElementById('panelApprenants').style.display = tab === 'apprenants' ? '' : 'none';
  document.getElementById('panelHistorique').style.display = tab === 'historique' ? '' : 'none';

  if (tab === 'apprenants') {
    renderList();
  } else {
    renderHistorique('');
  }
}

// =============================================
// INITIALISATION
// =============================================
document.addEventListener('DOMContentLoaded', () => {
  updateHistBadge();
  loadApprenants();
});

// =============================================
// CHARGEMENT DEPUIS si_gestion
// =============================================
async function loadApprenants() {
  try {
    const res = await fetch('api_apprenants.php');
    apprenants = await res.json();

    if (apprenants.error) {
      document.getElementById('apprenantsList').innerHTML =
        '<div class="empty-list">❌ ' + apprenants.error + '</div>';
      return;
    }

    apprenantsFiltres = [...apprenants];

    // Remplir le filtre sessions
    const sessions = [...new Set(apprenants.map(a => a.nom_session).filter(Boolean))];
    const sel = document.getElementById('sessionFilter');
    sessions.forEach(s => {
      const opt = document.createElement('option');
      opt.value = s;
      opt.textContent = s;
      sel.appendChild(opt);
    });

    renderList();

    // Auto-sélectionner le premier
    if (apprenants.length > 0) {
      selectApprenant(apprenants[0].id);
    }
  } catch (e) {
    document.getElementById('apprenantsList').innerHTML =
      '<div class="empty-list">❌ Erreur de connexion à la base de données.</div>';
  }
}

// =============================================
// RENDU LISTE
// =============================================
function renderList() {
  if (currentTab !== 'apprenants') return;
  const container = document.getElementById('apprenantsList');
  document.getElementById('count').textContent = apprenantsFiltres.length;

  if (apprenantsFiltres.length === 0) {
    container.innerHTML = '<div class="empty-list">Aucun apprenant trouvé</div>';
    return;
  }

  container.innerHTML = apprenantsFiltres.map(a => {
    const initiales = (a.prenom[0] + a.nom[0]).toUpperCase();
    const active = a.id == selectedId ? 'active' : '';
    const count = getPrintCount(a.id);
    const printedBadge = count > 0
      ? `<span class="printed-badge">✓ ${count}×</span>`
      : '';
    return `
    <div class="apprenant-card ${active}" onclick="selectApprenant(${a.id})" id="card-${a.id}">
      <div class="apprenant-avatar">${initiales}</div>
      <div class="apprenant-info">
        <div class="apprenant-nom">${a.nom} ${a.prenom}</div>
        <div class="apprenant-session">${a.nom_session || ''}</div>
        <div class="apprenant-cours">${a.formation}</div>
      </div>
      ${printedBadge}
    </div>`;
  }).join('');
}

// =============================================
// RENDU HISTORIQUE
// =============================================
function renderHistorique(query) {
  const container = document.getElementById('apprenantsList');
  const hist = getHistorique();
  const q = (query || '').toLowerCase().trim();

  // Grouper par id pour afficher dernier + nb impressions
  const grouped = {};
  hist.forEach(h => {
    if (!grouped[h.id]) grouped[h.id] = { ...h, count: 0, dates: [] };
    grouped[h.id].count++;
    grouped[h.id].dates.push(h.date);
  });

  let items = Object.values(grouped);
  if (q) {
    items = items.filter(h =>
      (h.nom + ' ' + h.prenom + ' ' + h.formation + ' ' + h.session).toLowerCase().includes(q)
    );
  }

  if (items.length === 0) {
    container.innerHTML = '<div class="empty-list">' + (hist.length === 0 ? '📭 Aucun certificat imprimé pour l\'instant' : 'Aucun résultat') + '</div>';
    return;
  }

  container.innerHTML = items.map(h => {
    const lastDate = new Date(h.dates[0]);
    const dateStr = lastDate.toLocaleDateString('fr-FR', {day:'2-digit', month:'short', year:'numeric'});
    const timeStr = lastDate.toLocaleTimeString('fr-FR', {hour:'2-digit', minute:'2-digit'});
    return `
    <div class="hist-item" onclick="selectFromHistorique(${h.id})">
      <div class="hist-nom">${h.nom} ${h.prenom}</div>
      <div class="hist-meta">${h.session || ''}${h.session && h.formation ? ' · ' : ''}${h.formation}</div>
      <div class="hist-date">🖨 Dernier : ${dateStr} à ${timeStr}</div>
      ${h.count > 1 ? `<div class="hist-count-pill">${h.count}×</div>` : ''}
    </div>`;
  }).join('');
}

function selectFromHistorique(id) {
  // Basculer vers l'onglet apprenants et sélectionner
  switchTab('apprenants');
  setTimeout(() => selectApprenant(id), 50);
}

// =============================================
// FILTRE RECHERCHE + SESSION
// =============================================
function filterApprenants(query) {
  const q = query.toLowerCase().trim();
  const sessionChoisie = document.getElementById('sessionFilter').value;

  apprenantsFiltres = apprenants.filter(a => {
    const matchNom = !q || (a.nom + ' ' + a.prenom + ' ' + a.formation).toLowerCase().includes(q);
    const matchSession = !sessionChoisie || a.nom_session === sessionChoisie;
    return matchNom && matchSession;
  });

  renderList();
}

// =============================================
// SÉLECTION — AFFICHAGE CERTIFICAT
// =============================================
function selectApprenant(id) {
  selectedId = id;
  const a = apprenants.find(ap => ap.id == id);
  if (!a) return;

  document.querySelectorAll('.apprenant-card').forEach(c => c.classList.remove('active'));
  const card = document.getElementById('card-' + id);
  if (card) {
    card.classList.add('active');
    card.scrollIntoView({ block: 'nearest' });
  }

  document.getElementById('btnPrint').disabled = false;
  document.getElementById('certArea').innerHTML = buildCertHTML(a);
}

// =============================================
// CONSTRUCTION HTML DU CERTIFICAT
// =============================================
function buildCertHTML(a) {
  const dateStr = a.date_certificat
    ? new Date(a.date_certificat).toLocaleDateString('fr-FR', {day:'2-digit', month:'long', year:'numeric'})
    : new Date().toLocaleDateString('fr-FR', {day:'2-digit', month:'long', year:'numeric'});

  const fullName = a.prenom + ' ' + a.nom;

  return `
  <div class="cert-wrapper">
  <div class="certificate">

    <!-- Fond hexagones gris -->
    <svg class="hex-bg" viewBox="0 0 800 565" xmlns="http://www.w3.org/2000/svg">
      ${hexGrid()}
    </svg>

    <!-- Hexagones haut droite -->
    <svg class="hex-group-tr" viewBox="0 0 200 180" xmlns="http://www.w3.org/2000/svg">
      ${hexShape(130, 55, 42, '#29abe2')}
      ${hexShape(88,  28, 38, '#f0a500')}
      ${hexShape(155, 110, 35, '#1a3a5c')}
    </svg>

    <!-- Hexagones bas gauche -->
    <svg class="hex-group-bl" viewBox="0 0 180 160" xmlns="http://www.w3.org/2000/svg">
      ${hexShape(55, 110, 42, '#f0a500')}
      ${hexShape(100, 80, 35, '#29abe2')}
      ${hexShape(25,  60, 30, '#1a3a5c')}
    </svg>

    <!-- Hexagone bleu côté droit -->
    <div class="hex-mid-r">
      <svg width="55" height="55" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg">
        ${hexShape(30, 30, 26, '#29abe2')}
      </svg>
    </div>

    <!-- HAUT : Logo + Session -->
    <div class="cert-top">
      <div class="cert-logo">
        <div class="cert-logo-js">JS</div>
        <div class="cert-logo-sub">Informatique</div>
      </div>
      ${a.nom_session ? `<div class="cert-session-tag">📅 ${a.nom_session}</div>` : ''}
    </div>

    <!-- CORPS -->
    <div class="cert-body">
      <div class="cert-title-main">Certificat</div>
      <div class="cert-badge">Informatique</div>
      <div class="cert-subtitle">Agréé par l'état</div>
      <div class="cert-divider"></div>

      <div class="cert-label">Délivré à</div>
      <div class="cert-name">${fullName}</div>

      <p class="cert-text">
        Nous vous félicitons pour votre réussite au sein de notre académie JS Informatique.
        N'oubliez pas : concevoir le monde demain avec l'informatique.
      </p>

      <div class="cert-formation">${a.formation}</div>
    </div>

    <!-- PIED : Date & Signature -->
    <div class="cert-footer">
      <div class="cert-sign-block">
        <div class="cert-sign-value">${dateStr}</div>
        <div class="cert-sign-line"></div>
        <div class="cert-sign-label">Date</div>
      </div>
      <div class="cert-sign-block">
        <div class="cert-sign-value" style="opacity:0">—</div>
        <div class="cert-sign-line"></div>
        <div class="cert-sign-label">Signature</div>
      </div>
    </div>

  </div>
  </div>`;
}

// =============================================
// HELPERS SVG HEXAGONES
// =============================================
function hexShape(cx, cy, r, color) {
  const pts = [];
  for (let i = 0; i < 6; i++) {
    const a = (Math.PI / 3) * i - Math.PI / 6;
    pts.push(`${(cx + r * Math.cos(a)).toFixed(1)},${(cy + r * Math.sin(a)).toFixed(1)}`);
  }
  const r2 = r * 0.62;
  const pts2 = [];
  for (let i = 0; i < 6; i++) {
    const a = (Math.PI / 3) * i - Math.PI / 6;
    pts2.push(`${(cx + r2 * Math.cos(a)).toFixed(1)},${(cy + r2 * Math.sin(a)).toFixed(1)}`);
  }
  return `
    <polygon points="${pts.join(' ')}" fill="${color}" opacity="0.9"/>
    <polygon points="${pts2.join(' ')}" fill="${lighten(color)}" opacity="0.85"/>
  `;
}

function lighten(hex) {
  const r = parseInt(hex.slice(1,3),16);
  const g = parseInt(hex.slice(3,5),16);
  const b = parseInt(hex.slice(5,7),16);
  const f = 1.4;
  return `rgb(${Math.min(255,Math.round(r*f))},${Math.min(255,Math.round(g*f))},${Math.min(255,Math.round(b*f))})`;
}

function hexGrid() {
  let out = '';
  const cols = 8, rows = 6, r = 28;
  const w = r * Math.sqrt(3), h = r * 2;
  for (let row = 0; row < rows; row++) {
    for (let col = 0; col < cols; col++) {
      const cx = col * w + (row % 2 === 1 ? w / 2 : 0) + 30;
      const cy = row * h * 0.75 + 40;
      const pts = [];
      for (let i = 0; i < 6; i++) {
        const a = (Math.PI / 3) * i - Math.PI / 6;
        pts.push(`${(cx + r * Math.cos(a)).toFixed(1)},${(cy + r * Math.sin(a)).toFixed(1)}`);
      }
      out += `<polygon points="${pts.join(' ')}" fill="none" stroke="#888" stroke-width="1"/>`;
    }
  }
  return out;
}

// =============================================
// IMPRESSION
// =============================================
function printCurrent() {
  const a = apprenants.find(ap => ap.id == selectedId);
  if (!a) return;

  // Enregistrer dans l'historique
  enregistrerImpression(a);

  const win = window.open('', '_blank', 'width=900,height=700');
  const styles = document.querySelector('style').innerHTML;

  win.document.write(`<!DOCTYPE html><html lang="fr"><head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
      * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; box-sizing: border-box; }
      @page { size: A4 landscape; margin: 8mm; }
      html, body { margin: 0; padding: 0; background: white; width: 100%; height: 100%; }
      .page-cert { width: 100%; height: 100vh; display: flex; align-items: center; justify-content: center; }
      .cert-wrapper { box-shadow: none !important; width: 100% !important; max-width: 100% !important; margin: 0 !important; }
      .certificate { aspect-ratio: 1.414 / 1 !important; height: auto !important; max-height: 90vh !important; page-break-inside: avoid !important; }
      ${styles}
    </style>
    </head><body>
    <div class="page-cert">${buildCertHTML(a)}</div>
    <script>document.fonts.ready.then(() => { window.print(); });<\/script>
    </body></html>`);
  win.document.close();
}

function printAll() {
  if (apprenants.length === 0) { alert('Aucun apprenant dans la base de données.'); return; }

  // Enregistrer tous dans l'historique
  apprenants.forEach(a => enregistrerImpression(a));

  const win = window.open('', '_blank', 'width=900,height=700');
  const styles = document.querySelector('style').innerHTML;
  const certs = apprenants.map(a => `<div class="page-cert">${buildCertHTML(a)}</div>`).join('');

  win.document.write(`<!DOCTYPE html><html lang="fr"><head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
      ${styles}
      * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
      @page { size: A4 landscape; margin: 8mm; }
      html, body { margin: 0; padding: 0; background: white; }
      .page-cert {
        width: 100%; height: 100vh;
        display: flex; align-items: center; justify-content: center;
        page-break-after: always; break-after: page;
        box-sizing: border-box; overflow: hidden;
      }
      .page-cert:last-child { page-break-after: avoid; break-after: avoid; }
      .cert-wrapper { box-shadow: none !important; width: 100% !important; max-width: 100% !important; margin: 0 !important; }
      .certificate { aspect-ratio: 1.414 / 1 !important; height: auto !important; max-height: 90vh !important; page-break-inside: avoid !important; break-inside: avoid !important; }
    </style>
    </head><body>${certs}
    <script>document.fonts.ready.then(() => { window.print(); });<\/script>
    </body></html>`);
  win.document.close();
}

