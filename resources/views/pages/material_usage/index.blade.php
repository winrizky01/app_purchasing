@extends('layout.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div id="alert"></div>

        @if (session('error'))
            <div class="bs-toast toast toast-placement-ex m-2 fade top-0 end-0 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="2000">
                <div class="toast-header">
                    <i class="ti ti-bell ti-xs me-2 text-danger"></i>
                    <div class="me-auto fw-medium">Warning</div>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">{{session('error')}}</div>
            </div>
        @endif
        
        @if (session('success'))
            <div class="bs-toast toast toast-placement-ex m-2 fade top-0 end-0 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="2000">
                <div class="toast-header">
                    <i class="ti ti-bell ti-xs me-2 text-success"></i>
                    <div class="me-auto fw-medium">Success</div>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">{{session('success')}}</div>
            </div>
        @endif
        
        <!-- Filter -->
        <div class="card mb-3">
            <div class="card-header">Filter Table</div>
            <div class="card-body">
                <div class="row pb-2 gap-3 gap-md-0">
                    <div class="col-md-4">
                        <label for="filterCode">No. Document</label>
                        <input type="text" class="form-control" name="filterCode" id="filterCode" placeholder="Enter No. Document">
                    </div>
                    <div class="col-md-4">
                        <label for="filterstatus">Status</label>
                        <select class="select2 form-control" name="filterstatus" id="filterstatus">
                            <option value="">Select Status</option>
                            <option value="1">Submit</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" id="handleReset" class="btn btn-secondary btn-sm">Reset</button>
                <button type="button" id="handleFilter" class="btn btn-primary btn-sm">Filter</button>
            </div>             
        </div>
        
        <!-- Product List Table -->
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-1">{{ $title }}</h5>
                <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                    <div class="col-md-4 user_role"></div>
                    <div class="col-md-4 user_plan"></div>
                    <div class="col-md-4 user_status"></div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables table border-top">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>No. Document</th>
                            <th>Warehouse</th>
                            <th>Note</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Confirm Delete -->
    <div class="modal fade" id="modalCenter" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Confirm Delete Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="confirmDelete" action="" method="post">
                    @csrf
                    @method('DELETE')                
                    <div class="modal-body">                        
                        <div class="row">
                            <div class="col">
                                Are you sure to delete this data?
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        // Setup Datatable
        var ajaxUrl  = "{{ url('inventory/material-usage/dataTables') }}";
        var ajaxData = [];
        var columns  = [{ data: 'usage_date' }, {data: 'code'}, {data: 'warehouse.name'}, {data: 'description'}, { data: 'document.name' }, { data: 'action' }];
        var columnDefs  =  [
            {
                // Convert date
                targets: 0,
                render: function(data, type, full, meta) {
                    var parts = data.split('-');
                    var formattedDay = ('0' + parts[0]).slice(-2);
                    var formattedMonth = ('0' + parts[1]).slice(-2);
                    var formattedYear = parts[2];
                    var formattedDate = formattedDay + '-' + formattedMonth + '-' + formattedYear;
                    return formattedDate;
                }
            },
            {
                // User Status
                targets: -2,
                render: function(data, type, full, meta) {
                    console.log(full.document_status.name)
                    var status = '';
                    if ((full.document_status.name == 'Submit')) {
                        status =
                            '<span class="badge bg-label-success" text-capitalized> Submit </span>';
                    } else if ((full.document_status.name == 'Draft')) {
                        status =
                            '<span class="badge bg-label-secondary" text-capitalized> Draft </span>';
                    }

                    return (status);
                }
            },
            {
                // Actions
                targets: -1,
                title: 'Actions',
                searchable: false,
                orderable: false,
                render: function(data, type, full, meta) {
                    return (
                        '<div class="d-flex align-items-center">' +
                            '<a href="{{ url("inventory/material-usage/show") }}/' + full.id +
                            '" class="text-body edit-record"><i class="ti ti-eye ti-sm me-2"></i></a>' +
                        '</div>'
                    );
                }
            }
        ];
        var buttons     =  [
            {
                className: 'btn btn-primary mx-3 btn-sm',
                text: 'Add Material Usage',
                action: function() {
                    window.location.href = '{{url("inventory/material-usage/create")}}'; // Ganti URL_ANDA_DISINI dengan URL yang diinginkan
                }
            }, 
        ]
        // Setup Datatable

        $(document).ready(function() {
            initializeDataTable(ajaxUrl, ajaxData, columns, columnDefs, buttons);

            // Delete Record
            $('.datatables tbody').on('click', '.delete-record', function() {
                var selectedRow = $(this).closest('tr');
                var rowData = $('.datatables').DataTable().row(selectedRow).data();
                $('#confirmDelete').attr('action', '{{url("inventory/material_usage/delete")}}/'+rowData.id);
                $('#modalCenter').modal('toggle');
            });

            $('#handleReset').click(function() {
                $('#filterCode').val('');
                $('#filterstatus').val('').trigger('change');
                ajaxData = {};
                initializeDataTable(ajaxUrl, ajaxData, columns, columnDefs, buttons);
            });
            $('#handleFilter').click(function(){
                var code    = "";
                var status  = "";
                if($('#filterCode').val() != ""){
                    code = $('#filterCode').val();
                }
                if($('#filterstatus').val() != ""){
                    status = $('#filterstatus').val();
                }

                if((code == "")&&(status == "")){
                    toasMassage({status:false, message:'Opps, please fill out some form!'});
                    return false;
                }

                ajaxData = {'code':code, 'status': status};
                initializeDataTable(ajaxUrl, ajaxData, columns, columnDefs, buttons);
            });

        });
    </script>
@endsection
