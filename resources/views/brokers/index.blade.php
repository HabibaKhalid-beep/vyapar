@extends('layouts.app')

@section('title', 'Vyapar - Brokers')
@section('description', 'Manage brokers, commission rates, and brokerage balances.')
@section('page', 'brokers')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/brokers.css') }}">
@endpush

@section('content')
<div class="brokers-page">
  <div class="brokers-header">
    <div>
      <p class="brokers-eyebrow">Broker Management</p>
      <h1 class="brokers-title">Brokers</h1>
      <p class="brokers-subtitle">Track commission setup, brokerage balances, and broker contact details in one place.</p>
    </div>
    <button type="button" class="brokers-add-btn" data-bs-toggle="modal" data-bs-target="#brokerModal">
      <i class="fa-solid fa-plus"></i>
      Add Broker
    </button>
  </div>

  @if (session('success'))
    <div class="alert alert-success brokers-alert" role="alert">
      {{ session('success') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger brokers-alert" role="alert">
      <strong>Please fix the highlighted broker form fields.</strong>
    </div>
  @endif

  <div class="brokers-metrics">
    <div class="brokers-metric-card">
      <span class="brokers-metric-label">Total Brokers</span>
      <strong>{{ $metrics['total_brokers'] }}</strong>
    </div>
    <div class="brokers-metric-card">
      <span class="brokers-metric-label">Active Brokers</span>
      <strong>{{ $metrics['active_brokers'] }}</strong>
    </div>
    <div class="brokers-metric-card">
      <span class="brokers-metric-label">Total Brokerage</span>
      <strong>Rs {{ number_format($metrics['total_brokerage'], 2) }}</strong>
    </div>
    <div class="brokers-metric-card">
      <span class="brokers-metric-label">Remaining Brokerage</span>
      <strong>Rs {{ number_format($metrics['remaining_brokerage'], 2) }}</strong>
    </div>
  </div>

  <div class="brokers-panel">
    <div class="brokers-panel-toolbar">
      <div class="brokers-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="brokerSearchInput" placeholder="Search broker name, phone, or city">
      </div>
    </div>

    <div class="table-responsive">
      <table class="table brokers-table align-middle mb-0">
        <thead>
          <tr>
            <th>Name</th>
            <th>Phone</th>
            <th>City</th>
            <th>Commission</th>
            <th>Total</th>
            <th>Paid</th>
            <th>Remaining</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody id="brokerTableBody">
          @forelse ($brokers as $broker)
            @php
              $remaining = (float) ($broker->remaining_brokerage ?? $broker->remaining_amount);
            @endphp
            <tr
              data-broker-row
              data-search="{{ strtolower(trim($broker->name . ' ' . ($broker->phone ?? '') . ' ' . ($broker->city ?? ''))) }}"
            >
              <td>
                <div class="brokers-name-cell">
                  <strong>{{ $broker->name }}</strong>
                  <span>{{ $broker->address ?: 'No address added' }}</span>
                </div>
              </td>
              <td>{{ $broker->phone ?: '-' }}</td>
              <td>{{ $broker->city ?: '-' }}</td>
              <td>{{ $broker->commission_type === 'percent' ? 'Percent' : 'Fixed' }} ({{ $broker->commission_label }})</td>
              <td>Rs {{ number_format((float) $broker->total_brokerage, 2) }}</td>
              <td>Rs {{ number_format((float) $broker->paid_brokerage, 2) }}</td>
              <td>Rs {{ number_format($remaining, 2) }}</td>
              <td>
                <span class="brokers-status {{ $broker->status ? 'active' : 'inactive' }}">
                  {{ $broker->status ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="text-end">
                <div class="brokers-actions">
                  <button
                    type="button"
                    class="btn btn-sm brokers-edit-btn"
                    data-bs-toggle="modal"
                    data-bs-target="#brokerModal"
                    data-broker-id="{{ $broker->id }}"
                    data-broker-name="{{ $broker->name }}"
                    data-broker-phone="{{ $broker->phone }}"
                    data-broker-city="{{ $broker->city }}"
                    data-broker-address="{{ $broker->address }}"
                    data-broker-commission-type="{{ $broker->commission_type }}"
                    data-broker-commission-rate="{{ number_format((float) $broker->commission_rate, 2, '.', '') }}"
                    data-broker-total="{{ number_format((float) $broker->total_brokerage, 2, '.', '') }}"
                    data-broker-paid="{{ number_format((float) $broker->paid_brokerage, 2, '.', '') }}"
                    data-broker-notes="{{ $broker->notes }}"
                    data-broker-status="{{ $broker->status ? 1 : 0 }}"
                  >
                    Edit
                  </button>

                  <form action="{{ route('brokers.destroy', $broker) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this broker?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm brokers-delete-btn">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr id="brokerEmptyState">
              <td colspan="9" class="text-center py-5 text-muted">
                No brokers found. Add your first broker to start tracking brokerage.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@section('modals')
<div class="modal fade" id="brokerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content broker-modal-card">
      <form method="POST" id="brokerForm" action="{{ route('brokers.store') }}">
        @csrf
        <input type="hidden" name="_method" id="brokerFormMethod" value="POST">

        <div class="modal-header broker-modal-header">
          <div>
            <h5 class="modal-title" id="brokerModalTitle">Add Broker</h5>
            <p class="broker-modal-subtitle mb-0">Save broker details, commission setup, and balance tracking.</p>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Broker Name</label>
              <input type="text" class="form-control" name="name" id="brokerName" value="{{ old('name') }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone</label>
              <input type="text" class="form-control" name="phone" id="brokerPhone" value="{{ old('phone') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">City</label>
              <input type="text" class="form-control" name="city" id="brokerCity" value="{{ old('city') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Status</label>
              <div class="form-check form-switch broker-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="brokerStatus" name="status" value="1" {{ old('status', 1) ? 'checked' : '' }}>
                <label class="form-check-label" for="brokerStatus">Keep broker active</label>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Commission Type</label>
              <select class="form-select" name="commission_type" id="brokerCommissionType" required>
                <option value="fixed" {{ old('commission_type') === 'fixed' ? 'selected' : '' }}>Fixed</option>
                <option value="percent" {{ old('commission_type') === 'percent' ? 'selected' : '' }}>Percent</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Commission Rate</label>
              <input type="number" step="0.01" min="0" class="form-control" name="commission_rate" id="brokerCommissionRate" value="{{ old('commission_rate', 0) }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">Total Brokerage</label>
              <input type="number" step="0.01" min="0" class="form-control" name="total_brokerage" id="brokerTotalBrokerage" value="{{ old('total_brokerage', 0) }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">Paid Brokerage</label>
              <input type="number" step="0.01" min="0" class="form-control" name="paid_brokerage" id="brokerPaidBrokerage" value="{{ old('paid_brokerage', 0) }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">Remaining</label>
              <input type="text" class="form-control" id="brokerRemainingPreview" value="Rs 0.00" readonly>
            </div>
            <div class="col-12">
              <label class="form-label">Address</label>
              <textarea class="form-control" name="address" id="brokerAddress" rows="2">{{ old('address') }}</textarea>
            </div>
            <div class="col-12">
              <label class="form-label">Notes</label>
              <textarea class="form-control" name="notes" id="brokerNotes" rows="3">{{ old('notes') }}</textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer broker-modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn brokers-submit-btn">Save Broker</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  (function () {
    const brokerModal = document.getElementById('brokerModal');
    const brokerForm = document.getElementById('brokerForm');
    const brokerModalTitle = document.getElementById('brokerModalTitle');
    const brokerFormMethod = document.getElementById('brokerFormMethod');
    const searchInput = document.getElementById('brokerSearchInput');
    const brokerRows = Array.from(document.querySelectorAll('[data-broker-row]'));
    const emptyStateRow = document.getElementById('brokerEmptyState');

    const fields = {
      name: document.getElementById('brokerName'),
      phone: document.getElementById('brokerPhone'),
      city: document.getElementById('brokerCity'),
      address: document.getElementById('brokerAddress'),
      commissionType: document.getElementById('brokerCommissionType'),
      commissionRate: document.getElementById('brokerCommissionRate'),
      total: document.getElementById('brokerTotalBrokerage'),
      paid: document.getElementById('brokerPaidBrokerage'),
      notes: document.getElementById('brokerNotes'),
      status: document.getElementById('brokerStatus'),
      remaining: document.getElementById('brokerRemainingPreview'),
    };

    const formatCurrency = (value) => `Rs ${Number(value || 0).toFixed(2)}`;

    const updateRemainingPreview = () => {
      const total = Number(fields.total.value || 0);
      const paid = Number(fields.paid.value || 0);
      const remaining = Math.max(0, total - paid);
      fields.remaining.value = formatCurrency(remaining);
    };

    const resetFormForCreate = () => {
      brokerForm.action = "{{ route('brokers.store') }}";
      brokerFormMethod.value = 'POST';
      brokerModalTitle.textContent = 'Add Broker';
      brokerForm.reset();
      fields.status.checked = true;
      fields.commissionType.value = 'fixed';
      fields.commissionRate.value = '0';
      fields.total.value = '0';
      fields.paid.value = '0';
      updateRemainingPreview();
    };

    document.querySelectorAll('.brokers-edit-btn').forEach((button) => {
      button.addEventListener('click', () => {
        const brokerId = button.dataset.brokerId;
        brokerForm.action = `/dashboard/brokers/${brokerId}`;
        brokerFormMethod.value = 'PUT';
        brokerModalTitle.textContent = 'Edit Broker';
        fields.name.value = button.dataset.brokerName || '';
        fields.phone.value = button.dataset.brokerPhone || '';
        fields.city.value = button.dataset.brokerCity || '';
        fields.address.value = button.dataset.brokerAddress || '';
        fields.commissionType.value = button.dataset.brokerCommissionType || 'fixed';
        fields.commissionRate.value = button.dataset.brokerCommissionRate || '0';
        fields.total.value = button.dataset.brokerTotal || '0';
        fields.paid.value = button.dataset.brokerPaid || '0';
        fields.notes.value = button.dataset.brokerNotes || '';
        fields.status.checked = button.dataset.brokerStatus === '1';
        updateRemainingPreview();
      });
    });

    if (brokerModal) {
      brokerModal.addEventListener('show.bs.modal', (event) => {
        const trigger = event.relatedTarget;
        if (!trigger || !trigger.classList.contains('brokers-edit-btn')) {
          resetFormForCreate();
        }
      });
    }

    [fields.total, fields.paid].forEach((input) => {
      input?.addEventListener('input', updateRemainingPreview);
    });

    searchInput?.addEventListener('input', () => {
      const query = searchInput.value.trim().toLowerCase();
      let visibleRows = 0;

      brokerRows.forEach((row) => {
        const haystack = row.dataset.search || '';
        const matches = !query || haystack.includes(query);
        row.style.display = matches ? '' : 'none';
        if (matches) visibleRows += 1;
      });

      if (emptyStateRow) {
        emptyStateRow.style.display = visibleRows === 0 ? '' : 'none';
      }
    });

    updateRemainingPreview();

    @if ($errors->any())
      const modal = bootstrap.Modal.getOrCreateInstance(brokerModal);
      modal.show();
    @endif
  })();
</script>
@endpush
