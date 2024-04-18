@extends('layout.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div id="alert"></div>

        @if (session('error'))
            <div class="bs-toast toast toast-placement-ex m-2 fade top-0 end-0 show" role="alert"
                aria-live="assertive" aria-atomic="true" data-bs-delay="2000">
                <div class="toast-header">
                    <i class="ti ti-bell ti-xs me-2 text-danger"></i>
                    <div class="me-auto fw-medium">Warning</div>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">{{ session('error') }}</div>
            </div>
        @endif

        @if (session('success'))
            <div class="bs-toast toast toast-placement-ex m-2 fade top-0 end-0 show" role="alert"
                aria-live="assertive" aria-atomic="true" data-bs-delay="2000">
                <div class="toast-header">
                    <i class="ti ti-bell ti-xs me-2 text-success"></i>
                    <div class="me-auto fw-medium">Success</div>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">{{ session('success') }}</div>
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

        <!-- Warehouse List Table -->
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title">{{ $title }}</h5>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables table border-top">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Create & Edit -->
    <div class="modal fade" id="modalCreateEdit" tabindex="-1" aria-hidden="true" aria-data="create">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Create Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="formDepartment">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label for="type" class="form-label">Code</label>
                                <input type="text" name="code" id="code" class="form-control" placeholder="Enter Code">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Enter Name">
                            </div>
                            <div class="col-md-12 mb-2">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label class="form-label" for="status">Status</label>
                                <select id="status" name="status" class="form-select select2" data-allow-clear="true"
                                    required>
                                    <option value="">Select</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
    
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary waves-effect"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Save</button>
                    </div>
                </form>
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
        var ajaxUrl  = "{{ url('master/department/dataTables') }}";
        var ajaxData = [];
        var columns  = [{ data: 'code' }, { data: 'name' }, { data: 'description' }, { data: 'status' }, { data: 'action' }];
        var columnDefs  =  [
            {
                // User Status
                targets: -2,
                render: function(data, type, full, meta) {
                    var status = '';
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
                            '<button class="btn btn-link text-body edit-record" data-id="' + full.id + '"><i class="ti ti-edit ti-sm me-2"></i></button>' +
                            '<a href="javascript:;" class="text-body delete-record"><i class="ti ti-trash ti-sm mx-2"></i></a>' +
                        '</div>'
                    );
                }
            }
        ];
        var buttons     =  [
            {
                className: 'btn btn-primary mx-3 btn-sm',
                text: 'Add Department',
                action: function() {
                    $('#formDepartment')[0].reset();
                    $('#modalTitle').text('Create Department');
                    $('#modalCreateEdit form').attr('action', '{{ url("master/department/store") }}');
                    $('#modalCreateEdit').attr('aria-data', 'create');
                    $('#modalCreateEdit').modal('toggle');
                }
            }, 
        ] 
        // Setup Datatable

        $(document).ready(function() {
            initializeDataTable(ajaxUrl, ajaxData, columns, columnDefs, buttons);

            // Edit Record
            $('.datatables tbody').on('click', '.edit-record', function() {
                $('#modalTitle').text('Edit Department');
                $('#modalCreateEdit form').attr('action', '{{ url("master/department/update") }}'+ '/' + $(this).data('id'));
                $('#modalCreateEdit').attr('aria-data', 'edit');
                $('#modalCreateEdit').modal('toggle');

                fetchData($(this).data('id'));
            });

            // Delete Record
            $('.datatables tbody').on('click', '.delete-record', function() {
                var selectedRow = $(this).closest('tr');
                var rowData = $('.datatables').DataTable().row(selectedRow).data();
                $('#confirmDelete').attr('action', '{{ url("master/department/delete") }}/' + rowData.id);
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

        function fetchData(id){
            $.ajax({
                // beforeSend : function(){
                //     $('#overlay').css('display','flex');
                // },
                url : '{{ url("master/department?id=") }}'+id,
                data: [],
                type: 'GET',
                headers: {
                    'X-CRSF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'JSON',
                success: function(rs) {
                    if(rs.results.length > 0){
                        $('#code').val(rs.results[0].code);
                        $('#name').val(rs.results[0].name);
                        $('#description').val(rs.results[0].description);
                        $("#status").val(rs.results[0].status).change();
                    }
                },
                // complete: function(){
                //     $('#overlay').css('display','none');
                // }
            });
        }
    </script>
@endsection
