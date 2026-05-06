<div class="modal fade" id="bankAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-1">Add Bank Account</h5>
                    <p class="text-muted small mb-0">Save a bank account and use it immediately in payment type dropdowns.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="bankAccountForm" action="{{ route('bank-accounts.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="bankDisplayName" class="form-label">Account Display Name *</label>
                            <input type="text" class="form-control" id="bankDisplayName" name="display_name" placeholder="Enter account display name" required>
                        </div>
                        <div class="col-md-4">
                            <label for="bankOpeningBalance" class="form-label">Opening Balance</label>
                            <input type="number" class="form-control" id="bankOpeningBalance" name="opening_balance" min="0" step="0.01" placeholder="Enter opening balance">
                        </div>
                        <div class="col-md-4">
                            <label for="bankAsOfDate" class="form-label">As of Date *</label>
                            <input type="date" class="form-control" id="bankAsOfDate" name="as_of_date" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-link p-0 add-more-fields-btn">+ Add More Fields</button>
                    </div>

                    <div class="extra-fields d-none mt-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="bankAccountNumber" class="form-label">Account Number</label>
                                <input type="text" class="form-control" id="bankAccountNumber" name="account_number" placeholder="Enter account number">
                            </div>
                            <div class="col-md-4">
                                <label for="bankSwiftCode" class="form-label">SWIFT Code</label>
                                <input type="text" class="form-control" id="bankSwiftCode" name="swift_code" placeholder="Enter SWIFT code">
                            </div>
                            <div class="col-md-4">
                                <label for="bankIban" class="form-label">IBAN</label>
                                <input type="text" class="form-control" id="bankIban" name="iban" placeholder="Enter IBAN">
                            </div>
                            <div class="col-md-6">
                                <label for="bankName" class="form-label">Bank Name</label>
                                <input type="text" class="form-control" id="bankName" name="bank_name" placeholder="Enter bank name">
                            </div>
                            <div class="col-md-6">
                                <label for="bankAccountHolderName" class="form-label">Account Holder Name</label>
                                <input type="text" class="form-control" id="bankAccountHolderName" name="account_holder_name" placeholder="Enter account holder name">
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="bankPrintOnInvoice" name="print_on_invoice">
                                    <label class="form-check-label" for="bankPrintOnInvoice">
                                        Print Bank Details on Invoice
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary save-bank-account-btn">Save Details</button>
                </div>
            </form>
        </div>
    </div>
</div>
