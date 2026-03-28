@php
  $role_permissions = $role_permissions ?? [];
  $role = $role ?? null;
  $selectedPermissions = old('permissions', $role_permissions);

  $selectedRadio = old('radio_option', []);
  if (empty($selectedRadio) && !empty($role_permissions)) {
    $selectedRadio = [
      'supplier_view' => in_array('supplier.view', $role_permissions) ? 'supplier.view' : (in_array('supplier.view_own', $role_permissions) ? 'supplier.view_own' : ''),
      'purchase_view' => in_array('purchase.view', $role_permissions) ? 'purchase.view' : (in_array('view_own_purchase', $role_permissions) ? 'view_own_purchase' : ''),
    ];
  }

  $hasPurchaseCreate = in_array('purchase.create', $selectedPermissions);
@endphp

<style>
  .role-builder { max-width: 1180px; margin: 0 auto; }
  .role-builder-hero {
    background: linear-gradient(135deg, #0f172a, #0f766e 58%, #7dd3fc);
    border-radius: 28px;
    padding: 28px 30px;
    color: #fff;
    box-shadow: 0 24px 60px rgba(15, 23, 42, 0.16);
    position: relative;
    overflow: hidden;
    margin-bottom: 22px;
  }
  .role-builder-hero::after {
    content: '';
    position: absolute;
    right: -60px;
    bottom: -90px;
    width: 240px;
    height: 240px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.12);
  }
  .role-builder-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 28px;
    box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
    overflow: hidden;
  }
  .role-builder-card-inner { padding: 28px; }
  .role-section-tag {
    font-size: 0.78rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 1rem;
  }
  .role-name-control {
    border: 1px solid #cbd5e1;
    border-radius: 18px;
    min-height: 54px;
    padding: 0.9rem 1rem;
    box-shadow: none;
  }
  .role-name-control:focus {
    border-color: #0f766e;
    box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.12);
  }
  .permission-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 18px;
  }
  .permission-card {
    border: 1px solid #dbe4f0;
    border-radius: 24px;
    background: linear-gradient(180deg, #ffffff, #f8fafc);
    overflow: hidden;
  }
  .permission-card-header {
    padding: 18px 18px 12px;
    border-bottom: 1px solid #eef2f7;
    background: linear-gradient(180deg, rgba(240, 253, 250, 0.9), rgba(255, 255, 255, 0.95));
  }
  .permission-card-header h4 {
    margin: 0;
    font-size: 1rem;
    color: #0f172a;
    font-weight: 700;
  }
  .permission-card-header p {
    margin: 0.35rem 0 0;
    color: #64748b;
    font-size: 0.9rem;
  }
  .permission-card-body { padding: 16px 18px 18px; }
  .permission-stack { display: grid; gap: 10px; }
  .permission-substack {
    display: grid;
    gap: 10px;
    margin-left: 10px;
    padding-left: 14px;
    border-left: 2px dashed #dbe4f0;
  }
  .permission-item { position: relative; }
  .permission-item input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
  }
  .permission-box {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    border: 1px solid #dbe4f0;
    border-radius: 18px;
    background: #fff;
    cursor: pointer;
    transition: 0.2s ease;
  }
  .permission-box:hover {
    border-color: #99f6e4;
    background: #f0fdfa;
    transform: translateY(-1px);
  }
  .permission-box-strong { background: linear-gradient(135deg, #ecfeff, #f8fafc); }
  .permission-indicator {
    width: 22px;
    height: 22px;
    border-radius: 7px;
    border: 2px solid #94a3b8;
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: 0.2s ease;
  }
  .permission-indicator::after {
    content: '';
    width: 9px;
    height: 9px;
    border-radius: 3px;
    background: #fff;
    transform: scale(0);
    transition: transform 0.2s ease;
  }
  .permission-item input:checked + .permission-box {
    border-color: #0f766e;
    background: linear-gradient(135deg, #ecfeff, #ccfbf1);
    box-shadow: 0 10px 20px rgba(15, 118, 110, 0.1);
  }
  .permission-item input:checked + .permission-box .permission-indicator {
    border-color: #0f766e;
    background: #0f766e;
  }
  .permission-item input:checked + .permission-box .permission-indicator::after { transform: scale(1); }
  .permission-title {
    display: block;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.3;
  }
  .permission-note {
    display: block;
    color: #64748b;
    font-size: 0.84rem;
    margin-top: 0.1rem;
  }
  .permission-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 14px;
    flex-wrap: wrap;
  }
  .permission-toolbar-text { color: #64748b; font-size: 0.9rem; }
  .permission-mini-action {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    border-radius: 999px;
    background: #f8fafc;
    border: 1px solid #dbe4f0;
    font-size: 0.9rem;
    font-weight: 700;
    color: #0f172a;
    cursor: pointer;
  }
  .permission-radio-group { display: grid; gap: 10px; }
  .role-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 26px;
  }
  .role-actions .btn {
    min-width: 145px;
    border-radius: 999px;
    padding: 0.85rem 1.35rem;
    font-weight: 700;
  }
  @media (max-width: 767.98px) {
    .role-builder-hero, .role-builder-card-inner { padding: 22px; }
  }
</style>

<div class="role-builder">
  <div class="role-builder-hero">
    <div class="position-relative" style="z-index:1;">
      <span class="badge rounded-pill text-bg-light text-success mb-3 px-3 py-2">Permissions Studio</span>
      <h2 class="h3 mb-2 text-white">{{ $role ? __('role.update_role') : __('role.save_role') }}</h2>
      <p class="mb-0 text-white-50">Grouped permissions, cleaner checkboxes, and smarter module selection for faster role setup.</p>
    </div>
  </div>

  <div class="role-builder-card">
    <div class="role-builder-card-inner">
      <div class="role-section-tag">{{ __('role.role_name') }}</div>
      <div class="mb-4">
        <label for="name" class="form-label fw-semibold text-dark">{{ __('role.role_name') }}</label>
        <input id="name" name="name" type="text" class="form-control role-name-control" value="{{ old('name', $role->name ?? '') }}" required>
      </div>

      <div class="permission-toolbar">
        <div>
          <div class="role-section-tag mb-1">{{ __('role.role_permissions') }}</div>
          <div class="permission-toolbar-text">Main modules aur actions ko clean permission cards me organize kiya gaya hai.</div>
        </div>
        <label class="permission-mini-action mb-0">
          <input class="form-check-input group-select-all d-none" type="checkbox" id="select_all_app_sections" data-group="app_sections">
          <span>Select All App Sections</span>
        </label>
      </div>

      <div class="permission-grid">
        <div class="permission-card">
          <div class="permission-card-header">
            <h4>{{ __('role.role_permissions') }}</h4>
            <p>Role management actions</p>
          </div>
          <div class="permission-card-body">
            <div class="permission-stack">
              <label class="permission-item">
                <input type="checkbox" name="permissions[]" value="roles.view" id="roles_view" {{ in_array('roles.view', $selectedPermissions) ? 'checked' : '' }}>
                <span class="permission-box"><span class="permission-indicator"></span><span><span class="permission-title">{{ __('role.view_role') }}</span><span class="permission-note">Allow viewing roles</span></span></span>
              </label>
              <label class="permission-item">
                <input type="checkbox" name="permissions[]" value="roles.create" id="roles_create" {{ in_array('roles.create', $selectedPermissions) ? 'checked' : '' }}>
                <span class="permission-box"><span class="permission-indicator"></span><span><span class="permission-title">{{ __('role.add_role') }}</span><span class="permission-note">Create new roles</span></span></span>
              </label>
              <label class="permission-item">
                <input type="checkbox" name="permissions[]" value="roles.update" id="roles_update" {{ in_array('roles.update', $selectedPermissions) ? 'checked' : '' }}>
                <span class="permission-box"><span class="permission-indicator"></span><span><span class="permission-title">{{ __('role.edit_role') }}</span><span class="permission-note">Edit existing roles</span></span></span>
              </label>
              <label class="permission-item">
                <input type="checkbox" name="permissions[]" value="roles.delete" id="roles_delete" {{ in_array('roles.delete', $selectedPermissions) ? 'checked' : '' }}>
                <span class="permission-box"><span class="permission-indicator"></span><span><span class="permission-title">{{ __('lang_v1.delete_role') }}</span><span class="permission-note">Delete role entries</span></span></span>
              </label>
            </div>
          </div>
        </div>

        <div class="permission-card">
          <div class="permission-card-header">
            <h4>{{ __('role.user_management') }}</h4>
            <p>User account access controls</p>
          </div>
          <div class="permission-card-body">
            <div class="permission-stack">
              <label class="permission-item">
                <input type="checkbox" name="permissions[]" value="user.view" id="user_view" {{ in_array('user.view', $selectedPermissions) ? 'checked' : '' }}>
                <span class="permission-box"><span class="permission-indicator"></span><span><span class="permission-title">{{ __('role.user.view') }}</span><span class="permission-note">See user list and details</span></span></span>
              </label>
              <label class="permission-item">
                <input type="checkbox" name="permissions[]" value="user.create" id="user_create" {{ in_array('user.create', $selectedPermissions) ? 'checked' : '' }}>
                <span class="permission-box"><span class="permission-indicator"></span><span><span class="permission-title">{{ __('role.user.create') }}</span><span class="permission-note">Add new users</span></span></span>
              </label>
              <label class="permission-item">
                <input type="checkbox" name="permissions[]" value="user.update" id="user_update" {{ in_array('user.update', $selectedPermissions) ? 'checked' : '' }}>
                <span class="permission-box"><span class="permission-indicator"></span><span><span class="permission-title">{{ __('role.user.update') }}</span><span class="permission-note">Edit user profiles</span></span></span>
              </label>
              <label class="permission-item">
                <input type="checkbox" name="permissions[]" value="user.delete" id="user_delete" {{ in_array('user.delete', $selectedPermissions) ? 'checked' : '' }}>
                <span class="permission-box"><span class="permission-indicator"></span><span><span class="permission-title">{{ __('role.user.delete') }}</span><span class="permission-note">Remove user accounts</span></span></span>
              </label>
            </div>
          </div>
        </div>

        <div class="permission-card" style="grid-column: 1 / -1;">
          <div class="permission-card-header">
            <h4>{{ __('role.app_sections') }} / Main</h4>
            <p>Module-level access. Parent view toggle se related child options bhi select ho jati hain.</p>
          </div>
          <div class="permission-card-body">
            <div class="permission-grid">
              <div class="permission-card">
                <div class="permission-card-body">
                  <div class="permission-stack">
                    <label class="permission-item">
                      <input class="group-option" data-group="app_sections" type="checkbox" name="permissions[]" value="party.view" id="party_view" {{ in_array('party.view', $selectedPermissions) ? 'checked' : '' }}>
                      <span class="permission-box permission-box-strong"><span class="permission-indicator"></span><span><span class="permission-title">{{ __('role.party.view') }}</span><span class="permission-note">Open party section</span></span></span>
                    </label>
                    <label class="permission-item">
                      <input class="group-option" data-group="app_sections" type="checkbox" name="permissions[]" value="product.view" id="product_view" {{ in_array('product.view', $selectedPermissions) ? 'checked' : '' }}>
                      <span class="permission-box permission-box-strong"><span class="permission-indicator"></span><span><span class="permission-title">{{ __('role.product.view') }}</span><span class="permission-note">Open items and products</span></span></span>
                    </label>
                    <label class="permission-item">
                      <input class="group-option parent-module-toggle" data-group="app_sections" data-child-group="sales_children" type="checkbox" name="permissions[]" value="sales.view" id="sales_view" {{ in_array('sales.view', $selectedPermissions) ? 'checked' : '' }}>
                      <span class="permission-box permission-box-strong"><span class="permission-indicator"></span><span><span class="permission-title">{{ __('role.sales.view') }}</span><span class="permission-note">Select to enable all sale sub-sections together</span></span></span>
                    </label>
                    <div class="permission-substack">
                      <label class="permission-item"><input class="group-option module-child" data-group="app_sections" data-parent="sales_view" data-child-group="sales_children" type="checkbox" name="permissions[]" value="sales.invoice" id="sales_invoice" {{ in_array('sales.invoice', $selectedPermissions) ? 'checked' : '' }}><span class="permission-box"><span class="permission-indicator"></span><span class="permission-title">{{ __('role.sales.invoice') }}</span></span></label>
                      <label class="permission-item"><input class="group-option module-child" data-group="app_sections" data-parent="sales_view" data-child-group="sales_children" type="checkbox" name="permissions[]" value="sales.estimate" id="sales_estimate" {{ in_array('sales.estimate', $selectedPermissions) ? 'checked' : '' }}><span class="permission-box"><span class="permission-indicator"></span><span class="permission-title">{{ __('role.sales.estimate') }}</span></span></label>
                      <label class="permission-item"><input class="group-option module-child" data-group="app_sections" data-parent="sales_view" data-child-group="sales_children" type="checkbox" name="permissions[]" value="sales.payment_in" id="sales_payment_in" {{ in_array('sales.payment_in', $selectedPermissions) ? 'checked' : '' }}><span class="permission-box"><span class="permission-indicator"></span><span class="permission-title">{{ __('role.sales.payment_in') }}</span></span></label>
                      <label class="permission-item"><input class="group-option module-child" data-group="app_sections" data-parent="sales_view" data-child-group="sales_children" type="checkbox" name="permissions[]" value="sales.proforma" id="sales_proforma" {{ in_array('sales.proforma', $selectedPermissions) ? 'checked' : '' }}><span class="permission-box"><span class="permission-indicator"></span><span class="permission-title">{{ __('role.sales.proforma') }}</span></span></label>
                      <label class="permission-item"><input class="group-option module-child" data-group="app_sections" data-parent="sales_view" data-child-group="sales_children" type="checkbox" name="permissions[]" value="sales.order" id="sales_order" {{ in_array('sales.order', $selectedPermissions) ? 'checked' : '' }}><span class="permission-box"><span class="permission-indicator"></span><span class="permission-title">{{ __('role.sales.order') }}</span></span></label>
                      <label class="permission-item"><input class="group-option module-child" data-group="app_sections" data-parent="sales_view" data-child-group="sales_children" type="checkbox" name="permissions[]" value="sales.delivery_challan" id="sales_delivery_challan" {{ in_array('sales.delivery_challan', $selectedPermissions) ? 'checked' : '' }}><span class="permission-box"><span class="permission-indicator"></span><span class="permission-title">{{ __('role.sales.delivery_challan') }}</span></span></label>
                      <label class="permission-item"><input class="group-option module-child" data-group="app_sections" data-parent="sales_view" data-child-group="sales_children" type="checkbox" name="permissions[]" value="sales.sale_return" id="sales_sale_return" {{ in_array('sales.sale_return', $selectedPermissions) ? 'checked' : '' }}><span class="permission-box"><span class="permission-indicator"></span><span class="permission-title">{{ __('role.sales.sale_return') }}</span></span></label>
                      <label class="permission-item"><input class="group-option module-child" data-group="app_sections" data-parent="sales_view" data-child-group="sales_children" type="checkbox" name="permissions[]" value="sales.pos" id="sales_pos" {{ in_array('sales.pos', $selectedPermissions) ? 'checked' : '' }}><span class="permission-box"><span class="permission-indicator"></span><span class="permission-title">{{ __('role.sales.pos') }}</span></span></label>
                    </div>
                  </div>
                </div>
              </div>

              <div class="permission-card">
                <div class="permission-card-body">
                  <div class="permission-stack">
                    <label class="permission-item">
                      <input class="group-option parent-module-toggle" data-group="app_sections" data-child-group="purchase_children" type="checkbox" name="permissions[]" value="purchase.view" id="purchase_view" {{ in_array('purchase.view', $selectedPermissions) ? 'checked' : '' }}>
                      <span class="permission-box permission-box-strong"><span class="permission-indicator"></span><span><span class="permission-title">{{ __('role.purchase.view') }}</span><span class="permission-note">Purchase view select karte hi child access bhi select ho jayega</span></span></span>
                    </label>
                    <div class="permission-substack">
                      <label class="permission-item"><input class="group-option module-child" data-group="app_sections" data-parent="purchase_view" data-child-group="purchase_children" type="checkbox" name="permissions[]" value="purchase.bill" id="purchase_bill" {{ in_array('purchase.bill', $selectedPermissions) || $hasPurchaseCreate ? 'checked' : '' }}><span class="permission-box"><span class="permission-indicator"></span><span class="permission-title">{{ __('role.purchase.bill') }}</span></span></label>
                      <label class="permission-item"><input class="group-option module-child" data-group="app_sections" data-parent="purchase_view" data-child-group="purchase_children" type="checkbox" name="permissions[]" value="purchase.payment_out" id="purchase_payment_out" {{ in_array('purchase.payment_out', $selectedPermissions) || in_array('purchase.payments', $selectedPermissions) || $hasPurchaseCreate ? 'checked' : '' }}><span class="permission-box"><span class="permission-indicator"></span><span class="permission-title">{{ __('role.purchase.payment_out') }}</span></span></label>
                      <label class="permission-item"><input class="group-option module-child" data-group="app_sections" data-parent="purchase_view" data-child-group="purchase_children" type="checkbox" name="permissions[]" value="purchase.return" id="purchase_return" {{ in_array('purchase.return', $selectedPermissions) || in_array('purchase.update', $selectedPermissions) || $hasPurchaseCreate ? 'checked' : '' }}><span class="permission-box"><span class="permission-indicator"></span><span class="permission-title">{{ __('role.purchase.return') }}</span></span></label>
                      <label class="permission-item"><input class="group-option module-child" data-group="app_sections" data-parent="purchase_view" data-child-group="purchase_children" type="checkbox" name="permissions[]" value="purchase.expense" id="purchase_expense" {{ in_array('purchase.expense', $selectedPermissions) || in_array('purchase.delete', $selectedPermissions) || $hasPurchaseCreate ? 'checked' : '' }}><span class="permission-box"><span class="permission-indicator"></span><span class="permission-title">{{ __('role.purchase.expense') }}</span></span></label>
                      <label class="permission-item"><input class="group-option module-child" data-group="app_sections" data-parent="purchase_view" data-child-group="purchase_children" type="checkbox" name="permissions[]" value="purchase.order" id="purchase_order" {{ in_array('purchase.order', $selectedPermissions) || $hasPurchaseCreate ? 'checked' : '' }}><span class="permission-box"><span class="permission-indicator"></span><span class="permission-title">{{ __('role.purchase.order') }}</span></span></label>
                    </div>

                    <label class="permission-item">
                      <input class="group-option parent-module-toggle" data-group="app_sections" data-child-group="cashbank_children" type="checkbox" name="permissions[]" value="cashbank.view" id="cashbank_view" {{ in_array('cashbank.view', $selectedPermissions) ? 'checked' : '' }}>
                      <span class="permission-box permission-box-strong"><span class="permission-indicator"></span><span><span class="permission-title">{{ __('role.cashbank.view') }}</span><span class="permission-note">Enable Cash & Bank module with one click</span></span></span>
                    </label>
                    <div class="permission-substack">
                      <label class="permission-item"><input class="group-option module-child" data-group="app_sections" data-parent="cashbank_view" data-child-group="cashbank_children" type="checkbox" name="permissions[]" value="cashbank.loan_accounts" id="cashbank_loan_accounts" {{ in_array('cashbank.loan_accounts', $selectedPermissions) ? 'checked' : '' }}><span class="permission-box"><span class="permission-indicator"></span><span class="permission-title">{{ __('role.cashbank.loan_accounts') }}</span></span></label>
                      <label class="permission-item"><input class="group-option module-child" data-group="app_sections" data-parent="cashbank_view" data-child-group="cashbank_children" type="checkbox" name="permissions[]" value="cashbank.bank_accounts" id="cashbank_bank_accounts" {{ in_array('cashbank.bank_accounts', $selectedPermissions) ? 'checked' : '' }}><span class="permission-box"><span class="permission-indicator"></span><span class="permission-title">{{ __('role.cashbank.bank_accounts') }}</span></span></label>
                    </div>
                  </div>
                </div>
              </div>

              <div class="permission-card">
                <div class="permission-card-body">
                  <div class="permission-stack">
                    <label class="permission-item"><input class="group-option" data-group="app_sections" type="checkbox" name="permissions[]" value="report.view" id="report_view" {{ in_array('report.view', $selectedPermissions) ? 'checked' : '' }}><span class="permission-box permission-box-strong"><span class="permission-indicator"></span><span class="permission-title">{{ __('role.report.view') }}</span></span></label>
                    <label class="permission-item"><input class="group-option" data-group="app_sections" type="checkbox" name="permissions[]" value="sync.view" id="sync_view" {{ in_array('sync.view', $selectedPermissions) ? 'checked' : '' }}><span class="permission-box permission-box-strong"><span class="permission-indicator"></span><span class="permission-title">{{ __('role.sync.view') }}</span></span></label>
                    <label class="permission-item"><input class="group-option" data-group="app_sections" type="checkbox" name="permissions[]" value="utilities.view" id="utilities_view" {{ in_array('utilities.view', $selectedPermissions) ? 'checked' : '' }}><span class="permission-box permission-box-strong"><span class="permission-indicator"></span><span class="permission-title">{{ __('role.utilities.view') }}</span></span></label>
                    <label class="permission-item"><input class="group-option" data-group="app_sections" type="checkbox" name="permissions[]" value="settings.view" id="settings_view" {{ in_array('settings.view', $selectedPermissions) ? 'checked' : '' }}><span class="permission-box permission-box-strong"><span class="permission-indicator"></span><span class="permission-title">{{ __('role.settings.view') }}</span></span></label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="permission-card">
          <div class="permission-card-header">
            <h4>{{ __('role.supplier') }}</h4>
            <p>Choose supplier visibility scope and creation access</p>
          </div>
          <div class="permission-card-body">
            <div class="permission-radio-group">
              <label class="permission-item">
                <input type="radio" name="radio_option[supplier_view]" value="supplier.view" id="supplier_view_all" {{ old('radio_option.supplier_view', $selectedRadio['supplier_view'] ?? '') == 'supplier.view' ? 'checked' : '' }}>
                <span class="permission-box"><span class="permission-indicator"></span><span><span class="permission-title">{{ __('lang_v1.view_all_supplier') }}</span><span class="permission-note">Full supplier visibility</span></span></span>
              </label>
              <label class="permission-item">
                <input type="radio" name="radio_option[supplier_view]" value="supplier.view_own" id="supplier_view_own" {{ old('radio_option.supplier_view', $selectedRadio['supplier_view'] ?? '') == 'supplier.view_own' ? 'checked' : '' }}>
                <span class="permission-box"><span class="permission-indicator"></span><span><span class="permission-title">{{ __('lang_v1.view_own_supplier') }}</span><span class="permission-note">Only own supplier records</span></span></span>
              </label>
              <label class="permission-item">
                <input type="checkbox" name="permissions[]" value="supplier.create" id="supplier_create" {{ in_array('supplier.create', $selectedPermissions) ? 'checked' : '' }}>
                <span class="permission-box"><span class="permission-indicator"></span><span><span class="permission-title">{{ __('role.supplier.create') }}</span><span class="permission-note">Allow creating suppliers</span></span></span>
              </label>
            </div>
          </div>
        </div>
      </div>

      <div class="role-actions">
        <button type="submit" class="btn btn-primary">{{ $role ? __('role.update_role') : __('role.save_role') }}</button>
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">{{ __('role.cancel') }}</a>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const syncMasterState = (group) => {
      const master = document.querySelector(`.group-select-all[data-group="${group}"]`);
      if (!master) return;
      const options = Array.from(document.querySelectorAll(`.group-option[data-group="${group}"]`));
      if (!options.length) return;
      master.checked = options.every((item) => item.checked);
      master.indeterminate = !master.checked && options.some((item) => item.checked);
    };

    const syncParentState = (parentId, childGroup) => {
      const parent = document.getElementById(parentId);
      const children = Array.from(document.querySelectorAll(`.module-child[data-child-group="${childGroup}"]`));
      if (!parent || !children.length) return;
      const checkedChildren = children.filter((child) => child.checked);
      parent.checked = checkedChildren.length > 0;
      parent.indeterminate = checkedChildren.length > 0 && checkedChildren.length < children.length;
    };

    document.querySelectorAll('.group-select-all').forEach((master) => {
      const group = master.dataset.group;
      master.addEventListener('change', () => {
        document.querySelectorAll(`.group-option[data-group="${group}"]`).forEach((checkbox) => {
          checkbox.checked = master.checked;
          checkbox.indeterminate = false;
        });
        document.querySelectorAll('.parent-module-toggle').forEach((parent) => {
          if (parent.dataset.group === group) {
            parent.indeterminate = false;
          }
        });
      });
      syncMasterState(group);
    });

    document.querySelectorAll('.parent-module-toggle').forEach((parent) => {
      const childGroup = parent.dataset.childGroup;
      parent.addEventListener('change', () => {
        document.querySelectorAll(`.module-child[data-child-group="${childGroup}"]`).forEach((child) => {
          child.checked = parent.checked;
        });
        parent.indeterminate = false;
        syncMasterState(parent.dataset.group);
      });
      syncParentState(parent.id, childGroup);
    });

    document.querySelectorAll('.module-child').forEach((child) => {
      child.addEventListener('change', () => {
        syncParentState(child.dataset.parent, child.dataset.childGroup);
        syncMasterState(child.dataset.group);
      });
    });

    document.querySelectorAll('.group-option').forEach((checkbox) => {
      checkbox.addEventListener('change', () => {
        if (!checkbox.classList.contains('module-child') && !checkbox.classList.contains('parent-module-toggle')) {
          syncMasterState(checkbox.dataset.group);
        }
      });
    });

    document.querySelectorAll('.parent-module-toggle').forEach((parent) => {
      syncParentState(parent.id, parent.dataset.childGroup);
    });
    syncMasterState('app_sections');
  });
</script>
@endpush
