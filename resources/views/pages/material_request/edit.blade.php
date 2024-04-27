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
                                    <label class="form-label" for="material_request_type_id">Type Material Request</label>
                                    <select class="form-select select2" id="material_request_type_id" name="material_request_type_id" data-allow-clear="true" required>
                                        <option value="">Select Value</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="form-label" for="code">No. Document</label>
                                    <input type="text" class="form-control" id="code" name="code" value="{{ $data->code }}" readonly>
                                </div>
                                <div class="col">
                                    <label class="form-label" for="request_date">Request Date</label>
                                    <input type="text" class="form-control" id="request_date" name="request_date"
                                        value="{{ date('d-m-Y', strtotime($data->request_date)) }}" readonly>
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
                                    <label class="form-label" for="justification">Justification</label>
                                    <textarea class="form-control" id="justification" name="justification" rows="1">{{ $data->justification }}</textarea>
                                </div>
                                <div class="col">
                                    <div class="row">
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
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Revision / Rejected Reason</label>
                                    <textarea class="form-control" readonly>{{ $data->last_reason}}</textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="divider">
                                    <div class="divider-text">Material Request Detail</div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-sm btn-primary" type="button" id="showModal">Add Product</button>
                            </div>
                            <div class="col-md-12">
                                <table class="table" id="listMaterialDetail">
                                    <thead>
                                        <th>Cat.</th>
                                        <th>Product</th>
                                        <th>Function</th>
                                        <th>Unit</th>
                                        <th style="width: 12%">Qty</th>
                                        <th>Note</th>
                                        <th>Action</th>
                                    </thead>
                                    <tbody>
                                        @php
                                            $index = 0;
                                        @endphp
                                        @foreach ($data->material_request_details as $item)
                                            <tr id="{{ $item->product_id }}">
                                                <input type="hidden" name="material_request_details[{{$index}}][product_id]" value="{{ $item->product_id }}">
                                                <td>{{ $item->product->product_category->name }}</td>
                                                <td>{{ $item->product->name }}</td>
                                                <td>{{ $item->product->description }}</td>
                                                <td>{{ $item->product->product_unit["name"] }}</td>
                                                <td style="text-transform: capitalize"><input type="number"class="form-control" name="material_request_details[{{$index}}][product_qty]" value="{{ $item->qty }}"></td>
                                                <td style="text-transform: capitalize"><input type="text" class="form-control" name="material_request_details[{{$index}}][product_note]" value="{{ $item->notes }}"/></td>
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
                                    <div class="divider-text">Material Request Detail</div>
                                </div>
                            </div>
                            <div class="col-md-12 text-muted" style="font-size: 8pt">
                                <span>Created By : {{ $data->createdBy->name }} - {{ date("d-m-Y H:i:s", strtotime($data->created_at)) }}</span> <br/>
                                <span>Last Update By : {{ $data->last_update != null ? $data->last_update->name : "" }} - {{  $data->last_update != null ? date("d-m-Y H:i:s", strtotime($data->updated_at)) : "" }}</span>
                            </div>
                        </div>
                        <div class="card-footer border-top py-3">
                            <a href="{{ url('inventory/material-request') }}" class="btn btn-secondary btn-sm">Cancel</a>
                            @if(session('role')->name == "Tech Support")
                            <button type="button" id="buttonModalReject" data-value="Reject" class="btn btn-danger btn-sm buttonModalConfirm">Reject</button>
                            <button type="submit" name="submitButton" id="buttonModalApproved" data-value="Approved" class="btn btn-primary btn-sm buttonModalConfirm">Submit</button>
                            @elseif(session('role')->name == "Plant Manager")
                            <button type="submit" name="submitButton" id="buttonModalApproved" data-value="Approved" class="btn btn-primary btn-sm buttonModalConfirm">Submit</button>
                            <button type="button" id="buttonModalRevision" data-value="Revision" class="btn btn-warning btn-sm buttonModalConfirm">Revision</button>
                            <button type="button" id="buttonModalReject" data-value="Reject" class="btn btn-danger btn-sm buttonModalConfirm">Reject</button>
                            @elseif(session('role')->name == "End User")
                            <button type="submit" name="submitButton" id="buttonModalApproved" data-value="Update" class="btn btn-primary btn-sm buttonModalConfirm">Submit</button>
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
                            <input type="hidden" class="form-control" name="modalNote" id="modalNote">
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
        var role   = "{{session('role')->name}}";
        var mode   = "{{ $mode }}";
        var dataId = "{{ $data->id }}";
        var formCheck  = true;
        var formMode   = "";
        var existingMeterialRequestType = <?php echo json_encode($data->type_material_request); ?>;
        var existingRemarkId            = <?php echo json_encode($data->remark_id); ?>;
        var documentSatus = <?php echo json_encode($data->document_status->name);?>;
        var existingDocumentStatus = <?php echo json_encode($data->document_status_id);?>;
        
        // Setup Datatable
        var ajaxUrl  = "{{ url('master/product/dataTables') }}";
        var ajaxData = [];
        var columns  = [{ data: 'sku' }, { data: 'name' }, {data: 'product_unit.name'}, { data: 'qty' }, { data: 'action' }];
        var columnDefs  =  [
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
    
        $(document).ready(function() {
            if((mode == "show")){
                $("form :input").prop("disabled", true);
            }

            // initial change
            var isChanged = false;
            var initialValues = {};
            $('form :input').not('select[name="remark_id"], select[name="document_status_id"]').each(function() {
                initialValues[$(this).attr('name')] = $(this).val();
            });
            $('form :input').not('select[name="remark_id"], select[name="document_status_id"]').on('change keyup', function() {
                var fieldName = $(this).attr('name');
                var newValue = $(this).val();
                var oldValue = initialValues[fieldName];

                if (newValue !== oldValue) {
                    isChanged = true;
                }
            });
            // initial change

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
                'url' : '{{ url("setting/general/select?type=remark_id") }}',
                'data': [],
                'optionType' : 'remark',
                'type': 'GET'
            });
            // request select option

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
                        $('#modalNote').attr("type","text");
                        $('form').attr('action','{{ url("inventory/material-request/reject")}}'+'/'+dataId);
                    }
                    else if(mode == 'Revision'){
                        var modalCenterTitle = "Confirm Revision Data";
                        var modalMessage = "Please, fill the reason!";
                        $('#modalNote').attr("type","text");
                        $('form').attr('action','{{ url("inventory/material-request/revision")}}'+'/'+dataId);
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
                        $('#modalNote').attr("type","hidden");
                        if(isChanged == true){
                            var modalMessage = "Are you sure to approve and revisied this data?";
                        }
                        else{
                            var modalMessage = "Are you sure to approve this data?";
                        }
                        $('form').attr('action','{{ url("inventory/material-request/update")}}'+'/'+dataId);
                    }
                    else if(mode == "Update"){
                        var modalCenterTitle = "Confirm Update Data";
                        var modalMessage = "Are you sure to update this data?";
                        $('#modalNote').attr("type","hidden");
                        $('form').attr('action','{{ url("inventory/material-request/update")}}'+'/'+dataId);
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

        function setDataSelect(optionType, response) {
            var id = "";
            var existingId = "";
            if (optionType == 'product_category') {
                id = "#modalProductCategory";
            } else if (optionType == 'document_status') {
                id = "#document_status_id";
                existingId = existingDocumentStatus;
            } else if (optionType == 'remark') {
                id = "#remark_id";
                existingId = existingRemarkId;
            } else if (optionType == 'material_request_type'){
                id = "#material_request_type_id";
                existingId = existingMeterialRequestType;
            }
            
            $.each(response.results, function(index, data) {
                var option = '<option value="' + data.id + '">' + data.name + '</option>';
                if(existingId.length > 1){
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
        }

        function handleAddModalProduct(param){
            var tempAccess = param["data"]['id']
            var e = 0;
            var index = 0;
            $('#listMaterialDetail tbody tr').each(function(){
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
                var html = '<tr id="'+tempAccess+'">'+
                        '<input type="hidden" name="material_request_details['+index+'][product_id]" value="'+tempAccess+'">'+
                        '<td style="text-transform: capitalize">'+param["data"]["product_category"]["name"]+'</td>'+
                        '<td style="text-transform: capitalize">'+param["data"]["name"]+'</td>'+
                        '<td style="text-transform: capitalize">'+param["data"]["description"]+'</td>'+
                        '<td style="text-transform: capitalize">'+param["data"]["product_unit"]["name"]+'</td>'+
                        '<td style="text-transform: capitalize"><input type="number"class="form-control" name="material_request_details['+index+'][product_qty]" value="'+param["qty"]+'"></td>'+
                        '<td style="text-transform: capitalize"><input type="text" class="form-control" name="material_request_details['+index+'][product_note]"/></td>'+
                        '<td><button type="button" class="btn btn-danger btn-sm deleteList"><i class="fa fa-trash"></i></button></td>'+
                    '<tr>';

                $("#listMaterialDetail tbody").append(html);
            }

        }
    </script>
@endsection
