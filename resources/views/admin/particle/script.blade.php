<!-- jQuery -->
<script src="{{ asset('admin/plugins/jquery/jquery.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('admin/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{ asset('admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('admin/plugins/select2/js/select2.full.min.js') }}"></script>
<!-- ChartJS -->
{{-- <script src="{{ asset('admin/plugins/chart.js/Chart.min.js') }}"></script> --}}
<!-- Sparkline -->
<script src="{{ asset('admin/plugins/sparklines/sparkline.js') }}"></script>
<!-- JQVMap -->
{{-- <script src="{{ asset('admin/plugins/jqvmap/jquery.vmap.min.js') }}"></script> --}}
{{-- <script src="{{ asset('admin/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script> --}}
<!-- jQuery Knob Chart -->
<script src="{{ asset('admin/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
<!-- daterangepicker -->
<script src="{{ asset('admin/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('admin/plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('admin/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<!-- Summernote -->
<script src="{{ asset('admin/plugins/summernote/summernote-bs4.min.js') }}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('admin/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<!-- bs-custom-file-input -->
<script src="{{ asset('admin/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<!-- SweetAlert2 -->
<script src="{{ asset('admin/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Toastr -->
<script src="{{ asset('admin/plugins/toastr/toastr.min.js') }}"></script>
<!-- DataTables  & Plugins -->
<script src="{{ asset('admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('admin/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('admin/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('admin/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('admin/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('admin/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('admin/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('admin/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('admin/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
{{-- <script src="{{ asset('admin/plugins/autocomplete/jquery.autocomplete.js') }}"></script> --}}
<!-- AdminLTE App -->
<script src="{{ asset('admin/dist/js/adminlte.js') }}"></script>
<!-- AdminLTE for demo purposes -->
{{-- <script src="{{ asset('admin/dist/js/demo.js') }}"></script> --}}
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{ asset('admin/dist/js/pages/dashboard.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
<script src="{!!asset('admin/dist/js/numeric.js')!!}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

<script>
// Toggle Labour fields in expense create/edit forms
$(function () {
    function toggleLabourFields() {
        var cat = $('#category').val();
        if (cat === 'Labour') {
            $('#labourFields').removeClass('d-none');
            $('#vendor_id, #start_date, #end_date, #total_labour').attr('required', true);
        } else {
            $('#labourFields').addClass('d-none');
            $('#vendor_id, #start_date, #end_date, #total_labour').removeAttr('required').val('');
        }
    }

    // Bind change event (works with native select and select2)
    $(document).on('change', '#category', toggleLabourFields);
    $(document).on('select2:select', '#category', toggleLabourFields);

    // Initialize on page load
    toggleLabourFields();
});
</script>

<script>
    $(function () {
    })
</script>

<script>
$(document).ready(function () {

    $('.select2').select2();

    toastr.options = {
        closeButton: true,
        progressBar: true,
        newestOnTop: true,
        positionClass: 'toast-top-right',
        timeOut: 5000,
        extendedTimeOut: 1500
    };

    // Date range pickers
    $('.single_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        autoUpdateInput: false,
        locale: { format: 'MM/YYYY' }
    }).on('apply.daterangepicker', function (e, picker) {
        picker.element.val(picker.startDate.format(picker.locale.format));
    });

    $('.dob').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        autoUpdateInput: false,
        locale: { format: 'DD/MM/YYYY' }
    }).on('apply.daterangepicker', function (e, picker) {
        picker.element.val(picker.startDate.format(picker.locale.format));
    });

    bsCustomFileInput.init();

    var Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });

    @if (session('success'))
        Toast.fire({ icon: 'success', title: '{{ session('success') }}' })
    @endif
    @if (session('error'))
        Toast.fire({ icon: 'error', title: '{{ session('error') }}' })
    @endif
    @if (session('warning'))
        Toast.fire({ icon: 'warning', title: '{{ session('warning') }}' })
    @endif
    @if ($errors->any())
        const validationErrors = @json($errors->all());
        validationErrors.forEach(function (message) {
            toastr.error(message, 'Validation Error');
        });
    @endif

    $(document).on('click', '.deleteButton', function (event) {
        event.preventDefault();
        const form = this.closest('form');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => { if (result.isConfirmed) form.submit(); });
    });

    // Roles table
    $('#roleTable').DataTable({
        paging: true, lengthChange: true, searching: true, ordering: true, info: true,
        autoWidth: false, responsive: true, processing: true, serverSide: true,
        order: [0, 'asc'],
        ajax: { url: '{{ route('roles.list') }}', dataType: 'json', type: 'GET', data: { _token: '{{csrf_token()}}', route: 'roles.list' } },
        columns: [{ data: 'id' }, { data: 'name' }, { data: 'action' }],
        aoColumnDefs: [{ bSortable: false, aTargets: [-1] }]
    });

    // Permissions table
    $('#permissionsTable').DataTable({
        paging: true, lengthChange: false, searching: true, ordering: true, info: true,
        autoWidth: false, responsive: true, processing: true, serverSide: true,
        order: [0, 'desc'],
        ajax: { url: '{{ route('permissions.list') }}', dataType: 'json', type: 'GET', data: { _token: '{{csrf_token()}}', route: 'permissions.list' } },
        columns: [{ data: 'id' }, { data: 'name' }, { data: 'action' }],
        aoColumnDefs: [{ bSortable: false, aTargets: [-1] }]
    });

    // Users table loader
    function load_user() {
        $('#userTable').DataTable({
            paging: true, lengthChange: true, searching: true, ordering: true, info: true,
            autoWidth: false, responsive: true, processing: true, serverSide: true,
            order: [0, 'desc'],
            ajax: {
                url: '{{ route('users.list') }}',
                dataType: 'json',
                type: 'GET',
                data: { _token: '{{csrf_token()}}' }
            },
            columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'email' },
            { data: 'mobile' },
            { data: 'project' },
            { data: 'amount' },
            { data: 'role' },
            { data: 'status' },
            { data: 'action' }
        ]
            , aoColumnDefs: [{ bSortable: false, aTargets: [-1] }]
        });
    }
    load_user();

    // Projects table loader
    function load_projects() {
        $('#projectsTable').DataTable({
            paging: true, lengthChange: true, searching: true, ordering: true, info: true,
            autoWidth: false, responsive: true, processing: true, serverSide: true,
            order: [0, 'desc'],
            ajax: { url: '{{ route('projects.list') }}', dataType: 'json', type: 'GET', data: { _token: '{{csrf_token()}}', route: 'projects.list' } },
            columns: [ { data: 'id' }, { data: 'name' }, { data: 'start_date' }, { data: 'end_date' }, { data: 'users_count' }, { data: 'action' } ],
            aoColumnDefs: [{ bSortable: false, aTargets: [-1] }]
        });
    }
    load_projects();

    // Expense table loader
    function load_expense() {
        $('#ExpenseTable').DataTable({
            processing: true, serverSide: true, responsive: true, autoWidth: false, order: [[0, 'desc']],
            ajax: { url: '{{ route('expense.list') }}', type: 'GET', data: { _token: '{{ csrf_token() }}' } },
            columns: [
                { data: 'id', name: 'id' }, { data: 'project', name: 'project', orderable: false }, { data: 'expense_date', name: 'expense_date' },
                { data: 'amount', name: 'amount' }, { data: 'payment_mode', name: 'payment_mode', orderable: false }, { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    }
    load_expense();

    // Category table
   function load_category() {
            var table = $('#CategoryTable').DataTable({
                paging: true, 
                lengthChange: true, 
                searching: true, 
                ordering: true, 
                info: true,
                autoWidth: false, 
                responsive: true, 
                processing: true, 
                serverSide: true, 
                order: [[2, 'asc']],
                rowReorder: { dataSrc: 'position', selector: '.drag-handle' },
                ajax: { 
                    url: '{{ route("category.list") }}', 
                    dataType: 'json', 
                    type: 'GET', 
                    data: { _token: '{{ csrf_token() }}' },
                    error: function(xhr) {
                        console.log('DataTable Ajax Error - Status: ' + xhr.status);
                        console.log('Response: ' + xhr.responseText);
                    }
                },
                columns: [
                    { data: 'position', orderable: false, className: 'drag-handle'},
                    { data: 'id', orderable: false },
                    { data: 'name' },
                    { data: 'action', orderable: false, searchable: false }
                ],
                aoColumnDefs: [{ bSortable: false, aTargets: [-1] }],
            language: { paginate: { previous: "Previous", next: "Next" } },
            drawCallback: function () { $('.dataTables_paginate > .pagination').addClass('pagination-rounded'); $('[data-toggle="tooltip"]').tooltip(); }
            });

            table.on('row-reorder', function (e, diff) {
                var order = [];
            diff.forEach(function (item) { var rowData = table.row(item.node).data(); order.push({ id: rowData.id, position: item.newPosition + 1 }); });
            });
        }
        load_category();

        // Modal handlers
        $(document).on('click', '.category-date-modal', function () {
            $('#categoryForm')[0].reset();
            $('#categoryForm').attr('action', '{{ route("category.store") }}');
            $('#categoryForm').find('input[name="_method"]').remove();
            $('#category_id').val('');
            $('.error').text('');
            $('input').removeClass('is-invalid');
            $('#CategoryModal').modal('show');
        });

        $(document).on('click', '.edit-category-date-modal', function () {
            let categoryId = $(this).data('id');
            let categoryName = $(this).data('name');
            $('#categoryForm')[0].reset();
            $('#category_id').val(categoryId);
            $('#category_name').val(categoryName);
            $('.error').text('');
            $('input').removeClass('is-invalid');
            let updateUrl = '{{ route("category.update", ":id") }}'.replace(':id', categoryId);
            $('#categoryForm').attr('action', updateUrl);
            $('#categoryForm').find('input[name="_method"]').remove();
            $('#categoryForm').append('<input type="hidden" name="_method" value="PUT">');
            $('#CategoryModal').modal('show');
        });

    $('#CategoryModal').on('hidden.bs.modal', function () { $('#categoryForm')[0].reset(); $('#categoryForm').find('input[name="_method"]').remove(); $('#category_id').val(''); $('.error').text(''); $('input').removeClass('is-invalid'); });

        // Save the Category (client validation)
        $(document).on('click', '#saveCategory', function (e) {
            e.preventDefault();
            let categoryName = $('#category_name').val();
        $('.error').text(''); $('input').removeClass('is-invalid');
            let errors = {};
            if (!categoryName) errors.categoryName = 'Category Name is required.';
        if (Object.keys(errors).length > 0) { if (errors.categoryName) { $('#category_name').addClass('is-invalid'); $('.category-name-error').text(errors.categoryName); } return; }
            $('#categoryForm').submit();
        });
        
    function load_transfer() {
        $('#TransferTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            order: [[0, 'desc']],
            ajax: {
                url: '{{ route("transfer.list") }}',
                type: 'GET',
                data: { _token: '{{ csrf_token() }}' }
            },
            columns: [
                { data: 'id',         name: 'id' },
                { data: 'user',       name: 'user',       orderable: false },
                { data: 'start_date', name: 'start_date' },
                { data: 'note',       name: 'note',       orderable: false },
                { data: 'amount',     name: 'amount' },
            ]
        });
    }
    load_transfer();

    function load_report_timeline() {
        var $reportTable = $('#ReportTimelineTable');

        if (! $reportTable.length) {
            return;
        }

        $reportTable.DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            order: [[2, 'desc']],
            ajax: {
                url: $reportTable.data('url'),
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}',
                    projects_id: $reportTable.data('projects-id'),
                    users_id: $reportTable.data('users-id'),
                    from_date: $reportTable.data('from-date'),
                    to_date: $reportTable.data('to-date'),
                    entry_type: $reportTable.data('entry-type')
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'type', name: 'type', orderable: false, searchable: false },
                { data: 'date', name: 'date' },
                { data: 'time', name: 'time' },
                { data: 'project', name: 'project', orderable: false },
                { data: 'user', name: 'user', orderable: false },
                { data: 'amount', name: 'amount', orderable: false, searchable: false }
            ]
        });
    }
    load_report_timeline();

    function load_credit() {
        $('#CreditTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            order: [[0, 'desc']],
            ajax: {
                url: '{{ route("credit.list") }}',
                type: 'GET',
                data: { _token: '{{ csrf_token() }}' }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'project', name: 'project', orderable: false },
                { data: 'credit_date', name: 'credit_date' },
                { data: 'amount', name: 'amount' },
                { data: 'created_by', name: 'created_by', orderable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    }
    load_credit();


});
</script>
