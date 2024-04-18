@extends('layout.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-4">
            <div class="col-12">
                <div id="alert"></div>

                <div class="card">
                    <h5 class="card-header border-bottom mb-3">{{ $title }}</h5>
                    <form id="form" action="{{ url('master/vendor/update').'/'.$data->id }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body row g-3">
                            <!-- first column -->
                            <div class="col-12 col-lg-3 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="user-avatar-section">
                                            <div class="d-flex align-items-center flex-column">
                                                <label>Pict. Vendor</label>
                                                @if ($data->photo != null)
                                                <img class="img-fluid rounded mb-3 mt-3" id="tempImage" src="{{ url($data->photo) }}" height="250" width="250">
                                                @else
                                                <img class="img-fluid rounded mb-3 mt-3" id="tempImage" src="{{ url('template/assets/img/noimage.png') }}" height="250" width="250">
                                                @endif
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
                                        <input type="text" class="form-control" id="code" placeholder="Code" name="code" value="{{ $data->code }}" required>
                                    </div>
                                    <div class="col">
                                        <label class="form-label" for="tax">TAX</label>
                                        <select id="tax" name="tax" class="form-select select2" data-allow-clear="true" required>
                                            <option value="">Select Value</option>
                                            @php
                                                $tax  = ['yes','no']; 
                                            @endphp
                                            @for($i=0; $i<count($tax); $i++)
                                                @if($tax[$i] == $data->tax)
                                                <option value="{{ $tax[$i] }}" selected>{{ ucfirst($tax[$i]) }}</option>
                                                @else
                                                <option value="{{ $tax[$i] }}">{{ ucfirst($tax[$i]) }}</option>
                                                @endif
                                            @endFor
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ $data->name }}" data-allow-clear="true" required>
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label class="form-label" for="npwp">NPWP</label>
                                        <input type="number" class="form-control" id="npwp" name="npwp" value="{{ $data->npwp }}">
                                    </div>
                                    <div class="col">
                                        <label class="form-label" for="payment_terms_id">Payment Terms</label>
                                        <select id="payment_terms_id" name="payment_terms_id" class="form-select select2" data-allow-clear="true" required>
                                            <option value="">Select Value</option>
                                        </select>
                                    </div>    
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="email">Email</label>
                                    <input type="text" class="form-control" id="email" name="email" value="{{ $data->email }}" data-allow-clear="true" required>
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label class="form-label" for="contact_person">Contact Peson</label>
                                        <input type="text" class="form-control" id="contact_person" name="contact_person" value="{{ $data->contact_person }}" data-allow-clear="true" required>
                                    </div>

                                    <div class="col">
                                        <label class="form-label" for="contact_person_number">Contact Peson Number</label>
                                        <input type="text" class="form-control" id="contact_person_number" name="contact_person_number" value="{{ $data->contact_person_number }}" data-allow-clear="true" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="address">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3">{{ $data->address }}</textarea>
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
                                        <label class="form-label" for="bank_accound_number">Bank Account Number</label>
                                        <input type="number" class="form-control" id="bank_accound_number" name="bank_accound_number">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="description">Note</label>
                                    <textarea class="form-control" id="description" name="description" rows="3">{{ $data->description }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="status">Status</label>
                                    <select id="status" name="status" class="form-select select2" data-allow-clear="true" required>
                                        <option value="">Select Value</option>
                                        @php
                                            $status  = ['active','inactive']; 
                                        @endphp
                                        @for($i=0; $i<count($status); $i++)
                                            @if($status[$i] == $data->status)
                                            <option value="{{ $status[$i] }}" selected>{{ ucfirst($status[$i]) }}</option>
                                            @else
                                            <option value="{{ $status[$i] }}">{{ ucfirst($status[$i]) }}</option>
                                            @endif
                                        @endFor
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
        var existingPaymentTermsId  = <?php echo json_encode($data->payment_terms_id); ?>;
        var existingPaymentMethodtId= <?php echo json_encode($data->payment_method_id); ?>;
        var existingBankAccountId   = <?php echo json_encode($data->bank_account_id); ?>;

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
            var existingId = "";
            if(optionType == 'payment_method'){
                id = "#payment_method_id";
                existingId = existingPaymentMethodtId;
            }
            else if(optionType == 'bank_account'){
                id = "#bank_account_id";
                existingId = existingBankAccountId;
            }
            else if(optionType == 'payment_terms'){
                id = "#payment_terms_id";
                existingId = existingPaymentTermsId;
            }

            $.each(response.results, function(index, data) {
                var option = '<option value="' + data.id + '">' + data.name + '</option>';
                if (existingId && existingId == data.id) {
                    option = '<option value="' + data.id + '" selected>' + data.name + '</option>';
                }
                $(id).append(option);
            });
        }
    </script>
@endsection
