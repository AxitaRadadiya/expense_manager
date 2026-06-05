@extends('admin.layouts.app')
@section('title', 'Edit Invoice')

@section('content')
<div class="content-header">
    <div class="container-fluid-85">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Invoice</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('invoice.index') }}">Invoices</a></li>
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
                <form action="{{ route('invoice.update', $invoice->id) }}" method="POST" id="invoice-form">
                    @csrf
                    @method('PUT')
                    <div class="row">

                        {{-- Customer --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Customer <span class="text-danger">*</span></label>
                                <select name="customer_id" class="form-control select2" required>
                                    <option value="">-- Select Customer --</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}" {{ $invoice->customer_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Project --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Project <span class="text-danger">*</span></label>
                                <select name="project_id" class="form-control select2" required>
                                    <option value="">-- Select Project --</option>
                                    @foreach($projects as $p)
                                        <option value="{{ $p->id }}" {{ $invoice->project_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Invoice Date --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Invoice Date <span class="text-danger">*</span></label>
                                <input type="date" name="invoice_date" class="form-control"
                                    value="{{ $invoice->invoice_date }}" required>
                            </div>
                        </div>

                        {{-- Item List --}}
                        <div class="col-md-12 mt-2">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Item List</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="items-table">
                                            <thead>
                                                <tr>
                                                    <th style="width:45px;">SR No.</th>
                                                    <th style="width:160px;">Select Item</th>
                                                    <th style="width:160px;">Income Type <span class="text-danger">*</span></th>
                                                    <th style="width:80px;">Qty</th>
                                                    <th style="width:120px;">Amount (Per Unit)</th>
                                                    <th style="width:110px;">Total Amount</th>
                                                    <th style="width:50px;"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="items-body">
                                                @if($invoice->invoiceItems && $invoice->invoiceItems->count() > 0)
                                                    @foreach($invoice->invoiceItems as $index => $item)
                                                    <tr class="invoice-item-row">
                                                        <td class="row-index">{{ $loop->iteration }}</td>
                                                        <td>
                                                            <select name="items[{{ $index }}][item_id]" class="form-control item-select select2">
                                                                <option value="">Select Item</option>
                                                                @foreach($items as $it)
                                                                    <option value="{{ $it->id }}" {{ $item->item_id == $it->id ? 'selected' : '' }}>{{ $it->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select name="items[{{ $index }}][sub_category_id]" class="form-control subcategory-select select2" required>
                                                                <option value="">Select</option>
                                                                @foreach($incomeSubCategories as $s)
                                                                    <option value="{{ $s->id }}" {{ $item->sub_category_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><input type="number" name="items[{{ $index }}][qty]" min="1" step="0.01" class="form-control qty-input" value="{{ $item->qty }}"></td>
                                                        <td><input type="number" name="items[{{ $index }}][unit_amount]" step="0.01" class="form-control unit-price-input" value="{{ $item->unit_amount }}"></td>
                                                        <td class="text-right"><span class="row-total">{{ number_format($item->qty * $item->unit_amount, 2) }}</span></td>
                                                        <td class="text-center"><button type="button" class="btn btn-link text-danger remove-row" title="Delete"><i class="fa fa-trash"></i></button></td>
                                                    </tr>
                                                    @endforeach
                                                @else
                                                    <tr class="invoice-item-row">
                                                        <td class="row-index">1</td>
                                                        <td>
                                                            <select name="items[0][item_id]" class="form-control item-select">
                                                                <option value="">Select Item</option>
                                                                @foreach($items as $it)
                                                                    <option value="{{ $it->id }}">{{ $it->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select name="items[0][sub_category_id]" class="form-control subcategory-select" required>
                                                                <option value="">Select</option>
                                                                @foreach($incomeSubCategories as $s)
                                                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><input type="number" name="items[0][qty]" min="1" step="0.01" class="form-control qty-input" value="1"></td>
                                                        <td><input type="number" name="items[0][unit_amount]" step="0.01" class="form-control unit-price-input" value="0"></td>
                                                        <td class="text-right"><span class="row-total">0.00</span></td>
                                                        <td class="text-center"><button type="button" class="btn btn-link text-danger remove-row" title="Delete"><i class="fa fa-trash"></i></button></td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="7">
                                                        <div class="d-flex justify-content-end">
                                                            <button type="button" id="add-item" class="btn-submit">+ Add New Row</button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Items subtotal --}}
                        <div class="col-md-12 mt-2">
                            <div class="d-flex justify-content-end align-items-center">
                                <div class="mr-4"><strong>Total Amount</strong></div>
                                <div class="text-primary" id="items-total">0.00</div>
                            </div>
                        </div>

                        {{-- Due Amount Info --}}
                        @if(($invoice->due_amount ?? 0) > 0)
                        <div class="col-md-12 mt-2">
                            <div class="d-flex justify-content-end align-items-center">
                                <div class="mr-4"><strong>Due Amount (Current)</strong></div>
                                <div class="text-warning" style="font-size:1.2rem; font-weight:600">₹ {{ number_format($invoice->due_amount, 2) }}</div>
                            </div>
                        </div>
                        @endif

                        {{-- Grand Total Summary --}}
                        <div class="col-md-12 mt-4">
                            <div class="card p-3">
                                <div class="row align-items-center">
                                    <div class="col-md-7">
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div><strong>Total Amount</strong></div>
                                                <div class="text-primary" id="summary-items-total">0.00</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="d-flex justify-content-end align-items-center">
                                            <div class="text-right">
                                                <div class="mb-1"><strong>Grand Total</strong></div>
                                                <div id="grand-total" class="text-success" style="font-size:1.8rem; font-weight:800">0.00</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Note --}}
                        <div class="col-md-12 mt-3">
                            <div class="form-group">
                                <label>Note <span class="text-danger">*</span></label>
                                <textarea name="note" class="form-control" rows="3" required>{{ $invoice->note }}</textarea>
                            </div>
                        </div>

                        <input type="hidden" id="invoice-amount" name="amount" value="{{ $invoice->amount }}">
                        <input type="hidden" id="total-paid" name="total_paid" value="{{ $invoice->amount - ($invoice->due_amount ?? 0) }}">

                    </div>

                    <div class="card-footer p-0">
                        <button class="btn-submit" type="submit"><i class="fa fa-save mr-1"></i> Update Invoice</button>
                        <a href="{{ route('invoice.index') }}" class="btn-cancel ml-2"><i class="fa fa-times mr-1"></i> Cancel</a>
                    </div>
                </form>

                {{-- Hidden option templates for JS row cloning --}}
                <select id="_item_options_tpl" style="display:none">
                    <option value="">Select Item</option>
                    @foreach($items as $it)
                        <option value="{{ $it->id }}">{{ $it->name }}</option>
                    @endforeach
                </select>
                <select id="_subcat_options_tpl" style="display:none">
                    <option value="">Select</option>
                    @foreach($incomeSubCategories as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>

                <script>
                document.addEventListener('DOMContentLoaded', function () {
                    let currentMaxIndex = {{ $invoice->invoiceItems ? $invoice->invoiceItems->count() : 1 }};
                    const totalPaid = parseFloat(document.getElementById('total-paid').value) || 0;

                    const fmt = n => parseFloat(n || 0).toFixed(2);

                    function recalcRow(row) {
                        const qty  = parseFloat(row.querySelector('.qty-input').value) || 0;
                        const unit = parseFloat(row.querySelector('.unit-price-input').value) || 0;
                        const total = qty * unit;
                        const totalSpan = row.querySelector('.row-total');
                        if (totalSpan) {
                            totalSpan.textContent = fmt(total);
                        }
                        return total;
                    }

                    function recalcTotals() {
                        const rows = [...document.querySelectorAll('#items-body .invoice-item-row')];
                        const itemsTotal = rows.reduce((sum, row) => sum + recalcRow(row), 0);

                        const itemsTotalElem = document.getElementById('items-total');
                        const summaryItemsTotalElem = document.getElementById('summary-items-total');
                        const grandTotalElem = document.getElementById('grand-total');
                        const invoiceAmountElem = document.getElementById('invoice-amount');
                        
                        if (itemsTotalElem) itemsTotalElem.textContent = fmt(itemsTotal);
                        if (summaryItemsTotalElem) summaryItemsTotalElem.textContent = fmt(itemsTotal);
                        if (grandTotalElem) grandTotalElem.textContent = fmt(itemsTotal);
                        if (invoiceAmountElem) invoiceAmountElem.value = fmt(itemsTotal);
                    }

                    function makeItemRow() {
                        const itemOpts = document.getElementById('_item_options_tpl').innerHTML;
                        const subcatOpts = document.getElementById('_subcat_options_tpl').innerHTML;
                        const newIndex = currentMaxIndex;
                        currentMaxIndex++;
                        
                        return `
                            <tr class="invoice-item-row">
                                <td class="row-index">${newIndex + 1}</td>
                                <td><select name="items[${newIndex}][item_id]" class="form-control item-select">${itemOpts}</select></td>
                                <td><select name="items[${newIndex}][sub_category_id]" class="form-control subcategory-select" required>${subcatOpts}</select></td>
                                <td><input type="number" name="items[${newIndex}][qty]" min="1" step="0.01" class="form-control qty-input" value="1"></td>
                                <td><input type="number" name="items[${newIndex}][unit_amount]" step="0.01" class="form-control unit-price-input" value="0"></td>
                                <td class="text-right"><span class="row-total">0.00</span></td>
                                <td class="text-center"><button type="button" class="btn btn-link text-danger remove-row" title="Delete"><i class="fa fa-trash"></i></button></td>
                            </tr>
                        `;
                    }

                    function reindexRows() {
                        document.querySelectorAll('#items-body .invoice-item-row').forEach((row, idx) => {
                            const indexCell = row.querySelector('.row-index');
                            if (indexCell) {
                                indexCell.textContent = idx + 1;
                            }
                            
                            // Update name attributes with new indices
                            const selects = row.querySelectorAll('select');
                            const inputs = row.querySelectorAll('input');
                            
                            selects.forEach(select => {
                                const name = select.getAttribute('name');
                                if (name) {
                                    const newName = name.replace(/items\[\d+\]/, `items[${idx}]`);
                                    select.setAttribute('name', newName);
                                }
                            });
                            
                            inputs.forEach(input => {
                                const name = input.getAttribute('name');
                                if (name && !name.includes('_paid') && !name.includes('amount')) {
                                    const newName = name.replace(/items\[\d+\]/, `items[${idx}]`);
                                    input.setAttribute('name', newName);
                                }
                            });
                        });
                    }

                    // Add row
                    const addButton = document.getElementById('add-item');
                    if (addButton) {
                        addButton.addEventListener('click', function () {
                            const tbody = document.getElementById('items-body');
                            const newRow = makeItemRow();
                            tbody.insertAdjacentHTML('beforeend', newRow);
                            recalcTotals();
                        });
                    }

                    // Remove row (event delegation)
                    document.addEventListener('click', function (e) {
                        if (!e.target.closest('.remove-row')) return;
                        const tbody = document.getElementById('items-body');
                        if (tbody.children.length <= 1) {
                            const row = tbody.querySelector('.invoice-item-row');
                            if (row) {
                                const qtyInput = row.querySelector('.qty-input');
                                const unitInput = row.querySelector('.unit-price-input');
                                const totalSpan = row.querySelector('.row-total');
                                if (qtyInput) qtyInput.value = '1';
                                if (unitInput) unitInput.value = '0';
                                if (totalSpan) totalSpan.textContent = '0.00';
                                
                                // Reset selects
                                const itemSelect = row.querySelector('.item-select');
                                const subcatSelect = row.querySelector('.subcategory-select');
                                if (itemSelect) itemSelect.value = '';
                                if (subcatSelect) subcatSelect.value = '';
                            }
                            recalcTotals();
                            return;
                        }
                        e.target.closest('.invoice-item-row').remove();
                        reindexRows();
                        recalcTotals();
                    });

                    // Recalc on input
                    const itemsBody = document.getElementById('items-body');
                    if (itemsBody) {
                        itemsBody.addEventListener('input', function (e) {
                            if (e.target.classList.contains('qty-input') || e.target.classList.contains('unit-price-input')) {
                                recalcTotals();
                            }
                        });
                    }

                    // Run on load
                    recalcTotals();
                });
                </script>

            </div>
        </div>
    </div>
</section>

@endsection