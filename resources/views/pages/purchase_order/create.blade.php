@extends('layout.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-4">
            <div class="col-12">
                <div id="alert"></div>

                <div class="card">
                    <h5 class="card-header border-bottom mb-3">{{ $title }}</h5>
                    <form id="form" action="{{ url('purchasing/purchase-request/store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card-body row g-3">
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label" for="code">No. Document</label>
                                    <input type="text" class="form-control" id="code" name="code" value="{{ $document_number }}" readonly>
                                </div>
                                <div class="col">
                                    <label class="form-label" for="date">Request Date</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="{{ date('d-m-Y') }}" disabled>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label" for="effective_date">Estimation Required</label>
                                    <input type="date" class="form-control" id="effective_date" name="effective_date" required>
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
                                    <select class="form-select select2" data-allow-clear="true" id="warehouse_id" name="warehouse_id" required>
                                        <option value="">Select Value</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="form-label" for="remark_id">Remark</label>
                                    <select class="form-select select2" data-allow-clear="true" id="remark_id" name="remark_id" required>
                                        <option value="">Select Value</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="form-label" for="document_status_id">Status</label>
                                    <select class="form-select select2" data-allow-clear="true" id="document_status_id" name="document_status_id" required>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label" for="document_photo">Photo</label>
                                    <input type="file" class="form-control" id="document_photo" name="document_photo">
                                </div>
                                <div class="col">
                                    <label class="form-label" for="document_pdf">Document PDF</label>
                                    <input type="file" class="form-control" id="document_pdf" name="document_pdf">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label" for="description">Justification</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="divider">
                                    <div class="divider-text">Purchase Request Detail</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-sm btn-primary" type="button" id="showModal" data-mode="showProduct">Show Product</button>
                                <button class="btn btn-sm btn-primary" type="button" id="newProduct" data-mode="newProduct">New Product</button>
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
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <div class="divider">
                                    <div class="divider-text">Purchase Request Detail</div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer border-top py-3">
                            <a href="{{ url('purchasing/material-request') }}" class="btn btn-secondary btn-sm">Cancel</a>
                            <button type="submit" name="submitButton" class="btn btn-primary btn-sm">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalShowProduct" tabindex="-1" aria-modal="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <select class="form-select select2" data-allow-clear="true" id="modalProductCategory" placeholder="Product Category" required>
                                <option value="">All Product</option>
                            </select>
                        </div>
                        <div class="col-md-3">
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

    <div class="modal fade" id="modalShowAddNewProduct" tabindex="-1" aria-modal="true">
        <div class="modal-dialog modal-dialog-scrollable modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" >New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="newProduct">
                        <div class="row mb-2">
                            <div class="col-md-12 mb-2">
                                <label for="" class="form-label">Product Category</label>
                                <select class="form-select select2" name="modalAddProduct-product_category_id" data-allow-clear="true" id="productCategory" placeholder="All Products" required>
                                </select>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label for="" class="form-label">SKU</label>
                                <input class="form-control" type="text" name="modalAddProduct-sku" id="modalAddProduct-sku"/>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label for="" class="form-label">Code</label>
                                <input class="form-control" type="text" name="modalAddProduct-code" id="modalAddProduct-code"/>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label for="" class="form-label">Name</label>
                                <input class="form-control" type="text" name="modalAddProduct-name" id="modalAddProduct-name"/>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label for="" class="form-label">Description</label>
                                <textarea class="form-control" name="modalAddProduct-description" id="modalAddProduct-description"></textarea>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label for="" class="form-label">Unit</label>
                                <select class="form-control" name="modalAddProduct-unit_id" id="modalAddProduct-unit_id"></select>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label for="" class="form-label">In Stock</label>
                                <select class="form-control" name="modalAddProduct-is_inventory" id="modalAddProduct-is_inventory">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-2">
                                <div class="divider">
                                    <div class="divider-text">Additional</div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label for="" class="form-label">Dimensi</label>
                                <input class="form-control" type="text" name="modalAddProduct-dimension" id="modalAddProduct-dimension"/>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label for="" class="form-label">Part Number</label>
                                <input class="form-control" type="text" name="modalAddProduct-part_number" id="modalAddProduct-part_number"/>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label for="" class="form-label">For Machine</label>
                                <select id="modalAddProduct-machine_id" name="modalAddProduct-machine_id[]" class="form-select select2" data-allow-clear="true" multiple>
                                </select>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label for="" class="form-label">Spesification</label>
                                <textarea class="form-control" name="modalAddProduct-spesification" id="modalAddProduct-spesification"></textarea>
                            </div>
                            <div class="col-md-12 mb-2">
                                <div class="divider">
                                    <div class="divider-text">Additional</div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">
                      Close
                    </button>
                    <button type="button" class="btn btn-primary waves-effect waves-light" id="submitNewProduct">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
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
            requestSelectAjax({
                'url' : '{{ url("setting/general/select?whereIn=Submit") }}',
                'data': [],
                'optionType' : 'document_status',
                'type': 'GET'
            });

            $('#showModal').click(function() {
                // Setup Datatable
                initializeDataTable(ajaxUrl, ajaxData, columns, columnDefs, buttons);

                $('#modalShowProduct').modal('toggle');
            });

            $('#department_id').change(function(){
                requestSelectAjax({
                    'url' : '{{ url("master/division/select?department_id=") }}' + $(this).val(),
                    'data': [],
                    'optionType' : 'division',
                    'type': 'GET'
                });
            });

            // add new product
            $('#newProduct').click(function(){
                requestSelectAjax({
                    'url' : '{{ url("setting/general/select?type=product_category_id") }}',
                    'data': [],
                    'optionType' : 'add_product_category',
                    'type': 'GET'
                });
                requestSelectAjax({
                    'url' : '{{ url("setting/general/select?type=unit_id") }}',
                    'data': [],
                    'optionType' : 'add_product_unit',
                    'type': 'GET'
                });
                requestSelectAjax({
                    'url' : '{{ url("setting/general/select?type=machine_id") }}',
                    'data': [],
                    'optionType' : 'add_product_machine',
                    'type': 'GET'
                });

                $('#modalShowAddNewProduct').modal('toggle');
            });
            $('body').on('click', '#submitNewProduct', function(){
                if(
                    ($('#productCategory').val() == "") || 
                    ($('#modalAddProduct-sku').val() == "") || 
                    ($('#modalAddProduct-code').val() == "") || 
                    ($('#modalAddProduct-name').val() == "") || 
                    ($('#modalAddProduct-description').val() == "") || 
                    ($('#modalAddProduct-unit_id').val() == "") || 
                    ($('#modalAddProduct-is_inventory').val() == "")
                ){
                    toasMassage({"status":false, message:'Opps, please fill this form!'})
                    return false;
                }

                if($('#productCategory option:selected').text() == "Sparepart"){
                    if(
                        ($('#modalAddProduct-dimension').val() == "") ||
                        ($('#modalAddProduct-part_number').val() == "") || 
                        ($('#modalAddProduct-machine_id').val() == "") || 
                        ($('#modalAddProduct-spesification').val() == "")
                    ){
                        toasMassage({"status":false, message:'Opps, please fill Additional input!'})
                        return false;
                    }
                }

                data = {
                    'product_category_id' : $('#productCategory').val(),
                    'code'          : $('#modalAddProduct-code').val(),
                    'sku'           : $('#modalAddProduct-sku').val(),
                    'name'          : $('#modalAddProduct-name').val(),
                    'unit_id'       : $('#modalAddProduct-unit_id').val(),
                    'is_inventory'  : $('#modalAddProduct-is_inventory').val(),
                    'dimension'     : $('#modalAddProduct-dimension').val(),
                    'part_number'   : $('#modalAddProduct-part_number').val(),
                    'description'   : $('#modalAddProduct-description').val(),
                    'spesification' : $('#modalAddProduct-spesification').val(),
                    'machine_id'    : $('#modalAddProduct-machine_id').val(),
                    'status'        : 1
                };

                $.ajax({
                    url     : '{{ url("master/product/store") }}',
                    method  : 'POST',
                    data    : data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    success : function(response) {
                        toasMassage({"status":response.status, message:response.message})
                        if(response.status == true){
                            $('#modalShowAddNewProduct').modal('toggle');
                        }
                    },
                    error: function(xhr, status, error) {
                    }
                });
            });
            // add new product

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
            })

            $('form').submit(function(e){
                var count = 0;
                $('#listPurchaseDetail tbody tr').each(function(){
                    count++;
                });

                if(count == 0){
                    toasMassage({status:false, message:'Opps, please fill purchase request detail!'});
                    e.preventDefault();
                }
            });
        })

        function setDataSelect(optionType, response) {
            var id = "";
            if (optionType == 'product_category') {
                id = "#modalProductCategory";
            } else if (optionType == "department"){
                id = "#department_id";
            } else if (optionType == "division"){
                id = "#division_id";
            } else if (optionType == "warehouse"){
                id = "#warehouse_id";
            } else if (optionType == "remark"){
                id = "#remark_id";
            } else if (optionType == 'document_status') {
                id = "#document_status_id";
            } else if (optionType == 'add_product_machine') {
                id = "#modalAddProduct-machine_id";
            } else if (optionType == 'add_product_category'){
                id = "#productCategory";
            } else if (optionType == 'add_product_unit'){
                id = "#modalAddProduct-unit_id";
            }
            
            $.each(response.results, function(index, data) {
                $(id).append('<option value="' + data.id + '">' + data.name + '</option>');
            });
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
