@extends('admin.layouts.app')
@section('title', 'Edit Payment')

@section('content')

<div class="content-header">
    <div class="container-fluid-85">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="mr-2 text-teal"></i>Edit Payment</h1>
            </div>
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

<div class="container-fluid-85">
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-pen mr-2"></i>Edit Payment</h3>
            <div class="card-tools">
                <a href="{{ route('payment-receive.index') }}" class="btn-cancel"><i class="fas fa-arrow-left mr-1"></i>Back</a>
            </div>
        </div>
        <form class="prevent-multiple-submit" action="{{ route('payment-receive.update', $payment->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <input type="hidden" name="project_id" id="payment_project_id" value="">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Payment Type <span class="text-danger">*</span></label>
                            <select name="payment_type" class="form-control select2" required>
                                <option value="cash" {{ old('payment_type', $payment->payment_type)=='cash'?'selected':'' }}>Cash</option>
                                <option value="online" {{ old('payment_type', $payment->payment_type)=='online'?'selected':'' }}>Online</option>
                                <option value="cheque" {{ old('payment_type', $payment->payment_type)=='cheque'?'selected':'' }}>Cheque</option>
                            </select>
                            @error('payment_type')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Customer <span class="text-danger">*</span></label>
                            <select name="customer_id" class="form-control select2" required>
                                <option value="">Select</option>
                                @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ old('customer_id', $payment->customer_id)==$c->id? 'selected':'' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                            @error('customer_id')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Amount <span class="text-danger">*</span></label>
                            <input type="number" name="amount" step="0.01" class="form-control" value="{{ old('amount', $payment->amount) }}" required>
                            @error('amount')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control" min="{{ date('Y-m-d') }}" value="{{ old('payment_date', $payment->payment_date) }}" required>
                            @error('payment_date')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
                <!-- Pending invoices list for selected customer -->
                <div class="row mt-3" id="invoices-for-customer" style="display:none;">
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
                                                <th class="text-right">Invoice Amount</th>
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
                        <div class="card m-0">
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
            </div>
            <div class="card-footer pt-0">
                <button type="submit" class="btn-submit"><i class="fas fa-save mr-1"></i>Update Payment</button>
                <a href="{{ route('payment-receive.index') }}" class="btn-cancel ml-2"><i class="fas fa-times mr-1"></i>Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection
        @section('pageScript')
        <script>
            (function(){
                const allocations = @json($allocations ?? []);
                const customerSelect = document.querySelector('select[name="customer_id"]');
                const invoicesWrap = document.getElementById('invoices-for-customer');
                const invoicesTableBody = document.querySelector('#CustomerInvoicesTable tbody');
                const amountInput = document.querySelector('input[name="amount"]');
                const summaryReceived = document.getElementById('summary-amount-received');
                const summaryUsed = document.getElementById('summary-amount-used');
                const summaryExcess = document.getElementById('summary-amount-excess');

                function formatCurrency(v){
                    v = parseFloat(v) || 0;
                    return '₹ ' + v.toFixed(2);
                }

                function recalcSummary(){
                    const received = parseFloat(amountInput.value) || 0;
                    let used = 0;
                    invoicesTableBody.querySelectorAll('input.invoice-payment').forEach(function(inp){
                        used += parseFloat(inp.value) || 0;
                    });
                    const excess = Math.max(0, received - used);
                    summaryReceived.textContent = formatCurrency(received);
                    summaryUsed.textContent = formatCurrency(used);
                    summaryExcess.textContent = formatCurrency(excess);

                    // toggle full-payment icon per row if present
                    invoicesTableBody.querySelectorAll('input.invoice-payment').forEach(function(inp){
                        const mx = parseFloat(inp.max) || 0;
                        const v = parseFloat(inp.value) || 0;
                        const icon = inp.parentElement.querySelector('.full-paid-icon');
                        if (icon) {
                            if (mx > 0 && v >= mx - 0.0001) { icon.style.display = 'inline-block'; }
                            else { icon.style.display = 'none'; }
                        }
                    });
                }

                function buildRow(inv){
                    const tr = document.createElement('tr');
                    const dateTd = document.createElement('td'); dateTd.textContent = inv.invoice_date || '-';
                    const noTd = document.createElement('td'); noTd.textContent = inv.invoice_no ?? ('#' + (inv.invoice_id || ''));
                    const projectTd = document.createElement('td'); projectTd.textContent = inv.project ?? '-';
                    const amtTd = document.createElement('td'); amtTd.className = 'text-right'; amtTd.textContent = (inv.amount? ('₹ ' + parseFloat(inv.amount).toFixed(2)):'₹ 0.00');
                    const dueTd = document.createElement('td'); dueTd.className = 'text-right'; dueTd.textContent = (typeof inv.due_amount !== 'undefined' ? ('₹ ' + parseFloat(inv.due_amount).toFixed(2)) : (inv.amount? ('₹ ' + parseFloat(inv.amount).toFixed(2)):'₹ 0.00'));
                    const payTd = document.createElement('td'); payTd.className = 'text-right';
                    // full-pay checkbox
                    const chk = document.createElement('input');
                    chk.type = 'checkbox'; chk.className = 'full-pay-checkbox'; chk.title = 'Full payment'; chk.style.marginRight = '6px';
                    const input = document.createElement('input');
                    input.type = 'number'; input.step = '0.01'; input.min = 0; input.value = 0; input.className = 'form-control form-control-sm invoice-payment';
                    // set max to invoice due amount to prevent overpayment in UI
                    const dueVal = (typeof inv.due_amount !== 'undefined') ? parseFloat(inv.due_amount) : (inv.amount? parseFloat(inv.amount): 0);
                    if (!isNaN(dueVal)) {
                        input.max = dueVal.toFixed(2);
                    }
                    // clamp input to max and to total payment amount on input
                    input.addEventListener('input', function(e){
                        const mx = parseFloat(this.max) || 0;
                        let v = parseFloat(this.value) || 0;
                        if (v > mx) { v = mx; this.value = v.toFixed(2); }

                        // ensure sum of all invoice payments does not exceed received amount
                        const received = parseFloat(amountInput.value) || 0;
                        let sumExcl = 0;
                        invoicesTableBody.querySelectorAll('input.invoice-payment').forEach(function(inp){ if (inp !== e.target) { sumExcl += parseFloat(inp.value) || 0; } });
                        const allowedForThis = Math.max(0, received - sumExcl);
                        if (v > allowedForThis) { v = allowedForThis; this.value = v.toFixed(2); }

                        // uncheck full-pay box if user manually changes value below max
                        const box = this.parentElement.querySelector('.full-pay-checkbox');
                        if (box && v < (parseFloat(this.max) || 0) - 0.0001) { box.checked = false; this.readOnly = false; }
                        recalcSummary();
                    });
                    input.name = 'invoice_payments[' + (inv.invoice_id || '') + ']';
                    input.dataset.invoiceId = inv.invoice_id;
                    input.dataset.projectId = inv.project_id;
                    input.style.width = '100px';

                    // checkbox behavior: when checked, set input to full due (clamped by remaining payment)
                    chk.addEventListener('change', function(e){
                        const mx = parseFloat(input.max) || 0;
                        const received = parseFloat(amountInput.value) || 0;
                        let sumExcl = 0;
                        invoicesTableBody.querySelectorAll('input.invoice-payment').forEach(function(inp){ if (inp !== input) { sumExcl += parseFloat(inp.value) || 0; } });
                        const allowedForThis = Math.max(0, received - sumExcl);
                        if (this.checked) {
                            const setVal = Math.min(mx, allowedForThis);
                            input.value = setVal.toFixed(2);
                            input.readOnly = true;
                        } else {
                            input.value = '0.00';
                            input.readOnly = false;
                        }
                        recalcSummary();
                    });

                    // add a small icon to indicate full payment for this invoice
                    const icon = document.createElement('span');
                    icon.className = 'ml-2 full-paid-icon';
                    icon.style.display = 'none';
                    icon.innerHTML = '<i class="fa fa-check-circle text-success" title="Fully paid"></i>';

                    payTd.appendChild(chk);
                    payTd.appendChild(input);
                    payTd.appendChild(icon);

                    tr.appendChild(dateTd);
                    tr.appendChild(noTd);
                    tr.appendChild(projectTd);
                    tr.appendChild(amtTd);
                    tr.appendChild(dueTd);
                    tr.appendChild(payTd);

                    // prefill allocation if present
                    setTimeout(function(){
                        const alloc = allocations[inv.invoice_id] || allocations[String(inv.invoice_id)];
                        if (alloc && parseFloat(alloc) > 0) {
                            // ensure not exceeding max/remaining
                            const mx = parseFloat(input.max) || 0;
                            const received = parseFloat(amountInput.value) || 0;
                            let sumExcl = 0;
                            invoicesTableBody.querySelectorAll('input.invoice-payment').forEach(function(inp){ if (inp !== input) { sumExcl += parseFloat(inp.value) || 0; } });
                            const allowedForThis = Math.max(0, received - sumExcl);
                            const setVal = Math.min(parseFloat(alloc), mx, allowedForThis);
                            input.value = setVal.toFixed(2);
                            if (setVal >= mx - 0.0001) { chk.checked = true; input.readOnly = true; }
                        }
                        recalcSummary();
                    }, 0);

                    return tr;
                }

                function fetchPendingInvoices(customerId){
                    if(!customerId){ invoicesTableBody.innerHTML = ''; invoicesWrap.style.display = 'none'; recalcSummary(); return; }
                    const params = new URLSearchParams({ length: 1000, start: 0, customer_id: customerId, status: 'Pending' });
                    fetch('{{ route('invoice.list') }}' + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.json())
                    .then(data => {
                        invoicesTableBody.innerHTML = '';
                        if(data && Array.isArray(data.data) && data.data.length){
                            data.data.forEach(function(row){
                                const tr = buildRow(row);
                                invoicesTableBody.appendChild(tr);
                            });
                            invoicesWrap.style.display = '';
                        } else {
                            invoicesWrap.style.display = 'none';
                        }
                        recalcSummary();
                    }).catch(err => {
                        console.error('Failed to load invoices', err);
                        invoicesWrap.style.display = 'none';
                    });
                }

                if(customerSelect){
                    customerSelect.addEventListener('change', function(){ fetchPendingInvoices(this.value); });
                    if (window.jQuery) {
                        $(document).on('select2:select', 'select[name="customer_id"]', function (e) { fetchPendingInvoices(this.value); });
                    }
                    // initial load for selected customer in edit
                    if(customerSelect.value){ fetchPendingInvoices(customerSelect.value); }
                }

                // ensure project_id is set before form submit
                const form = document.querySelector('form.prevent-multiple-submit');
                if(form) {
                    form.addEventListener('submit', function(e){
                        let projectId = '';
                        const payments = invoicesTableBody.querySelectorAll('input.invoice-payment');
                        payments.forEach(function(p){ if(!projectId){ if(parseFloat(p.value) > 0){ projectId = p.dataset.projectId || ''; } } });
                        if(!projectId && payments.length){ projectId = payments[0].dataset.projectId || ''; }
                        document.getElementById('payment_project_id').value = projectId;
                        return true;
                    });
                }

                if(amountInput){ amountInput.addEventListener('input', recalcSummary); }
            })();
        </script>
        @endsection