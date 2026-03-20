/**
 * ═══════════════════════════════════════════
 *  VYAPAR — Shared Components (Navbar + Sidebar)
 *  Injected via JS to avoid HTML duplication
 *  ✅ Laravel version — uses URL paths not file paths
 * ═══════════════════════════════════════════
 */

(function () {
  const appUser = window.App?.user || null;
  const logoutUrl = window.App?.logoutUrl || null;
  const csrfToken = window.App?.csrfToken || null;

  const getInitials = (name) => {
    if (!name) return 'GS';
    return name
      .split(' ')
      .filter(Boolean)
      .slice(0, 2)
      .map((word) => word[0].toUpperCase())
      .join('');
  };

  const userName = appUser?.name || 'Grocery Store';
  const userInitials = getInitials(appUser?.name);

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
        <a class="nav-link" data-page="parties" href="/dashboard/parties">
          <i class="fa-solid fa-users"></i> <span>Parties</span>
          <span class="badge-plus" data-modal="addPartyModal" title="Add Party">+</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-page="items" href="/dashboard/items">
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
          <li><a class="nav-link" data-page="invoice" href="/dashboard/sales"><span>Sale Invoice</span></a></li>
          <li><a class="nav-link" href="/dashboard/sales/estimate"><span>Estimate / Quotation</span></a></li>
          <li><a class="nav-link" href="#"><span>Payment In</span></a></li>
          <li><a class="nav-link" href="#"><span>Sale Return / Cr. Note</span></a></li>
        <li><a class="nav-link" href="#"><span>Vyapar POS</span></a></li>

        </ul>
      </li>
      <li class="nav-item">
        <a class="nav-link sidebar-dropdown-toggle" href="#">
          <i class="fa-solid fa-cart-shopping"></i> <span>Purchase & Expense</span>
          <i class="fa-solid fa-chevron-down dropdown-arrow"></i>
        </a>
        <ul class="sidebar-submenu" id="purchaseSubmenu">
          <li><a class="nav-link" href="/dashboard/purchase-bill"><span>Purchase Bill</span></a></li>
          <li><a class="nav-link" href="/dashboard/payment-out"><span>Payment Out</span></a></li>
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
  <a class="nav-link sidebar-dropdown-toggle" href="#">
    <i class="fa-solid fa-wallet"></i> <span>Cash & Bank</span>
    <i class="fa-solid fa-chevron-down dropdown-arrow"></i>
  </a>

  <ul class="sidebar-submenu" id="cashBankSubmenu">
    <li>
      <a class="nav-link" href="/dashboard/loan-accounts">
        <span>Loan Accounts</span>
      </a>
    </li>
    <li>
      <a class="nav-link" href="/dashboard/bank-accounts">
        <span>Bank Accounts</span>
      </a>
    </li>
  </ul>
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
    <div class="sidebar-company" id="sidebarCompany">
      <div class="company-avatar">${userInitials}</div>
      <div class="company-info">
        <div class="company-name">${userName}</div>
        <div class="company-role">My Company</div>
      </div>
      ${logoutUrl && window.App?.isAuthenticated ? `
      <div class="company-dropdown" id="companyDropdown">
        <button class="company-dropdown-item" type="button" id="sidebarLogoutBtn">
          <i class="fa-solid fa-right-from-bracket"></i> Logout
        </button>
      </div>
      ` : ''}
    </div>
  </aside>`;


  // ── Secondary Topbar ──
  const topbarHTML = `
      <!-- topbar -->
      <div id="topbar" class="bg-white border-bottom d-flex align-items-center mb-4" style="margin: -20px -24px 20px -24px; padding: 12px 24px;">
        <div class="topbar-inner w-100 d-flex align-items-center">

          <div class="topbar-search ms-3">
            <span class="search-icon"><i class="bi bi-search"></i></span>
            <input type="text" placeholder="Search...">
          </div>

          <div class="topbar-actions">
            <button
  onclick="window.location.href='{{ route('sale.create') }}'"
  class="btn rounded-pill"
  style="background-color:#FFD7DC;"
>
  <span class="text-danger fw-bold px-3">
    <span class="pe-1">+</span> Add Sale
  </span>
</button>
            <button class="btn rounded-pill" style="background-color: #CCE6FF;"><span class="text-primary fw-bold px-1"><span class="pe-1">+</span> Add
                Purchase</span></button>
            <button class="btn rounded-pill me-2" style="background-color: #CCE6FF;"><span class="text-primary fw-bold px-1"><span class="pe-1">+</span> Add
                More</span></button>


           <span class="text-secondary ps-3" style="border-left:1px solid black;"><i class="fas fa-print"></i></span>
           <span class="text-secondary ps-3"><i class="fa-solid fa-ellipsis-vertical"></i></span>

          </div>
        </div>
      </div>`;




  // ── Inject into page ──
  document.body.insertAdjacentHTML('afterbegin', sidebarHTML);
  document.body.insertAdjacentHTML('afterbegin', navbarHTML);

  // ── Logout button (if available) ──
  if (logoutUrl && window.App?.isAuthenticated) {
    const logoutBtn = document.getElementById('sidebarLogoutBtn');
    if (logoutBtn) {
      logoutBtn.addEventListener('click', () => {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = logoutUrl;
        form.style.display = 'none';
        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = csrfToken || '';
        form.appendChild(token);
        document.body.appendChild(form);
        form.submit();
      });
    }
  }


  const mainContent = document.getElementById('mainContent');
  if (mainContent) {
    mainContent.insertAdjacentHTML('afterbegin', topbarHTML);
  }


  // ── Company dropdown toggle (logout menu) ──
  const sidebarCompany = document.getElementById('sidebarCompany');
  const companyDropdown = document.getElementById('companyDropdown');
  if (sidebarCompany && companyDropdown) {
    sidebarCompany.addEventListener('click', (event) => {
      event.stopPropagation();
      companyDropdown.classList.toggle('open');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', () => {
      companyDropdown.classList.remove('open');
    });

    companyDropdown.addEventListener('click', (event) => {
      event.stopPropagation();
    });
  }

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
