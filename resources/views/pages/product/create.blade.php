@extends('layout.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-4">
            <div class="col-12">
                <div id="alert"></div>

                <div class="card">
                    <h5 class="card-header border-bottom mb-3">{{ $title }}</h5>
                    <form id="form" action="{{ url('master/product/store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body row g-3">
                            <!-- first column -->
                            <div class="col-12 col-lg-3 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="user-avatar-section">
                                            <div class="d-flex align-items-center flex-column">
                                                <label>Pict. Product</label>
                                                <img class="img-fluid rounded mb-3 mt-3" id="tempImage" src="{{ url('template/assets/img/noimage.png') }}" height="250" width="250">
                                                <input type="file" id="media" name="media" class="d-none"/>
                                                <div class="user-info text-center">
                                                    <button class="btn btn-primary btn-sm" type="button" id="changeImage">Change</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Second column -->
                            <div class="col-12 col-lg-9 mb-3">
                                <div class="mb-3">
                                    <label class="form-label" for="product_category_id">Product Category</label>
                                    <select id="product_category_id" name="product_category_id" class="form-select select2" data-allow-clear="true" required>
                                        <option value="">Select Value</option>
                                    </select>
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label class="form-label" for="sku">SKU</label>
                                        <input type="text" class="form-control" id="sku" placeholder="SKU" name="sku" required>
                                    </div>
                                    <div class="col">
                                        <label class="form-label" for="code">Code</label>
                                        <input type="text" class="form-control" id="code" name="code" data-allow-clear="true" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" data-allow-clear="true" required>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label class="form-label" for="unit_id">Unit</label>
                                        <select id="unit_id" name="unit_id" class="form-select select2" data-allow-clear="true" required>
                                            <option value="">Select Value</option>
                                        </select>
                                    </div>

                                    <div class="col">
                                        <label class="form-label" for="is_inventory">In Stock</label>
                                        <select id="is_inventory" name="is_inventory" class="form-select select2" data-allow-clear="true" required>
                                            <option value="">Select Value</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="">
                                    <div class="divider">
                                        <div class="divider-text">Additional</div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label class="form-label" for="dimension">Dimensions</label>
                                        <input type="text" class="form-control" id="dimension" name="dimension">
                                    </div>
                                    <div class="col">
                                        <label class="form-label" for="part_number">Part Number</label>
                                        <input type="text" class="form-control" id="part_number" name="part_number">
                                    </div>
                                    <div class="col">
                                        <label class="form-label" for="machine_id">For Machine</label>
                                        <select id="machine_id" name="machine_id[]" class="form-select select2" data-allow-clear="true" multiple>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="spesification">Spesification</label>
                                    <textarea class="form-control" id="spesification" name="spesification" rows="3"></textarea>
                                </div>
                                <div class="">
                                    <div class="divider">
                                        <div class="divider-text">Additional</div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="status">Status</label>
                                    <select id="status" name="status" class="form-select select2" data-allow-clear="true" required>
                                        <option value="">Select Value</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer border-top py-3">
                            <a href="{{ url('master/product') }}" class="btn btn-secondary btn-sm">Cancel</a>
                            <button type="submit" name="submitButton" class="btn btn-primary btn-sm">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            requestSelectAjax({
                'url' : '{{ url("setting/general/select?type=product_category_id") }}',
                'data': [],
                'optionType' : 'product_category',
                'type': 'GET'
            });
            requestSelectAjax({
                'url' : '{{ url("setting/general/select?type=unit_id") }}',
                'data': [],
                'optionType' : 'unit',
                'type': 'GET'
            });
            requestSelectAjax({
                'url' : '{{ url("setting/general/select?type=machine_id") }}',
                'data': [],
                'optionType' : 'machine',
                'type': 'GET'
            });

            $('#changeImage').click(function(){
                $('#media').click();
            });
            $('#media').change(function() {
                filePreview(this);
            });

        })

        function setDataSelect(optionType, response){
            var id = "";
            if(optionType == 'product_category'){
                id = "#product_category_id";
            }
            else if(optionType == 'unit'){
                id = "#unit_id";
            }
            else if(optionType == 'machine'){
                id = "#machine_id";
            }

            $.each(response.results, function(index, data) {
                $(id).append('<option value="' + data.id + '">' + data.name + '</option>');
            });
        }
    </script>
@endsection
