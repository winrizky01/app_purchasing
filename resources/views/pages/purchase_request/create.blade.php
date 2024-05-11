@extends('layout.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-4">
            <div class="col-12">
                <div id="alert"></div>

                <div class="card">
                    <h5 class="card-header border-bottom mb-3">{{ $title }}</h5>
                    <form id="form" action="{{ url('purchasing/material-request/store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card-body row g-3">
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label" for="product_category_id">Type Purchase Request</label>
                                    @php
                                        $readOnly = '';
                                        $value = '';
                                        if (auth()->user()->user_location_id != null) {
                                            $readOnly = 'disabled';
                                            if (auth()->user()->user_location->name == 'Site') {
                                                $value = 'normal';
                                            } else {
                                                $value = 'general';
                                            }
                                        }
                                    @endphp
                                    <select class="form-select select2" data-allow-clear="true" required
                                        {{ $readOnly }}>
                                        <option value="">Select Value</option>
                                        @php
                                            $v = ['normal', 'general'];
                                        @endphp
                                        @for ($i = 0; $i < count($v); $i++)
                                            @if ($v[$i] == $value)
                                                <option value="{{ $v[$i] }}" selected>{{ ucfirst($v[$i]) }}</option>
                                            @else
                                                <option value="{{ $v[$i] }}">{{ ucfirst($v[$i]) }}</option>
                                            @endif
                                        @endFor
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="form-label" for="product_category_id">Request Date</label>
                                    <input type="text" class="form-control" id="sku" name="sku"
                                        value="{{ date('d-m-Y') }}" disabled>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label" for="product_category_id">No. Document</label>
                                    <input type="text" class="form-control" id="document_number" name="document_number" value="{{ $document_number }}" readonly>
                                </div>
                                <div class="col">
                                    <label class="form-label" for="product_category_id">Estimation Required</label>
                                    <input type="date" class="form-control" id="sku" name="sku">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label" for="product_category_id">Department</label>
                                    <select class="form-select select2" data-allow-clear="true" required>
                                        <option value="">Select Value</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="form-label" for="product_category_id">Divisi</label>
                                    <select class="form-select select2" data-allow-clear="true" required>
                                        <option value="">Select Value</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label" for="product_category_id">Warehouse</label>
                                    <select class="form-select select2" data-allow-clear="true" required>
                                        <option value="">Select Value</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="form-label" for="product_category_id">Remark</label>
                                    <select class="form-select select2" data-allow-clear="true" required>
                                        <option value="">Select Value</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="form-label" for="product_category_id">Status</label>
                                    <select class="form-select select2" data-allow-clear="true" required>
                                        <option value="">Select Value</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label" for="product_category_id">Notes</label>
                                    <textarea class="form-control" id="spesification" name="spesification" rows="3"></textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="divider">
                                    <div class="divider-text">Purchase Request Detail</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-sm btn-warning" type="button" id="showModal" data-mode="showPurchase">From MR</button>
                                <button class="btn btn-sm btn-primary" type="button" id="showModal" data-mode="showProduct">Add Product</button>
                            </div>
                            <div class="col-md-12">
                                <table class="table" id="listMaterialDetail">
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
                'url' : '{{ url('setting/general/select?type=product_category_id') }}',
                'data': [],
                'optionType' : 'product_category',
                'type': 'GET'
            });
            // requestSelectAjax({
            //     'url' : '{{ url('setting/general/select?type=unit_id') }}',
            //     'data': [],
            //     'optionType' : 'unit',
            //     'type': 'GET'
            // });
            // requestSelectAjax({
            //     'url' : '{{ url('setting/general/select?type=machine_id') }}',
            //     'data': [],
            //     'optionType' : 'machine',
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
            if (optionType == 'product_category') {
                id = "#modalProductCategory";
            } else if (optionType == 'product') {
                id = "#unit_id";
            } else if (optionType == 'machine') {
                id = "#machine_id";
            }

            $.each(response.results, function(index, data) {
                $(id).append('<option value="' + data.id + '">' + data.name + '</option>');
            });
        }

        function handleAddModalProduct(param){
            var tempAccess = param["data"]['id']
            var e = 0;
            $('#listMaterialDetail tbody tr').each(function(){
                if($(this).attr('id') == tempAccess){
                    e++;
                }
            });

            if(e > 0){
                toasMassage({status:false, message:'Opps, product already exist!'});
                return false;
            }
            else{
                var html = '<tr id="'+tempAccess+'">'+
                        '<input type="hidden" name="product_id[]" value="'+tempAccess+'">'+
                        '<td style="text-transform: capitalize">'+param["data"]["product_category"]["name"]+'</td>'+
                        '<td style="text-transform: capitalize">'+
                            param["data"]["name"]+ ' - ' +
                            param["data"]["description"]+ ' - ' +
                            param["data"]["dimension"]+ ' - ' +
                            param["data"]["part_number"]+
                        '</td>'+
                        '<td style="text-transform: capitalize">'+param["data"]["product_unit"]["name"]+'</td>'+
                        '<td style="text-transform: capitalize"><input type="number"class="form-control" name="product_qty[]" value="'+param["qty"]+'"></td>'+
                        '<td style="text-transform: capitalize"><input type="text" class="form-control" name="product_note[]"/></td>'+
                        '<td><button type="button" class="btn btn-danger btn-sm deleteList"><i class="fa fa-trash"></i></button></td>'+
                    '<tr>';

                $("#listMaterialDetail tbody").append(html);
            }

        }
    </script>
@endsection
