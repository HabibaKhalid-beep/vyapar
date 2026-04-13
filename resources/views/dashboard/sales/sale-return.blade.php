<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Vyapar - Sale Return / Credit Notes</title>
  <meta name="description" content="Manage sale return and credit notes in Vyapar.">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
  <style>
    .sale-return-page {
      padding: 1.25rem;
    }

    .sale-return-card {
      border: 0;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
    }

    .sale-return-toolbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 1rem;
      margin-bottom: 1.25rem;
      flex-wrap: wrap;
    }

    .sale-return-search {
      position: relative;
      min-width: 280px;
      max-width: 360px;
      width: 100%;
    }

    .sale-return-search i {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: #64748b;
    }

    .sale-return-search input {
      border-radius: 999px;
      border: 1px solid #d7deea;
      padding: 0.85rem 1rem 0.85rem 2.75rem;
      width: 100%;
      background: #fff;
    }

    .sale-return-add-btn {
      border-radius: 999px;
      background: #1d8cf8;
      border: 0;
      color: #fff;
      padding: 0.8rem 1.35rem;
      font-weight: 600;
      box-shadow: 0 10px 20px rgba(29, 140, 248, 0.18);
    }

    .sale-return-table {
      min-width: 1180px;
    }

    .sale-return-table thead th {
      background: #f8fbff;
      color: #334155;
      font-size: 0.92rem;
      font-weight: 700;
      border-bottom: 1px solid #dbe4f0;
      padding: 1rem 0.85rem;
      vertical-align: middle;
      white-space: nowrap;
    }

    .sale-return-table tbody td {
      padding: 1rem 0.85rem;
      border-bottom: 1px solid #edf2f7;
      vertical-align: middle;
      color: #0f172a;
      white-space: nowrap;
    }

    .sale-return-table tbody tr:hover {
      background: #f8fbff;
    }

    .status-pill {
      display: inline-flex;
      align-items: center;
      border-radius: 999px;
      padding: 0.38rem 0.8rem;
      font-size: 0.83rem;
      font-weight: 600;
    }

    .status-pill.paid {
      background: #e9f9ef;
      color: #16a34a;
    }

    .status-pill.partial {
      background: #eef4ff;
      color: #2563eb;
    }

    .status-pill.unpaid {
      background: #fff4e8;
      color: #f97316;
    }

    .icon-action {
      border: 0;
      background: transparent;
      color: #64748b;
      padding: 0.2rem 0.35rem;
      font-size: 1.1rem;
    }

    .action-menu-btn {
      border: 0;
      background: transparent;
      color: #64748b;
      padding: 0.2rem 0.35rem;
    }

    .action-menu-btn::after {
      display: none;
    }
  </style>

  <script>
    const authUser = @json(Auth::user());
    window.App = window.App || {
      isAuthenticated: @json(Auth::check()),
      user: authUser ? {
        id: authUser.id,
        name: authUser.name,
        roles: @json(Auth::user()?->roles()->pluck('name')->toArray() ?? []),
        permissions: @json(Auth::user()?->getAllPermissions() ?? []),
      } : { id: null, name: null, roles: [], permissions: [] },
      logoutUrl: "{{ route('logout') }}",
      csrfToken: "{{ csrf_token() }}",
    };
  </script>
</head>

<body data-page="sales-return">
  <main class="main-content sale-return-page" id="mainContent">
    <div class="card sale-return-card">
      <div class="card-body">
        <div class="row g-2 mb-1">
          <p class="fw-bold mb-0">Transactions</p>
        </div>

        <div class="sale-return-toolbar">
          <form method="GET" action="{{ route('sale-return') }}" class="sale-return-search">
            <i class="bi bi-search"></i>
            <input type="text" name="search" placeholder="Search Transactions" value="{{ $search ?? '' }}">
          </form>

          <button class="btn sale-return-add-btn" onclick="window.location='{{ route('sale-return.create') }}'">
            <i class="fa-solid fa-plus me-2"></i>Add Credit Note
          </button>
        </div>

        <div class="table-responsive">
          <table class="table sale-return-table align-middle mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Date</th>
                <th>Ref No.</th>
                <th>Party Name</th>
                <th>Type</th>
                <th class="text-end">Total</th>
                <th class="text-end">Received/Paid</th>
                <th class="text-end">Balance</th>
                <th>Status</th>
                <th>Print / Share</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @forelse($saleReturns as $index => $saleReturn)
                @php
                  $status = strtolower((string) ($saleReturn->status ?? 'unpaid'));
                  $statusClass = match ($status) {
                      'paid' => 'paid',
                      'partial' => 'partial',
                      default => 'unpaid',
                  };
                @endphp
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ optional($saleReturn->order_date ?? $saleReturn->invoice_date)->format('d/m/Y') ?? '-' }}</td>
                  <td>{{ $saleReturn->bill_number ?? '-' }}</td>
                  <td>{{ $saleReturn->display_party_name }}</td>
                  <td>Credit Note</td>
                  <td class="text-end">Rs {{ number_format($saleReturn->grand_total ?? 0, 2) }}</td>
                  <td class="text-end">Rs {{ number_format($saleReturn->received_amount ?? 0, 2) }}</td>
                  <td class="text-end">Rs {{ number_format($saleReturn->balance ?? 0, 2) }}</td>
                  <td>
                    <span class="status-pill {{ $statusClass }}">{{ ucfirst($status) }}</span>
                  </td>
                  <td>
                    <a href="#" onclick="openSaleReturnPrint('{{ route('invoice', ['sale_id' => $saleReturn->id, 'print' => 1]) }}'); return false;" class="icon-action" title="Print">
                      <i class="fa-solid fa-print"></i>
                    </a>
                    <div class="dropdown d-inline">
                      <button class="icon-action dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Share">
                        <i class="fa-solid fa-share-nodes"></i>
                      </button>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="shareSaleReturn('whatsapp', '{{ route('invoice', ['sale_id' => $saleReturn->id]) }}'); return false;"><i class="fa-brands fa-whatsapp me-2"></i>WhatsApp</a></li>
                        <li><a class="dropdown-item" href="#" onclick="shareSaleReturn('gmail', '{{ route('invoice', ['sale_id' => $saleReturn->id]) }}'); return false;"><i class="fa-solid fa-envelope me-2"></i>Gmail</a></li>
                        <li><a class="dropdown-item" href="#" onclick="shareSaleReturn('copy', '{{ route('invoice', ['sale_id' => $saleReturn->id]) }}'); return false;"><i class="fa-regular fa-copy me-2"></i>Copy Link</a></li>
                      </ul>
                    </div>
                  </td>
                  <td class="text-center">
                    <div class="dropdown">
                      <button class="btn btn-sm action-menu-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><a class="dropdown-item" href="{{ route('sale-return.edit', $saleReturn->id) }}"><i class="fas fa-edit me-2"></i>View/Edit</a></li>
                        <li><a class="dropdown-item" href="#" onclick="openSaleReturnPdf('{{ route('invoice', ['sale_id' => $saleReturn->id]) }}'); return false;"><i class="fas fa-file-pdf me-2"></i>Open PDF</a></li>
                        <li><a class="dropdown-item" href="#" onclick="openSaleReturnPreview('{{ route('invoice', ['sale_id' => $saleReturn->id]) }}'); return false;"><i class="fas fa-file-alt me-2"></i>Preview</a></li>
                        <li><a class="dropdown-item" href="#" onclick="openSaleReturnPrint('{{ route('invoice', ['sale_id' => $saleReturn->id, 'print' => 1]) }}'); return false;"><i class="fas fa-print me-2"></i>Print</a></li>
                        <li><a class="dropdown-item" href="#" onclick="viewSaleReturnHistory('{{ route('sale-return.bank-history', $saleReturn->id) }}'); return false;"><i class="fas fa-clock-rotate-left me-2"></i>View History</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteSaleReturn('{{ route('sale-return.destroy', $saleReturn->id) }}'); return false;"><i class="fas fa-trash me-2"></i>Delete</a></li>
                      </ul>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="11" class="text-center text-muted py-5">No credit notes found.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

  <div class="modal fade" id="saleReturnHistoryModal" tabindex="-1" aria-labelledby="saleReturnHistoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="saleReturnHistoryLabel">View History</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="table-responsive">
            <table class="table table-sm">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Bank</th>
                  <th>Type</th>
                  <th>Amount</th>
                  <th>Reference</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody id="saleReturnHistoryBody">
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">No history to show.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/components.js') }}"></script>
  <script src="{{ asset('js/common.js') }}"></script>
  <script>
    const saleReturnCsrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function openSaleReturnPdf(url) {
      window.open(url, '_blank');
    }

    function openSaleReturnPreview(url) {
      window.open(url, '_blank');
    }

    function openSaleReturnPrint(url) {
      window.open(url, '_blank');
    }

    function shareSaleReturn(channel, url) {
      const encoded = encodeURIComponent(url);
      if (channel === 'whatsapp') {
        window.open(`https://wa.me/?text=${encoded}`, '_blank');
        return;
      }

      if (channel === 'gmail') {
        window.open(`https://mail.google.com/mail/?view=cm&fs=1&su=Sale%20Return&body=${encoded}`, '_blank');
        return;
      }

      if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(() => alert('Link copied to clipboard.'));
      } else {
        window.prompt('Copy this link:', url);
      }
    }

    function viewSaleReturnHistory(historyUrl) {
      const modalEl = document.getElementById('saleReturnHistoryModal');
      const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
      const tbody = document.getElementById('saleReturnHistoryBody');
      tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">Loading...</td></tr>`;

      fetch(historyUrl, {
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': saleReturnCsrf,
        },
      })
        .then(res => res.json())
        .then(data => {
          const rows = (data.entries || []).map((entry, index) => `
            <tr>
              <td>${index + 1}</td>
              <td>${entry.bank_name || '-'}</td>
              <td>${entry.type || '-'}</td>
              <td>Rs ${Number(entry.amount || 0).toFixed(2)}</td>
              <td>${entry.reference || '-'}</td>
              <td>${entry.date || '-'}</td>
            </tr>
          `).join('');

          tbody.innerHTML = rows || `<tr><td colspan="6" class="text-center text-muted py-4">No history found.</td></tr>`;
          modal.show();
        })
        .catch(() => {
          tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">Unable to load history.</td></tr>`;
          modal.show();
        });
    }

    function deleteSaleReturn(url) {
      if (!confirm('Are you sure you want to delete this credit note?')) {
        return;
      }

      fetch(url, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': saleReturnCsrf,
          'Accept': 'application/json',
        },
      })
        .then(async (response) => {
          const data = await response.json();

          if (!response.ok) {
            throw new Error(data.message || 'Delete failed');
          }

          window.location.reload();
        })
        .catch((error) => {
          alert(error.message || 'Unable to delete credit note.');
        });
    }
  </script>
</body>

</html>
