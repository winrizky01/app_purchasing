@extends('layout.app')
@section('content')
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
                                    <input type="date" class="form-control" id="usage_date" data-allow-clear="true" name="usage_date">
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
            // requestSelectAjax({
            //     'url' : '{{ url('master/department/select') }}',
            //     'data': [],
            //     'optionType' : 'department',
            //     'type': 'GET'
            // });
            // requestSelectAjax({
            //     'url' : '{{ url('master/division/select') }}',
            //     'data': [],
            //     'optionType' : 'division',
            //     'type': 'GET'
            // });

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
        })

        function setDataSelect(optionType, response) {
            var id = "";
            if (optionType == 'warehouse') {
                id = "#warehouse_id";
            }

            $.each(response.results, function(index, data) {
                $(id).append('<option value="' + data.id + '">' + data.name + '</option>');
            });
        }

        function handleAddModalProduct(param){
            var detail = param['data']['material_request_details'];

            for(var i = 0; i < detail.length; i++){
                var html = '<tr id=>'+
                    '<input type="hidden" name="material_usage['+i+'][material_request_id]" value="'+param["data"]["id"]+'">'+
                    '<input type="hidden" name="material_usage['+i+'][material_request_detail_id]" value="'+detail[i]["id"]+'">'+
                    '<input type="hidden" name="material_usage['+i+'][product_id]" value="'+detail[i]["product_id"]+'">'+
                    '<input type="hidden" name="material_usage['+i+'][qty]" value="'+detail[i]["qty"]+'">'+
                    '<td style="text-transform: capitalize">'+param["data"]["code"]+'</td>'+
                    '<td style="text-transform: capitalize">'+param["data"]["request_date"]+'</td>'+
                    '<td style="text-transform: capitalize">'+detail[i]["product"]["name"]+'</td>'+
                    '<td style="text-transform: capitalize">'+detail[i]["product"]["product_unit"]["name"]+'</td>'+
                    '<td style="text-transform: capitalize">'+detail[i]["qty"]+'</td>'+
                    '<td style="text-transform: capitalize">'+detail[i]["notes"]+'</td>'+
                    '<td><button type="button" class="btn btn-danger btn-sm deleteList"><i class="fa fa-trash"></i></button></td>'+
                '</tr>';

                $("#listMaterialDetail tbody").append(html);
            }
            // console.log(detail);
            // return false;
            // var tempAccess = param["data"]['id']
            // var e = 0;
            // $('#listMaterialDetail tbody tr').each(function(){
            //     if($(this).attr('id') == tempAccess){
            //         e++;
            //     }
            // });

            // if(e > 0){
            //     toasMassage({status:false, message:'Opps, product already exist!'});
            //     return false;
            // }
            // else{
            //     var html = '<tr id="'+tempAccess+'">'+
            //             '<input type="hidden" name="product_id[]" value="'+tempAccess+'">'+
            //             '<td style="text-transform: capitalize">'+param["data"]["product_category"]["name"]+'</td>'+
            //             '<td style="text-transform: capitalize">'+param["data"]["name"]+'</td>'+
            //             '<td style="text-transform: capitalize">'+param["data"]["description"]+'</td>'+
            //             '<td style="text-transform: capitalize">'+param["data"]["product_unit"]["name"]+'</td>'+
            //             '<td style="text-transform: capitalize"><input type="number"class="form-control" name="product_qty[]" value="'+param["qty"]+'"></td>'+
            //             '<td style="text-transform: capitalize"><input type="text" class="form-control" name="product_note[]"/></td>'+
            //             '<td><button type="button" class="btn btn-danger btn-sm deleteList"><i class="fa fa-trash"></i></button></td>'+
            //         '<tr>';

            //     $("#listMaterialDetail tbody").append(html);
            // }

        }
    </script>
@endsection
