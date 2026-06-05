@extends('admin.layouts.app')
@section('title', 'Create Purchase')

@section('content')
<div class="content-header">
  <div class="container-fluid-85">
    <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0">Create Purchase</h1></div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('purchase.index') }}">Purchases</a></li>
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
        <form class="prevent-multiple-submit" action="{{ route('purchase.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="row">

            {{-- Vendor --}}
            <div class="col-md-4">
              <div class="form-group">
                <label>Vendor <span class="text-danger">*</span></label>
                <select name="vendor_id" class="form-control select2" required>
                  <option value="">Select Vendor</option>
                  @foreach($vendors as $v)
                    <option value="{{ $v->id }}">{{ $v->name }}</option>
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

            {{-- Date --}}
            <div class="col-md-4">
              <div class="form-group">
                <label>Date <span class="text-danger">*</span></label>
                <input type="date" name="purchase_date" class="form-control"
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
                          <th style="width:45px;">SR.No.</th>
                          <th style="width:160px;">Select Item</th>
                          <th style="width:160px;">Expense Type <span class="text-danger">*</span></th>
                          <th style="width:80px;">Qty</th>
                          <th style="width:120px;">Amount (Per Piece)</th>
                          <th style="width:110px;">Total Amount</th>
                          <th style="width:50px;"></th>
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
                              @foreach($expenseSubCategories as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                              @endforeach
                            </select>
                          </td>
                          <td><input type="number" name="items[0][quantity]" min="1" step="0.01" class="form-control qty-input" value="1"></td>
                          <td><input type="number" name="items[0][amount]" step="0.01" class="form-control unit-price-input" value="0"></td>
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
                <div class="mr-4"><strong>Total Amount </strong></div>
                <div class="text-primary" id="items-total">0.00</div>
              </div>
            </div>

            {{-- Grand Total and Image in same row, different boxes --}}
            <div class="col-md-12 mt-4">
              <div class="row">
                <div class="col-md-6">
                  <div class="card shadow-sm">
                    <div class="card-body">
                      <label class="font-weight-bold">Attach Image</label>
                      <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="card shadow-sm">
                    <div class="card-body text-right">
                      <div class="mb-1"><strong>Grand Total</strong></div>
                      <div id="grand-total" class="text-success" style="font-size:1.8rem; font-weight:800">₹ 0.00</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {{-- Note --}}
            <div class="col-md-12 mt-3">
              <div class="form-group">
                <label>Note</label>
                <textarea name="note" class="form-control" rows="3"></textarea>
              </div>
            </div>

            <div class="card-footer py-0">
              <button class="btn-submit" type="submit"><i class="fa fa-save"></i> Save Order</button>
              <button type="button" id="reset-form" class="btn-cancel ml-2"><i class="fa fa-sync-alt mr-1"></i> Reset</button>
            </div>

            <input type="hidden" id="purchase-amount" name="amount" value="0">

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
          @foreach($expenseSubCategories as $s)
            <option value="{{ $s->id }}">{{ $s->name }}</option>
          @endforeach
        </select>

        <script>
        document.addEventListener('DOMContentLoaded', function () {
          let idx = 1;

          const fmt = n => parseFloat(n || 0).toFixed(2);

          function recalcRow(row) {
            const qty   = parseFloat(row.querySelector('.qty-input').value)        || 0;
            const unit  = parseFloat(row.querySelector('.unit-price-input').value) || 0;
            const total = qty * unit;
            row.querySelector('.row-total').textContent = fmt(total);
            return total;
          }

          function recalcTotals() {
            const rows = [...document.querySelectorAll('#items-body tr')];
            const itemsTotal = rows.reduce((sum, row) => sum + recalcRow(row), 0);

            document.getElementById('items-total').textContent = fmt(itemsTotal);
            const summaryEl = document.getElementById('summary-items-total');
            if (summaryEl) { summaryEl.textContent = fmt(itemsTotal); }
            const grandEl = document.getElementById('grand-total');
            if (grandEl) { grandEl.textContent = fmt(itemsTotal); }
            const purchaseAmount = document.getElementById('purchase-amount');
            if (purchaseAmount) { purchaseAmount.value = fmt(itemsTotal); }
          }

          function makeItemRow(i) {
            const itemOpts  = document.getElementById('_item_options_tpl').innerHTML;
            const subcatOpts = document.getElementById('_subcat_options_tpl').innerHTML;
            return `
              <td class="row-index">${i}</td>
              <td><select name="items[${idx}][item_id]" class="form-control">${itemOpts}</select></td>
              <td><select name="items[${idx}][sub_category_id]" class="form-control" required>${subcatOpts}</select></td>
              <td><input type="number" name="items[${idx}][quantity]" min="1" step="0.01" class="form-control qty-input" value="1"></td>
              <td><input type="number" name="items[${idx}][amount]" step="0.01" class="form-control unit-price-input" value="0"></td>
              <td class="text-right"><span class="row-total">0.00</span></td>
              <td class="text-center">
                <button type="button" class="btn btn-link text-danger remove-row" title="Delete"><i class="fa fa-trash"></i></button>
              </td>`;
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
              row.querySelector('.qty-input').value        = '1';
              row.querySelector('.unit-price-input').value = '0';
              row.querySelector('.row-total').textContent  = '0.00';
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

          // Reset
          document.getElementById('reset-form').addEventListener('click', function () {
            ['vendor_id', 'project_id'].forEach(n => {
              const el = document.querySelector(`[name="${n}"]`);
              if (el) el.value = '';
            });
            document.querySelector('[name="purchase_date"]').value = '{{ date("Y-m-d") }}';
            idx = 1;
            const tbody = document.querySelector('#items-body');
            tbody.innerHTML = `<tr>${makeItemRow(1)}</tr>`;
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