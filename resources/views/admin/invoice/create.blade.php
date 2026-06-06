@extends('admin.layouts.app')
@section('title', 'Create Invoice')

@section('content')
<div class="content-header">
    <div class="container-fluid-85">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Create Invoice</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('invoice.index') }}">Invoices</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid-85">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-body">
                <form class="prevent-multiple-submit" action="{{ route('invoice.store') }}" method="POST">
                    @csrf
                    <div class="row">

                        {{-- Customer --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Customer <span class="text-danger">*</span></label>
                                <select name="customer_id" class="form-control select2" required>
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Project --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Project <span class="text-danger">*</span></label>
                                <select name="project_id" class="form-control select2" required>
                                    <option value="">Select Project</option>
                                    @foreach($projects as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Invoice Date --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Invoice Date <span class="text-danger">*</span></label>
                                <input type="date" name="invoice_date" class="form-control"
                                    value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
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
                                                    <th style="width:40px;">SR.No.</th>
                                                    <th>Select Item</th>
                                                    <th>Income Type <span class="text-danger">*</span></th>
                                                    <th style="width:100px;">Qty</th>
                                                    <th style="width:160px;">Amount (Per Unit)</th>
                                                    <th style="width:140px;">Total Amount</th>
                                                    <th style="width:60px;"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="items-body">
                                                <tr>
                                                    <td class="row-index">1</td>
                                                    <td>
                                                        <select name="items[0][item_id]" class="form-control select2">
                                                            <option value="">Select Item</option>
                                                            @foreach($items as $it)
                                                                <option value="{{ $it->id }}">{{ $it->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="items[0][sub_category_id]" class="form-control select2" required>
                                                            <option value="">Select</option>
                                                            @foreach($incomeSubCategories as $s)
                                                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="items[0][qty]" min="1" step="0.01" class="form-control qty-input" value="1"></td>
                                                    <td><input type="number" name="items[0][unit_amount]" step="0.01" class="form-control unit-price-input" value="0"></td>
                                                    <td class="text-right"><span class="row-total">0.00</span></td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-link text-danger remove-row" title="Delete"><i class="fa fa-trash"></i></button>
                                                    </td>
                                                </tr>
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

                        {{-- Grand Total Summary --}}
                        <div class="col-md-12 mt-4">
                            <div class="card p-3">
                                <div class="row align-items-center">
                                    <div class="col-md-7">
                                        <div class="flex-grow-1"></div>
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
                                <textarea name="note" class="form-control" rows="3" required></textarea>
                            </div>
                        </div>

                        <div class="card-footer py-0">
                            <button class="btn-submit" type="submit"><i class="fa fa-save"></i> Save Invoice</button>
                            <button type="button" id="reset-form" class="btn-cancel ml-2"><i class="fa fa-sync-alt mr-1"></i> Reset</button>
                            <a href="{{ route('invoice.index') }}" class="btn-cancel ml-2"><i class="fas fa-times mr-1"></i> Cancel</a>
                        </div>

                        <input type="hidden" id="invoice-amount" name="amount" value="0">

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
                    let idx = 1;

                    const fmt = n => parseFloat(n || 0).toFixed(2);

                    function recalcRow(row) {
                        const qty   = parseFloat(row.querySelector('.qty-input').value) || 0;
                        const unit  = parseFloat(row.querySelector('.unit-price-input').value) || 0;
                        const total = qty * unit;
                        row.querySelector('.row-total').textContent = fmt(total);
                        return total;
                    }

                    function recalcTotals() {
                        const rows = [...document.querySelectorAll('#items-body tr')];
                        const itemsTotal = rows.reduce((sum, row) => sum + recalcRow(row), 0);

                        document.getElementById('items-total').textContent = fmt(itemsTotal);
                        document.getElementById('grand-total').textContent = fmt(itemsTotal);
                        document.getElementById('invoice-amount').value = fmt(itemsTotal);
                    }

                    function makeItemRow(i) {
                        const itemOpts   = document.getElementById('_item_options_tpl').innerHTML;
                        const subcatOpts = document.getElementById('_subcat_options_tpl').innerHTML;
                        return `
                            <td class="row-index">${i}</td>
                            <td><select name="items[${idx}][item_id]" class="form-control">${itemOpts}</select></td>
                            <td><select name="items[${idx}][sub_category_id]" class="form-control" required>${subcatOpts}</select></td>
                            <td><input type="number" name="items[${idx}][qty]" min="1" step="0.01" class="form-control qty-input" value="1"></td>
                            <td><input type="number" name="items[${idx}][unit_amount]" step="0.01" class="form-control unit-price-input" value="0"></td>
                            <td class="text-right"><span class="row-total">0.00</span></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-link text-danger remove-row" title="Delete"><i class="fa fa-trash"></i></button>
                            </td>
                        `;
                    }

                    function reindex() {
                        document.querySelectorAll('#items-body .row-index').forEach((el, i) => el.textContent = i + 1);
                    }

                    // Add row
                    document.getElementById('add-item').addEventListener('click', function () {
                        const tbody = document.querySelector('#items-body');
                        const tr = document.createElement('tr');
                        tr.innerHTML = makeItemRow(tbody.children.length + 1);
                        tbody.appendChild(tr);
                        idx++;
                        recalcTotals();
                    });

                    // Remove row (event delegation)
                    document.addEventListener('click', function (e) {
                        if (!e.target.closest('.remove-row')) return;
                        const tbody = document.querySelector('#items-body');
                        if (tbody.children.length <= 1) {
                            // Last row: clear instead of remove
                            const row = tbody.querySelector('tr');
                            row.querySelector('.qty-input').value = '1';
                            row.querySelector('.unit-price-input').value = '0';
                            row.querySelector('.row-total').textContent = '0.00';
                            recalcTotals();
                            return;
                        }
                        e.target.closest('tr').remove();
                        reindex();
                        recalcTotals();
                    });

                    // Recalc on input — delegated to tbody
                    document.getElementById('items-body').addEventListener('input', function (e) {
                        if (e.target.classList.contains('qty-input') || e.target.classList.contains('unit-price-input')) {
                            recalcTotals();
                        }
                    });

                    // Reset form
                    document.getElementById('reset-form').addEventListener('click', function () {
                        document.querySelector('[name="customer_id"]').value = '';
                        document.querySelector('[name="project_id"]').value = '';
                        document.querySelector('[name="invoice_date"]').value = '{{ date("Y-m-d") }}';
                        document.querySelector('[name="note"]').value = '';
                        idx = 1;
                        const tbody = document.querySelector('#items-body');
                        tbody.innerHTML = `<td>${makeItemRow(1)}</tr>`;
                        recalcTotals();
                    });

                    recalcTotals();
                });
                </script>
            </div>
        </div>
    </div>
</section>
@endsection