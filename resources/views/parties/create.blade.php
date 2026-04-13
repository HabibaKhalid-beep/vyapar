@extends('layouts.app')

@section('title', 'Create Party')
@section('page', 'parties')

@push('styles')
<style>
.create-party-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 30px 20px;
}

.party-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 30px;
}

.party-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
}

.party-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: #111;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.party-header a {
    color: #2563eb;
    text-decoration: none;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.party-header a:hover {
    text-decoration: underline;
}

.tab-content {
    margin-top: 20px;
}

.form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
}

.form-control, .form-select {
    border-radius: 6px;
    border: 1px solid #ddd;
    padding: 10px 12px;
}

.form-control:focus, .form-select:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.required::after {
    content: ' *';
    color: #ef4444;
}

.button-group {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #f0f0f0;
}

.btn-cancel {
    padding: 10px 20px;
    border-radius: 6px;
    border: 1px solid #ddd;
    background: #f3f4f6;
    color: #6b7280;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.2s;
}

.btn-cancel:hover {
    background: #e5e7eb;
    color: #374151;
}

.btn-save {
    padding: 10px 24px;
    border-radius: 6px;
    border: none;
    background: #2563eb;
    color: #fff;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.2s;
}

.btn-save:hover {
    background: #1d4ed8;
}

.btn-save:disabled {
    background: #cbd5e1;
    cursor: not-allowed;
}

.nav-tabs {
    border-bottom: 2px solid #f0f0f0;
}

.nav-tabs .nav-link {
    color: #6b7280;
    border: none;
    border-bottom: 3px solid transparent;
    padding: 12px 16px;
    font-weight: 500;
    transition: all 0.2s;
}

.nav-tabs .nav-link:hover {
    color: #374151;
}

.nav-tabs .nav-link.active {
    color: #2563eb;
    border-bottom-color: #2563eb;
}

.input-group-text {
    background: #f3f4f6;
    border: 1px solid #ddd;
}
</style>
@endpush

@section('content')
<div class="create-party-container">
    <div class="party-card">
        <div class="party-header">
            <h1>
                <i class="fa-solid fa-user-plus" style="color: #2563eb;"></i>
                Create Party
            </h1>
            <a href="javascript:history.back()">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>

        <form id="createPartyForm" method="POST" action="{{ route('parties.store') }}">
            @csrf
            <input type="hidden" name="return_url" value="{{ $returnUrl ?? '' }}">

            <!-- Basic Information -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label required">Party Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter party name" required>
                    @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone Number</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                        <input type="tel" name="phone" class="form-control" placeholder="Enter phone number">
                    </div>
                    @error('phone')<small class="text-danger">{{ $message }}</small>@enderror
                </div>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="party-address-tab" data-bs-toggle="tab" data-bs-target="#partyAddressPane" type="button" role="tab">
                        <i class="fa-solid fa-location-dot me-1"></i> Address
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="party-credit-tab" data-bs-toggle="tab" data-bs-target="#partyCreditPane" type="button" role="tab">
                        <i class="fa-solid fa-credit-card me-1"></i> Credit & Balance
                    </button>
                </li>
            </ul>

            <div class="tab-content pt-3">
                <!-- Address Tab -->
                <div class="tab-pane fade show active" id="partyAddressPane" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Email ID</label>
                            <input type="email" name="email" class="form-control" placeholder="example@email.com">
                            @error('email')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" placeholder="Enter city">
                            @error('city')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Billing Address</label>
                            <textarea name="billing_address" class="form-control" rows="3" placeholder="Enter billing address"></textarea>
                            @error('billing_address')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Shipping Address</label>
                            <textarea name="shipping_address" class="form-control" rows="3" placeholder="Enter shipping address"></textarea>
                            @error('shipping_address')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </div>

                <!-- Credit & Balance Tab -->
                <div class="tab-pane fade" id="partyCreditPane" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Opening Balance</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="opening_balance" class="form-control" placeholder="0.00" step="0.01">
                            </div>
                            @error('opening_balance')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">As Of Date</label>
                            <input type="date" name="as_of_date" class="form-control" value="{{ date('Y-m-d') }}">
                            @error('as_of_date')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Party Type</label>
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="party_type[]" value="customer" id="customerParty">
                                    <label class="form-check-label" for="customerParty">Customer</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="party_type[]" value="supplier" id="supplierParty">
                                    <label class="form-check-label" for="supplierParty">Supplier</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="form-label">Transaction Type</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="transaction_type" value="receive" id="toReceive">
                                <label class="form-check-label" for="toReceive">To Receive</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="transaction_type" value="pay" id="toPay">
                                <label class="form-check-label" for="toPay">To Pay</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="button-group">
                <button type="button" class="btn-cancel" onclick="window.history.back()">Cancel</button>
                <button type="submit" class="btn-save" id="savePartyBtn">
                    <i class="fa-solid fa-check me-1"></i> Create Party
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('createPartyForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);

    // Show loading state
    const saveBtn = document.getElementById('savePartyBtn');
    const originalText = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Creating...';

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            // Check if there's a return URL
            const returnUrl = form.querySelector('input[name="return_url"]').value;
            if(returnUrl) {
                window.location.href = returnUrl;
            } else {
                window.location.href = '{{ route('parties') }}';
            }
        } else {
            alert(data.message || 'Error creating party');
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
    })
    .catch(err => {
        console.error(err);
        alert('Something went wrong! Check console for details.');
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
    });
});
</script>
@endsection
