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
                        <label for="filterCode">No. Document</label>
                        <input type="text" class="form-control" name="filterCode" id="filterCode" placeholder="Enter No. Document">
                    </div>
                    <div class="col-md-4">
                        <label for="filterstatus">Status</label>
                        <select class="select2 form-control" name="filterstatus" id="filterstatus">
                            <option value="">Select Status</option>
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
                            <th>Request Date</th>
                            <th>No. Document</th>
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
        var columns  = [{ data: 'request_date' }, { data: 'code' }, {data: 'department.name'}, { data: 'status' }, { data: 'action' }];
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
                    var msg = '';
                    var cls = '';
                    if(full['document_status_id']){
                        msg = full['document_status']['name'];
                        if (
                            (full['document_status']['name'] == 'Waiting Approval Tech Support')||
                            (full['document_status']['name'] == 'Waiting Approval Plant Manager'||
                            (full['document_status']['name'] == 'Draft')
                        )) {
                            cls = 'bg-label-warning';
                        }
                        else if (full['document_status']['name'] == 'Approved Plant Manager') {
                            cls = 'bg-label-success';
                        }
                        else if ((full['document_status']['name'] == 'Rejected Tech Support')||(full['document_status']['name'] == 'Rejected Plant Manager')) {
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
                        action += '<a href="{{ url("inventory/material-request/show") }}/' + full.id + '" class="text-body"><i class="ti ti-eye ti-sm mx-2"></i></a>';
                    }
                    else if(role == "Tech Support"){
                        if(full.document_status.name == "Waiting Approval Tech Support"){
                            action += '<a href="{{ url("inventory/material-request/edit") }}/' + full.id +'" class="text-body edit-record"><i class="ti ti-edit ti-sm me-2"></i></a>';
                        }
                        else{
                            action += '<a href="{{ url("inventory/material-request/show") }}/' + full.id + '" class="text-body"><i class="ti ti-eye ti-sm mx-2"></i></a>';
                        }
                    }
                    else if(role == "Plan Manager"){
                        if(full.document_status.name == "Waiting Approval Plan Manager"){
                            action += '<a href="{{ url("inventory/material-request/edit") }}/' + full.id + '" class="text-body edit-record"><i class="ti ti-edit ti-sm me-2"></i></a>';
                        }
                        else{
                            action += '<a href="{{ url("inventory/material-request/show") }}/' + full.id + '" class="text-body"><i class="ti ti-eye ti-sm mx-2"></i></a>';
                        }
                    }
                    else{
                        action += '<a href="{{ url("inventory/material-request/edit") }}/' + full.id + '" class="text-body edit-record"><i class="ti ti-edit ti-sm me-2"></i></a>';
                    }

                    action += '<button type="button" class="btn btn-icon waves-effect download-record"><i class="ti ti-printer ti-sm me-2"></i></button> </div>';

                    return action;
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
            requestSelectAjax({
                'url' : '{{ url("setting/general/select?whereIn=Waiting Approval Tech Support-Waiting Approval Plant Manager-Approved Plant Manager-Revisied Plant Manager-Rejected Tech Support-Rejected Plant Manager-Approved Tech Support") }}',
                'data': [],
                'optionType' : 'document_status',
                'type': 'GET'
            });

            initializeDataTable(ajaxUrl, ajaxData, columns, columnDefs, buttons);

            // Delete Record
            $('.datatables tbody').on('click', '.delete-record', function() {
                var selectedRow = $(this).closest('tr');
                var rowData = $('.datatables').DataTable().row(selectedRow).data();
                $('#confirmDelete').attr('action', '{{url("inventory/material-request/delete")}}/'+rowData.id);
                $('#modalCenter').modal('toggle');
            });
            $('.datatables tbody').on('click', '.download-record', function(){
                var selectedRow = $(this).closest('tr');
                var rowData = $('.datatables').DataTable().row(selectedRow).data();
                rowData = rowData.id
                $.ajax({
                    url: '{{ url("inventory/material-request/print") }}/' + rowData,
                    type: 'GET',
                    xhrFields: {
                        responseType: 'blob' // Mengindikasikan bahwa respons adalah binary data (blob)
                    },
                    success: function(response) {
                        // Membuat objek URL untuk file blob
                        var url = window.URL.createObjectURL(new Blob([response]));

                        // Membuat elemen anchor untuk menautkan ke objek URL
                        var a = document.createElement('a');
                        a.href = url;
                        a.download = 'document.pdf'; // Nama file yang akan diunduh
                        document.body.appendChild(a);

                        // Mengklik elemen anchor secara otomatis untuk memulai unduhan
                        a.click();

                        // Menghapus elemen anchor setelah selesai
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                    },
                    error: function(xhr, status, error) {
                        console.error("Request failed: " + error);
                    }
                });
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

        function setDataSelect(optionType, response) {
            var id = "";
            if (optionType == 'document_status') {
                id = "#filterstatus";
            }
            
            $.each(response.results, function(index, data) {
                $(id).append('<option value="' + data.id + '">' + data.name + '</option>');
            });
        }

        function handleRequestAjax(option, data){
            if(option == "pdf"){
                console.log(data);
                window.open(data.url, '_blank');
            }
        }

    </script>
@endsection
