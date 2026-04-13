{{-- DISCOUNT REPORT TAB --}}
<div id="tab-discount report" class="report-tab-content d-none">
    <div class="d-flex flex-column" style="min-height:100vh;padding:24px;background:#fff;border:1px solid #e5e7eb;">

        {{-- TOP BAR --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div class="d-flex align-items-center flex-wrap gap-3">
                {{-- Date range (always visible like Vyapar) --}}
                <div style="display:flex;align-items:center;gap:8px;background:#f1f5f9;border-radius:6px;padding:4px 12px;">
                    <span style="font-size:12px;color:#6b7280;">From</span>
                    <input type="date" id="dr-from" style="border:none;outline:none;font-size:13px;color:#374151;background:transparent;font-weight:500;" value="{{ date('Y-m-d', strtotime('first day of this month')) }}">
                    <span style="font-size:12px;color:#9ca3af;">—</span>
                    <span style="font-size:12px;color:#6b7280;">To</span>
                    <input type="date" id="dr-to" style="border:none;outline:none;font-size:13px;color:#374151;background:transparent;font-weight:500;" value="{{ date('Y-m-d') }}">
                    <button id="dr-apply" style="font-size:11px;padding:4px 12px;background:#6366f1;color:#fff;border:none;border-radius:4px;cursor:pointer;margin-left:4px;">Apply</button>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button id="dr-excel-btn" title="Export Excel" style="width:38px;height:38px;border-radius:50%;border:1px solid #e5e7eb;background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                    <i class="fa-solid fa-file-excel" style="color:#10b981;font-size:17px;"></i>
                </button>
                <button id="dr-print-btn" title="Print" style="width:38px;height:38px;border-radius:50%;border:1px solid #e5e7eb;background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                    <i class="fa-solid fa-print" style="color:#4b5563;font-size:17px;"></i>
                </button>
            </div>
        </div>

        <h2 style="font-weight:700;color:#1f2937;font-size:22px;margin:8px 0 20px;">Discount Report</h2>
        <div id="dr-loading" class="d-none text-center py-5"><div class="spinner-border text-primary"><span class="visually-hidden">Loading…</span></div></div>

        <div id="dr-table-wrap" class="table-responsive">
            <table style="width:100%;border-collapse:collapse;">
                <thead style="background:#f3f4f6;">
                    <tr style="border-bottom:2px solid #e5e7eb;">
                        <th style="padding:11px 16px;font-size:12px;font-weight:600;color:#6b7280;text-align:left;border-right:1px solid #e5e7eb;">Party Name</th>
                        <th style="padding:11px 16px;font-size:12px;font-weight:600;color:#6b7280;text-align:right;border-right:1px solid #e5e7eb;width:200px;">Sale Discount</th>
                        <th style="padding:11px 16px;font-size:12px;font-weight:600;color:#6b7280;text-align:right;width:240px;">Purchase / Expense Discount</th>
                    </tr>
                </thead>
                <tbody id="dr-body">
                    <tr><td colspan="3" style="text-align:center;color:#9ca3af;padding:40px;font-size:14px;">Loading…</td></tr>
                </tbody>
            </table>
        </div>

        {{-- Footer Totals Bar --}}
        <div id="dr-footer-bar" style="display:none;position:sticky;bottom:0;background:#fff;border-top:2px solid #e5e7eb;padding:12px 16px;display:flex;justify-content:space-between;margin-top:8px;">
            <span style="font-size:13px;font-weight:600;color:#16a34a;">Total Sale Discount: <span id="dr-total-sale">Rs 0.00</span></span>
            <span style="font-size:13px;font-weight:600;color:#ef4444;">Total Purchase Discount: <span id="dr-total-purchase">Rs 0.00</span></span>
        </div>
    </div>
</div>

{{-- PRINT MODAL --}}
<div class="modal fade" id="drPrintModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content" style="border-radius:10px;overflow:hidden;">
      <div class="modal-header" style="background:#f9fafb;border-bottom:1px solid #e5e7eb;">
        <h5 class="modal-title fw-bold"><i class="fa-solid fa-print me-2 text-secondary"></i>Print Preview — Discount Report</h5>
        <div class="d-flex gap-2 align-items-center">
          <button id="dr-do-print" class="btn btn-sm btn-primary px-3"><i class="fa-solid fa-print me-1"></i>Print</button>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
      </div>
      <div class="modal-body p-0" style="background:#e5e7eb;">
        <div id="dr-print-area" style="background:#fff;margin:24px auto;padding:40px 48px;max-width:860px;box-shadow:0 4px 20px rgba(0,0,0,.10);border-radius:8px;font-family:Inter,sans-serif;">
          <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
            <div>
              <h2 style="font-size:20px;font-weight:700;color:#111827;margin:0 0 4px;">Discount Report</h2>
              <p id="dr-p-range" style="font-size:12px;color:#9ca3af;margin:0;"></p>
            </div>
            <p style="font-size:12px;color:#9ca3af;margin:0;">Printed: <span id="dr-p-date"></span></p>
          </div>
          <hr style="border-color:#e5e7eb;margin-bottom:16px;">
          <table style="width:100%;border-collapse:collapse;">
            <thead>
              <tr style="background:#f3f4f6;">
                <th style="padding:9px 12px;font-size:12px;font-weight:600;color:#6b7280;border:1px solid #e5e7eb;">Party Name</th>
                <th style="padding:9px 12px;font-size:12px;font-weight:600;color:#6b7280;text-align:right;border:1px solid #e5e7eb;">Sale Discount</th>
                <th style="padding:9px 12px;font-size:12px;font-weight:600;color:#6b7280;text-align:right;border:1px solid #e5e7eb;">Purchase / Expense Discount</th>
              </tr>
            </thead>
            <tbody id="dr-p-body"></tbody>
            <tfoot>
              <tr style="background:#f9fafb;">
                <td style="padding:9px 12px;font-size:12px;font-weight:700;border:1px solid #e5e7eb;">Total Sale Discount</td>
                <td id="dr-p-total-sale" style="padding:9px 12px;font-size:12px;font-weight:700;color:#16a34a;text-align:right;border:1px solid #e5e7eb;"></td>
                <td style="border:1px solid #e5e7eb;"></td>
              </tr>
              <tr style="background:#f9fafb;">
                <td style="padding:9px 12px;font-size:12px;font-weight:700;border:1px solid #e5e7eb;">Total Purchase Discount</td>
                <td style="border:1px solid #e5e7eb;"></td>
                <td id="dr-p-total-purchase" style="padding:9px 12px;font-size:12px;font-weight:700;color:#ef4444;text-align:right;border:1px solid #e5e7eb;"></td>
              </tr>
            </tfoot>
          </table>
          <p style="font-size:11px;color:#d1d5db;text-align:center;margin-top:24px;">Computer-generated — no signature required.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  var _drData=[];
  var csrf=window.App?.csrfToken||document.querySelector('meta[name="csrf-token"]')?.content||'';

  function fmt(v){var n=parseFloat(v||0);return'Rs '+Math.abs(n).toLocaleString('en-PK',{minimumFractionDigits:2,maximumFractionDigits:2});}
  function fmtD(s){if(!s)return'';var d=new Date(s);return isNaN(d)?s:d.toLocaleDateString('en-GB');}

  document.getElementById('dr-apply').addEventListener('click',load);

  // Auto-load on tab activation
  document.addEventListener('DOMContentLoaded',function(){
    var link=document.querySelector('[data-target="discount report"]');
    if(link) link.addEventListener('click',function(){setTimeout(load,50);});
  });

  function load(){
    var f=document.getElementById('dr-from').value, t=document.getElementById('dr-to').value;
    var p=new URLSearchParams();
    if(f)p.append('from',f); if(t)p.append('to',t);
    document.getElementById('dr-loading').classList.remove('d-none');
    document.getElementById('dr-table-wrap').classList.add('d-none');
    fetch('/dashboard/reports/discount-report?'+p,{headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf}})
      .then(function(r){if(!r.ok)throw new Error(r.status);return r.json();})
      .then(function(d){_drData=d.rows||[];renderTable(_drData,d);})
      .catch(function(e){console.error(e);renderEmpty('Failed to load data.');})
      .finally(function(){
        document.getElementById('dr-loading').classList.add('d-none');
        document.getElementById('dr-table-wrap').classList.remove('d-none');
      });
  }
  function renderEmpty(msg){
    document.getElementById('dr-body').innerHTML='<tr><td colspan="3" style="text-align:center;color:#9ca3af;padding:40px;font-size:14px;">'+msg+'</td></tr>';
    document.getElementById('dr-footer-bar').style.display='none';
    _drData=[];
  }
  function renderTable(rows,summary){
    if(!rows||!rows.length){renderEmpty('No discount data found for this period.');return;}
    document.getElementById('dr-body').innerHTML=rows.map(function(r){
      return'<tr style="border-bottom:1px solid #f3f4f6;">'
        +'<td style="padding:12px 16px;font-size:13px;color:#374151;border-right:1px solid #e5e7eb;">'+(r.party_name||'—')+'</td>'
        +'<td style="padding:12px 16px;font-size:13px;color:#16a34a;text-align:right;border-right:1px solid #e5e7eb;">'+fmt(r.sale_discount)+'</td>'
        +'<td style="padding:12px 16px;font-size:13px;color:#ef4444;text-align:right;">'+fmt(r.purchase_discount)+'</td>'
        +'</tr>';
    }).join('');
    document.getElementById('dr-total-sale').textContent=fmt(summary.total_sale_discount||0);
    document.getElementById('dr-total-purchase').textContent=fmt(summary.total_purchase_discount||0);
    document.getElementById('dr-footer-bar').style.display='flex';
  }

  /* Print */
  document.getElementById('dr-print-btn').addEventListener('click',function(){
    document.getElementById('dr-p-date').textContent=new Date().toLocaleDateString('en-GB');
    var f=document.getElementById('dr-from').value,t=document.getElementById('dr-to').value;
    document.getElementById('dr-p-range').textContent='Period: '+fmtD(f)+' — '+fmtD(t);
    document.getElementById('dr-p-body').innerHTML=_drData.length?_drData.map(function(r){
      return'<tr><td style="padding:7px 10px;font-size:12px;border:1px solid #e5e7eb;">'+(r.party_name||'—')+'</td>'
        +'<td style="padding:7px 10px;font-size:12px;color:#16a34a;text-align:right;border:1px solid #e5e7eb;">'+fmt(r.sale_discount)+'</td>'
        +'<td style="padding:7px 10px;font-size:12px;color:#ef4444;text-align:right;border:1px solid #e5e7eb;">'+fmt(r.purchase_discount)+'</td></tr>';
    }).join(''):'<tr><td colspan="3" style="text-align:center;color:#9ca3af;padding:14px;font-size:12px;border:1px solid #e5e7eb;">No data</td></tr>';
    document.getElementById('dr-p-total-sale').textContent=document.getElementById('dr-total-sale').textContent;
    document.getElementById('dr-p-total-purchase').textContent=document.getElementById('dr-total-purchase').textContent;
    new bootstrap.Modal(document.getElementById('drPrintModal')).show();
  });
  document.getElementById('dr-do-print').addEventListener('click',function(){
    var w=window.open('','_blank','width=940,height=720');
    w.document.write('<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Discount Report</title><style>*{box-sizing:border-box;margin:0;padding:0}body{font-family:Inter,sans-serif;padding:30px 38px}table{width:100%;border-collapse:collapse}th,td{padding:8px 10px;font-size:12px;border:1px solid #e5e7eb}th{background:#f3f4f6;font-weight:600;color:#6b7280}h2{font-size:20px;margin-bottom:4px}p{font-size:12px;color:#6b7280;margin:2px 0}hr{border-color:#e5e7eb;margin:14px 0}@media print{@page{margin:14mm}}</style></head><body>'+document.getElementById('dr-print-area').innerHTML+'</body></html>');
    w.document.close();w.focus();setTimeout(function(){w.print();w.close();},400);
  });

  /* Excel */
  document.getElementById('dr-excel-btn').addEventListener('click',function(){
    var p=new URLSearchParams({export:'excel'});
    var f=document.getElementById('dr-from').value,t=document.getElementById('dr-to').value;
    if(f)p.append('from',f);if(t)p.append('to',t);
    window.location.href='/dashboard/reports/discount-report/export?'+p;
  });

  // Load immediately when first rendered if visible
  setTimeout(function(){ if(!document.getElementById('tab-discount report').classList.contains('d-none')) load(); }, 100);
})();
</script>