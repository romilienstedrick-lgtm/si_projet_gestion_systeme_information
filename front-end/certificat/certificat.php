<div class="app">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="sidebar-header">
      <div class="sidebar-tabs">
        <button class="tab-btn active" id="tabApprenants" onclick="switchTab('apprenants')">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users-round-icon lucide-users-round-certificat">
        <path d="M18 21a8 8 0 0 0-16 0"/><circle cx="10" cy="8" r="5"/><path d="M22 20c0-3.37-2-6.5-4-8a5 5 0 0 0-.45-8.3"/></svg>  
        Apprenants <span class="count-badge" id="count">0</span>
        </button>
        <button class="tab-btn" id="tabHistorique" onclick="switchTab('historique')">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-history-icon lucide-history">
        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/></svg>
           Historique <span class="count-badge hist-badge" id="histCount">0</span>
        </button>
      </div>
      <div id="panelApprenants">
        <div class="search-box">
          <input type="text" id="searchInput" placeholder="Rechercher un apprenant..." oninput="filterApprenants(this.value)">
        </div>
        <div class="session-filter">
          <select id="sessionFilter" onchange="filterApprenants(document.getElementById('searchInput').value)">
            <option value="">Toutes les sessions</option>
          </select>
        </div>
      </div>
      <div id="panelHistorique" style="display:none;">
        <div class="search-box">
          <input type="text" id="searchHist" placeholder="Rechercher dans l'historique..." oninput="renderHistorique(this.value)">
        </div>
        <button class="btn-clear-hist" onclick="clearHistorique()">🗑 Effacer l'historique</button>
      </div>
    </div>

    <div class="apprenants-list" id="apprenantsList">
      <div class="loading">Chargement...</div>
    </div>
  </aside>

  <!-- MAIN -->
  <main class="main">
    <div class="cert-controls">
      <button class="btn-print" onclick="printCurrent()" id="btnPrint" disabled>
        🖨 Imprimer ce certificat
      </button>
      <button class="btn-all" onclick="printAll()">
        📄 Imprimer tous les certificats
      </button>
    </div>

    <div id="certArea">
      <div class="placeholder">
        <div class="placeholder-icon">🏆</div>
        <div class="placeholder-text">Sélectionnez un apprenant<br>pour afficher son certificat</div>
      </div>
    </div>
  </main>
</div>
