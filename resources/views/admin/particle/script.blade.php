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
    $(function () {
    })
</script>

<script>
$(document).ready( function () {
        
        $('.select2').select2();
        //Date range picker
        $('.single_date').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: false,
            locale: {
                format: 'MM/YYYY'
            }
        }).on("apply.daterangepicker", function (e, picker) {
            picker.element.val(picker.startDate.format(picker.locale.format));
        });

        $('.dob').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: false,
            locale: {
                format: 'DD/MM/YYYY'
            }
        }).on("apply.daterangepicker", function (e, picker) {
            picker.element.val(picker.startDate.format(picker.locale.format));
        });

        bsCustomFileInput.init();

        // $('.single_date').daterangepicker({
        //     singleDatePicker: true,
        //     showDropdowns: true,
        //     autoUpdateInput: false,
        //     locale: {
        //         format: 'MM/YYYY'
        //     },
        //     minYear: 2000,
        //     maxYear: 2050,
        // }, function(start, end, label) {
        //     $(this.element).val(start.format('MM/YYYY'));
        // });

        // $('.single_date').on('show.daterangepicker', function(ev, picker) {
        //     picker.container.find('.calendar-table thead tr:not(:first-child)').hide();
        //     picker.container.find('.calendar-table tbody tr').hide();
        //     // picker.container.find('.monthselect').css('display', 'block');
        //     // picker.container.find('.yearselect').css('display', 'block');
        // });

        var Toast = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000
        });

        @if (session('success'))
            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            })
        @endif
        @if (session('error'))
            Toast.fire({
                icon: 'error',
                title: '{{ session('error') }}'
            })
        @endif

        $(document).on("click", ".deleteButton", function(event){
            
            // Prevent the default form submission
            event.preventDefault();
            const form = this.closest('form');

            // Show the SweetAlert2 confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, submit the form
                    form.submit();
                }
            });
        });

        
        $('#roleTable').DataTable({
              "paging": true,
              "lengthChange": false,
              "searching": true,
              "ordering": true,
              "info": true,
              "autoWidth": false,
              "responsive": true,
              "processing": true,
              "serverSide": true,
              
            "order": [0, 'desc'],
              "ajax":{
                 "url": "{{ route('roles.list') }}",
                 "dataType": "json",
                 "type": "GET",
                 "data":{ _token: "{{csrf_token()}}",route:'roles.list'}
              },
              "columns": [
                 { "data": "id" },
                 { "data": "name" },
                 { "data": "action" }
              ],
              aoColumnDefs: [
                 {
                    bSortable: false,
                    aTargets: [ -1 ]
                 }
              ]  
        });

        $('#permissionsTable').DataTable({
              "paging": true,
              "lengthChange": false,
              "searching": true,
              "ordering": true,
              "info": true,
              "autoWidth": false,
              "responsive": true,
              "processing": true,
              "serverSide": true,
              
            "order": [0, 'desc'],
              "ajax":{
                 "url": "{{ route('permissions.list') }}",
                 "dataType": "json",
                 "type": "GET",
                 "data":{ _token: "{{csrf_token()}}",route:'permissions.list'}
              },
              "columns": [
                 { "data": "id" },
                 { "data": "name" },
                 { "data": "action" }
              ],
              aoColumnDefs: [
                 {
                    bSortable: false,
                    aTargets: [ -1 ]
                 }
              ]  
        });

        load_user();
        function load_user(){

            $('#userTable').DataTable({
              "paging": true,
              "lengthChange": false,
              "searching": true,
              "ordering": true,
              "info": true,
              "autoWidth": false,
              "responsive": true,
              "processing": true,
              "serverSide": true,
              
            "order": [0, 'desc'],
              "ajax":{
                 "url": "{{ route('users.list') }}",
                 "dataType": "json",
                 "type": "GET",
                 "data":{ _token: "{{csrf_token()}}",route:'users.list'}
              },
              "columns": [
                { "data": "id" },
                { "data": "name" },
                { "data": "email" },
                { "data": "mobile" },
                { "data": "role" },
                { "data": "status" },
                { "data": "action", "orderable": false, "searchable": false }
                ],
              aoColumnDefs: [
                 {
                    bSortable: false,
                    aTargets: [ -1 ]
                 }
              ]  
            });
        }  
         load_projects();
        function load_projects(){

            $('#projectsTable').DataTable({
              "paging": true,
              "lengthChange": false,
              "searching": true,
              "ordering": true,
              "info": true,
              "autoWidth": false,
              "responsive": true,
              "processing": true,
              "serverSide": true,
              
            "order": [0, 'desc'],
              "ajax":{
                 "url": "{{ route('projects.list') }}",
                 "dataType": "json",
                 "type": "GET",
                 "data":{ _token: "{{csrf_token()}}",route:'projects.list'}
              },
              "columns": [
                 { "data": "id" },
                 { "data": "name" },
                 { "data": "start_date" },
                 { "data": "end_date" },
                 { "data": "status" },
                 { "data": "amount" },
                 { "data": "action" }
              ],
              aoColumnDefs: [
                 {
                    bSortable: false,
                    aTargets: [ -1 ]
                 }
              ]  
            });
        }  
        
        load_expense();
        function load_expense(){

            $('#ExpenseTable').DataTable({
                processing:  true,
                serverSide:  true,
                responsive:  true,
                autoWidth:   false,
                order:       [[0, 'desc']],
                ajax: {
                    url:  "{{ route('expense.list') }}",
                    type: 'GET',
                    data: { _token: '{{ csrf_token() }}' }
                },
                columns: [
                    { data: 'id',           name: 'id' },
                    { data: 'project',      name: 'project',      orderable: false },
                    { data: 'expense_date', name: 'expense_date' },
                    { data: 'amount',       name: 'amount' },
                    { data: 'payment_mode', name: 'payment_mode', orderable: false },
                    { data: 'status',       name: 'status' },
                    { data: 'action',       name: 'action',       orderable: false, searchable: false }
                ]
            });
        }
});
</script>

