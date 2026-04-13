{{-- ============================================================
     Tab: Profit and Loss — FIXED
     - Excel: client-side CSV (no route needed, no 404)
     - Print: hides sidebar, shows only report
     - Amount column always visible
     ============================================================ --}}

<div id="tab-ProfitAndLoss" class="report-tab-content d-none"
     style="padding: 24px; background: #fff; min-height: 100vh; overflow-y: auto;">

    {{-- ── Top Bar ── --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div class="d-flex align-items-center border rounded px-3 py-2 bg-white"
             style="gap:12px; border-color:#e5e7eb;">
            <span class="text-secondary fw-medium" style="font-size:14px;">From</span>
            <input type="date" id="pnlFrom" class="fw-medium text-dark bg-transparent border-0"
                   style="font-size:14px; outline:none;">
            <span class="text-secondary fw-medium" style="font-size:14px;">To</span>
            <input type="date" id="pnlTo" class="fw-medium text-dark bg-transparent border-0"
                   style="font-size:14px; outline:none;">
            <button class="btn btn-sm btn-primary ms-2" id="pnlApplyBtn" style="font-size:13px;">Apply</button>
        </div>
        <div class="d-flex gap-2">
            <button class="btn d-flex align-items-center justify-content-center p-0" id="pnlExcelBtn"
                    style="width:40px;height:40px;border-radius:50%;border:1px solid #e5e7eb;background:#fff;"
                    title="Export CSV">
                <i class="fa-regular fa-file-excel text-success" style="font-size:18px;"></i>
            </button>
            <button class="btn d-flex align-items-center justify-content-center p-0" id="pnlPrintBtn"
                    style="width:40px;height:40px;border-radius:50%;border:1px solid #e5e7eb;background:#fff;"
                    title="Print">
                <i class="fa-solid fa-print text-secondary" style="font-size:18px;"></i>
            </button>
        </div>
    </div>

    <h4 class="fw-bold text-dark mb-4 text-uppercase" style="letter-spacing:.5px;">Profit and Loss Report</h4>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <span class="text-secondary fw-medium" style="font-size:14px;">View :</span>
            <div class="form-check mb-0">
                <input class="form-check-input" type="radio" name="pnlViewType" id="pnlViewVyapar" value="vyapar" checked>
                <label class="form-check-label fw-medium text-dark" for="pnlViewVyapar" style="font-size:14px;">Vyapar</label>
            </div>
            <div class="form-check mb-0">
                <input class="form-check-input" type="radio" name="pnlViewType" id="pnlViewAccounting" value="accounting">
                <label class="form-check-label fw-medium text-dark" for="pnlViewAccounting" style="font-size:14px;">Accounting</label>
            </div>
        </div>
        <button id="pnlExpandAllBtn" class="btn btn-link text-primary text-decoration-none p-0 fw-medium d-none" style="font-size:14px;">
            <i class="fa-solid fa-chevron-down me-1"></i> Expand all accounts
        </button>
    </div>

    <div id="pnlLoading" class="text-center py-4 d-none">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="text-muted mt-2" style="font-size:13px;">Loading...</p>
    </div>

    {{-- Table header --}}
    <div class="d-flex justify-content-between align-items-center px-4 py-2 mb-1 rounded" style="background:#f3f4f6;">
        <span class="fw-bold text-dark" style="font-size:14px;">Particulars</span>
        <span class="fw-bold text-dark" style="font-size:14px;">Amount</span>
    </div>

    {{-- VYAPAR VIEW --}}
    <div id="pnlVyaparView" class="w-100">
        @php
        $pnlRows = [
            ['label'=>'Sale (+)',                 'key'=>'sale',             'sign'=>'+','indent'=>0],
            ['label'=>'Credit Note (-)',           'key'=>'credit_note',     'sign'=>'-','indent'=>0],
            ['label'=>'Sale FA (+)',               'key'=>'sale_fa',         'sign'=>'+','indent'=>0],
            ['label'=>'Purchase (-)',              'key'=>'purchase',        'sign'=>'-','indent'=>0],
            ['label'=>'Debit Note (+)',            'key'=>'debit_note',      'sign'=>'+','indent'=>0],
            ['label'=>'Purchase FA (-)',           'key'=>'purchase_fa',     'sign'=>'-','indent'=>0],
            ['label'=>'Direct Expenses(-)',        'key'=>null,              'sign'=>null,'indent'=>0,'header'=>true],
            ['label'=>'Other Direct Expenses (-)', 'key'=>'other_direct_exp','sign'=>'-','indent'=>1],
            ['label'=>'Payment-in Discount (-)',   'key'=>'payin_discount',  'sign'=>'-','indent'=>1],
            ['label'=>'Tax Payable (-)',           'key'=>null,              'sign'=>null,'indent'=>0,'header'=>true],
            ['label'=>'Tax Payable (-)',           'key'=>'tax_payable',     'sign'=>'-','indent'=>1],
            ['label'=>'TCS Payable (-)',           'key'=>'tcs_payable',     'sign'=>'-','indent'=>1],
            ['label'=>'TDS Payable (-)',           'key'=>'tds_payable',     'sign'=>'-','indent'=>1],
            ['label'=>'Tax Receivable (+)',        'key'=>null,              'sign'=>null,'indent'=>0,'header'=>true],
            ['label'=>'Tax Receivable (+)',        'key'=>'tax_receivable',  'sign'=>'+','indent'=>1],
            ['label'=>'TCS Receivable (+)',        'key'=>'tcs_receivable',  'sign'=>'+','indent'=>1],
            ['label'=>'TDS Receivable (+)',        'key'=>'tds_receivable',  'sign'=>'+','indent'=>1],
            ['label'=>'Opening Stock (-)',         'key'=>'opening_stock',   'sign'=>'-','indent'=>0],
            ['label'=>'Closing Stock (+)',         'key'=>'closing_stock',   'sign'=>'+','indent'=>0],
            ['label'=>'Opening Stock FA (-)',      'key'=>'opening_stock_fa','sign'=>'-','indent'=>0],
            ['label'=>'Closing Stock FA (+)',      'key'=>'closing_stock_fa','sign'=>'+','indent'=>0],
            ['label'=>'Gross Profit',             'key'=>'gross_profit',     'sign'=>'+','indent'=>0,'total'=>true],
            ['label'=>'Other Income',             'key'=>'other_income',     'sign'=>'+','indent'=>0],
            ['label'=>'Indirect Expenses (-)',    'key'=>null,               'sign'=>null,'indent'=>0,'header'=>true],
            ['label'=>'Other Expenses',           'key'=>'other_expenses',   'sign'=>'-','indent'=>1],
            ['label'=>'Loan Interest Expenses',   'key'=>'loan_interest',    'sign'=>'-','indent'=>1],
            ['label'=>'Loan Processing Fee',      'key'=>'loan_processing',  'sign'=>'-','indent'=>1],
            ['label'=>'Loan Charges Expense',     'key'=>'loan_charges',     'sign'=>'-','indent'=>1],
            ['label'=>'Profit',                   'key'=>'net_profit',       'sign'=>'+','indent'=>0,'total'=>true],
        ];
        @endphp

        @foreach($pnlRows as $row)
            @php
                $isHeader = $row['header'] ?? false;
                $isTotal  = $row['total']  ?? false;
                $indent   = $row['indent'] ?? 0;
                $bg       = $indent === 0 ? '#fafafa' : '#ffffff';
                $pl       = $indent === 0 ? '1.5rem' : '3rem';
                $dataKey  = $row['key'] ?? '';
            @endphp
            <div class="d-flex justify-content-between align-items-center border-bottom py-3 px-4"
                 style="background:{{ $bg }}; padding-left:{{ $pl }} !important;">

                @if($isTotal)
                    <span class="fw-bold text-dark" style="font-size:14px;">{{ $row['label'] }}</span>
                    <span class="fw-bold text-success" style="font-size:14px;"
                          data-pnl-key="{{ $dataKey }}">Rs 0.00</span>
                @elseif($isHeader)
                    <span class="fw-medium text-dark" style="font-size:14px;">{{ $row['label'] }}</span>
                    <span class="text-muted" style="font-size:14px;">—</span>
                @else
                    <span class="{{ $indent ? 'text-dark' : 'fw-medium text-dark' }}" style="font-size:14px;">{{ $row['label'] }}</span>
                    <span class="fw-medium {{ $row['sign']==='+'?'text-success':'text-danger' }}"
                          style="font-size:14px;"
                          data-pnl-key="{{ $dataKey }}">Rs 0.00</span>
                @endif
            </div>
        @endforeach
    </div>

    {{-- ACCOUNTING VIEW --}}
    <div id="pnlAccountingView" class="w-100 d-none">
        <div class="d-flex justify-content-between px-4 py-3 border-bottom" style="background:#f9fafb;">
            <span class="fw-bold text-dark" style="font-size:14px;">Net Profit (Incomes - Expenses)</span>
            <span class="fw-bold text-success" id="accNetProfitTop" style="font-size:14px;">Rs 0.00</span>
        </div>

        {{-- Incomes section --}}
        <div class="d-flex justify-content-between px-4 py-3 border-bottom pnl-acc-hdr"
             style="background:#fafafa;cursor:pointer;" data-body="pnl-incomes">
            <span class="fw-bold text-dark" style="font-size:14px;">
                <i class="fa-solid fa-chevron-down me-2 pnl-ci" style="color:#3b82f6;font-size:11px;"></i>Incomes
            </span>
            <span class="fw-bold text-success" id="accTotalIncome" style="font-size:14px;">Rs 0.00</span>
        </div>
        <div id="pnl-incomes">
            <div class="d-flex justify-content-between py-3 border-bottom pnl-acc-hdr"
                 style="padding-left:3.5rem;padding-right:1.5rem;cursor:pointer;" data-body="pnl-sale-acc">
                <span class="fw-medium text-dark" style="font-size:14px;">
                    <i class="fa-solid fa-chevron-down me-1 pnl-ci" style="color:#3b82f6;font-size:11px;"></i>Sale Accounts
                </span>
                <span class="text-success fw-medium" id="accSaleAcc" style="font-size:14px;">Rs 0.00</span>
            </div>
            <div id="pnl-sale-acc">
                <div class="d-flex justify-content-between py-2 border-bottom" style="padding-left:6.5rem;padding-right:1.5rem;">
                    <span class="text-secondary" style="font-size:14px;">&bull; Sale Revenue Account</span>
                    <span class="text-success fw-medium" id="accSaleRevenue" style="font-size:14px;">Rs 0.00</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom" style="padding-left:6.5rem;padding-right:1.5rem;">
                    <span class="text-secondary" style="font-size:14px;">&bull; Additional Charges on Sale</span>
                    <span class="text-success fw-medium" style="font-size:14px;">Rs 0.00</span>
                </div>
            </div>
            <div class="d-flex justify-content-between py-3 border-bottom pnl-acc-hdr"
                 style="padding-left:3.5rem;padding-right:1.5rem;cursor:pointer;" data-body="pnl-oid">
                <span class="fw-medium text-dark" style="font-size:14px;">
                    <i class="fa-solid fa-chevron-down me-1 pnl-ci" style="color:#3b82f6;font-size:11px;"></i>Other Incomes (Direct)
                </span>
                <span class="text-success fw-medium" style="font-size:14px;">Rs 0.00</span>
            </div>
            <div id="pnl-oid">
                <div class="d-flex justify-content-between py-2 border-bottom" style="padding-left:6.5rem;padding-right:1.5rem;">
                    <span class="text-secondary" style="font-size:14px;">&bull; Payment-Out Discount</span>
                    <span class="text-success fw-medium" style="font-size:14px;">Rs 0.00</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom" style="padding-left:6.5rem;padding-right:1.5rem;">
                    <span class="text-secondary" style="font-size:14px;">&bull; Other Direct Incomes</span>
                    <span class="text-success fw-medium" style="font-size:14px;">Rs 0.00</span>
                </div>
            </div>
            <div class="d-flex justify-content-between py-3 border-bottom pnl-acc-hdr"
                 style="padding-left:3.5rem;padding-right:1.5rem;cursor:pointer;" data-body="pnl-oii">
                <span class="fw-medium text-dark" style="font-size:14px;">
                    <i class="fa-solid fa-chevron-down me-1 pnl-ci" style="color:#3b82f6;font-size:11px;"></i>Other Incomes (Indirect)
                </span>
                <span class="text-success fw-medium" style="font-size:14px;">Rs 0.00</span>
            </div>
            <div id="pnl-oii">
                <div class="d-flex justify-content-between py-2 border-bottom" style="padding-left:6.5rem;padding-right:1.5rem;">
                    <span class="text-secondary" style="font-size:14px;">&bull; Profit on Sale of Assets</span>
                    <span class="text-success fw-medium" style="font-size:14px;">Rs 0.00</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom" style="padding-left:6.5rem;padding-right:1.5rem;">
                    <span class="text-secondary" style="font-size:14px;">&bull; Appreciation on Assets</span>
                    <span class="text-success fw-medium" style="font-size:14px;">Rs 0.00</span>
                </div>
            </div>
        </div>

        {{-- Expenses section --}}
        <div class="d-flex justify-content-between px-4 py-3 border-bottom pnl-acc-hdr"
             style="background:#fafafa;cursor:pointer;" data-body="pnl-expenses">
            <span class="fw-bold text-dark" style="font-size:14px;">
                <i class="fa-solid fa-chevron-down me-2 pnl-ci" style="color:#3b82f6;font-size:11px;"></i>Expenses
            </span>
            <span class="fw-bold text-danger" id="accTotalExp" style="font-size:14px;">Rs 0.00</span>
        </div>
        <div id="pnl-expenses">
            <div class="d-flex justify-content-between py-3 border-bottom pnl-acc-hdr"
                 style="padding-left:3.5rem;padding-right:1.5rem;cursor:pointer;" data-body="pnl-cogs">
                <span class="fw-medium text-dark" style="font-size:14px;">
                    <i class="fa-solid fa-chevron-down me-1 pnl-ci" style="color:#3b82f6;font-size:11px;"></i>Cost of Goods Sold
                </span>
                <span class="text-danger fw-medium" id="accCOGS" style="font-size:14px;">Rs 0.00</span>
            </div>
            <div id="pnl-cogs">
                <div class="d-flex justify-content-between py-2 border-bottom" style="padding-left:5rem;padding-right:1.5rem;">
                    <span class="text-dark" style="font-size:14px;">&rsaquo; Purchase Accounts</span>
                    <span class="text-danger fw-medium" id="accPurchAcc" style="font-size:14px;">Rs 0.00</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom" style="padding-left:6.5rem;padding-right:1.5rem;">
                    <span class="text-secondary" style="font-size:14px;">&bull; Opening Stock</span>
                    <span class="text-danger fw-medium" id="accOpenStock" style="font-size:14px;">Rs 0.00</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom" style="padding-left:6.5rem;padding-right:1.5rem;">
                    <span class="text-secondary" style="font-size:14px;">&bull; Closing Stock</span>
                    <span class="text-success fw-medium" id="accCloseStock" style="font-size:14px;">Rs 0.00</span>
                </div>
            </div>
            <div class="d-flex justify-content-between py-3 border-bottom pnl-acc-hdr"
                 style="padding-left:3.5rem;padding-right:1.5rem;cursor:pointer;" data-body="pnl-dexp">
                <span class="fw-medium text-dark" style="font-size:14px;">
                    <i class="fa-solid fa-chevron-down me-1 pnl-ci" style="color:#3b82f6;font-size:11px;"></i>Direct Expenses
                </span>
                <span class="text-danger fw-medium" id="accDirExp" style="font-size:14px;">Rs 0.00</span>
            </div>
            <div id="pnl-dexp">
                <div class="d-flex justify-content-between py-2 border-bottom" style="padding-left:6.5rem;padding-right:1.5rem;">
                    <span class="text-secondary" style="font-size:14px;">&bull; Payment-In Discount</span>
                    <span class="text-danger fw-medium" id="accPayinDisc" style="font-size:14px;">Rs 0.00</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom" style="padding-left:6.5rem;padding-right:1.5rem;">
                    <span class="text-secondary" style="font-size:14px;">&bull; Manufacturing Expense</span>
                    <span class="text-danger fw-medium" style="font-size:14px;">Rs 0.00</span>
                </div>
            </div>
            <div class="d-flex justify-content-between py-3 border-bottom pnl-acc-hdr"
                 style="padding-left:3.5rem;padding-right:1.5rem;cursor:pointer;" data-body="pnl-iexp">
                <span class="fw-medium text-dark" style="font-size:14px;">
                    <i class="fa-solid fa-chevron-down me-1 pnl-ci" style="color:#3b82f6;font-size:11px;"></i>Indirect Expenses
                </span>
                <span class="text-danger fw-medium" id="accIndirExp" style="font-size:14px;">Rs 0.00</span>
            </div>
            <div id="pnl-iexp">
                <div class="d-flex justify-content-between py-2 border-bottom" style="padding-left:5rem;padding-right:1.5rem;">
                    <span class="text-dark" style="font-size:14px;">&rsaquo; Cost of Financing</span>
                    <span class="text-danger fw-medium" id="accFinancing" style="font-size:14px;">Rs 0.00</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom" style="padding-left:6.5rem;padding-right:1.5rem;">
                    <span class="text-secondary" style="font-size:14px;">&bull; Charges On Loan</span>
                    <span class="text-danger fw-medium" id="accLoanChg" style="font-size:14px;">Rs 0.00</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom" style="padding-left:6.5rem;padding-right:1.5rem;">
                    <span class="text-secondary" style="font-size:14px;">&bull; Processing Fee for Loans</span>
                    <span class="text-danger fw-medium" id="accLoanProc" style="font-size:14px;">Rs 0.00</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom" style="padding-left:5rem;padding-right:1.5rem;">
                    <span class="text-dark" style="font-size:14px;">&rsaquo; Other Expenses</span>
                    <span class="text-danger fw-medium" id="accOtherExp" style="font-size:14px;">Rs 0.00</span>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between px-4 py-4 border-top">
            <span class="fw-bold text-dark" style="font-size:14px;">Net Profit (Incomes - Expenses)</span>
            <span class="fw-bold text-success" id="accNetProfitBot" style="font-size:14px;">Rs 0.00</span>
        </div>
    </div>
</div>

<script>
(function () {
    const fmt  = v => 'Rs ' + parseFloat(v||0).toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2});
    const $id  = id => document.getElementById(id);
    const today    = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const toYMD    = d => d.toISOString().split('T')[0];

    $id('pnlFrom').value = toYMD(firstDay);
    $id('pnlTo').value   = toYMD(today);

    /* ── view toggle ── */
    document.querySelectorAll('input[name="pnlViewType"]').forEach(r => {
        r.addEventListener('change', function() {
            const acc = this.value === 'accounting';
            $id('pnlVyaparView').classList.toggle('d-none', acc);
            $id('pnlAccountingView').classList.toggle('d-none', !acc);
            $id('pnlExpandAllBtn').classList.toggle('d-none', !acc);
        });
    });

    /* ── accordion ── */
    document.addEventListener('click', function(e) {
        const hdr = e.target.closest('.pnl-acc-hdr');
        if (!hdr) return;
        const body = $id(hdr.dataset.body);
        if (!body) return;
        const hidden = body.style.display === 'none';
        body.style.display = hidden ? '' : 'none';
        const icon = hdr.querySelector('.pnl-ci');
        if (icon) {
            icon.classList.toggle('fa-chevron-down', hidden);
            icon.classList.toggle('fa-chevron-right', !hidden);
        }
    });

    /* ── expand all ── */
    $id('pnlExpandAllBtn') && $id('pnlExpandAllBtn').addEventListener('click', () => {
        document.querySelectorAll('#pnlAccountingView [id^="pnl-"]').forEach(b => b.style.display = '');
        document.querySelectorAll('#pnlAccountingView .pnl-ci').forEach(i => {
            i.classList.remove('fa-chevron-right'); i.classList.add('fa-chevron-down');
        });
    });

    /* ── apply ── */
    $id('pnlApplyBtn').addEventListener('click', loadPnL);

    /* ════════════════════════
       PRINT — hides sidebar
    ════════════════════════ */
    $id('pnlPrintBtn').addEventListener('click', function() {
        const sidebar  = document.querySelector('.reports-sidebar, aside');
        const allTabs  = document.querySelectorAll('.report-tab-content');
        const origSide = sidebar ? sidebar.style.display : null;

        // Hide sidebar
        if (sidebar) sidebar.style.display = 'none';

        // Hide all tabs except P&L
        allTabs.forEach(t => {
            t._prev = t.style.display;
            if (t.id !== 'tab-ProfitAndLoss') t.style.display = 'none';
        });

        window.print();

        // Restore after print dialog closes
        setTimeout(() => {
            if (sidebar && origSide !== null) sidebar.style.display = origSide;
            allTabs.forEach(t => { if (t._prev !== undefined) t.style.display = t._prev; });
        }, 800);
    });

    /* ════════════════════════
       EXCEL — client-side CSV
       NO ROUTE NEEDED
    ════════════════════════ */
    $id('pnlExcelBtn').addEventListener('click', function() {
        const csvRows = [['Particulars', 'Amount']];

        document.querySelectorAll('#pnlVyaparView > div').forEach(row => {
            const spans = row.querySelectorAll('span');
            if (spans.length >= 2) {
                const label  = spans[0].textContent.trim();
                const amount = spans[spans.length - 1].textContent.trim();
                if (label && amount !== '—') {
                    csvRows.push([label, amount]);
                }
            }
        });

        const csv  = csvRows.map(r => r.map(c => '"' + String(c).replace(/"/g,'""') + '"').join(',')).join('\n');
        const blob = new Blob([csv], {type:'text/csv;charset=utf-8;'});
        const url  = URL.createObjectURL(blob);
        const a    = document.createElement('a');
        a.href     = url;
        a.download = 'ProfitAndLoss_' + $id('pnlFrom').value + '_to_' + $id('pnlTo').value + '.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    });

    /* ════════════════════════
       FETCH
    ════════════════════════ */
    function loadPnL() {
        const from = $id('pnlFrom').value;
        const to   = $id('pnlTo').value;
        $id('pnlLoading').classList.remove('d-none');

        fetch('/reports/profit-loss?from=' + from + '&to=' + to, {
            headers: {'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}
        })
        .then(r => r.json())
        .then(data => {
            $id('pnlLoading').classList.add('d-none');
            if (data.success) renderPnL(data);
        })
        .catch(() => $id('pnlLoading').classList.add('d-none'));
    }

    /* ════════════════════════
       RENDER
    ════════════════════════ */
    function renderPnL(d) {
        const map = d.data || {};

        // Vyapar spans
        document.querySelectorAll('#pnlVyaparView span[data-pnl-key]').forEach(el => {
            const key = el.dataset.pnlKey;
            if (map[key] === undefined) return;
            const val = parseFloat(map[key]);
            el.textContent = fmt(val);
            el.className = el.className.replace(/text-(success|danger)/g,'').trim();
            const isTotal = ['gross_profit','net_profit'].includes(key);
            el.classList.add(val >= 0 ? 'text-success' : 'text-danger');
            if (isTotal) el.classList.add('fw-bold'); else el.classList.add('fw-medium');
        });

        // Accounting spans
        const a = d.accounting || {};
        const set = (id, val, pos) => {
            const el = $id(id); if (!el) return;
            el.textContent = fmt(val);
            el.className = el.className.replace(/text-(success|danger)/g,'').trim();
            el.classList.add(pos ? 'text-success' : 'text-danger');
        };
        const np = parseFloat(a.net_profit || 0);
        set('accNetProfitTop', np,                        np >= 0);
        set('accNetProfitBot', np,                        np >= 0);
        set('accTotalIncome',  a.total_income    || 0,   true);
        set('accTotalExp',     a.total_expenses  || 0,   false);
        set('accSaleAcc',      a.sale_accounts   || 0,   true);
        set('accSaleRevenue',  a.sale_revenue    || 0,   true);
        set('accCOGS',         a.cogs            || 0,   false);
        set('accPurchAcc',     a.purchase_accounts||0,   false);
        set('accOpenStock',    a.opening_stock   || 0,   false);
        set('accCloseStock',   a.closing_stock   || 0,   true);
        set('accDirExp',       a.direct_expenses || 0,   false);
        set('accPayinDisc',    a.payin_discount  || 0,   false);
        set('accIndirExp',     a.indirect_expenses||0,   false);
        set('accFinancing',    a.cost_financing  || 0,   false);
        set('accLoanChg',      a.loan_charges    || 0,   false);
        set('accLoanProc',     a.loan_processing || 0,   false);
        set('accOtherExp',     a.other_expenses  || 0,   false);
    }

    /* ── auto-load on tab show ── */
    const tabEl = $id('tab-ProfitAndLoss');
    if (tabEl) {
        new MutationObserver(m => {
            m.forEach(() => {
                if (!tabEl.classList.contains('d-none') && !tabEl.dataset.loaded) {
                    tabEl.dataset.loaded = '1';
                    loadPnL();
                }
            });
        }).observe(tabEl, {attributes:true, attributeFilter:['class']});
    }
})();
</script>