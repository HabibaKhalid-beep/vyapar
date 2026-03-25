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
  const userPermissions = Array.isArray(appUser?.permissions) ? appUser.permissions : [];
  const isSuperAdmin = appUser?.id === 1;

  const hasPermission = (permission) => {
    if (isSuperAdmin) return true;
    return userPermissions.includes(permission);
  };

  const permissionAliases = {
    'purchase.bill': ['purchase.bill', 'purchase.view', 'purchase.create'],
    'purchase.payment_out': ['purchase.payment_out', 'purchase.payments', 'purchase.create'],
    'purchase.return': ['purchase.return', 'purchase.update', 'purchase.create'],
    'purchase.expense': ['purchase.expense', 'purchase.delete', 'purchase.create'],
    'purchase.order': ['purchase.order', 'purchase.create'],
  };

  const hasExtendedPermission = (permission) => {
    if (isSuperAdmin) return true;

    if (!permission) {
      return true;
    }

    const normalized = permissionAliases[permission] || [permission];
    return normalized.some((perm) => hasPermission(perm));
  };

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
  const currentUrl = window.location.pathname;

  const canViewRole = isSuperAdmin || hasPermission('roles.view');
  const canViewUser = isSuperAdmin || hasPermission('user.view');
  const canViewParty = isSuperAdmin || hasPermission('party.view');
  const canViewProduct = isSuperAdmin || hasPermission('product.view');
  const canViewGrow = isSuperAdmin || hasPermission('grow.view');

  const menuItems = [
    {
      label: 'Home',
      icon: 'fa-house',
      href: '/dashboard',
      dataPage: 'dashboard',
      permission: null,
    },
    {
      label: 'User Management',
      icon: 'fa-users-gear',
      permission: 'roles.view',
      children: [
        { label: 'Roles', href: '/dashboard/roles', dataPage: 'roles', permission: 'roles.view' },
        { label: 'Users', href: '/dashboard/users', dataPage: 'users', permission: 'user.view' },
      ],
    },
    {
      label: 'Parties',
      icon: 'fa-users',
      href: '/dashboard/parties',
      dataPage: 'parties',
      permission: 'party.view',
      add: { label: 'Add Party', modal: 'addPartyModal' },
    },
    {
      label: 'Items',
      icon: 'fa-boxes-stacked',
      href: '/dashboard/items',
      dataPage: 'items',
      permission: 'product.view',
      add: { label: 'Add Item', modal: 'addItemModal' },
    },
    {
      label: 'Sale',
      icon: 'fa-file-invoice-dollar',

      children: [
        { label: 'Sale Invoice', href: '/dashboard/sales', dataPage: 'invoice', permission: 'sales.invoice' },
        { label: 'Estimate / Quotation', href: '/dashboard/sales/estimate', dataPage: 'estimate', permission: 'sales.estimate' },
        { label: 'Payment In', href: '/dashboard/payment-in', dataPage: 'payment-in', permission: 'sales.payment_in' },
        { label: 'Proforma Invoice', href: '/dashboard/proforma-invoice', dataPage: 'proforma-invoice', permission: 'sales.proforma' },
        { label: 'Sale Order', href: '/dashboard/sale-order', dataPage: 'sale-order', permission: 'sales.order' },
        { label: 'Delivery Challan', href: '/dashboard/delivery-challan', dataPage: 'delivery-challan', permission: 'sales.delivery_challan' },
        { label: 'Sale Return / Cr. Note', href: '/dashboard/sale-return', dataPage: 'sale-return', permission: 'sales.sale_return' },
        { label: 'Vyapar POS', href: '/dashboard/sales/pos', dataPage: 'pos', permission: 'sales.pos' },
      ],
    },
    {
      label: 'Purchase & Expense',
      icon: 'fa-cart-shopping',
      permission: 'purchase.view',
      children: [
        { label: 'Purchase Bill', href: '/dashboard/purchase-bill', dataPage: 'purchase-bill', permission: 'purchase.bill' },
        { label: 'Payment Out', href: '/dashboard/payment-out', dataPage: 'payment-out', permission: 'purchase.payment_out' },
        { label: 'Purchase Return / Dr. Note', href: '/dashboard/purchase-return', dataPage: 'purchase-return', permission: 'purchase.return' },
        { label: 'Expense', href: '/dashboard/expense', dataPage: 'expense', permission: 'purchase.expense' },
        { label: 'Purchase Order', href: '/dashboard/purchase-order', dataPage: 'purchase-order', permission: 'purchase.order' },
      ],
    },
    {
      label: 'Grow Your Business',
      icon: 'fa-rocket',
      href: '#',
      permission: 'grow.view',
      dataPage: 'grow',
    },
    {
      label: 'Cash & Bank',
      icon: 'fa-wallet',
      permission: 'cashbank.view',
      children: [
        { label: 'Loan Accounts', href: '/dashboard/loan-accounts', dataPage: 'loan-accounts', permission: 'cashbank.loan_accounts' },
        { label: 'Bank Accounts', href: '/dashboard/bank-accounts', dataPage: 'bank-accounts', permission: 'cashbank.bank_accounts' },
      ],
    },
    { label: 'Reports', icon: 'fa-chart-pie', href: '#', permission: 'report.view', dataPage: 'reports' },
    { label: 'Sync / Share / Backup', icon: 'fa-cloud-arrow-up', href: '#', permission: 'sync.view', dataPage: 'sync' },
    { label: 'Utilities', icon: 'fa-screwdriver-wrench', href: '#', permission: 'utilities.view', dataPage: 'utilities' },
    { label: 'Settings', icon: 'fa-sliders', href: '#', permission: 'settings.view', dataPage: 'settings' },
  ];

const canViewMenuItem = (item) => {
  if (isSuperAdmin) return true;

  // ✅ If dropdown → check children ONLY
  if (item.children && item.children.length) {
    return item.children.some(child => canViewMenuItem(child));
  }

  // ✅ Normal item
  return !item.permission || hasExtendedPermission(item.permission);
};

  const renderMenu = () => {
    return menuItems
      .filter(canViewMenuItem)
      .map((item) => {
        const hasChildren = Array.isArray(item.children) && item.children.length;
        if (!hasChildren) {
          const currentIcon = item.icon ? `<i class="fa-solid ${item.icon}"></i> ` : '';
          const href = item.href || '#';
          const activeClass = currentUrl === href || currentUrl === item.dataPage ? 'active' : '';
          return `
            <li class="nav-item">
              <a class="nav-link ${activeClass}" data-page="${item.dataPage || ''}" href="${href}">
                ${currentIcon}<span>${item.label}</span>
              </a>
            </li>
          `;
        }

        const submenuHtml = item.children
  .filter(child => canViewMenuItem(child))
          .map((child) => {
            const activeClass = currentUrl === child.href || currentUrl === child.dataPage ? 'active' : '';
            return `
              <li class="${activeClass}"><a class="nav-link" data-page="${child.dataPage || ''}" href="${child.href}"><span>${child.label}</span></a></li>
            `;
          })
          .join('');

        if (!submenuHtml) return '';

        return `
          <li class="nav-item">
            <a class="nav-link sidebar-dropdown-toggle" href="#">
              <i class="fa-solid ${item.icon}"></i> <span>${item.label}</span>
              <i class="fa-solid fa-chevron-down dropdown-arrow"></i>
            </a>
            <ul class="sidebar-submenu">${submenuHtml}</ul>
          </li>
        `;
      })
      .join('');
  };

  const sidebarHTML = `
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-search position-relative">
      <i class="fa-solid fa-magnifying-glass search-icon"></i>
      <input type="text" placeholder="Open Anything (Ctrl+F)" id="sidebarSearch">
    </div>
    <ul class="sidebar-nav">
      ${renderMenu()}
    </ul>

    <div class="sidebar-promo">
      <span class="promo-badge">Vyapar</span>
      <h6>EARLY BIRD OFFER</h6>
      <p>Upto <strong>50% OFF</strong> on all plans. Limited time only!</p>
      <button class="btn-promo">Buy Now</button>
    </div>

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

  // subsequent code (topbar creation and injection etc.) remains unchanged


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

  const topbarHTML = `
      <!-- topbar -->
      <div id="topbar" class="bg-white border-bottom d-flex align-items-center mb-4" style="margin: -20px -24px 20px -24px; padding: 12px 24px; margin-top:5px;">
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

  let bestMatch = null;
  let bestMatchLength = 0;

  document.querySelectorAll('.sidebar-nav .nav-link').forEach(link => {
    const href = link.getAttribute('href');

   if (href && href !== '/') {
  // exact match OR child route match (but not root dashboard)
  const isExactMatch = currentUrl === href;
  const isChildMatch = currentUrl.startsWith(href + '/') && href !== '/dashboard';

  if (isExactMatch || isChildMatch) {
    if (href.length > bestMatchLength) {
      if (bestMatch) bestMatch.classList.remove('active');
      bestMatch = link;
      bestMatchLength = href.length;
    }
  }
}

// Handle Home separately
if (currentUrl === '/dashboard') {
  document.querySelectorAll('.sidebar-nav .nav-link').forEach(link => {
    if (link.getAttribute('href') === '/dashboard') {
      link.classList.add('active');
    }
  });
}

if (bestMatch) {
  bestMatch.classList.add('active');

  const parentSubmenu = bestMatch.closest('.sidebar-submenu');
  if (parentSubmenu) {
    parentSubmenu.classList.add('open');

    const toggle = parentSubmenu.previousElementSibling;
    if (toggle) toggle.classList.add('expanded');
  }
}
  });

  if (bestMatch) {
    bestMatch.classList.add('active');

    // open parent dropdown
    const parentSubmenu = bestMatch.closest('.sidebar-submenu');
    if (parentSubmenu) {
      parentSubmenu.classList.add('open');

      const toggle = parentSubmenu.previousElementSibling;
      if (toggle) toggle.classList.add('expanded');
    }
  }

  const currentPage = document.body.getAttribute('data-page');
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
