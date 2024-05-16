@extends('layout.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-4">
            <div class="col-12">
                <div id="alert"></div>

                <div class="card">
                    <h5 class="card-header border-bottom mb-3">{{ $title }}</h5>
                    <form id="form" action="{{ url('purchasing/purchase-order/store') }}" method="POST"
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
                                    <input type="text" class="form-control" id="date" name="date" value="{{ date('d-m-Y') }}" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label" for="effective_date">Vendor</label>
                                    <select class="form-select select2" data-allow-clear="true" id="vendor_id" name="vendor_id[]" required>
                                        <option value="">Select Value</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label" for="effective_date">Estimation Required</label>
                                    <input type="date" class="form-control" id="effective_date" name="effective_date" required>
                                </div>
                                <div class="col">
                                    <label class="form-label" for="max_date_delivery">Max Delivery</label>
                                    <input type="date" class="form-control" id="max_date_delivery" name="max_date_delivery" required>
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
                                <button class="btn btn-sm btn-primary" type="button" id="showModal" data-mode="showProduct">Show PR</button>
                            </div>
                            <div class="col-md-12">
                                <table class="table" id="listPurchaseDetail">
                                    <thead>
                                        <th>No PR</th>
                                        <th>Request Date</th>
                                        <th>Description</th>
                                        <th>Unit</th>
                                        <th>Qty</th>
                                        <th style="width: 15%">Price</th>
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
                            <a href="{{ url('purchasing/purchase-order') }}" class="btn btn-secondary btn-sm">Cancel</a>
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
                                        <th>No PR</th>
                                        <th>PR Date</th>
                                        <th>Description</th>
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

    <script type="text/javascript">
        // Setup Datatable
        var ajaxUrl  = "{{ url('purchasing/purchase-request/dataTables') }}";
        var ajaxData = {"mode":"get_for_po"};
        var columns  = [{ data: 'code' }, { data: 'date' }, { data: 'product.name' }, { data: 'product.product_unit.name' }, { data: 'qty' }, { data: 'action' }];
        var columnDefs  =  [
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
                'url' : '{{ url("master/vendor/select") }}',
                'data': [],
                'optionType' : 'vendor',
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
                var rowData     = $('#modalProduct').DataTable().row(selectedRow).data();

                handleAddModalProduct({'data':rowData});
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
            if (optionType == 'vendor') {
                id = "#vendor_id";
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
                var html = '<tr id="'+tempAccess+'">'+
                        '<input type="hidden" name="purchase_order_details['+index+'][id]" value="'+tempAccess+'">'+
                        '<input type="hidden" name="purchase_order_details['+index+'][purchase_request_id]" value="'+param["data"]["purchase_request_id"]+'">'+
                        '<input type="hidden" name="purchase_order_details['+index+'][purchase_request_detail_id]" value="'+param["data"]["id"]+'">'+
                        '<input type="hidden" name="purchase_order_details['+index+'][product_id]" value="'+param["data"]["product_id"]+'">'+
                        '<input type="hidden" name="purchase_order_details['+index+'][qty]" value="'+param["data"]["qty"]+'">'+
                        '<td style="text-transform: capitalize">'+param["data"]["code"]+'</td>'+
                        '<td style="text-transform: capitalize">'+param["data"]["date"]+'</td>'+
                        '<td style="text-transform: capitalize">'+param["data"]["product"]["name"]+'</td>'+
                        '<td style="text-transform: capitalize">'+param["data"]["product"]["product_unit"]["name"]+'</td>'+
                        '<td style="text-transform: capitalize">'+param["data"]["qty"]+'</td>'+
                        '<td style="text-transform: capitalize"><input type="number"class="form-control" name="purchase_order_details['+index+'][price]"></td>'+
                        '<td style="text-transform: capitalize"><input type="text" class="form-control" name="purchase_order_details['+index+'][note]"/></td>'+
                        '<td><button type="button" class="btn btn-danger btn-sm deleteList"><i class="fa fa-trash"></i></button></td>'+
                    '</tr>';

                $("#listPurchaseDetail tbody").append(html);
            }

        }
    </script>
@endsection
