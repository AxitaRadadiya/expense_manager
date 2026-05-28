@extends('admin.layouts.app')
@section('title', 'Edit Payment')

@section('content')
<div class="content-header">
  <div class="container-fluid-80">
    <div class="row mb-2">
      <div class="col-sm-6"><h1 class="m-0">Edit Payment</h1></div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('payment.index') }}">Payments</a></li>
          <li class="breadcrumb-item active">Edit</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid-80">
    <div class="card card-outline card-primary shadow-sm">
      <div class="card-body">
        <form class="prevent-multiple-submit" action="{{ route('payment.update', $payment->id) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Vendor <span class="text-danger">*</span></label>
                <select name="vendor_id" class="form-control select2" required>
                  <option value="">-- Select Vendor --</option>
                  @foreach($vendors as $v)
                    <option value="{{ $v->id }}" {{ $payment->vendor_id == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <label>Amount <span class="text-danger">*</span></label>
                <input type="number" name="amount" step="0.01" min="0.01" value="{{ $payment->amount }}" class="form-control" required>
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <label>Payment Date <span class="text-danger">*</span></label>
                <input type="date" name="payment_date" class="form-control" min="{{ date('Y-m-d') }}" value="{{ $payment->payment_date }}" required>
              </div>
            </div>
          </div>

          <!-- Pending purchases for selected vendor -->
          <div class="row mt-3" id="purchases-for-vendor" style="display:none;">
            <div class="col-md-8">
              <div class="card">
                <div class="card-body p-3">
                  <h6>Pending Purchases</h6>
                  <div class="table-responsive">
                    <table class="table table-sm table-bordered" id="VendorPurchasesTable">
                      <thead>
                        <tr>
                          <th>Date</th>
                          <th>Purchase No</th>
                          <th>Project</th>
                          <th class="text-right">Total Amount</th>
                          <th class="text-right">Due Amount</th>
                          <th class="text-right">Payment</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="card">
                <div class="card-body p-3">
                  <h6>Payment Summary</h6>
                  <div class="mb-2"><strong>Amount Paid:</strong> <span id="summary-amount-paid">₹ 0.00</span></div>
                  <div class="mb-2"><strong>Amount used for Payments:</strong> <span id="summary-amount-used">₹ 0.00</span></div>
                  <div class="mb-2"><strong>Amount in Excess:</strong> <span id="summary-amount-excess">₹ 0.00</span></div>
                </div>
              </div>
            </div>
          </div>

          <div class="card-footer">
            <button class="btn-submit" type="submit">Update Payment</button>
            <a href="{{ route('payment.index') }}" class="btn-cancel ml-2">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

@endsection

@section('pageScript')
<script>
  (function(){
    const vendorSelect = document.querySelector('select[name="vendor_id"]');
    const purchasesWrap = document.getElementById('purchases-for-vendor');
    const purchasesTableBody = document.querySelector('#VendorPurchasesTable tbody');
    const amountInput = document.querySelector('input[name="amount"]');
    const summaryPaid = document.getElementById('summary-amount-paid');
    const summaryUsed = document.getElementById('summary-amount-used');
    const summaryExcess = document.getElementById('summary-amount-excess');

    const existingAllocations = @json($payment->allocations->pluck('amount','purchase_id')) || {};

    function formatCurrency(v){ v = parseFloat(v) || 0; return '₹ ' + v.toFixed(2); }

    function recalcSummary(){
      const paid = parseFloat(amountInput.value) || 0;
      let used = 0;
      purchasesTableBody.querySelectorAll('input.purchase-payment').forEach(function(inp){ used += parseFloat(inp.value) || 0; });
      const excess = Math.max(0, paid - used);
      summaryPaid.textContent = formatCurrency(paid);
      summaryUsed.textContent = formatCurrency(used);
      summaryExcess.textContent = formatCurrency(excess);
    }

    function buildRow(p){
      const tr = document.createElement('tr');
      const dateTd = document.createElement('td'); dateTd.textContent = p.purchase_date || '-';
      const noTd = document.createElement('td'); noTd.textContent = '#'+(p.id||'');
      const projectTd = document.createElement('td'); projectTd.textContent = p.project || '-';
      const amtTd = document.createElement('td'); amtTd.className = 'text-right'; amtTd.textContent = (p.amount? ('₹ ' + parseFloat(p.amount).toFixed(2)):'₹ 0.00');
      const dueTd = document.createElement('td'); dueTd.className = 'text-right'; dueTd.textContent = (typeof p.due_amount !== 'undefined' ? ('₹ ' + parseFloat(p.due_amount).toFixed(2)) : '₹ 0.00');
      const payTd = document.createElement('td'); payTd.className = 'text-right';
      const chk = document.createElement('input'); chk.type='checkbox'; chk.className='full-pay-checkbox'; chk.title='Full payment'; chk.style.marginRight='6px';
      const input = document.createElement('input'); input.type='number'; input.step='0.01'; input.min=0; input.value=0; input.className='form-control form-control-sm purchase-payment';
      const dueVal = (typeof p.due_amount !== 'undefined') ? parseFloat(p.due_amount) : (p.amount? parseFloat(p.amount): 0);
      if(!isNaN(dueVal)){ input.max = dueVal.toFixed(2); }
      input.addEventListener('input', function(e){
        const mx = parseFloat(this.max) || 0; let v = parseFloat(this.value) || 0; if(v>mx){ v=mx; this.value = v.toFixed(2);} 
        const paid = parseFloat(amountInput.value) || 0; let sumExcl=0; purchasesTableBody.querySelectorAll('input.purchase-payment').forEach(function(inp){ if(inp !== e.target){ sumExcl += parseFloat(inp.value) || 0; } });
        const allowedForThis = Math.max(0, paid - sumExcl); if(v>allowedForThis){ v = allowedForThis; this.value = v.toFixed(2); }
        const icon = this.parentElement.querySelector('.full-pay-checkbox'); if(icon && v < (parseFloat(this.max) || 0) - 0.0001){ icon.checked = false; this.readOnly = false; }
        recalcSummary();
      });
      input.name = 'purchase_payments[' + (p.id || '') + ']';
      input.dataset.purchaseId = p.id;
      input.dataset.projectId = p.project_id;
      input.style.width = '100px';

      if (existingAllocations && existingAllocations[p.id]){
        input.value = parseFloat(existingAllocations[p.id]).toFixed(2);
        if (parseFloat(input.value) >= (parseFloat(input.max) || 0) - 0.0001) { chk.checked = true; input.readOnly = true; }
      }

      chk.addEventListener('change', function(e){ const mx = parseFloat(input.max) || 0; const paid = parseFloat(amountInput.value) || 0; let sumExcl=0; purchasesTableBody.querySelectorAll('input.purchase-payment').forEach(function(inp){ if(inp !== input){ sumExcl += parseFloat(inp.value) || 0; } }); const allowedForThis = Math.max(0, paid - sumExcl); if(this.checked){ const setVal = Math.min(mx, allowedForThis); input.value = setVal.toFixed(2); input.readOnly = true; } else { input.value = '0.00'; input.readOnly = false; } recalcSummary(); });
      payTd.appendChild(chk); payTd.appendChild(input);
      tr.appendChild(dateTd); tr.appendChild(noTd); tr.appendChild(projectTd); tr.appendChild(amtTd); tr.appendChild(dueTd); tr.appendChild(payTd);
      return tr;
    }

    function fetchPendingPurchases(vendorId){
      if(!vendorId){ purchasesTableBody.innerHTML = ''; purchasesWrap.style.display = 'none'; recalcSummary(); return; }
      const params = new URLSearchParams({ length: 1000, start: 0, vendor_id: vendorId, status: 'pending' });
      fetch('{{ route('purchase.list') }}' + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => r.json())
      .then(data => {
        purchasesTableBody.innerHTML = '';
        if(data && Array.isArray(data.data) && data.data.length){
          data.data.forEach(function(row){ const tr = buildRow(row); purchasesTableBody.appendChild(tr); }); purchasesWrap.style.display = ''; 
        } else { purchasesWrap.style.display = 'none'; }
        recalcSummary();
      }).catch(err => { console.error('Failed to load purchases', err); purchasesWrap.style.display = 'none'; });
    }

    if(vendorSelect){
      vendorSelect.addEventListener('change', function(){ fetchPendingPurchases(this.value); });
      if(window.jQuery){ $(document).on('select2:select', 'select[name="vendor_id"]', function(e){ fetchPendingPurchases(this.value); }); }
      if(vendorSelect.value){ fetchPendingPurchases(vendorSelect.value); }
    }

    const form = document.querySelector('form.prevent-multiple-submit');
    if(form){
      form.addEventListener('submit', function(e){ return true; });
    }

    if(amountInput){ amountInput.addEventListener('input', recalcSummary); }
  })();
</script>
@endsection
