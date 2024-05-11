@extends('layout.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-4">
            <div class="col-12">
                <div id="alert"></div>

                <div class="card">
                    <h5 class="card-header border-bottom mb-3">{{ $title }}</h5>
                    <form id="form" action="#" method="POST" enctype="multipart/form-data">
                        {{-- <form id="form" action="{{ url('inventory/material-request/store') }}" method="POST" enctype="multipart/form-data"> --}}
                        @csrf
                        <div class="card-body row g-3">
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label" for="code">No. Document</label>
                                    <input type="text" class="form-control" id="code" name="code" value="{{ $document_number }}" readonly>
                                </div>
                                <div class="col">
                                    <label class="form-label" for="date">Receipt Date</label>
                                    <input type="text" class="form-control" id="date" name="date"
                                        value="{{ date('d-m-Y') }}" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label" for="vendor_id">Vendor</label>
                                    <select class="form-select select2" id="vendor_id" name="vendor_id" data-allow-clear="true" required>
                                        <option value="">Select Value</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="form-label" for="vendor_id">Nomer Surat Jalan</label>
                                    <input type="text" class="form-control" id="code" name="code">
                                </div>
                            </div>
                            <div class="row mb-3">
                                    <div class="col">
                                    <label class="form-label" for="vendor_id">Nomer Pengiriman</label>
                                    <input type="text" class="form-control" id="code" name="code">
                                </div>
                                <div class="col">
                                    <label class="form-label" for="vendor_id">Nomer Kendaraan</label>
                                    <input type="text" class="form-control" id="code" name="code">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="document_status_id">Status</label>
                                    <select class="form-select select2" name="document_status_id" id="document_status_id" data-allow-clear="true" required>
                                        <option value="">Select Value</option>
                                    </select>
                                </div>                
                                <div class="col-md-12 mt-3">
                                    <label class="form-label" for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="2" required></textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="divider">
                                    <div class="divider-text">Material Receipt Detail</div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-sm btn-primary" type="button" id="showModal">Purchase Order</button>
                            </div>
                            <div class="col-md-12">
                                <table class="table" id="listMaterialDetail">
                                    <thead>
                                        <th>No. PO</th>
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
                                    <div class="divider-text">Material Receipt Detail</div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer border-top py-3">
                            <a href="{{ url('inventory/material-receipt') }}" class="btn btn-secondary btn-sm">Cancel</a>
                            <button type="submit" name="submitButton" class="btn btn-primary btn-sm">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalShowTransaction" tabindex="-1" aria-modal="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label for="" class="form-label">Date Transaction</label>
                            <select class="form-select select2" data-allow-clear="true" id="modalTransactionDate" placeholder="All Products" required>
                                <option value="">All Product</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="" class="form-label">Number Transaction</label>
                            <input type="text" class="form-control" id="modalTransactionNumber">
                        </div>
                    </div>
                    <div class="col">
                        <div class="card-datatable table-responsive">
                            <table class="datatables table border-top" id="modalTransaction" style="font-size: 10pt">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Number Transaction</th>
                                        <th style="width: 10%">Note</th>
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
        var ajaxUrl  = "{{ url('purchasing/purchase_order/dataTables') }}";
        var ajaxData = [];
        var columns  = [{ data: 'date' }, { data: 'number' }, {data: 'notes'}, { data: 'action' }];
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
                if($('#vendor_id').val() == ''){
                    toasMassage({status:false, message:'Opps, please select Vendor!'});
                    return false;
                }
                ajaxData = [{"vendor_id":$('#vendor_id').val()}];

                // Setup Datatable
                initializeDataTable(ajaxUrl, ajaxData, columns, columnDefs, buttons);

                $('#modalShowTransaction').modal('toggle');
            });

            $('body').on('click', '.btnAddTransactionModal', function(){
                var selectedRow = $(this).closest('tr');
                var rowData     = $('#modalTransaction').DataTable().row(selectedRow).data();

                requestAjax({
                    'url' : '{{ url("purchasing/purchase-order?code='+rowData+'") }}',
                    'data': [],
                    'optionType' : 'remark',
                    'type': 'GET'
                });
                // handleAddModalTransaction({'data':rowData});
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

        function handleRequestAjax(optionType, response){}

        function handleAddModalTransaction(param){
            console.log(param);
            return false;

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
                        '<input type="hidden" name="material_receipt_details['+index+'][purchase_order_id]" value="'+tempAccess+'">'+
                        '<input type="hidden" name="material_receipt_details['+index+'][product_id]" value="'+param["product_id"]+'">'+
                        '<td style="text-transform: capitalize">'+param["data"]["product"]["name"]+'</td>'+
                        '<td style="text-transform: capitalize">'+param["data"]["product_unit"]["name"]+'</td>'+
                        '<td style="text-transform: capitalize"><input type="number"class="form-control" name="material_request_details['+index+'][qty]" value="'+param["qty"]+'"></td>'+
                        '<td style="text-transform: capitalize"><input type="text" class="form-control" name="material_request_details['+index+'][notes]"/></td>'+
                        '<td><button type="button" class="btn btn-danger btn-sm deleteList"><i class="fa fa-trash"></i></button></td>'+
                    '<tr>';

                $("#listMaterialDetail tbody").append(html);
            }

        }
    </script>
@endsection
