<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Simple Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#f5f7fb; }
        .wrap { max-width: 1180px; margin: 28px auto; }
        .card-box { background:#fff; border:1px solid #e5eaf2; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); }
        .section-title { font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.08em; margin-bottom:10px; }
        .value-box { background:#f8fbff; border:1px solid #dbe5f0; border-radius:14px; padding:12px 14px; min-height:48px; }
        .summary-box { background:#f8fbff; border:1px solid #dbe5f0; border-radius:16px; padding:16px; }
        .summary-value { font-size:28px; font-weight:800; color:#15335a; }
        .print-note { font-size:12px; color:#64748b; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1">Simple Invoice / Parchi</h2>
            <p class="text-secondary mb-0">Auto-fill, auto-calculate, clean printable flow</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('market-invoices.store') }}" class="card-box p-4">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="section-title">Party</div>
                        <input list="partySuggestions" id="partySearch" class="form-control" placeholder="Search party by name" autocomplete="off">
                        <datalist id="partySuggestions">
                            @foreach($parties as $party)
                                <option value="{{ $party->name }}"
                                        data-id="{{ $party->id }}"
                                        data-city="{{ $party->city }}"
                                        data-phone="{{ $party->phone }}"
                                        data-address="{{ $party->address }}"
                                        data-billing="{{ $party->billing_address }}"
                                        data-shipping="{{ $party->shipping_address }}"></option>
                            @endforeach
                        </datalist>
                        <input type="hidden" name="party_id" id="partyId" required>
                    </div>
                    <div class="col-md-6">
                        <div class="section-title">City</div>
                        <input type="text" id="partyCity" class="form-control" readonly>
                    </div>
                    <div class="col-md-6">
                        <div class="section-title">Broker</div>
                        <input list="brokerSuggestions" id="brokerSearch" class="form-control" placeholder="Search broker by name" autocomplete="off">
                        <datalist id="brokerSuggestions">
                            @foreach($brokers as $broker)
                                <option value="{{ $broker->name }}"
                                        data-id="{{ $broker->id }}"
                                        data-phone="{{ $broker->phone }}"
                                        data-commission-type="{{ $broker->commission_type }}"
                                        data-commission-value="{{ $broker->commission_rate }}"></option>
                            @endforeach
                        </datalist>
                        <input type="hidden" name="broker_id" id="brokerId">
                    </div>
                    <div class="col-md-6">
                        <div class="section-title">Broker Phone</div>
                        <input type="text" id="brokerPhone" class="form-control" readonly>
                    </div>
                    <div class="col-md-4">
                        <div class="section-title">Tadad</div>
                        <input type="number" name="tadad" id="tadad" class="form-control" min="0" value="{{ old('tadad', 0) }}" required>
                    </div>
                    <div class="col-md-4">
                        <div class="section-title">Total Wazan</div>
                        <input type="number" step="0.01" name="total_wazan" id="totalWazan" class="form-control" min="0" value="{{ old('total_wazan', 0) }}">
                    </div>
                    <div class="col-md-4">
                        <div class="section-title">Safi Wazan</div>
                        <input type="number" step="0.01" name="safi_wazan" id="safiWazan" class="form-control" min="0" value="{{ old('safi_wazan', 0) }}" required>
                    </div>
                    <div class="col-md-4">
                        <div class="section-title">Rate</div>
                        <input type="number" step="0.01" name="rate" id="rate" class="form-control" min="0" value="{{ old('rate', 0) }}" required>
                    </div>
                    <div class="col-md-4">
                        <div class="section-title">Amount</div>
                        <input type="text" id="amountDisplay" class="form-control" readonly value="0.00">
                    </div>
                    <div class="col-md-4">
                        <div class="section-title">Deo</div>
                        <input type="text" name="deo" class="form-control" value="{{ old('deo') }}">
                    </div>
                </div>

                <hr class="my-4">

                <div class="section-title">Broker Commission</div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <select name="broker_commission_type" id="brokerCommissionType" class="form-select">
                            <option value="">Select Type</option>
                            <option value="fixed">Fixed</option>
                            <option value="percent">Percent</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="number" step="0.01" min="0" name="broker_commission_value" id="brokerCommissionValue" class="form-control" placeholder="Default / Override value">
                    </div>
                    <div class="col-md-4">
                        <input type="number" step="0.01" min="0" name="broker_commission" id="brokerCommissionAmount" class="form-control" placeholder="Applied commission">
                    </div>
                </div>

                <hr class="my-4">

                <div class="section-title">Ikhrajaat</div>
                <div class="row g-3">
                    <div class="col-md-4"><input type="number" step="0.01" min="0" name="bardana" class="form-control expense-input" placeholder="Bardana"></div>
                    <div class="col-md-4"><input type="number" step="0.01" min="0" name="mazdori" class="form-control expense-input" placeholder="Mazdori"></div>
                    <div class="col-md-4"><input type="number" step="0.01" min="0" name="rehra_mazdori" class="form-control expense-input" placeholder="Rehra Mazdori"></div>
                    <div class="col-md-4"><input type="number" step="0.01" min="0" name="dak_karaya" class="form-control expense-input" placeholder="Dak ka Karaya"></div>
                    <div class="col-md-4"><input type="number" step="0.01" min="0" name="brokeri" id="brokeriExpense" class="form-control expense-input" placeholder="Brokeri"></div>
                    <div class="col-md-4"><input type="number" step="0.01" min="0" name="local_izafi" class="form-control expense-input" placeholder="Local Izafi"></div>
                </div>

                <div class="mt-4">
                    <div class="section-title">Notes</div>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="summary-box">
                    <div class="section-title">Summary</div>
                    <div class="mb-3">
                        <span class="text-secondary d-block small">Amount</span>
                        <span class="summary-value" id="summaryAmount">0.00</span>
                    </div>
                    <div class="mb-3">
                        <span class="text-secondary d-block small">Total Expense</span>
                        <span class="summary-value" id="summaryExpense">0.00</span>
                    </div>
                    <div class="mb-3">
                        <span class="text-secondary d-block small">Final Amount</span>
                        <span class="summary-value" id="summaryFinal">0.00</span>
                    </div>
                    <div class="print-note mb-3">
                        Party print me sirf party name, city, tadad, wazan, rate aur amount jayega. Internal expenses hidden rahenge.
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save Invoice</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    const partyInput = document.getElementById('partySearch');
    const partyIdInput = document.getElementById('partyId');
    const partyCityInput = document.getElementById('partyCity');
    const brokerInput = document.getElementById('brokerSearch');
    const brokerIdInput = document.getElementById('brokerId');
    const brokerPhoneInput = document.getElementById('brokerPhone');
    const brokerTypeInput = document.getElementById('brokerCommissionType');
    const brokerValueInput = document.getElementById('brokerCommissionValue');
    const brokerAmountInput = document.getElementById('brokerCommissionAmount');
    const brokeriExpenseInput = document.getElementById('brokeriExpense');
    const safiWazanInput = document.getElementById('safiWazan');
    const rateInput = document.getElementById('rate');
    const amountDisplay = document.getElementById('amountDisplay');
    const summaryAmount = document.getElementById('summaryAmount');
    const summaryExpense = document.getElementById('summaryExpense');
    const summaryFinal = document.getElementById('summaryFinal');

    function findDatalistOption(listId, value) {
        return Array.from(document.querySelectorAll(`#${listId} option`)).find(option => option.value === value);
    }

    function toNumber(value) {
        const parsed = parseFloat(value || 0);
        return Number.isFinite(parsed) ? parsed : 0;
    }

    function syncParty() {
        const option = findDatalistOption('partySuggestions', partyInput.value);
        if (!option) {
            partyIdInput.value = '';
            partyCityInput.value = '';
            return;
        }

        partyIdInput.value = option.dataset.id || '';
        partyCityInput.value = option.dataset.city || '';
    }

    function calculateCommission(amount, safiWazan) {
        const type = brokerTypeInput.value;
        const value = toNumber(brokerValueInput.value);

        if (!type || value <= 0) {
            return 0;
        }

        if (type === 'percent') {
            return amount * (value / 100);
        }

        return safiWazan * value;
    }

    function updateCalculations() {
        const safiWazan = toNumber(safiWazanInput.value);
        const rate = toNumber(rateInput.value);
        const amount = safiWazan * rate;
        const commission = calculateCommission(amount, safiWazan);

        if (!document.activeElement || document.activeElement !== brokerAmountInput) {
            brokerAmountInput.value = commission.toFixed(2);
        }

        if (!document.activeElement || document.activeElement !== brokeriExpenseInput) {
            brokeriExpenseInput.value = commission.toFixed(2);
        }

        const totalExpense = Array.from(document.querySelectorAll('.expense-input'))
            .reduce((sum, input) => sum + toNumber(input.value), 0);

        const finalAmount = amount - totalExpense;

        amountDisplay.value = amount.toFixed(2);
        summaryAmount.textContent = amount.toFixed(2);
        summaryExpense.textContent = totalExpense.toFixed(2);
        summaryFinal.textContent = finalAmount.toFixed(2);
    }

    function syncBroker() {
        const option = findDatalistOption('brokerSuggestions', brokerInput.value);
        if (!option) {
            brokerIdInput.value = '';
            brokerPhoneInput.value = '';
            return updateCalculations();
        }

        brokerIdInput.value = option.dataset.id || '';
        brokerPhoneInput.value = option.dataset.phone || '';
        brokerTypeInput.value = option.dataset.commissionType || '';
        brokerValueInput.value = option.dataset.commissionValue || 0;
        updateCalculations();
    }

    partyInput.addEventListener('change', syncParty);
    partyInput.addEventListener('input', syncParty);
    brokerInput.addEventListener('change', syncBroker);
    brokerInput.addEventListener('input', syncBroker);
    [safiWazanInput, rateInput, brokerTypeInput, brokerValueInput, brokerAmountInput, brokeriExpenseInput, ...document.querySelectorAll('.expense-input')].forEach(input => {
        input.addEventListener('input', updateCalculations);
    });

    updateCalculations();
</script>
</body>
</html>
