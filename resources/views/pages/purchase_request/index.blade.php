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
                        <label for="filterDate">Date Document</label>
                        <input type="text" class="form-control flatpickr-input active" placeholder="YYYY-MM-DD to YYYY-MM-DD" id="flatpickr-range">
                    </div>
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
        
        <!-- Purchasing List Table -->
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
                            <th>Request Date</th>
                            <th>Code</th>
                            <th>Department</th>
                            <th>Revision</th>
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
        var role     = "{{session('role')->name}}";
        // Setup Datatable
        var ajaxUrl  = "{{ url('purchasing/purchase-request/dataTables') }}";
        var ajaxData = [];
        var columns  = [{ data: 'date' }, { data: 'code' }, {data: 'department.name'}, {data: 'revision'} , { data: 'status' }, { data: 'action' }];
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
                // Check History
                targets: -3,
                render: function(data, type, full, meta) {
                    var a = 0;
                    if(data > 0){
                        a = '<a href="{{ url("purchasing/purchase-request/history") }}/' + full.id + '" class="text-warning" style="text-decoration:underline">'+ data +'</a>';
                    }
                    return a;
                }
            },
            {
                // User Status
                targets: -2,
                render: function(data, type, full, meta) {
                    var msg = '';
                    var cls = '';
                    if(full['document_status_id']){
                        msg = full['document_status']['name'];
                        if (
                            (full['document_status']['name'] == 'Waiting Approval Tech Support')||
                            (full['document_status']['name'] == 'Waiting Approval Plant Manager'||
                            (full['document_status']['name'] == 'Draft') ||
                            (full['document_status']['name'] == 'Revisied Plant Manager')
                        )) {
                            cls = 'bg-label-warning';
                        }
                        else if (full['document_status']['name'] == 'Approved Plant Manager') {
                            cls = 'bg-label-success';
                        }
                        else if (
                            (full['document_status']['name'] == 'Rejected Tech Support')||
                            (full['document_status']['name'] == 'Rejected Plant Manager')||
                            (full['document_status']['name'] == 'Processed')||
                            (full['document_status']['name'] == 'Closed')
                        ) {
                            cls = 'bg-label-danger';
                        }
                    }

                    var status = '<span class="badge '+ cls +'" text-capitalized> '+ msg +' </span>';
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
                    var action = '<div class="d-flex align-items-center">';
                    if(role == "End User"){
                        action += '<a href="{{ url("purchasing/purchase-request/show") }}/' + full.id + '" class="text-body"><i class="ti ti-eye ti-sm mx-2"></i></a>';
                    }
                    else if(role == "Tech Support"){
                        if(full.document_status.name == "Waiting Approval Tech Support"){
                            action += '<a href="{{ url("purchasing/purchase-request/edit") }}/' + full.id +'" class="text-body edit-record"><i class="ti ti-edit ti-sm me-2"></i></a>';
                        }
                        else if(full.document_status.name == "Revisied Plant Manager"){
                            action += '<a href="{{ url("purchasing/purchase-request/edit") }}/' + full.id +'" class="text-body edit-record"><i class="ti ti-edit ti-sm me-2"></i></a>';
                        }
                        else{
                            action += '<a href="{{ url("purchasing/purchase-request/show") }}/' + full.id + '" class="text-body"><i class="ti ti-eye ti-sm mx-2"></i></a>';
                        }
                    }
                    else if(role == "Plant Manager"){
                        if(full.document_status.name == "Waiting Approval Plant Manager"){
                            action += '<a href="{{ url("purchasing/purchase-request/edit") }}/' + full.id + '" class="text-body edit-record"><i class="ti ti-edit ti-sm me-2"></i></a>';
                        }
                        else{
                            action += '<a href="{{ url("purchasing/purchase-request/show") }}/' + full.id + '" class="text-body"><i class="ti ti-eye ti-sm mx-2"></i></a>';
                        }
                    }
                    else{
                        action += '<a href="{{ url("purchasing/purchase-request/show") }}/' + full.id + '" class="text-body"><i class="ti ti-eye ti-sm mx-2"></i></a>';
                    }

                    action += '<button type="button" class="btn btn-icon waves-effect download-record"><i class="ti ti-printer ti-sm me-2"></i></button> </div>';

                    return action;
                }
            }
        ];
        var buttons     =  [
            {
                className: 'btn btn-primary mx-3 btn-sm',
                text: 'Add Purchase Request',
                action: function() {
                    window.location.href = '{{url("purchasing/purchase-request/create")}}'; // Ganti URL_ANDA_DISINI dengan URL yang diinginkan
                }
            }, 
        ]
        // Setup Datatable

        $(document).ready(function() {
            $('#flatpickr-range').flatpickr({
                mode: 'range'
            });

            initializeDataTable(ajaxUrl, ajaxData, columns, columnDefs, buttons);

            // Delete Record
            $('.datatables tbody').on('click', '.delete-record', function() {
                var selectedRow = $(this).closest('tr');
                var rowData = $('.datatables').DataTable().row(selectedRow).data();
                $('#confirmDelete').attr('action', '{{url("master/product/delete")}}/'+rowData.id);
                $('#modalCenter').modal('toggle');
            });

            $('#handleReset').click(function() {
                $('#filterName').val('');
                $('#filterstatus').val('').trigger('change');
                ajaxData = {};
                initializeDataTable(ajaxUrl, ajaxData, columns, columnDefs, buttons);
            });
            $('#handleFilter').click(function(){
                var date    = "";
                var code    = "";
                var status  = "";
                if($('#flatpickr-range').val() != ""){
                    date = $('#flatpickr-range').val();
                }
                if($('#filterCode').val() != ""){
                    code = $('#filterCode').val();
                }
                if($('#filterstatus').val() != ""){
                    status = $('#filterstatus').val();
                }

                if((date == "")&&(code == "")&&(status == "")){
                    toasMassage({status:false, message:'Opps, please fill out some form!'});
                    return false;
                }

                ajaxData = {'date':date, 'code':code, 'status': status};
                initializeDataTable(ajaxUrl, ajaxData, columns, columnDefs, buttons);
            });

        });
    </script>
@endsection
