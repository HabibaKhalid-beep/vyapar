{{-- TAX REPORT TAB --}}
<div id="tab-tax report" class="report-tab-content d-none">
    <div class="d-flex flex-column" style="min-height:100vh;padding:24px;background:#fff;border:1px solid #e5e7eb;">

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div class="d-flex align-items-center flex-wrap gap-3">
                <div style="display:flex;align-items:center;gap:8px;background:#f1f5f9;border-radius:6px;padding:4px 12px;">
                    <span style="font-size:12px;color:#6b7280;">From</span>
                    <input type="date" id="tr-from" style="border:none;outline:none;font-size:13px;color:#374151;background:transparent;font-weight:500;" value="{{ date('Y-m-d', strtotime('first day of this month')) }}">
                    <span style="font-size:12px;color:#9ca3af;">—</span>
                    <span style="font-size:12px;color:#6b7280;">To</span>
                    <input type="date" id="tr-to" style="border:none;outline:none;font-size:13px;color:#374151;background:transparent;font-weight:500;" value="{{ date('Y-m-d') }}">
                    <button id="tr-apply" style="font-size:11px;padding:4px 12px;background:#6366f1;color:#fff;border:none;border-radius:4px;cursor:pointer;margin-left:4px;">Apply</button>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button id="tr-excel-btn" title="Export Excel" style="width:38px;height:38px;border-radius:50%;border:1px solid #e5e7eb;background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                    <i class="fa-solid fa-file-excel" style="color:#10b981;font-size:17px;"></i>
                </button>
                <button id="tr-print-btn" title="Print" style="width:38px;height:38px;border-radius:50%;border:1px solid #e5e7eb;background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                    <i class="fa-solid fa-print" style="color:#4b5563;font-size:17px;"></i>
                </button>
            </div>
        </div>

        <h2 style="font-weight:700;color:#1f2937;font-size:22px;margin:8px 0 20px;">Tax Report</h2>
        <div id="tr-loading" class="d-none text-center py-5"><div class="spinner-border text-primary"><span class="visually-hidden">Loading…</span></div></div>

        <div id="tr-table-wrap" class="table-responsive">
            <table style="width:100%;border-collapse:collapse;">
                <thead style="background:#f3f4f6;">
                    <tr style="border-bottom:2px solid #e5e7eb;">
                        <th style="padding:11px 16px;font-size:12px;font-weight:600;color:#6b7280;text-align:left;border-right:1px solid #e5e7eb;">Party Name</th>
                        <th style="padding:11px 16px;font-size:12px;font-weight:600;color:#6b7280;text-align:right;border-right:1px solid #e5e7eb;width:200px;">Sale Tax</th>
                        <th style="padding:11px 16px;font-size:12px;font-weight:600;color:#6b7280;text-align:right;width:240px;">Purchase / Expense Tax</th>
                    </tr>
                </thead>
                <tbody id="tr-body">
                    <tr><td colspan="3" style="text-align:center;color:#9ca3af;padding:40px;font-size:14px;">Loading…</td></tr>
                </tbody>
            </table>
        </div>

        <div id="tr-footer-bar" style="display:none;position:sticky;bottom:0;background:#fff;border-top:2px solid #e5e7eb;padding:12px 16px;display:flex;justify-content:space-between;margin-top:8px;">
            <span style="font-size:13px;font-weight:600;color:#16a34a;">Total Tax In: <span id="tr-total-in">Rs 0.00</span></span>
            <span style="font-size:13px;font-weight:600;color:#ef4444;">Total Tax Out: <span id="tr-total-out">Rs 0.00</span></span>
        </div>
    </div>
</div>

{{-- PRINT MODAL --}}
<div class="modal fade" id="trPrintModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content" style="border-radius:10px;overflow:hidden;">
      <div class="modal-header" style="background:#f9fafb;border-bottom:1px solid #e5e7eb;">
        <h5 class="modal-title fw-bold"><i class="fa-solid fa-print me-2 text-secondary"></i>Print Preview — Tax Report</h5>
        <div class="d-flex gap-2 align-items-center">
          <button id="tr-do-print" class="btn btn-sm btn-primary px-3"><i class="fa-solid fa-print me-1"></i>Print</button>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
      </div>
      <div class="modal-body p-0" style="background:#e5e7eb;">
        <div id="tr-print-area" style="background:#fff;margin:24px auto;padding:40px 48px;max-width:860px;box-shadow:0 4px 20px rgba(0,0,0,.10);border-radius:8px;font-family:Inter,sans-serif;">
          <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
            <div>
              <h2 style="font-size:20px;font-weight:700;color:#111827;margin:0 0 4px;">Tax Report</h2>
              <p id="tr-p-range" style="font-size:12px;color:#9ca3af;margin:0;"></p>
            </div>
            <p style="font-size:12px;color:#9ca3af;margin:0;">Printed: <span id="tr-p-date"></span></p>
          </div>
          <hr style="border-color:#e5e7eb;margin-bottom:16px;">
          <table style="width:100%;border-collapse:collapse;">
            <thead>
              <tr style="background:#f3f4f6;">
                <th style="padding:9px 12px;font-size:12px;font-weight:600;color:#6b7280;border:1px solid #e5e7eb;">Party Name</th>
                <th style="padding:9px 12px;font-size:12px;font-weight:600;color:#6b7280;text-align:right;border:1px solid #e5e7eb;">Sale Tax</th>
                <th style="padding:9px 12px;font-size:12px;font-weight:600;color:#6b7280;text-align:right;border:1px solid #e5e7eb;">Purchase / Expense Tax</th>
              </tr>
            </thead>
            <tbody id="tr-p-body"></tbody>
            <tfoot>
              <tr style="background:#f9fafb;">
                <td style="padding:9px 12px;font-size:12px;font-weight:700;border:1px solid #e5e7eb;">Total Tax In</td>
                <td id="tr-p-total-in" style="padding:9px 12px;font-size:12px;font-weight:700;color:#16a34a;text-align:right;border:1px solid #e5e7eb;"></td>
                <td style="border:1px solid #e5e7eb;"></td>
              </tr>
              <tr style="background:#f9fafb;">
                <td style="padding:9px 12px;font-size:12px;font-weight:700;border:1px solid #e5e7eb;">Total Tax Out</td>
                <td style="border:1px solid #e5e7eb;"></td>
                <td id="tr-p-total-out" style="padding:9px 12px;font-size:12px;font-weight:700;color:#ef4444;text-align:right;border:1px solid #e5e7eb;"></td>
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
  var _trData=[];
  var csrf=window.App?.csrfToken||document.querySelector('meta[name="csrf-token"]')?.content||'';

  function fmt(v){var n=parseFloat(v||0);return'Rs '+Math.abs(n).toLocaleString('en-PK',{minimumFractionDigits:2,maximumFractionDigits:2});}
  function fmtD(s){if(!s)return'';var d=new Date(s);return isNaN(d)?s:d.toLocaleDateString('en-GB');}

  document.getElementById('tr-apply').addEventListener('click',load);

  function load(){
    var f=document.getElementById('tr-from').value,t=document.getElementById('tr-to').value;
    var p=new URLSearchParams();
    if(f)p.append('from',f);if(t)p.append('to',t);
    document.getElementById('tr-loading').classList.remove('d-none');
    document.getElementById('tr-table-wrap').classList.add('d-none');
    fetch('/dashboard/reports/tax-report?'+p,{headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf}})
      .then(function(r){if(!r.ok)throw new Error(r.status);return r.json();})
      .then(function(d){_trData=d.rows||[];renderTable(_trData,d);})
      .catch(function(e){console.error(e);renderEmpty('Failed to load data.');})
      .finally(function(){
        document.getElementById('tr-loading').classList.add('d-none');
        document.getElementById('tr-table-wrap').classList.remove('d-none');
      });
  }
  function renderEmpty(msg){
    document.getElementById('tr-body').innerHTML='<tr><td colspan="3" style="text-align:center;color:#9ca3af;padding:40px;font-size:14px;">'+msg+'</td></tr>';
    document.getElementById('tr-footer-bar').style.display='none';
    _trData=[];
  }
  function renderTable(rows,summary){
    if(!rows||!rows.length){renderEmpty('No tax data found for this period.');return;}
    document.getElementById('tr-body').innerHTML=rows.map(function(r){
      return'<tr style="border-bottom:1px solid #f3f4f6;">'
        +'<td style="padding:12px 16px;font-size:13px;color:#374151;border-right:1px solid #e5e7eb;">'+(r.party_name||'—')+'</td>'
        +'<td style="padding:12px 16px;font-size:13px;color:#16a34a;text-align:right;border-right:1px solid #e5e7eb;">'+fmt(r.sale_tax)+'</td>'
        +'<td style="padding:12px 16px;font-size:13px;color:#ef4444;text-align:right;">'+fmt(r.purchase_tax)+'</td>'
        +'</tr>';
    }).join('');
    document.getElementById('tr-total-in').textContent=fmt(summary.total_tax_in||0);
    document.getElementById('tr-total-out').textContent=fmt(summary.total_tax_out||0);
    document.getElementById('tr-footer-bar').style.display='flex';
  }

  /* Print */
  document.getElementById('tr-print-btn').addEventListener('click',function(){
    document.getElementById('tr-p-date').textContent=new Date().toLocaleDateString('en-GB');
    var f=document.getElementById('tr-from').value,t=document.getElementById('tr-to').value;
    document.getElementById('tr-p-range').textContent='Period: '+fmtD(f)+' — '+fmtD(t);
    document.getElementById('tr-p-body').innerHTML=_trData.length?_trData.map(function(r){
      return'<tr><td style="padding:7px 10px;font-size:12px;border:1px solid #e5e7eb;">'+(r.party_name||'—')+'</td>'
        +'<td style="padding:7px 10px;font-size:12px;color:#16a34a;text-align:right;border:1px solid #e5e7eb;">'+fmt(r.sale_tax)+'</td>'
        +'<td style="padding:7px 10px;font-size:12px;color:#ef4444;text-align:right;border:1px solid #e5e7eb;">'+fmt(r.purchase_tax)+'</td></tr>';
    }).join(''):'<tr><td colspan="3" style="text-align:center;color:#9ca3af;padding:14px;font-size:12px;border:1px solid #e5e7eb;">No data</td></tr>';
    document.getElementById('tr-p-total-in').textContent=document.getElementById('tr-total-in').textContent;
    document.getElementById('tr-p-total-out').textContent=document.getElementById('tr-total-out').textContent;
    new bootstrap.Modal(document.getElementById('trPrintModal')).show();
  });
  document.getElementById('tr-do-print').addEventListener('click',function(){
    var w=window.open('','_blank','width=940,height=720');
    w.document.write('<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Tax Report</title><style>*{box-sizing:border-box;margin:0;padding:0}body{font-family:Inter,sans-serif;padding:30px 38px}table{width:100%;border-collapse:collapse}th,td{padding:8px 10px;font-size:12px;border:1px solid #e5e7eb}th{background:#f3f4f6;font-weight:600;color:#6b7280}h2{font-size:20px;margin-bottom:4px}p{font-size:12px;color:#6b7280;margin:2px 0}hr{border-color:#e5e7eb;margin:14px 0}@media print{@page{margin:14mm}}</style></head><body>'+document.getElementById('tr-print-area').innerHTML+'</body></html>');
    w.document.close();w.focus();setTimeout(function(){w.print();w.close();},400);
  });
  document.getElementById('tr-excel-btn').addEventListener('click',function(){
    var p=new URLSearchParams({export:'excel'});
    var f=document.getElementById('tr-from').value,t=document.getElementById('tr-to').value;
    if(f)p.append('from',f);if(t)p.append('to',t);
    window.location.href='/dashboard/reports/tax-report/export?'+p;
  });

  setTimeout(function(){if(!document.getElementById('tab-tax report').classList.contains('d-none'))load();},100);
})();
</script>