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
  const isAuthenticated = window.App?.isAuthenticated ?? false;
  const userPermissionsRaw = Array.isArray(appUser?.permissions) ? appUser.permissions : [];
  const userPermissions = userPermissionsRaw.map((p) => (typeof p === 'string' ? p.trim().toLowerCase() : p));

  const userRoles = Array.isArray(appUser?.roles) ? appUser.roles.map((r) => (typeof r === 'string' ? r.trim().toLowerCase() : '')) : [];
  const userRoleFallback = typeof appUser?.role === 'string' ? appUser.role.trim().toLowerCase() : '';

  // If user is authenticated but appUser is null, treat as super admin (fail-safe)
  const isSuperAdmin =
    appUser?.id === 1 ||
    userRoles.some((r) => r.includes('admin')) ||
    userRoleFallback.includes('admin') ||
    userPermissions.includes('admin') ||
    userPermissions.includes('super-admin') ||
    (typeof appUser?.name === 'string' && appUser.name.toLowerCase().includes('admin')) ||
    (userRoles.length === 0 && userPermissions.length === 0 && appUser?.id && appUser?.id !== 0 && appUser?.name && appUser.name.toLowerCase().includes('super')) ||
    (isAuthenticated && !appUser); // Fallback: if authenticated but no user object, show full menu

  console.log('Sidebar debug: window.App', window.App, 'appUser', appUser, 'userRoles', userRoles, 'userPermissions', userPermissions, 'isSuperAdmin', isSuperAdmin);

  const hasPermission = (permission) => {
    if (isSuperAdmin) return true;
    if (!permission || typeof permission !== 'string') return false;
    return userPermissions.includes(permission.trim().toLowerCase());
  };

  const permissionAliases = {
    // Purchase sub-items: only specific permissions (no purchase.view/create fallback)
    'purchase.bill': ['purchase.bill'],
    'purchase.payment_out': ['purchase.payment_out'],
    'purchase.return': ['purchase.return'],
    'purchase.expense': ['purchase.expense'],
    'purchase.order': ['purchase.order'],
    // Purchase parent: all purchase sub-permissions
    'purchase.view': ['purchase.view', 'purchase.bill', 'purchase.payment_out', 'purchase.return', 'purchase.expense', 'purchase.order'],

    // Sales sub-items: only specific permissions (no sales.view/create fallback)
    'sales.invoice': ['sales.invoice'],
    'sales.estimate': ['sales.estimate'],
    'sales.payment_in': ['sales.payment_in'],
    'sales.proforma': ['sales.proforma'],
    'sales.order': ['sales.order'],
    'sales.delivery_challan': ['sales.delivery_challan'],
    'sales.sale_return': ['sales.sale_return'],
    'sales.pos': ['sales.pos'],
    // Sales parent: all sales sub-permissions
    'sales.view': ['sales.view', 'sales.invoice', 'sales.estimate', 'sales.payment_in', 'sales.proforma', 'sales.order', 'sales.delivery_challan', 'sales.sale_return', 'sales.pos'],

    // Cash/Bank items
    'cashbank.loan_accounts': ['cashbank.loan_accounts'],
    'cashbank.bank_accounts': ['cashbank.bank_accounts'],
    'cashbank.view': ['cashbank.view', 'cashbank.loan_accounts', 'cashbank.bank_accounts'],
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
      permission: 'sales.view',
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
        { label: 'Cash in Hand', href: '/dashboard/cash-in-hand', dataPage: 'cash-in-hand', permission: 'cashbank.view' },
      ],
    },
    { label: 'Reports', icon: 'fa-chart-pie', href: '#', permission: 'report.view', dataPage: 'reports' },
    { label: 'Sync / Share / Backup', icon: 'fa-cloud-arrow-up', href: '#', permission: 'sync.view', dataPage: 'sync' },
    { label: 'Utilities', icon: 'fa-screwdriver-wrench', href: '#', permission: 'utilities.view', dataPage: 'utilities' },
    { label: 'Settings', icon: 'fa-sliders', href: '/dashboard/settings/general', permission: 'settings.view', dataPage: 'settings' },
  ];

  const canViewMenuItem = (item) => {
    // Always show Home for authenticated users
    if (item.dataPage === 'dashboard') return true;
    if (isSuperAdmin) return true;

    const hasChild = item.children ? item.children.some(canViewMenuItem) : false;
    const hasOwn = item.permission ? hasExtendedPermission(item.permission) : false;
    const result = hasOwn || hasChild;

    console.log('Sidebar debug: canViewMenuItem', {
      label: item.label,
      permission: item.permission,
      isSuperAdmin,
      hasOwn,
      hasChild,
      result,
      itemChildren: item.children?.map((c) => c.label) ?? [],
      currentUrl,
    });

    return result;
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
          .filter(canViewMenuItem)
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
            <a href="${window.routes?.saleCreate || '/dashboard/sales/create'}" class="btn rounded-pill" style="background-color:#FFD7DC;">
  <span class="text-danger fw-bold px-3">
    <span class="pe-1">+</span> Add Sale
  </span>
</a>
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

  const normalize = (value) => {
    if (!value) return '';
    return value.trim().toLowerCase().replace(/^\/+|\/+$/g, '');
  };

  const normalizePageKey = (page) => {
    if (!page) return '';
    const normalized = page.trim().toLowerCase();
    if (normalized.endsWith('s')) return normalized.slice(0, -1);
    return normalized + 's';
  };

  const currentPath = normalize(currentUrl);
  const currentBodyPage = normalize(document.body.getAttribute('data-page'));

  let matchedLink = null;
  let matchedLength = 0;

  const links = Array.from(document.querySelectorAll('.sidebar-nav .nav-link'));

  links.forEach((link) => link.classList.remove('active'));
  document.querySelectorAll('.sidebar-nav .sidebar-submenu').forEach((submenu) => submenu.classList.remove('open'));
  document.querySelectorAll('.sidebar-nav .sidebar-dropdown-toggle').forEach((toggle) => toggle.classList.remove('expanded'));

  links.forEach((link) => {
    const href = normalize(link.getAttribute('href'));
    const linkPage = normalize(link.getAttribute('data-page'));

    // exact URL match has highest priority
    if (href && href !== '#' && (currentPath === href || currentPath === href.replace(/^dashboard\//, ''))) {
      if (href.length > matchedLength) {
        matchedLink = link;
        matchedLength = href.length;
      }
      return;
    }

    // child route in same section, e.g. /dashboard/sales/pos should mark /dashboard/sales
    if (href && href !== '#' && currentPath.startsWith(href + '/')) {
      if (href.length > matchedLength) {
        matchedLink = link;
        matchedLength = href.length;
      }
      return;
    }

    // fallback by data-page value (supports singular/plural)
    if (!matchedLink && currentBodyPage) {
      const normalizedLinkPage = normalizePageKey(linkPage);
      const normalizedBody = normalizePageKey(currentBodyPage);
      if (linkPage === currentBodyPage || normalizedLinkPage === normalizedBody || linkPage === normalizedBody || normalizedLinkPage === currentBodyPage) {
        matchedLink = link;
      }
    }
  });

  // Explicit home selection
  if (currentPath === 'dashboard' || currentBodyPage === 'dashboard') {
    const homeLink = document.querySelector('.sidebar-nav .nav-link[href="/dashboard"]');
    if (homeLink) {
      matchedLink = homeLink;
    }
  }

  if (matchedLink) {
    const parentSubmenu = matchedLink.closest('.sidebar-submenu');
    console.log('Sidebar debug: matched link', {
      currentUrl,
      currentPath,
      currentBodyPage,
      matchedHref: normalize(matchedLink.getAttribute('href')),
      matchedDataPage: normalize(matchedLink.getAttribute('data-page')),
      isDropdownChild: Boolean(parentSubmenu),
    });

    if (parentSubmenu) {
      // for dropdown items, add active to the <li>
      const li = matchedLink.closest('li');
      if (li) li.classList.add('active');
    } else {
      // for top-level items, add to the <a>
      matchedLink.classList.add('active');
    }

    if (parentSubmenu) {
      parentSubmenu.classList.add('open');
      const toggle = parentSubmenu.previousElementSibling;
      if (toggle) toggle.classList.add('expanded');
    }
  } else {
    console.log('Sidebar debug: no matched link found', {
      currentUrl,
      currentPath,
      currentBodyPage,
      userPermissions,
      isSuperAdmin,
    });
  }
})();
