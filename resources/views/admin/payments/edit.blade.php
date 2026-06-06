@extends('admin.layouts.app')
@section('title', 'Edit Payment')

@section('content')
<div class="content-header">
  <div class="container-fluid-85">
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
  <div class="container-fluid-85">
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
          <div class="row mt-2" id="purchases-for-vendor" style="display:none;">
            <div class="col-12">
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

            <div class="col-12 mt-2">
              <div class="d-flex justify-content-end">
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
          </div>

          <div class="card-footer p-0">
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
    const paymentId = {{ $payment->id }};

    // Store allocated purchase IDs to avoid duplicates when loading pending purchases
    let allocatedPurchaseIds = [];
    // Store existing allocations with purchase details
    let existingAllocations = {};

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

    function buildRow(p, isAllocated){
      const tr = document.createElement('tr');
      if (isAllocated) {
        tr.classList.add('table-active');
      }
      const dateTd = document.createElement('td'); dateTd.textContent = p.purchase_date || '-';
      const noTd = document.createElement('td'); noTd.textContent = (p.id||'');
      const projectTd = document.createElement('td'); projectTd.textContent = p.project || '-';
      const amtTd = document.createElement('td'); amtTd.className = 'text-right'; amtTd.textContent = (p.amount? ('₹ ' + parseFloat(p.amount).toFixed(2)):'₹ 0.00');
      const dueTd = document.createElement('td'); dueTd.className = 'text-right';
      // For allocated purchases, show the original due amount (before allocation was applied)
      // The current due_amount has already been reduced by the allocation
      const originalDue = p.allocated_amount !== undefined ? (parseFloat(p.due_amount) + parseFloat(p.allocated_amount)) : (typeof p.due_amount !== 'undefined' ? parseFloat(p.due_amount) : 0);
      dueTd.textContent = formatCurrency(originalDue);
      const payTd = document.createElement('td'); payTd.className = 'text-right';
      const chk = document.createElement('input'); chk.type='checkbox'; chk.className='full-pay-checkbox'; chk.title='Full payment'; chk.style.marginRight='6px';
      const input = document.createElement('input'); input.type='number'; input.step='0.01'; input.min=0; input.value=0; input.className='form-control form-control-sm purchase-payment';
      
      // Use original due amount as max for allocated purchases
      const dueVal = originalDue;
      if(!isNaN(dueVal) && dueVal > 0){ input.max = dueVal.toFixed(2); }
      
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

      // Set existing allocation value
      if (isAllocated && p.allocated_amount !== undefined && parseFloat(p.allocated_amount) > 0){
        input.value = parseFloat(p.allocated_amount).toFixed(2);
        if (parseFloat(input.value) >= (parseFloat(input.max) || 0) - 0.0001) { 
          chk.checked = true; 
          input.readOnly = true; 
        }
      }

      chk.addEventListener('change', function(e){ const mx = parseFloat(input.max) || 0; const paid = parseFloat(amountInput.value) || 0; let sumExcl=0; purchasesTableBody.querySelectorAll('input.purchase-payment').forEach(function(inp){ if(inp !== input){ sumExcl += parseFloat(inp.value) || 0; } }); const allowedForThis = Math.max(0, paid - sumExcl); if(this.checked){ const setVal = Math.min(mx, allowedForThis); input.value = setVal.toFixed(2); input.readOnly = true; } else { input.value = '0.00'; input.readOnly = false; } recalcSummary(); });
      payTd.appendChild(chk); payTd.appendChild(input);
      tr.appendChild(dateTd); tr.appendChild(noTd); tr.appendChild(projectTd); tr.appendChild(amtTd); tr.appendChild(dueTd); tr.appendChild(payTd);
      return tr;
    }

    function fetchAllocatedPurchases(){
      fetch('{{ route('payment.allocated-purchases', $payment->id) }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => r.json())
      .then(data => {
        if(data && data.allocated_purchases && data.allocated_purchases.length > 0){
          allocatedPurchaseIds = data.allocated_purchase_ids || [];
          existingAllocations = data.allocated_purchases;
          
          // Build rows for allocated purchases
          existingAllocations.forEach(function(p){ 
            const tr = buildRow(p, true); 
            purchasesTableBody.appendChild(tr); 
          });
          
          purchasesWrap.style.display = '';
          recalcSummary();
          
          // Also fetch pending purchases for the same vendor to allow adding more allocations
          if(data.payment && data.payment.vendor_id){
            vendorSelect.value = data.payment.vendor_id;
            fetchPendingPurchases(data.payment.vendor_id, allocatedPurchaseIds);
          }
        } else {
          // No allocations, just fetch pending purchases
          if(vendorSelect.value){ fetchPendingPurchases(vendorSelect.value, []); }
        }
      }).catch(err => { 
        console.error('Failed to load allocated purchases', err); 
        // Fallback to fetching pending purchases
        if(vendorSelect.value){ fetchPendingPurchases(vendorSelect.value, []); }
      });
    }

    function fetchPendingPurchases(vendorId, excludeIds){
      if(!vendorId){ 
        if(purchasesTableBody.children.length === 0) {
          purchasesWrap.style.display = 'none'; 
        }
        recalcSummary(); 
        return; 
      }
      if(!excludeIds) excludeIds = [];
      
      const params = new URLSearchParams({ length: 1000, start: 0, vendor_id: vendorId, status: 'pending' });
      fetch('{{ route('purchase.list') }}' + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => r.json())
      .then(data => {
        if(data && Array.isArray(data.data) && data.data.length){
          data.data.forEach(function(row){ 
            // Skip purchases that are already allocated (already in the table)
            if(excludeIds.includes(row.id)) return;
            const tr = buildRow(row, false); 
            purchasesTableBody.appendChild(tr); 
          }); 
        }
        purchasesWrap.style.display = '';
        recalcSummary();
      }).catch(err => { console.error('Failed to load purchases', err); });
    }

    if(vendorSelect){
      vendorSelect.addEventListener('change', function(){ 
        // When vendor changes, reload all purchases for the new vendor
        allocatedPurchaseIds = [];
        existingAllocations = {};
        purchasesTableBody.innerHTML = '';
        fetchPendingPurchases(this.value, []);
      });
      if(window.jQuery){ $(document).on('select2:select', 'select[name="vendor_id"]', function(e){ 
        allocatedPurchaseIds = [];
        existingAllocations = {};
        purchasesTableBody.innerHTML = '';
        fetchPendingPurchases(this.value, []);
      }); }
    }

    const form = document.querySelector('form.prevent-multiple-submit');
    if(form){
      form.addEventListener('submit', function(e){ return true; });
    }

    if(amountInput){ amountInput.addEventListener('input', recalcSummary); }
    
    // Load allocated purchases on page load
    fetchAllocatedPurchases();
  })();
</script>
@endsection
