/**
 * ═══════════════════════════════════════════
 *  VYAPAR — Shared Components (Navbar + Sidebar)
 *  Injected via JS to avoid HTML duplication
 *  ✅ Laravel version — uses URL paths not file paths
 * ═══════════════════════════════════════════
 */

(function () {
  // ── Top Navbar ──
  const navbarHTML = `
  <nav class="top-navbar" id="topNavbar">
    <div class="navbar-left">
      <span class="brand-logo"><i class="fa-solid fa-bolt"></i> Vyapar</span>
      <a href="#" class="nav-link-item"><i class="fa-regular fa-building"></i> Company</a>
      <a href="#" class="nav-link-item"><i class="fa-regular fa-circle-question"></i> Help</a>
      <a href="#" class="nav-link-item"><i class="fa-solid fa-code-branch"></i> Versions</a>
      <a href="#" class="nav-link-item"><i class="fa-regular fa-keyboard"></i> Shortcuts</a>
      <button class="btn-icon" title="Refresh"><i class="fa-solid fa-arrows-rotate"></i></button>
    </div>
    <div class="navbar-center">
      Customer Support : <i class="fa-solid fa-phone"></i>
      <span class="phone-number">(+91) 9333 911 911</span> |
      <a href="#">Get Instant Online Support</a>
    </div>
    <div class="navbar-right">
      <button class="btn-icon" title="Notifications"><i class="fa-regular fa-bell"></i></button>
      <button class="btn-icon" title="Settings"><i class="fa-solid fa-gear"></i></button>
    </div>
  </nav>`;

  // ── Left Sidebar ──
  const sidebarHTML = `
  <aside class="sidebar" id="sidebar">
    <!-- Search -->
    <div class="sidebar-search position-relative">
      <i class="fa-solid fa-magnifying-glass search-icon"></i>
      <input type="text" placeholder="Open Anything (Ctrl+F)" id="sidebarSearch">
    </div>

    <!-- Nav Menu -->
    <ul class="sidebar-nav">
      <li class="nav-item">
        <a class="nav-link" data-page="dashboard" href="/dashboard">
          <i class="fa-solid fa-house"></i> <span>Home</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-page="parties" href="/parties">
          <i class="fa-solid fa-users"></i> <span>Parties</span>
          <span class="badge-plus" data-modal="addPartyModal" title="Add Party">+</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-page="items" href="/items">
          <i class="fa-solid fa-boxes-stacked"></i> <span>Items</span>
          <span class="badge-plus" data-modal="addItemModal" title="Add Item">+</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link sidebar-dropdown-toggle" href="#">
          <i class="fa-solid fa-file-invoice-dollar"></i> <span>Sale</span>
          <i class="fa-solid fa-chevron-down dropdown-arrow"></i>
        </a>
        <ul class="sidebar-submenu" id="saleSubmenu">
          <li><a class="nav-link" data-page="invoice" href="/invoice"><span>Sale Invoice</span></a></li>
          <li><a class="nav-link" href="#"><span>Estimate / Quotation</span></a></li>
          <li><a class="nav-link" href="#"><span>Payment In</span></a></li>
          <li><a class="nav-link" href="#"><span>Sale Return / Cr. Note</span></a></li>
        </ul>
      </li>
      <li class="nav-item">
        <a class="nav-link sidebar-dropdown-toggle" href="#">
          <i class="fa-solid fa-cart-shopping"></i> <span>Purchase & Expense</span>
          <i class="fa-solid fa-chevron-down dropdown-arrow"></i>
        </a>
        <ul class="sidebar-submenu" id="purchaseSubmenu">
          <li><a class="nav-link" href="#"><span>Purchase Bill</span></a></li>
          <li><a class="nav-link" href="#"><span>Payment Out</span></a></li>
          <li><a class="nav-link" href="#"><span>Purchase Return / Dr. Note</span></a></li>
          <li><a class="nav-link" href="#"><span>Expense</span></a></li>
        </ul>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">
          <i class="fa-solid fa-rocket"></i> <span>Grow Your Business</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">
          <i class="fa-solid fa-wallet"></i> <span>Cash & Bank</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">
          <i class="fa-solid fa-chart-pie"></i> <span>Reports</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">
          <i class="fa-solid fa-cloud-arrow-up"></i> <span>Sync / Share / Backup</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">
          <i class="fa-solid fa-screwdriver-wrench"></i> <span>Utilities</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">
          <i class="fa-solid fa-sliders"></i> <span>Settings</span>
        </a>
      </li>
    </ul>

    <!-- Promo Card -->
    <div class="sidebar-promo">
      <span class="promo-badge">Vyapar</span>
      <h6>EARLY BIRD OFFER</h6>
      <p>Upto <strong>50% OFF</strong> on all plans. Limited time only!</p>
      <button class="btn-promo">Buy Now</button>
    </div>

    <!-- Company Selector -->
    <div class="sidebar-company">
      <div class="company-avatar">GS</div>
      <div class="company-info">
        <div class="company-name">Grocery Store</div>
        <div class="company-role">My Company</div>
      </div>
    </div>
  </aside>`;

  // ── Inject into page ──
  document.body.insertAdjacentHTML('afterbegin', sidebarHTML);
  document.body.insertAdjacentHTML('afterbegin', navbarHTML);

  // ── Highlight active page ──
  const currentPage = document.body.getAttribute('data-page') || 'dashboard';
  const activeLink = document.querySelector(`.sidebar-nav .nav-link[data-page="${currentPage}"]`);
  if (activeLink) {
    activeLink.classList.add('active');
    // If it's inside a submenu, open the parent dropdown
    const parentSubmenu = activeLink.closest('.sidebar-submenu');
    if (parentSubmenu) {
      parentSubmenu.classList.add('open');
      const toggle = parentSubmenu.previousElementSibling;
      if (toggle) toggle.classList.add('expanded');
    }
  }
})();
