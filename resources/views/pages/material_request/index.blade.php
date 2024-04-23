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
                        <label for="filterName">Name</label>
                        <input type="text" class="form-control" name="filterName" id="filterName" placeholder="Enter Name">
                    </div>
                    <div class="col-md-4">
                        <label for="filterstatus">Status</label>
                        <select class="select2 form-control" name="filterstatus" id="filterstatus">
                            <option value="">Select Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
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
                            <th>No. Document</th>
                            <th>Request Date</th>
                            <th>Department</th>
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
        var role     = "{{session('role')->name}}";
        var ajaxUrl  = "{{ url('inventory/material-request/dataTables') }}";
        var ajaxData = [];
        var columns  = [{ data: 'code' }, { data: 'request_date' }, {data: 'department.name'}, { data: 'status' }, { data: 'action' }];
        var columnDefs  =  [
            {
                // User Status
                targets: -2,
                render: function(data, type, full, meta) {
                    var status = '';
                    if(full['status']){
                        if ((full['status'] == 1) || (full['status'] == 'active')) {
                            status =
                                '<span class="badge bg-label-success" text-capitalized> Active </span>';
                        } else if ((full['status'] == 2) || (full['status'] == 'inactive')) {
                            status =
                                '<span class="badge bg-label-secondary" text-capitalized> Inactive </span>';
                        } else if ((full['status'] == 3) || (full['status'] == 'pending')) {
                            status =
                                '<span class="badge bg-label-warning" text-capitalized> Pending </span>';
                        }                         
                    }
                    else if(full['document_status_id']){
                        if (full['document_status']['name'] == 'Submit') {
                            status =
                                '<span class="badge bg-label-success" text-capitalized> Submit </span>';
                        } else if (full['document_status']['name'] == 'Draft') {
                            status =
                                '<span class="badge bg-label-warning" text-capitalized> Draft </span>';
                        }
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
                    if(role == "End User"){
                        return (
                            '<div class="d-flex align-items-center">' +
                                '<a href="javascript:;" class="text-body"><i class="ti ti-eye ti-sm mx-2"></i></a>' +
                            '</div>'
                        );
                    }
                    else{
                        return (
                            '<div class="d-flex align-items-center">' +
                                '<a href="{{ url("inventory/material-request/edit") }}/' + full.id +
                                '" class="text-body edit-record"><i class="ti ti-edit ti-sm me-2"></i></a>' +
                                '<a href="javascript:;" class="text-body delete-record"><i class="ti ti-trash ti-sm mx-2"></i></a>' +
                            '</div>'
                        );
                    }
                }
            }
        ];
        var buttons     =  [
            {
                className: 'btn btn-primary mx-3 btn-sm',
                text: 'Add Material Request',
                action: function() {
                    window.location.href = '{{url("inventory/material-request/create")}}'; // Ganti URL_ANDA_DISINI dengan URL yang diinginkan
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
                $('#confirmDelete').attr('action', '{{url("inventory/material-request/delete")}}/'+rowData.id);
                $('#modalCenter').modal('toggle');
            });

            $('#handleReset').click(function() {
                $('#filterName').val('');
                $('#filterstatus').val('').trigger('change');
                ajaxData = {};
                initializeDataTable(ajaxUrl, ajaxData, columns, columnDefs, buttons);
            });
            $('#handleFilter').click(function(){
                var name = "";
                var status = "";
                if($('#filterName').val() != ""){
                    name = $('#filterName').val();
                }
                if($('#filterstatus').val() != ""){
                    status = $('#filterstatus').val();
                }

                if((name == "")&&(status == "")){
                    toasMassage({status:false, message:'Opps, please fill out some form!'});
                    return false;
                }

                ajaxData = {'name':name, 'status': status};
                initializeDataTable(ajaxUrl, ajaxData, columns, columnDefs, buttons);
            });

        });
    </script>
@endsection
