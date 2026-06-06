@extends('admin.layouts.app')
@section('title', 'Edit Payment Received')

@section('content')
<div class="content-header">
  <div class="container-fluid-85">
    <div class="row mb-2">
      <div class="col-sm-6"><h1 class="m-0">Edit Payment Received</h1></div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('payment-receive.index') }}">Payments Received</a></li>
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
        <form class="prevent-multiple-submit" action="{{ route('payment-receive.update', $payment->id) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Customer <span class="text-danger">*</span></label>
                <select name="customer_id" class="form-control select2" required>
                  <option value="">-- Select Customer --</option>
                  @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ $payment->customer_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
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

          <!-- Pending invoices for selected customer -->
          <div class="row mt-2" id="invoices-for-customer" style="display:none;">
            <div class="col-12">
              <div class="card">
                <div class="card-body p-3">
                  <h6>Pending Invoices</h6>
                  <div class="table-responsive">
                    <table class="table table-sm table-bordered" id="CustomerInvoicesTable">
                      <thead>
                        <tr>
                          <th>Date</th>
                          <th>Invoice No</th>
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
                  <div class="mb-2"><strong>Amount Received:</strong> <span id="summary-amount-received">₹ 0.00</span></div>
                  <div class="mb-2"><strong>Amount used for Payments:</strong> <span id="summary-amount-used">₹ 0.00</span></div>
                  <div class="mb-2"><strong>Amount in Excess:</strong> <span id="summary-amount-excess">₹ 0.00</span></div>
                </div>
              </div>
              </div>
            </div>
          </div>

          <div class="card-footer p-0">
            <button class="btn-submit" type="submit">Update Payment</button>
            <a href="{{ route('payment-receive.index') }}" class="btn-cancel ml-2">Cancel</a>
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
    const customerSelect = document.querySelector('select[name="customer_id"]');
    const invoicesWrap = document.getElementById('invoices-for-customer');
    const invoicesTableBody = document.querySelector('#CustomerInvoicesTable tbody');
    const amountInput = document.querySelector('input[name="amount"]');
    const summaryReceived = document.getElementById('summary-amount-received');
    const summaryUsed = document.getElementById('summary-amount-used');
    const summaryExcess = document.getElementById('summary-amount-excess');
    const paymentId = {{ $payment->id }};

    // Store allocated invoice IDs to avoid duplicates when loading pending invoices
    let allocatedInvoiceIds = [];
    // Store existing allocations with invoice details
    let existingAllocations = {};

    function formatCurrency(v){ v = parseFloat(v) || 0; return '₹ ' + v.toFixed(2); }

    function recalcSummary(){
      const received = parseFloat(amountInput.value) || 0;
      let used = 0;
      invoicesTableBody.querySelectorAll('input.invoice-payment').forEach(function(inp){ used += parseFloat(inp.value) || 0; });
      const excess = Math.max(0, received - used);
      summaryReceived.textContent = formatCurrency(received);
      summaryUsed.textContent = formatCurrency(used);
      summaryExcess.textContent = formatCurrency(excess);
    }

    function buildRow(inv, isAllocated){
      const tr = document.createElement('tr');
      if (isAllocated) {
        tr.classList.add('table-active');
      }
      const dateTd = document.createElement('td'); dateTd.textContent = inv.invoice_date || '-';
      const noTd = document.createElement('td'); noTd.textContent = inv.invoice_no ?? ( (inv.invoice_id || ''));
      const projectTd = document.createElement('td'); projectTd.textContent = inv.project ?? '-';
      const amtTd = document.createElement('td'); amtTd.className = 'text-right'; amtTd.textContent = (inv.amount? ('₹ ' + parseFloat(inv.amount).toFixed(2)):'₹ 0.00');
      const dueTd = document.createElement('td'); dueTd.className = 'text-right';
      // For allocated invoices, show the original due amount (before allocation was applied)
      // The current due_amount has already been reduced by the allocation
      const originalDue = inv.allocated_amount !== undefined ? (parseFloat(inv.due_amount) + parseFloat(inv.allocated_amount)) : (typeof inv.due_amount !== 'undefined' ? parseFloat(inv.due_amount) : 0);
      dueTd.textContent = formatCurrency(originalDue);
      const payTd = document.createElement('td'); payTd.className = 'text-right';
      const chk = document.createElement('input'); chk.type='checkbox'; chk.className='full-pay-checkbox'; chk.title='Full payment'; chk.style.marginRight='6px';
      const input = document.createElement('input'); input.type='number'; input.step='0.01'; input.min=0; input.value=0; input.className='form-control form-control-sm invoice-payment';
      
      // Use original due amount as max for allocated invoices
      const dueVal = originalDue;
      if(!isNaN(dueVal) && dueVal > 0){ input.max = dueVal.toFixed(2); }
      
      input.addEventListener('input', function(e){
        const mx = parseFloat(this.max) || 0; let v = parseFloat(this.value) || 0; if(v>mx){ v=mx; this.value = v.toFixed(2);} 
        const received = parseFloat(amountInput.value) || 0; let sumExcl=0; invoicesTableBody.querySelectorAll('input.invoice-payment').forEach(function(inp){ if(inp !== e.target){ sumExcl += parseFloat(inp.value) || 0; } });
        const allowedForThis = Math.max(0, received - sumExcl); if(v>allowedForThis){ v = allowedForThis; this.value = v.toFixed(2); }
        const icon = this.parentElement.querySelector('.full-pay-checkbox'); if(icon && v < (parseFloat(this.max) || 0) - 0.0001){ icon.checked = false; this.readOnly = false; }
        recalcSummary();
      });
      input.name = 'invoice_payments[' + (inv.invoice_id || '') + ']';
      input.dataset.invoiceId = inv.invoice_id;
      input.dataset.projectId = inv.project_id;
      input.style.width = '100px';

      // Set existing allocation value
      if (isAllocated && inv.allocated_amount !== undefined && parseFloat(inv.allocated_amount) > 0){
        input.value = parseFloat(inv.allocated_amount).toFixed(2);
        if (parseFloat(input.value) >= (parseFloat(input.max) || 0) - 0.0001) { 
          chk.checked = true; 
          input.readOnly = true; 
        }
      }

      chk.addEventListener('change', function(e){ const mx = parseFloat(input.max) || 0; const received = parseFloat(amountInput.value) || 0; let sumExcl=0; invoicesTableBody.querySelectorAll('input.invoice-payment').forEach(function(inp){ if(inp !== input){ sumExcl += parseFloat(inp.value) || 0; } }); const allowedForThis = Math.max(0, received - sumExcl); if(this.checked){ const setVal = Math.min(mx, allowedForThis); input.value = setVal.toFixed(2); input.readOnly = true; } else { input.value = '0.00'; input.readOnly = false; } recalcSummary(); });
      payTd.appendChild(chk); payTd.appendChild(input);
      tr.appendChild(dateTd); tr.appendChild(noTd); tr.appendChild(projectTd); tr.appendChild(amtTd); tr.appendChild(dueTd); tr.appendChild(payTd);
      return tr;
    }

    function fetchAllocatedInvoices(){
      fetch('{{ route('payment-receive.allocated-invoices', $payment->id) }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => r.json())
      .then(data => {
        if(data && data.allocated_invoices && data.allocated_invoices.length > 0){
          allocatedInvoiceIds = data.allocated_invoice_ids || [];
          existingAllocations = data.allocated_invoices;
          
          // Build rows for allocated invoices
          existingAllocations.forEach(function(inv){ 
            const tr = buildRow(inv, true); 
            invoicesTableBody.appendChild(tr); 
          });
          
          invoicesWrap.style.display = '';
          recalcSummary();
          
          // Also fetch pending invoices for the same customer to allow adding more allocations
          if(data.payment && data.payment.customer_id){
            customerSelect.value = data.payment.customer_id;
            fetchPendingInvoices(data.payment.customer_id, allocatedInvoiceIds);
          }
        } else {
          // No allocations, just fetch pending invoices
          if(customerSelect.value){ fetchPendingInvoices(customerSelect.value, []); }
        }
      }).catch(err => { 
        console.error('Failed to load allocated invoices', err); 
        // Fallback to fetching pending invoices
        if(customerSelect.value){ fetchPendingInvoices(customerSelect.value, []); }
      });
    }

    function fetchPendingInvoices(customerId, excludeIds){
      if(!customerId){ 
        if(invoicesTableBody.children.length === 0) {
          invoicesWrap.style.display = 'none'; 
        }
        recalcSummary(); 
        return; 
      }
      if(!excludeIds) excludeIds = [];
      
      const params = new URLSearchParams({ length: 1000, start: 0, customer_id: customerId, status: 'pending' });
      fetch('{{ route('invoice.list') }}' + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => r.json())
      .then(data => {
        if(data && Array.isArray(data.data) && data.data.length){
          data.data.forEach(function(row){ 
            // Skip invoices that are already allocated (already in the table)
            if(excludeIds.includes(row.invoice_id)) return;
            const tr = buildRow(row, false); 
            invoicesTableBody.appendChild(tr); 
          }); 
        }
        invoicesWrap.style.display = '';
        recalcSummary();
      }).catch(err => { console.error('Failed to load invoices', err); });
    }

    if(customerSelect){
      customerSelect.addEventListener('change', function(){ 
        // When customer changes, reload all invoices for the new customer
        allocatedInvoiceIds = [];
        existingAllocations = {};
        invoicesTableBody.innerHTML = '';
        fetchPendingInvoices(this.value, []);
      });
      if(window.jQuery){ $(document).on('select2:select', 'select[name="customer_id"]', function(e){ 
        allocatedInvoiceIds = [];
        existingAllocations = {};
        invoicesTableBody.innerHTML = '';
        fetchPendingInvoices(this.value, []);
      }); }
    }

    const form = document.querySelector('form.prevent-multiple-submit');
    if(form){
      form.addEventListener('submit', function(e){ return true; });
    }

    if(amountInput){ amountInput.addEventListener('input', recalcSummary); }
    
    // Load allocated invoices on page load
    fetchAllocatedInvoices();
  })();
</script>
@endsection