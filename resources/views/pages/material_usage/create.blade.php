@extends('layout.app')
@section('content') 

    <div class="sk-wave sk-primary">
        <div class="sk-wave-rect"></div>
        <div class="sk-wave-rect"></div>
        <div class="sk-wave-rect"></div>
        <div class="sk-wave-rect"></div>
    </div>

    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="row mb-4">
            <div class="col-12">
                <div id="alert"></div>

                <div class="card">
                    <h5 class="card-header border-bottom mb-3">{{ $title }}</h5>
                    <form id="form" action="{{ url('inventory/material-usage/store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card-body row g-3">
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label" for="code">No. Document</label>
                                    <input type="text" class="form-control" id="code" name="code" value="{{ $document_number }}" readonly>
                                </div>
                                <div class="col mb-3">
                                    <label class="form-label" for="usage_date">Usage Date</label>
                                    <input type="date" class="form-control" id="usage_date" data-allow-clear="true" name="usage_date" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label" for="department_id">Department</label>
                                    <select class="form-select select2" id="department_id" data-allow-clear="true" required disabled>
                                        <option value="">Select Value</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="form-label" for="division_id">Divisi</label>
                                    <select class="form-select select2" id="division_id" name="division_id" data-allow-clear="true" required disabled>
                                        <option value="">Select Value</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col mb-3">
                                    <label class="form-label" for="warehouse_id">Warehouse</label>
                                    <select class="form-select select2" id="warehouse_id" name="warehouse_id" data-allow-clear="true" required>
                                        <option value="">Select Value</option>
                                    </select>
                                </div>
                                <div class="col mb-3">
                                    <label class="form-label" for="status_id">Status</label>
                                    <select class="form-select select2" id="status_id" name="status_id" data-allow-clear="true" required>
                                        <option value="1" selected>Submit</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label" for="description">Note</label>
                                    <textarea class="form-control" id="description" name="description" data-allow-clear="true" rows="1"></textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="divider">
                                    <div class="divider-text">Material Usage Detail</div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-sm btn-primary" type="button" id="showModal">Material Request</button>
                            </div>
                            <div class="col-md-12">
                                <table class="table" id="listMaterialDetail">
                                    <thead>
                                        <th>Material Req.</th>
                                        <th>Date Req.</th>
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
                                    <div class="divider-text">Material Usage Detail</div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer border-top py-3">
                            <a href="{{ url('inventory/material-usage') }}" class="btn btn-secondary btn-sm">Cancel</a>
                            <button type="button" id="checkStock" class="btn btn-primary btn-sm">Submit</button>
                            <button type="submit" id="submit" class="d-none"></button>
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
                    <div class="col">
                        <div class="card-datatable table-responsive">
                            <table class="datatables table border-top" id="modalProduct" style="font-size: 10pt">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Request Date</th>
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
        var existingDepartmentId;
        var existingDivisionId;
        var errorStock = true;

        // Setup Datatable
        var ajaxUrl  = "{{ url('inventory/material-request/dataTables') }}";
        var ajaxData = [];
        var columns  = [{ data: 'code' }, { data: 'request_date' }, { data: 'action' }];
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
                'url' : '{{ url("master/warehouse/select") }}',
                'data': [],
                'optionType' : 'warehouse',
                'type': 'GET'
            });

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
            })

            // Panggil fungsi checkStockBeforeSubmit saat tombol submit ditekan
            $('#checkStock').on('click', function(event) {
                checkStockBeforeSubmit().then(function(stockIsValid) {
                    // Jika stok valid, lanjutkan dengan pengiriman form
                    if (stockIsValid.status) {
                        toasMassage({status:true, message:'Sufficient stock for all products! Please wait a moment!'});

                        // Tunda pengiriman form
                        $('.sk-wave').show();
                        $('.card').addClass('blur');

                        $('#checkStock').attr('disabled', 'disabled');
                        setTimeout(function() {
                            $('#submit').click();
                        }, 6000); // Waktu tunda dalam milidetik (6000 ms = 6 detik)
                    } else {
                        // Beritahu pengguna bahwa stok tidak mencukupi
                        toasMassage({status:false, message:stockIsValid.message});
                    }
                });
            });

        })

        // Fungsi untuk melakukan pengecekan stok satu per satu
        function checkStockForProduct(productId, warehouseId, qty, callback) {
            $.ajax({
                type: 'GET',
                url: '{{ url("inventory/report/stock-product/checkStock") }}', // Ganti dengan URL yang benar
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    'product_id': productId,
                    'warehouse_id': warehouseId,
                    'usageQty': qty
                },
                beforeSend: function(){
                    $('.sk-wave').show();
                    $('.card').addClass('blur');
                },
                success: function(response) {
                    $('.sk-wave').hide();
                    $('.card').removeClass('blur');

                    callback(response)
                },
                error: function(xhr, status, error) {
                    // Tangani error saat melakukan AJAX
                    console.error('Error:', error);
                    callback({'status':false,'message':'Error get data!'}); // Set hasil pengecekan stok ke false jika terjadi error
                }
            });
        }

        function checkStockBeforeSubmit() {
            return new Promise(function(resolve, reject) {
                var allProductsValid        = true;
                var totalRequestsPending    = $('#listMaterialDetail tbody tr').length;

                // Loop melalui setiap baris pada tabel material usage detail
                $('#listMaterialDetail tbody tr').each(function(index, row) {
                    var productId   = $(row).find('input[name^="material_usage_detail["][name$="[product_id]"]').val();
                    var warehouseId = $('#warehouse_id').val(); // Ambil ID gudang dari form
                    var qty         = $(row).find('input[name^="material_usage_detail["][name$="[qty]"]').val();

                    if((productId != "")&&(warehouseId != "")&&(qty != "")){
                        // Panggil fungsi untuk memeriksa stok untuk setiap produk
                        checkStockForProduct(productId, warehouseId, qty, function(isValid) {
                            totalRequestsPending--; // Kurangi total panggilan AJAX yang masih tertunda

                            if (!isValid.status) {
                                allProductsValid = false;
                            }
                            // Jika semua panggilan AJAX selesai, resolve Promise dengan nilai allProductsValid
                            if (totalRequestsPending === 0) {
                                if(!isValid.product_name){
                                    var message = isValid.message;
                                }
                                else{
                                    var message = 'Stock is not sufficient for ' + isValid.product_name + '!';
                                }
                                resolve({'status':allProductsValid, 'message': message});
                            }
                        });
                    }
                    else{
                        resolve({'status':false, 'message':'Opps, please fill this form!'});
                    }
                });
            });
        }


        function setDataSelect(optionType, response) {
            var id = "";
            var existingId = "";
            if (optionType == 'warehouse') {
                id = "#warehouse_id";
            }
            else if (optionType == 'department'){
                id = "#department_id";
                existingId = existingDepartmentId;
            }
            else if (optionType == 'division'){
                id = "#division_id";
                existingId = existingDivisionId;
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
        }

        function handleAddModalProduct(param){
            $('#listMaterialDetail tbody').empty();

            existingDepartmentId = param.data.department_id;
            existingDivisionId   = param.data.division_id;

            requestSelectAjax({
                'url' : '{{ url("master/department/select") }}',
                'data': [],
                'optionType' : 'department',
                'type': 'GET'
            });
            requestSelectAjax({
                'url' : '{{ url("master/division/select?department_id") }}' + existingDepartmentId,
                'data': [],
                'optionType' : 'division',
                'type': 'GET'
            });

            var detail = param['data']['material_request_details'];
            for(var i = 0; i < detail.length; i++){
                var html = '<tr id=>'+
                    '<input type="hidden" name="material_usage_detail['+i+'][material_request_id]" value="'+param["data"]["id"]+'">'+
                    '<input type="hidden" name="material_usage_detail['+i+'][material_request_detail_id]" value="'+detail[i]["id"]+'">'+
                    '<input type="hidden" name="material_usage_detail['+i+'][product_id]" value="'+detail[i]["product_id"]+'">'+
                    '<input type="hidden" name="material_usage_detail['+i+'][qty]" value="'+detail[i]["qty"]+'">'+
                    '<td style="text-transform: capitalize">'+param["data"]["code"]+'</td>'+
                    '<td style="text-transform: capitalize">'+param["data"]["request_date"]+'</td>'+
                    '<td style="text-transform: capitalize">'+detail[i]["product"]["name"]+'</td>'+
                    '<td style="text-transform: capitalize">'+detail[i]["product"]["product_unit"]["name"]+'</td>'+
                    '<td style="text-transform: capitalize">'+detail[i]["qty"]+'</td>'+
                    '<td style="text-transform: capitalize"><input type="text" class="form-control" name="material_usage_detail['+i+'][notes]"/></td>'+
                    '<td><button type="button" class="btn btn-danger btn-sm deleteList"><i class="fa fa-trash"></i></button></td>'+
                '</tr>';

                $("#listMaterialDetail tbody").append(html);
            }

            $('#modalShowProduct').modal('toggle');
        }
    </script>
@endsection
