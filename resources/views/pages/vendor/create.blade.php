@extends('layout.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-4">
            <div class="col-12">
                <div id="alert"></div>

                <div class="card">
                    <h5 class="card-header border-bottom mb-3">{{ $title }}</h5>
                    <form id="form" action="{{ url('master/vendor/store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body row g-3">
                            <!-- first column -->
                            <div class="col-12 col-lg-3 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="user-avatar-section">
                                            <div class="d-flex align-items-center flex-column">
                                                <label>Pict. Vendor</label>
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
                                <div class="row mb-3">
                                    <div class="col">
                                        <label class="form-label" for="code">Code</label>
                                        <input type="text" class="form-control" id="code" placeholder="Code" name="code" required>
                                    </div>
                                    <div class="col">
                                        <label class="form-label" for="tax">TAX</label>
                                        <select id="tax" name="tax" class="form-select select2" data-allow-clear="true" required>
                                            <option value="">Select Value</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" data-allow-clear="true" required>
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label class="form-label" for="npwp">NPWP</label>
                                        <input type="number" class="form-control" id="npwp" name="npwp">
                                    </div>
                                    <div class="col">
                                        <label class="form-label" for="payment_terms_id">Payment Terms</label>
                                        <select id="payment_terms_id" name="payment_terms_id" class="form-select select2" data-allow-clear="true" required>
                                            <option value="">Select Value</option>
                                            {{-- <option value="30">30 Hari</option>
                                            <option value="60">60 Hari</option> --}}
                                        </select>
                                    </div>    
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="email">Email</label>
                                    <input type="text" class="form-control" id="email" name="email" data-allow-clear="true" required>
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label class="form-label" for="contact_person">Contact Peson</label>
                                        <input type="text" class="form-control" id="contact_person" name="contact_person" data-allow-clear="true" required>
                                    </div>

                                    <div class="col">
                                        <label class="form-label" for="contact_person_number">Contact Peson Number</label>
                                        <input type="text" class="form-control" id="contact_person_number" name="contact_person_number" data-allow-clear="true" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="address">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label class="form-label" for="payment_method_id">Payment Method</label>
                                        <select id="payment_method_id" name="payment_method_id" class="form-select select2" data-allow-clear="true">
                                            <option value="">Select Value</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <label class="form-label" for="bank_account_id">Bank Account</label>
                                        <select id="bank_account_id" name="bank_account_id" class="form-select select2" data-allow-clear="true">
                                            <option value="">Select Value</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <label class="form-label" for="bank_account_number">Bank Account Number</label>
                                        <input type="number" class="form-control" id="bank_account_number" name="bank_account_number">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="description">Note</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
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
                            <a href="{{ url('master/vendor') }}" class="btn btn-secondary btn-sm">Cancel</a>
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
                'url' : '{{ url("setting/general/select?type=payment_terms_id") }}',
                'data': [],
                'optionType' : 'payment_terms',
                'type': 'GET'
            });
            requestSelectAjax({
                'url' : '{{ url("setting/general/select?type=payment_method_id") }}',
                'data': [],
                'optionType' : 'payment_method',
                'type': 'GET'
            });
            requestSelectAjax({
                'url' : '{{ url("setting/general/select?type=bank_account_id") }}',
                'data': [],
                'optionType' : 'bank_account',
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
            if(optionType == 'payment_method'){
                id = "#payment_method_id";
            }
            else if(optionType == 'bank_account'){
                id = "#bank_account_id";
            }
            else if(optionType == 'payment_terms'){
                id = "#payment_terms_id";
            }

            $.each(response.results, function(index, data) {
                $(id).append('<option value="' + data.id + '">' + data.name + '</option>');
            });
        }
    </script>
@endsection
