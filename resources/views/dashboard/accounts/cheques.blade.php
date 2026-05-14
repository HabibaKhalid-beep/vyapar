@extends('layouts.app')

@section('title', 'Cheques')

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
            <h4 class="mb-0 fw-semibold text-dark">
                Cheques
                <span class="text-separator mx-2" style="color: #3B82F6;">|</span>
                <span class="text-primary fw-bold" style="color: #3B82F6;">
                    Rs {{ number_format($totalBalance, 2) }}
                </span>
            </h4>
        </div>

        <button class="btn btn-danger px-4 py-2 fw-semibold d-flex align-items-center gap-2"
                data-bs-toggle="modal" data-bs-target="#adjustChequeModal"
                style="background-color: #E53E3E; border: none; border-radius: 8px;">
            <i class="fas fa-sliders-h"></i>
            Adjust Cheque
        </button>
    </div>

    {{-- Summary Cards (optional, mirroring cash-hand pattern) --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3">
                <p class="text-muted small mb-1">Total Cheques In</p>
                <h5 class="fw-bold text-success mb-0">Rs {{ number_format($totalIn ?? 0, 2) }}</h5>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3">
                <p class="text-muted small mb-1">Total Cheques Out</p>
                <h5 class="fw-bold text-danger mb-0">Rs {{ number_format($totalOut ?? 0, 2) }}</h5>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3">
                <p class="text-muted small mb-1">Pending Cheques</p>
                <h5 class="fw-bold text-warning mb-0">{{ $pendingCount ?? 0 }}</h5>
            </div>
        </div>
    </div>

    {{-- Transactions Table --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="d-flex align-items-center justify-content-between px-4 pt-4 pb-3">
                <h6 class="fw-semibold mb-0">Transactions</h6>
                <button class="btn btn-sm btn-outline-secondary rounded-circle p-2">
                    <i class="fas fa-search" style="font-size: 12px;"></i>
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3 text-muted fw-semibold" style="font-size: 13px;">
                                Type
                                <i class="fas fa-caret-down ms-1 text-muted" style="font-size: 11px;"></i>
                            </th>
                            <th class="py-3 text-muted fw-semibold" style="font-size: 13px;">
                                Name
                                <i class="fas fa-caret-down ms-1 text-muted" style="font-size: 11px;"></i>
                            </th>
                            <th class="py-3 text-muted fw-semibold" style="font-size: 13px;">
                                Cheque No.
                                <i class="fas fa-caret-down ms-1 text-muted" style="font-size: 11px;"></i>
                            </th>
                            <th class="py-3 text-muted fw-semibold" style="font-size: 13px;">
                                Date
                                <i class="fas fa-caret-down ms-1 text-muted" style="font-size: 11px;"></i>
                            </th>
                            <th class="py-3 text-muted fw-semibold" style="font-size: 13px;">
                                Status
                                <i class="fas fa-caret-down ms-1 text-muted" style="font-size: 11px;"></i>
                            </th>
                            <th class="py-3 pe-4 text-muted fw-semibold text-end" style="font-size: 13px;">
                                Amount
                                <i class="fas fa-caret-down ms-1 text-muted" style="font-size: 11px;"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                       
    @forelse($transactions as $transaction)
    <tr>
        {{-- TYPE --}}
        <td class="px-4 py-3" style="font-size: 13px; font-weight: 500; color: #374151;">
            {{ strtoupper($transaction->type) }}
        </td>
        {{-- NAME --}}
        <td class="py-3" style="font-size: 13px; color: #374151;">
            {{ $transaction->name }}
        </td>
        {{-- CHEQUE NO --}}
        <td class="py-3" style="font-size: 13px; color: #374151;">
            {{ $transaction->cheque_number ?? '—' }}
        </td>
        {{-- DATE --}}
        <td class="py-3" style="font-size: 13px; color: #374151;">
            {{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}
        </td>
        {{-- STATUS DROPDOWN --}}
        <td class="py-3">
            <div class="dropdown">
                <button class="badge rounded-pill px-3 py-1 border-0 dropdown-toggle"
                        data-bs-toggle="dropdown"
                        style="cursor:pointer; font-size:11px; font-weight:500;
                            {{ $transaction->status === 'cleared' ? 'background:#D1FAE5; color:#065F46;' : '' }}
                            {{ $transaction->status === 'pending' ? 'background:#FEF3C7; color:#92400E;' : '' }}
                            {{ $transaction->status === 'bounced' ? 'background:#FEE2E2; color:#991B1B;' : '' }}">
                    {{ ucfirst($transaction->status ?? 'Unknown') }}
                </button>
                <ul class="dropdown-menu shadow border-0 rounded-3" style="min-width:130px;">
                    <li>
                        <form action="{{ route('cheques.update', $transaction->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="pending">
                            <button type="submit" class="dropdown-item" style="font-size:13px;">
                                <span class="badge rounded-pill me-2"
                                      style="background:#FEF3C7; color:#92400E;">Pending</span>
                            </button>
                        </form>
                    </li>
                    <li>
                        <form action="{{ route('cheques.update', $transaction->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="cleared">
                            <button type="submit" class="dropdown-item" style="font-size:13px;">
                                <span class="badge rounded-pill me-2"
                                      style="background:#D1FAE5; color:#065F46;">Cleared</span>
                            </button>
                        </form>
                    </li>
                    <li>
                        <form action="{{ route('cheques.update', $transaction->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="bounced">
                            <button type="submit" class="dropdown-item" style="font-size:13px;">
                                <span class="badge rounded-pill me-2"
                                      style="background:#FEE2E2; color:#991B1B;">Bounced</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </td>
        {{-- AMOUNT --}}
        <td class="py-3 pe-4 text-end fw-semibold" style="font-size: 13px; color: #374151;">
            Rs {{ number_format($transaction->amount, 2) }}
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="6" class="text-center py-5 text-muted" style="font-size: 14px;">
            <i class="fas fa-file-invoice-dollar mb-2 d-block" style="font-size: 32px; opacity: 0.3;"></i>
            No cheque transactions found
        </td>
    </tr>
    @endforelse
</tbody>
                        
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($transactions->hasPages())
            <div class="px-4 py-3 border-top d-flex justify-content-end">
                {{ $transactions->links() }}
            </div>
            @endif

        </div>
    </div>
</div>

{{-- ===================== ADD CHEQUE MODAL ===================== --}}
<div class="modal fade" id="addChequeModal" tabindex="-1" aria-labelledby="addChequeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-semibold" id="addChequeModalLabel">Add Cheque</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pb-4">
               <form action="{{ route('cheques.store') }}" method="POST">
                    @csrf

                    {{-- Type Selection --}}
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-semibold mb-2">Type</label>
                        <div class="d-flex gap-2">
                            <input type="hidden" name="type" id="chequeTypeInput" value="CHEQUE_IN">

                            <button type="button" id="btnChequeIn"
                                class="btn flex-fill py-2 fw-semibold cheque-type-btn active-type"
                                onclick="setType('CHEQUE_IN')"
                                style="border-radius: 8px; font-size: 14px;
                                       background: #3B82F6; color: #fff; border: 1.5px solid #3B82F6;">
                                Cheque In
                            </button>

                            <button type="button" id="btnChequeOut"
                                class="btn flex-fill py-2 fw-semibold cheque-type-btn"
                                onclick="setType('CHEQUE_OUT')"
                                style="border-radius: 8px; font-size: 14px;
                                       background: #fff; color: #374151; border: 1.5px solid #D1D5DB;">
                                Cheque Out
                            </button>
                        </div>
                    </div>

                    {{-- Transfer to Cash toggle --}}
                    <div class="mb-3 p-3 rounded-3" style="background:#F0FDF4; border:1px solid #BBF7D0;">
                        <div class="form-check form-switch d-flex align-items-center gap-2 m-0">
                            <input class="form-check-input" type="checkbox" id="transferToCash"
                                   name="transfer_to_cash" value="1"
                                   style="width:40px; height:22px; cursor:pointer;">
                            <label class="form-check-label fw-semibold" for="transferToCash"
                                   style="font-size:13px; color:#065F46; cursor:pointer;">
                                <i class="fas fa-exchange-alt me-1"></i>
                                Also add to Cash in Hand
                            </label>
                        </div>
                        <p class="text-muted mb-0 mt-1" style="font-size:11px; padding-left:50px;">
                            Enable this to simultaneously record this amount in your Cash in Hand account.
                        </p>
                    </div>

                    {{-- Cheque Number --}}
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold" for="chequeNumber">
                            Cheque Number
                        </label>
                        <input type="text" class="form-control rounded-3" id="chequeNumber"
                               name="cheque_number" placeholder="e.g. 000123456"
                               style="border: 1.5px solid #E5E7EB; font-size: 14px;">
                    </div>

                    {{-- Party Name --}}
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold" for="partyName">
                            Party / Description
                        </label>
                        <input type="text" class="form-control rounded-3" id="partyName"
                               name="name" placeholder="e.g. Cheque received for invoice #2026-220" required
                               style="border: 1.5px solid #E5E7EB; font-size: 14px;">
                    </div>

                    {{-- Amount --}}
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold" for="chequeAmount">
                            Amount (Rs)
                        </label>
                        <input type="number" class="form-control rounded-3" id="chequeAmount"
                               name="amount" placeholder="0.00" min="0" step="0.01" required
                               style="border: 1.5px solid #E5E7EB; font-size: 14px;">
                    </div>

                    {{-- Date --}}
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold" for="chequeDate">
                            Date
                        </label>
                        <input type="date" class="form-control rounded-3" id="chequeDate"
                               name="date" value="{{ date('Y-m-d') }}" required
                               style="border: 1.5px solid #E5E7EB; font-size: 14px;">
                    </div>

                    {{-- Status --}}
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-semibold" for="chequeStatus">
                            Status
                        </label>
                        <select class="form-select rounded-3" id="chequeStatus" name="status"
                                style="border: 1.5px solid #E5E7EB; font-size: 14px;">
                            <option value="pending">Pending</option>
                            <option value="cleared">Cleared</option>
                            <option value="bounced">Bounced</option>
                        </select>
                    </div>

                    {{-- Notes --}}
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-semibold" for="chequeNotes">
                            Notes (optional)
                        </label>
                        <textarea class="form-control rounded-3" id="chequeNotes"
                                  name="notes" rows="2" placeholder="Any additional notes..."
                                  style="border: 1.5px solid #E5E7EB; font-size: 14px; resize:none;"></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary flex-fill py-2 rounded-3"
                                data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn flex-fill py-2 fw-semibold rounded-3"
                                style="background:#E53E3E; color:#fff; border:none;">
                            Save Cheque
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="adjustChequeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <h5 class="modal-title fw-semibold">Adjust Cheque</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <form action="{{ route('cheques.adjust') }}" method="POST">
                    @csrf

                    {{-- Add / Reduce radio --}}
                    <div class="d-flex gap-4 mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="adjust_type"
                                   id="addCheque" value="add" checked
                                   onchange="updateChequePreview()">
                            <label class="form-check-label fw-semibold" for="addCheque">
                                Add Cheque
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="adjust_type"
                                   id="reduceCheque" value="reduce"
                                   onchange="updateChequePreview()">
                            <label class="form-check-label fw-semibold" for="reduceCheque">
                                Reduce Cheque
                            </label>
                        </div>
                    </div>

                    {{-- Amount --}}
                    <div class="mb-1">
                        <label class="form-label fw-semibold" for="adjustAmountInput">
                            Enter Amount <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control rounded-3" id="adjustAmountInput"
                               name="amount" placeholder="" min="0" step="0.01" required
                               oninput="updateChequePreview()"
                               style="border: 1.5px solid #E5E7EB; font-size: 14px;">
                        <p class="mt-1 mb-0" style="font-size: 13px; color: #3B82F6;">
                            Updated Cheque: Rs <span id="updatedChequeBalance">{{ number_format($totalBalance, 0) }}</span>
                        </p>
                    </div>

                    {{-- Date --}}
                    <div class="mb-3 mt-3">
                        <label class="form-label fw-semibold" for="adjustDateInput">
                            Adjustment Date
                        </label>
                        <input type="date" class="form-control rounded-3" id="adjustDateInput"
                               name="date" value="{{ date('Y-m-d') }}"
                               style="border: 1.5px solid #E5E7EB; font-size: 14px;">
                    </div>

                    {{-- Description --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="adjustDescInput">
                            Description
                        </label>
                        <input type="text" class="form-control rounded-3" id="adjustDescInput"
                               name="reason" placeholder=""
                               style="border: 1.5px solid #E5E7EB; font-size: 14px;">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button"
                                class="btn px-4 py-2 rounded-pill fw-semibold"
                                data-bs-dismiss="modal"
                                style="background:#6B7280; color:#fff; border:none;">
                            Cancel
                        </button>
                        <button type="submit"
                                class="btn px-4 py-2 rounded-pill fw-semibold"
                                style="background:#E53E3E; color:#fff; border:none;">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const currentChequeBalance = {{ $totalBalance }};

function updateChequePreview() {
    const amount = parseFloat(document.getElementById('adjustAmountInput').value) || 0;
    const isAdd  = document.getElementById('addCheque').checked;
    const updated = isAdd ? currentChequeBalance + amount : currentChequeBalance - amount;
    document.getElementById('updatedChequeBalance').textContent = Math.round(updated).toLocaleString();
}

function setType(type) {
    document.getElementById('chequeTypeInput').value = type;
    const btnIn  = document.getElementById('btnChequeIn');
    const btnOut = document.getElementById('btnChequeOut');
    if (type === 'CHEQUE_IN') {
        btnIn.style.cssText  = 'border-radius:8px;font-size:14px;background:#3B82F6;color:#fff;border:1.5px solid #3B82F6;';
        btnOut.style.cssText = 'border-radius:8px;font-size:14px;background:#fff;color:#374151;border:1.5px solid #D1D5DB;';
    } else {
        btnOut.style.cssText = 'border-radius:8px;font-size:14px;background:#3B82F6;color:#fff;border:1.5px solid #3B82F6;';
        btnIn.style.cssText  = 'border-radius:8px;font-size:14px;background:#fff;color:#374151;border:1.5px solid #D1D5DB;';
    }
}
</script>
@endpush
