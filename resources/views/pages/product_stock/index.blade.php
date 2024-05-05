@extends('layout.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Filter -->
        <div class="card mb-3">
            <div class="card-header">Filter Table</div>
            <div class="card-body">
                <div class="row pb-2 gap-3 gap-md-0">
                    <div class="col-md-4 mb-3">
                        <label for="filterDate">Date</label>
                        <input type="text" class="form-control flatpickr-input active" placeholder="YYYY-MM-DD to YYYY-MM-DD" id="flatpickr-range">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="filterWarehouse">Warehouse</label>
                        <select class="select2 form-control" name="filterwarehouse" id="filterWarehouse">
                            <option value="">Select Status</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="filterSectionWH">Section Warehouse</label>
                        <select class="select2 form-control" name="filtersectionwarehouse" id="filterSectionWH">
                            <option value="">Select Status</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="filterproductname">Product Name</label>
                        <select class="select2 form-control" name="filterproductname" id="filterproductname" multiple>
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
        
        <!-- Material List Table -->
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-1" style="text-align:center">{{ $title }} <br> 
                    Periode : <span id="report_date">{{date("m-d-Y")}}</span> 
                    <span id="warehouse"></span> 
                </h5>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables table border-top">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th style="width: 40%">Product</th>
                            <th style="width: 10%">In</th>
                            <th style="width: 10%">Out</th>
                            <th style="width: 10%">Last</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        // Setup Datatable
        var role     = "{{session('role')->name}}";
        var ajaxUrl  = "{{ url('inventory/report/stock-product/dataTables') }}";
        var ajaxData = {"option":"summary"};
        var columns  = [{ data: 'product_code' }, { data: 'product_name' }, {data: 'total_stock_in'}, {data: 'total_stock_out'}, { data: 'final_stock' }];
        var columnDefs  =  [];
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
            $('#flatpickr-range').flatpickr({
                mode: 'range'
            });

            requestSelectAjax({
                'url' : '{{ url("master/product/select") }}',
                'data': [],
                'optionType' : 'product',
                'type': 'GET'
            });
            requestSelectAjax({
                'url' : '{{ url("master/warehouse/select") }}',
                'data': [],
                'optionType' : 'warehouse',
                'type': 'GET'
            });
            // requestSelectAjax({
            //     'url' : '{{ url("master/product/select") }}',
            //     'data': [],
            //     'optionType' : 'product',
            //     'type': 'GET'
            // });

            initializeDataTable(ajaxUrl, ajaxData, columns, columnDefs, buttons);

            // // Delete Record
            // $('.datatables tbody').on('click', '.delete-record', function() {
            //     var selectedRow = $(this).closest('tr');
            //     var rowData = $('.datatables').DataTable().row(selectedRow).data();
            //     $('#confirmDelete').attr('action', '{{url("inventory/material-request/delete")}}/'+rowData.id);
            //     $('#modalCenter').modal('toggle');
            // });
            // $('.datatables tbody').on('click', '.download-record', function(){
            //     var selectedRow = $(this).closest('tr');
            //     var rowData = $('.datatables').DataTable().row(selectedRow).data();
            //     rowData = rowData.id
            //     $.ajax({
            //         url: '{{ url("inventory/material-request/print") }}/' + rowData,
            //         type: 'GET',
            //         xhrFields: {
            //             responseType: 'blob' // Mengindikasikan bahwa respons adalah binary data (blob)
            //         },
            //         success: function(response) {
            //             // Membuat objek URL untuk file blob
            //             var url = window.URL.createObjectURL(new Blob([response]));

            //             // Membuat elemen anchor untuk menautkan ke objek URL
            //             var a = document.createElement('a');
            //             a.href = url;
            //             a.download = 'document.pdf'; // Nama file yang akan diunduh
            //             document.body.appendChild(a);

            //             // Mengklik elemen anchor secara otomatis untuk memulai unduhan
            //             a.click();

            //             // Menghapus elemen anchor setelah selesai
            //             window.URL.revokeObjectURL(url);
            //             document.body.removeChild(a);
            //         },
            //         error: function(xhr, status, error) {
            //             console.error("Request failed: " + error);
            //         }
            //     });
            // });
            $('#handleReset').click(function() {
                $('#flatpickr-range').val('');
                $('#filterWarehouse').val('').trigger('change');
                $('#filterSectionWH').val('').trigger('change');
                $('#filterproductname').val('').trigger('change');
                ajaxData = {'option':'summary'};
                initializeDataTable(ajaxUrl, ajaxData, columns, columnDefs, buttons);
            });
            $('#handleFilter').click(function(){
                var dt  = "";
                var wh  = "";
                var whs = "";
                var pro = "";
                
                if($('#flatpickr-range').val() != ""){
                    dt = $('#flatpickr-range').val();
                    $('#report_date').text(dt);
                }
                
                if($('#filterWarehouse').val() != ""){
                    wh = $('#filterWarehouse').val();
                    $('#warehouse').html('<br> Warehouse :' + $('#filterWarehouse option:selected').text());
                }
                
                if($('#filterSectionWH').val() != ""){
                    whs = $('#filterstatus').val();
                }
                
                if($('#filterproductname').val() != ""){
                    pro = $('#filterproductname').val();
                }

                if((dt == "")&&(wh == "")&&(whs == "")&&(pro == "")){
                    toasMassage({status:false, message:'Opps, please fill out some form!'});
                    return false;
                }

                ajaxData = {'option':'summary', 'date':dt, 'warehouse_id':wh, 'section_warehouse_id': whs, 'product_id':pro};

                initializeDataTable(ajaxUrl, ajaxData, columns, columnDefs, buttons);
            });
        });

        function setDataSelect(optionType, response) {
            var id = "";
            if (optionType == 'product') {
                id = "#filterproductname";
            }
            else if (optionType == "warehouse"){
                id = "#filterWarehouse";
            }
            else if (optionType == "warehouse_section"){
                id = "#filterSectionWH";
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
