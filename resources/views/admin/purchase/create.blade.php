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
        <form class="prevent-multiple-submit" action="{{ route('purchase.store') }}" method="POST">
          @csrf
          <div class="row">
            <div class="col-md-3">
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

            <div class="col-md-3">
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

            <div class="col-md-3">
              <div class="form-group">
                <label>Date <span class="text-danger">*</span></label>
                <input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Subcategory <span class="text-danger">*</span></label>
                <select name="sub_category_id" class="form-control select2" required>
                  <option value="">Select Subcategory</option>
                  @foreach($expenseSubCategories as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-md-12 mt-2"></div>

            <div class="col-md-12">
              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h5 class="mb-0">Item List</h5>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered" id="items-table">
                      <thead>
                        <tr>
                          <th style="width:40px;">#</th>
                          <th>Select Item</th>
                          <th style="width:100px;">Qty</th>
                          <th style="width:320px;">Date Range</th>
                          <th style="width:160px;">Amount (Per Piece/Day)</th>
                          <th style="width:140px;">Total Amount</th>
                          <th style="width:60px;"></th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td class="row-index">1</td>
                          <td>
                            <select name="items[0][item_id]" class="form-control">
                              <option value="">Select Item</option>
                              @foreach($items as $it)
                                <option value="{{ $it->id }}">{{ $it->name }}</option>
                              @endforeach
                            </select>
                          </td>
                          <td><input type="number" name="items[0][quantity]" min="1" class="form-control qty-input" value="1"></td>
                          <td>
                            <div class="d-flex">
                              <input type="date" name="items[0][date_start]" class="form-control mr-2">
                              <div class="px-2 align-self-center">-</div>
                              <input type="date" name="items[0][date_end]" class="form-control ml-2">
                            </div>
                          </td>
                          <td><input type="number" name="items[0][amount]" step="0.01" class="form-control unit-price-input" value="0"></td>
                          <td class="text-right"><span class="row-total">0.00</span></td>
                          <td class="text-center"><button type="button" class="btn btn-link text-danger remove-row" title="Delete"><i class="fa fa-trash"></i></button></td>
                        </tr>
                      </tbody>
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

            <div class="col-md-12 mt-2">
              <div class="d-flex justify-content-end align-items-center">
                <div class="mr-4"><strong>Total Amount (Item List):</strong></div>
                <div class="text-primary" id="items-total">0.00</div>
              </div>
            </div>

            {{-- Labour List --}}
            <div class="col-md-12 mt-4">
              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h5 class="mb-0">Labour List</h5>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered" id="labours-table">
                      <thead>
                        <tr>
                          <th style="width:40px;">#</th>
                          <th>Labour (Category)</th>
                          <th style="width:100px;">Numbers</th>
                          <th style="width:320px;">Date Range</th>
                          <th style="width:180px;">Amount (Per Day/Person)</th>
                          <th style="width:140px;">Total Amount</th>
                          <th style="width:60px;"></th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td class="row-index">1</td>
                          <td><input type="text" name="labours[0][labour]" class="form-control" placeholder="Category"></td>
                          <td><input type="number" name="labours[0][numbers]" min="1" class="form-control labour-numbers" value="1"></td>
                          <td>
                            <div class="d-flex">
                              <input type="date" name="labours[0][date_start]" class="form-control mr-2">
                              <div class="px-2 align-self-center">-</div>
                              <input type="date" name="labours[0][date_end]" class="form-control ml-2">
                            </div>
                          </td>
                          <td><input type="number" name="labours[0][amount]" step="0.01" class="form-control labour-amount" value="0"></td>
                          <td class="text-right"><span class="labour-row-total">0.00</span></td>
                          <td class="text-center"><button type="button" class="btn btn-link text-danger remove-labour-row" title="Delete"><i class="fa fa-trash"></i></button></td>
                        </tr>
                        
                      </tbody>
                      </tbody>
                      <tfoot>
                        <tr>
                          <td colspan="7">
                            <div class="d-flex justify-content-end">
                              <button type="button" id="add-labour" class="btn-submit">+ Add New Row</button>
                            </div>
                          </td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-12 mt-2">
              <div class="d-flex justify-content-end align-items-center">
                <div class="mr-4"><strong>Total Amount (Labour List):</strong></div>
                <div class="text-primary" id="labours-total">0.00</div>
              </div>
            </div>

            <div class="col-md-12 mt-4">
              <div class="card p-3">
                <div class="row align-items-center">
                  <div class="col-md-7">
                    <div class="d-flex">
                        <div class="pr-4 border-right mr-4"></div>
                      <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                          <div><strong>Total Amount (Item List)</strong></div>
                          <div class="text-primary" id="summary-items-total">0.00</div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                          <div><strong>Total Amount (Labour List)</strong></div>
                          <div class="text-primary" id="summary-labours-total-small">0.00</div>
                        </div>
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
            <div class="col-md-12 mt-3">
              <div class="form-group">
                <label>Note</label>
                <textarea name="note" class="form-control" rows="3"></textarea>
              </div>
            </div>
            <div class="col-md-12 mt-3 d-flex justify-content-end align-items-center">
              <button type="button" id="reset-form" class="btn btn-outline-secondary mr-3"><i class="fa fa-sync-alt mr-1"></i> Reset</button>
              <button class="btn btn-primary" type="submit"><i class="fa fa-save mr-1"></i> Save Order</button>
            </div>

            <input type="hidden" id="purchase-amount" name="amount" value="0">

            
          </div>
        </form>

    <script>
      document.addEventListener('DOMContentLoaded', function () {
        let idx = 1;

        const fmt = n => new Intl.NumberFormat(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}).format(n);

        function calcDays(row) {
          const s = row.querySelector('input[name*="[date_start]"]').value;
          const e = row.querySelector('input[name*="[date_end]"]').value;
          if (!s || !e) return 1;
          const diff = Math.floor((new Date(e) - new Date(s)) / 86400000);
          return diff >= 0 ? diff + 1 : 1;
        }

        function recalcRow(row) {
          const total = (parseFloat(row.querySelector('.qty-input').value) || 0)
                      * (parseFloat(row.querySelector('.unit-price-input').value) || 0)
                      * calcDays(row);
          row.querySelector('.row-total').textContent = fmt(total);
          return total;
        }

        function recalcLabourRow(row) {
          const total = (parseFloat(row.querySelector('.labour-numbers').value) || 0)
                      * (parseFloat(row.querySelector('.labour-amount').value) || 0)
                      * calcDays(row);
          row.querySelector('.labour-row-total').textContent = fmt(total);
          return total;
        }

        function recalcTotals() {
          const sum = (sel, fn) => [...document.querySelectorAll(sel)].reduce((t, r) => t + fn(r), 0);
          const itemsTotal   = sum('#items-table tbody tr', recalcRow);
          const laboursTotal = sum('#labours-table tbody tr', recalcLabourRow);
          const grand = itemsTotal + laboursTotal;

          document.getElementById('items-total').textContent            = fmt(itemsTotal);
          document.getElementById('summary-items-total').textContent    = fmt(itemsTotal);
          document.getElementById('labours-total').textContent          = fmt(laboursTotal);
          document.getElementById('summary-labours-total-small').textContent = fmt(laboursTotal);
          document.getElementById('grand-total').textContent            = fmt(grand);
          document.getElementById('purchase-amount').value              = grand.toFixed(2);
        }

        function makeItemRow(i) {
          return `
            <td class="row-index">${i}</td>
            <td>
              <select name="items[${idx}][item_id]" class="form-control">
                <option value="">Select Item</option>
                @foreach($items as $it)
                  <option value="{{ $it->id }}">{{ $it->name }}</option>
                @endforeach
              </select>
            </td>
            <td><input type="number" name="items[${idx}][quantity]" min="1" class="form-control qty-input" value="1"></td>
            <td>
              <div class="d-flex">
                <input type="date" name="items[${idx}][date_start]" class="form-control mr-2">
                <div class="px-2 align-self-center">-</div>
                <input type="date" name="items[${idx}][date_end]" class="form-control ml-2">
              </div>
            </td>
            <td><input type="number" name="items[${idx}][amount]" step="0.01" class="form-control unit-price-input" value="0"></td>
            <td class="text-right"><span class="row-total">0.00</span></td>
            <td class="text-center"><button type="button" class="btn btn-link text-danger remove-row" title="Delete"><i class="fa fa-trash"></i></button></td>`;
        }

        function makeLabourRow(i) {
          return `
            <td class="row-index">${i}</td>
            <td><input type="text" name="labours[${idx}][labour]" class="form-control" placeholder="Category"></td>
            <td><input type="number" name="labours[${idx}][numbers]" min="1" class="form-control labour-numbers" value="1"></td>
            <td>
              <div class="d-flex">
                <input type="date" name="labours[${idx}][date_start]" class="form-control mr-2">
                <div class="px-2 align-self-center">-</div>
                <input type="date" name="labours[${idx}][date_end]" class="form-control ml-2">
              </div>
            </td>
            <td><input type="number" name="labours[${idx}][amount]" step="0.01" class="form-control labour-amount" value="0"></td>
            <td class="text-right"><span class="labour-row-total">0.00</span></td>
            <td class="text-center"><button type="button" class="btn btn-link text-danger remove-labour-row" title="Delete"><i class="fa fa-trash"></i></button></td>`;
        }

        function addRow(tbodySel, makeFn) {
          const tbody = document.querySelector(tbodySel);
          const tr = document.createElement('tr');
          tr.innerHTML = makeFn(tbody.children.length + 1);
          tbody.appendChild(tr);
          idx++;
          recalcTotals();
        }

        function reindex(tbodySel) {
          document.querySelectorAll(`${tbodySel} .row-index`).forEach((el, i) => el.textContent = i + 1);
        }

        document.getElementById('add-item').addEventListener('click',   () => addRow('#items-table tbody',   makeItemRow));
        document.getElementById('add-labour').addEventListener('click', () => addRow('#labours-table tbody', makeLabourRow));

        document.addEventListener('input', e => {
          if (e.target.matches('.qty-input, .unit-price-input, .labour-amount, .labour-numbers')) recalcTotals();
        });

        document.addEventListener('change', e => {
          if (e.target.matches('input[type="date"]')) recalcTotals();
        });

        document.addEventListener('click', e => {
          if (e.target.closest('.remove-row')) {
            e.target.closest('tr').remove();
            reindex('#items-table tbody');
            recalcTotals();
          }
          if (e.target.closest('.remove-labour-row')) {
            e.target.closest('tr').remove();
            reindex('#labours-table tbody');
            recalcTotals();
          }
        });

        document.getElementById('reset-form').addEventListener('click', function () {
          ['vendor_id','project_id','sub_category_id'].forEach(n => document.querySelector(`[name="${n}"]`).value = '');
          document.querySelector('[name="purchase_date"]').value = '{{ date("Y-m-d") }}';
          idx = 1;
          const tbody = document.querySelector('#items-table tbody');
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
