@extends('layout.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-4">
            <div class="col-12">
                <div id="alert"></div>

                <div class="card">
                    <h5 class="card-header border-bottom mb-3">{{ $title }}</h5>
                    <form id="form" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="isChange" value="false"/>
                        <input type="hidden" name="reason" id="reason"/>
                        @csrf
                        <div class="card-body row g-3">
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label" for="purchase_type_id">Type Purchase Request</label>
                                    <select class="form-select select2" id="purchase_type_id" name="purchase_type_id" data-allow-clear="true">
                                        <option value="">Select Value</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="form-label" for="date">Request Date</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="{{ date('d-m-Y', strtotime($data->date)) }}" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label" for="code">No. Document</label>
                                    <input type="text" class="form-control" id="code" name="code" value="{{ $data->code }}" readonly>
                                </div>
                                <div class="col">
                                    <label class="form-label" for="effective_date">Estimation Required</label>
                                    <input type="date" class="form-control" id="effective_date" name="effective_date" value="{{ date('Y-m-d', strtotime($data->effective_date)) }}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label" for="department_id">Department</label>
                                    <select class="form-select select2" data-allow-clear="true" id="department_id" name="department_id" required>
                                        <option value="">Select Value</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="form-label" for="division_id">Divisi</label>
                                    <select class="form-select select2" data-allow-clear="true" id="division_id" name="division_id" required>
                                        <option value="">Select Value</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label" for="warehouse_id">Warehouse</label>
                                    <select class="form-select select2" name="warehouse_id" id="warehouse_id" data-allow-clear="true" required>
                                        <option value="">Select Value</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="form-label" for="remark_id">Remaks</label>
                                    <select class="form-select select2" name="remark_id" id="remark_id" data-allow-clear="true" required>
                                        <option value="">Select Value</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="form-label" for="document_status_id">Status</label>
                                    <select class="form-select select2" name="document_status_id" id="document_status_id" data-allow-clear="true" required>
                                        <option value="">Select Value</option>
                                    </select>
                                </div>                
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label" for="document_photo">Photo</label>
                                    <div class="input-group">
                                        <input type="file" class="form-control" id="document_photo" name="document_photo">
                                        @if($data->document_photo != null)
                                            <a href="{{asset($data->document_photo)}}" class="btn btn-primary btn-sm" target="_blank">Show Photo</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="col">
                                    <label class="form-label" for="document_pdf">Document PDF</label>
                                    <div class="input-group">
                                        <input type="file" class="form-control" id="document_pdf" name="document_pdf">
                                        @if($data->document_pdf != null)
                                            <a href="{{asset($data->document_pdf)}}" class="btn btn-sm btn-primary" target="_blank">Show Pdf</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label" for="description">Justification</label>
                                    <textarea class="form-control" id="description" name="description" rows="3">{{ $data->description }}</textarea>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Revision / Rejected Reason</label>
                                    <textarea class="form-control" name="last_revision" readonly>{{ $data->last_reason}}</textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="divider">
                                    <div class="divider-text">Purchase Request Detail</div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-sm btn-primary" type="button" id="showModal">Add Product</button>
                            </div>
                            <div class="col-md-12">
                                <table class="table" id="listPurchaseDetail">
                                    <thead>
                                        <th>Cat.</th>
                                        <th>Product</th>
                                        <th>Unit</th>
                                        <th style="width: 12%">Qty</th>
                                        <th>Note</th>
                                        <th>Action</th>
                                    </thead>
                                    <tbody>
                                        @php
                                            $index = 0;
                                        @endphp
                                        @foreach ($data->detail as $item)
                                            <tr id="{{ $item->product_id }}">
                                                <input type="hidden" name="purchase_request_details[{{$index}}][product_id]" value="{{ $item->product_id }}">
                                                <td>{{ $item->product->product_category->name }}</td>
                                                <td>{{ $item->product->name }} - {{ $item->product->description }}</td>
                                                <td>{{ $item->product->product_unit["name"] }}</td>
                                                <td style="text-transform: capitalize"><input type="number"class="form-control" name="purchase_request_details[{{$index}}][qty]" value="{{ $item->qty }}"></td>
                                                <td style="text-transform: capitalize"><input type="text" class="form-control" name="purchase_request_details[{{$index}}][product_note]" value="{{ $item->notes }}"/></td>
                                                <td><button type="button" class="btn btn-danger btn-sm deleteList"><i class="fa fa-trash"></i></button></td>                        
                                            </tr>
                                            @php
                                                $index++;
                                            @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <div class="divider">
                                    <div class="divider-text">Purchase Request Detail</div>
                                </div>
                            </div>
                            <div class="col-md-12 text-muted" style="font-size: 8pt">
                                <span>Created By : {{ $data->createdBy->name }} - {{ date("d-m-Y H:i:s", strtotime($data->created_at)) }}</span> <br/>
                                <span>Last Update By : {{ $data->last_update != null ? $data->last_update->name : "" }} - {{  $data->last_update != null ? date("d-m-Y H:i:s", strtotime($data->updated_at)) : "" }}</span>
                            </div>
                        </div>
                        <div class="card-footer border-top py-3">
                            <a href="{{ url('purchasing/purchase-request') }}" class="btn btn-secondary btn-sm">Cancel</a>
                            @if(session('role')->name == "Tech Support")
                            <button type="button" id="buttonModalReject" data-value="Reject" class="btn btn-danger btn-sm buttonModalConfirm">Reject</button>
                            <button type="submit" id="buttonModalApproved" data-value="Approved" class="btn btn-primary btn-sm buttonModalConfirm">Submit</button>
                            @elseif(session('role')->name == "Plant Manager")
                            <button type="submit" id="buttonModalApproved" data-value="Approved" class="btn btn-primary btn-sm buttonModalConfirm">Submit</button>
                            <button type="button" id="buttonModalRevision" data-value="Revision" class="btn btn-warning btn-sm buttonModalConfirm">Revision</button>
                            <button type="button" id="buttonModalReject" data-value="Reject" class="btn btn-danger btn-sm buttonModalConfirm">Reject</button>
                            @elseif(session('role')->name == "End User")
                            <button type="submit" id="buttonModalApproved" data-value="Update" class="btn btn-primary btn-sm buttonModalConfirm">Submit</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Product -->
    <div class="modal fade" id="modalShowProduct" tabindex="-1" aria-modal="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label for="" class="form-label">Product Category</label>
                            <select class="form-select select2" data-allow-clear="true" id="modalProductCategory" placeholder="All Products" required>
                                <option value="">All Product</option>
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card-datatable table-responsive">
                            <table class="datatables table border-top" id="modalProduct" style="font-size: 10pt">
                                <thead>
                                    <tr>
                                        <th>SKU</th>
                                        <th>Name</th>
                                        <th>Unit</th>
                                        <th style="width: 20%">Stock</th>
                                        <th style="width: 20%">Qty</th>
                                        <th style="width: 20%">Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>                
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Confirm Update / revisi / reject -->
    <div class="modal fade" id="modalCenter" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Confirm Update Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">                        
                    <div class="row">
                        <div class="col-md-12 mb-3" id="modalCenterMessage"></div>
                        <div class="col-md-12">
                            <textarea class="form-control" name="modalNote" id="modalNote"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary buttonModalConfirm" id="submit" data-value="Submit">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        // setup data global
        var isChanged       = false;
        var initialValues   = {};
        var role            = "{{ session('role')->name }}";
        var mode            = "{{ $mode }}";
        var dataId          = "{{ $data->id }}";
        var formCheck       = true;
        var formMode        = "";
        var existingPurchaseRequestType = <?php echo json_encode($data->type_purchase_request); ?>;
        var existingDepartment          = <?php echo json_encode($data->department_id); ?>;
        var existingDivision            = <?php echo json_encode($data->division_id); ?>;
        var existingWarehouse           = <?php echo json_encode($data->warehouse_id); ?>;
        var existingRemarkId            = <?php echo json_encode($data->remark_id); ?>;
        var documentSatus               = <?php echo json_encode($data->document_status->name);?>;
        var existingDocumentStatus      = <?php echo json_encode($data->document_status_id);?>;
        // setup data global
        
        // Setup Datatable
        var ajaxUrl  = "{{ url('master/product/dataTables') }}";
        var ajaxData = [];
        var columns  = [{ data: 'sku' }, { data: 'name' }, {data: 'product_unit.name'}, { data: 'stock' }, { data: 'qty' }, { data: 'action' }];
        var columnDefs  =  [
            {
                // Qty
                targets: -3,
                render: function(data, type, full, meta) {
                    var status = '<input type="number" min="1" class="form-control form-control-sm" id="stock" name="stock" value="'+data+'" readonly/>';

                    return (status);
                }
            },
            {
                // Qty
                targets: -2,
                render: function(data, type, full, meta) {
                    var status = '<input type="number" min="1" class="form-control form-control-sm" id="modalQty" name="modalQty"/>';

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
                        '<button class="btn btn-sm btn-primary btnAddProductModal" type="button"><i style="font-size:8pt" class="ti ti-plus"></i></button>'
                    );
                }
            }
        ];
        var buttons =  []
        // Setup Datatable

        $(document).ready(function() {
            if((mode == "show")){
                $("form :input").prop("disabled", true);
            }

            // request select option
            var whereInStatusDocRole = "";
            if(role == "Tech Support"){
                whereInStatusDocRole = "Approved-Reject";
            }
            else if(role == "Plant Manager"){
                whereInStatusDocRole = "Approved-Reject-Revision";
            }
            else if(role == "End User"){
                if(documentSatus == "Draft"){
                    $("form :input").prop("disabled", false);
                    whereInStatusDocRole = "Submit-Draft";
                }
            }
            requestSelectAjax({
                'url' : '{{ url("setting/general/select?whereIn=") }}' + whereInStatusDocRole,
                'data': [],
                'optionType' : 'document_status',
                'type': 'GET'
            });
            requestSelectAjax({
                'url' : '{{ url("setting/general/select?type=material_request_type_id") }}',
                'data': [],
                'optionType' : 'material_request_type',
                'type': 'GET'
            });
            requestSelectAjax({
                'url' : '{{ url("setting/general/select?type=product_category_id") }}',
                'data': [],
                'optionType' : 'product_category',
                'type': 'GET'
            });
            requestSelectAjax({
                'url' : '{{ url("master/department/select") }}',
                'data': [],
                'optionType' : 'department',
                'type': 'GET'
            });
            requestSelectAjax({
                'url' : '{{ url("master/division/select") }}',
                'data': [],
                'optionType' : 'division',
                'type': 'GET'
            });
            requestSelectAjax({
                'url' : '{{ url("master/warehouse/select") }}',
                'data': [],
                'optionType' : 'warehouse',
                'type': 'GET'
            });
            requestSelectAjax({
                'url' : '{{ url("setting/general/select?type=remark_id") }}',
                'data': [],
                'optionType' : 'remark',
                'type': 'GET'
            });
            // request select option

            // initial change, pengecekan perubahan data
            $('form :input').not('select[name="document_status_id"]').on('change keyup', function() {
                var anyChange = false;

                // Periksa setiap input untuk perubahan, kecuali elemen dengan ID document_status_id
                $('form :input').not('#document_status_id').each(function() {
                    var fieldName = $(this).attr('name');
                    var newValue = $(this).val();
                    var oldValue = initialValues[fieldName];

                    // Jika ada perubahan pada input tertentu, atur isChanged ke true
                    if (newValue !== oldValue) {
                        anyChange = true;
                        return false; // Keluar dari loop karena sudah ada perubahan
                    }
                });

                // Set isChanged berdasarkan apakah ada perubahan input
                isChanged = anyChange;
                $('input[name="isChange"]').val(anyChange);
            });
            // initial change, pengecekan perubahan data

            // modal produk
            $('#showModal').click(function() {
                // Setup Datatable
                initializeDataTable(ajaxUrl, ajaxData, columns, columnDefs, buttons);

                $('#modalShowProduct').modal('toggle');
            });
            $('body').on('click', '.btnAddProductModal', function(){
                var selectedRow = $(this).closest('tr');
                var modalQty    = selectedRow.find('#modalQty').val();
                var rowData     = $('#modalProduct').DataTable().row(selectedRow).data();

                if(modalQty == ""){
                    toasMassage({status:false, message:'Opps, please fill qty product!'});
                    return false;
                }

                handleAddModalProduct({'data':rowData, 'qty': modalQty});
            });
            $('body').on('click', '.deleteList', function(){
                $(this).closest('tr').remove();
            });
            $('body').on('change', '#modalProductCategory', function(){
                // Setup Datatable
                var ajaxData = {'product_category_id':$(this).val()};
                // Setup Datatable
                initializeDataTable(ajaxUrl, ajaxData, columns, columnDefs, buttons);
            });
            // modal produk

            // handle button modal confirm
            $('.buttonModalConfirm').click(function(){
                var mode = $(this).attr('data-value');
                if(mode == "Submit"){
                    formCheck = false;
                    if((formMode == "Reject")||(formMode == "Revision")){
                        if(($('#modalNote').val() === "")||($('#modalNote').val() === null)||($('#modalNote').val() == undefined)){
                            toasMassage({status:false, message:'Opps, please fill reason!'});
                            return false;
                        }
                    }
                    $('#reason').val($('#modalNote').val());
                    $('form').submit();
                }
                else{
                    if(mode == 'Reject'){
                        var modalCenterTitle = "Confirm Reject Data";
                        var modalMessage = "Please, fill the reason!";
                        $('#modalNote').removeClass("d-none");
                        $('form').attr('action','{{ url("purchasing/purchase-request/reject")}}'+'/'+dataId);
                    }
                    else if(mode == 'Revision'){
                        var modalCenterTitle = "Confirm Revision Data";
                        var modalMessage = "Please, fill the reason!";
                        $('#modalNote').removeClass("d-none");
                        $('form').attr('action','{{ url("purchasing/purchase-request/revision")}}'+'/'+dataId);
                    }
                    else if(mode == "Approved"){
                        if($('#remark_id').val() == ""){
                            toasMassage({status:false, message:'Opps, please fill Remark!'});
                            return false;
                        }
                        if($('#document_status_id').val() == ""){
                            toasMassage({status:false, message:'Opps, please fill Status!'});
                            return false;
                        }

                        var modalCenterTitle = "Confirm Approve Data";
                        $('#modalNote').addClass("d-none");
                        if(isChanged == true){
                            var modalMessage = "Are you sure to approve and revisied this data?";
                        }
                        else{
                            var modalMessage = "Are you sure to approve this data?";
                        }
                        $('form').attr('action','{{ url("purchasing/purchase-request/update")}}'+'/'+dataId);
                    }
                    else if(mode == "Update"){
                        var modalCenterTitle = "Confirm Update Data";
                        var modalMessage = "Are you sure to update this data?";
                        $('#modalNote').addClass("d-none");
                        $('form').attr('action','{{ url("purchasing/purchase-request/update")}}'+'/'+dataId);
                    }

                    formMode = mode;
                    $('#modalCenterTitle').text(modalCenterTitle);
                    $('#modalCenterMessage').text(modalMessage);
                    $('#modalCenter').modal('toggle');
                }
            });
            // handle button modal confirm

            $('form').submit(function(e){
                if(formCheck == true){
                    var documentStatusSelected = $('#document_status_id').children('option:selected').text();
                    if(documentStatusSelected == "Reject"){
                        $('#buttonModalReject').click();
                    }
                    else if(documentStatusSelected == "Approved"){
                        $('#buttonModalApproved').click();
                    }
                    else if(documentStatusSelected == "Revision"){
                        $('#buttonModalReject').click();
                    }
                    e.preventDefault();
                }
            });
        })

        // fungsi init data awal dari db
        function initialization(){
            $('form :input').not('select[name="document_status_id"]').each(function() {
                initialValues[$(this).attr('name')] = $(this).val();
            });
        }
        // fungsi init data awal dari db

        function setDataSelect(optionType, response) {
            var id = "";
            var existingId = "";
            if (optionType == 'product_category') {
                id = "#modalProductCategory";
            } else if(optionType == "material_request_type") {
                id = "#purchase_type_id";
                existingId = existingPurchaseRequestType;
            } else if (optionType == "department"){
                id = "#department_id";
                existingId = existingDepartment;
            } else if (optionType == "division"){
                id = "#division_id";
                existingId = existingDivision;
            } else if (optionType == "warehouse"){
                id = "#warehouse_id";
                existingId = existingWarehouse;
            } else if (optionType == "remark"){
                id = "#remark_id";
                existingId = existingRemarkId;
            } else if (optionType == 'document_status') {
                id = "#document_status_id";
                existingId = existingDocumentStatus;
            } else if (optionType == 'add_product_machine') {
                id = "#modalAddProduct-machine_id";
            } else if (optionType == 'add_product_category'){
                id = "#productCategory";
            } else if (optionType == 'add_product_unit'){
                id = "#modalAddProduct-unit_id";
            }
            
            $.each(response.results, function(index, data) {
                var option = '<option value="' + data.id + '">' + data.name + '</option>';
                if (Array.isArray(existingId)) {
                    for(var i=0; i < existingId.length; i++){
                        if (existingId[i].id && existingId[i].id == data.id) {
                            option = '<option value="' + data.id + '" selected>' + data.name + '</option>';
                        }
                    }
                }
                else{
                    if (existingId && existingId == data.id) {
                        option = '<option value="' + data.id + '" selected>' + data.name + '</option>';
                    }
                }
                $(id).append(option);
            });

            initialization();
        }

        function handleAddModalProduct(param){
            var tempAccess = param["data"]['id']
            var e = 0;
            var index = 0;
            $('#listPurchaseDetail tbody tr').each(function(){
                if($(this).attr('id') == tempAccess){
                    e++;
                }
                index++;
            });

            if(e > 0){
                toasMassage({status:false, message:'Opps, product already exist!'});
                return false;
            }
            else{
                if(param['data']['product_category']['name'] == "Sparepart"){
                    var description = param["data"]["name"] + ' - ' + param["data"]["description"] + ' - ' + param["data"]["dimension"] + ' - ' + param["data"]["part_number"];
                }
                else{
                    var description = param["data"]["name"] + ' - ' + param["data"]["description"];
                }

                var html = '<tr id="'+tempAccess+'">'+
                            '<input type="hidden" name="purchase_request_details['+index+'][product_id]" value="'+tempAccess+'">'+
                            '<td style="text-transform: capitalize">'+param["data"]["product_category"]["name"]+'</td>'+
                            '<td style="text-transform: capitalize">'+description+'</td>'+
                            '<td style="text-transform: capitalize">'+param["data"]["product_unit"]["name"]+'</td>'+
                            '<td style="text-transform: capitalize"><input type="number"class="form-control" name="purchase_request_details['+index+'][qty]" value="'+param["qty"]+'"></td>'+
                            '<td style="text-transform: capitalize"><input type="text" class="form-control" name="purchase_request_details['+index+'][product_note]"/></td>'+
                            '<td><button type="button" class="btn btn-danger btn-sm deleteList"><i class="fa fa-trash"></i></button></td>'+
                        '</tr>';

                $("#listPurchaseDetail tbody").append(html);
            }

        }
    </script>
@endsection
