@extends('layout.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-4">
            <div class="col-12">
                <div id="alert"></div>

                <div class="card">
                    <h5 class="card-header border-bottom mb-3">{{ $title }}</h5>
                    <form id="form" action="{{ url('inventory/material-request/store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card-body row g-3">
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label" for="stock_type_id">Stock Type</label>
                                    <select class="form-select select2" id="stock_type_id" name="stock_type_id" data-allow-clear="true" required>
                                        <option value="">Select Value</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="form-label" for="code">No. Document</label>
                                    <input type="text" class="form-control" id="code" name="code" value="{{ $document_number }}" readonly>
                                </div>
                                <div class="col">
                                    <label class="form-label" for="request_date">Request Date</label>
                                    <input type="text" class="form-control" id="request_date" name="request_date"
                                        value="{{ date('d-m-Y') }}" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="row mb-3">
                                        <div class="col">
                                            <label class="form-label" for="warehouse_id">Warehouse</label>
                                            <select class="form-select select2" name="warehouse_id" id="warehouse_id" data-allow-clear="true" required>
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
                                <div class="col-md-12">
                                    <label class="form-label" for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="2" required></textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="divider">
                                    <div class="divider-text">Adjustment Detail</div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-sm btn-primary" type="button" id="showModal">Add Product</button>
                            </div>
                            <div class="col-md-12">
                                <table class="table" id="listMaterialDetail">
                                    <thead>
                                        <th>Code</th>
                                        <th>Product</th>
                                        <th>Unit</th>
                                        <th style="width: 15%">Qty On Hand</th>
                                        <th style="width: 12%">Qty</th>
                                        <th>Note</th>
                                        <th>Action</th>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <div class="divider">
                                    <div class="divider-text">Adjustment Detail</div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer border-top py-3">
                            <a href="{{ url('inventory/material-request') }}" class="btn btn-secondary btn-sm">Cancel</a>
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
                                        <th style="width: 10%">Unit</th>
                                        <th style="width: 20%">Qty On Hand</th>
                                        <th style="width: 20%">Qty</th>
                                        <th style="width: 10%">Actions</th>
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
        var ajaxUrl  = "{{ url('master/product/dataTables') }}";
        var ajaxData = [];
        var columns  = [{ data: 'sku' }, { data: 'name' }, {data: 'product_unit.name'}, { data: 'stock' }, { data: 'qty' }, { data: 'action' }];
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
                'url' : '{{ url("setting/general/select?type=stock_type_id") }}',
                'data': [],
                'optionType' : 'stock_type',
                'type': 'GET'
            });
            requestSelectAjax({
                'url' : '{{ url("master/warehouse/select") }}',
                'data': [],
                'optionType' : 'warehouse',
                'type': 'GET'
            });
            requestSelectAjax({
                'url' : '{{ url("setting/general/select?whereIn=Submit") }}',
                'data': [],
                'optionType' : 'document_status',
                'type': 'GET'
            });
            requestSelectAjax({
                'url' : '{{ url("setting/general/select?type=remark_id") }}',
                'data': [],
                'optionType' : 'remark',
                'type': 'GET'
            });
            
            $('#showModal').click(function() {
                if($('#stock_type_id').val() == ''){
                    toasMassage({status:false, message:'Opps, please select Stock Type!'});
                    return false;
                }
                if($('#warehouse_id').val() == ''){
                    toasMassage({status:false, message:'Opps, please select Warehouse!'});
                    return false;
                }
                
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

            $('form').submit(function(e){
            });
        })

        function setDataSelect(optionType, response) {
            var id = "";
            if (optionType == 'product_category') {
                id = "#modalProductCategory";
            } else if (optionType == 'document_status') {
                id = "#document_status_id";
            } else if (optionType == 'warehouse') {
                id = "#warehouse_id";
            } else if (optionType == 'stock_type'){
                id = "#stock_type_id";
            }
            
            $.each(response.results, function(index, data) {
                $(id).append('<option value="' + data.id + '">' + data.name + '</option>');
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
                        '<input type="hidden" name="adjustment_details['+index+'][product_id]" value="'+tempAccess+'">'+
                        '<td style="text-transform: capitalize">'+param["data"]["code"]+'</td>'+
                        '<td style="text-transform: capitalize">'+param["data"]["name"]+'</td>'+
                        '<td style="text-transform: capitalize">'+param["data"]["product_unit"]["name"]+'</td>'+
                        '<td style="text-transform: capitalize">'+param["data"]["stock"]+'</td>'+
                        '<td style="text-transform: capitalize"><input type="number"class="form-control" name="material_request_details['+index+'][product_qty]" value="'+param["qty"]+'"></td>'+
                        '<td style="text-transform: capitalize"><input type="text" class="form-control" name="material_request_details['+index+'][product_note]"/></td>'+
                        '<td><button type="button" class="btn btn-danger btn-sm deleteList"><i class="fa fa-trash"></i></button></td>'+
                    '<tr>';

                $("#listMaterialDetail tbody").append(html);
            }

        }
    </script>
@endsection