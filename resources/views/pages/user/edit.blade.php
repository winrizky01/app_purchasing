@extends('layout.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row mb-4">
        <!-- FormValidation -->
        <div class="col-12">
            <div id="alert"></div>

            <div class="card">
                <h5 class="card-header border-bottom">{{ $title }}</h5>
                <form id="form" action="{{ url('master/user/update'.'/'.$data->id) }}" method="POST">
                    @csrf
                    <div class="card-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="name">Full Name</label>
                            <input type="text" id="name" class="form-control" placeholder="John Doe" name="name" value="{{ $data->name }}" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="email">Email</label>
                            <input class="form-control" type="email" id="email" name="email" value="{{ $data->email }}" placeholder="john.doe" required />
                        </div>

                        <div class="col-md-6">
                            <div class="form-password-toggle">
                                <label class="form-label" for="password">Password</label>
                                <div class="input-group input-group-merge">
                                    <input class="form-control" type="password" id="password"
                                        name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="multicol-password2"/>
                                    <span class="input-group-text cursor-pointer" id="multicol-password2"><i
                                            class="ti ti-eye-off"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-password-toggle">
                                <label class="form-label" for="confirm_password">Confirm Password</label>
                                <div class="input-group input-group-merge">
                                    <input class="form-control" type="password" id="confirm_password"
                                        name="confirm_password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="multicol-confirm-password2"/>
                                    <span class="input-group-text cursor-pointer" id="multicol-confirm-password2"><i
                                            class="ti ti-eye-off"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="role_id">Role</label>
                            <select id="role_id" name="role_id" class="select2 form-select" data-allow-clear="true" required >
                                <option value="">Select</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="user_location_id">User Location</label>
                            <select id="user_location_id" name="user_location_id" class="select2 form-select" data-allow-clear="true" required >
                                <option value="">Select</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="department_id">Department</label>
                            <select id="department_id" name="department_id" class="select2 form-select" data-allow-clear="true" required >
                                <option value="">Select</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="division_id">Division</label>
                            <select id="division_id" name="division_id" class="select2 form-select" data-allow-clear="true" required >
                                <option value="">Select</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="status">Status</label>
                            <select id="status" name="status" class="form-select select2" data-allow-clear="true" required >
                                <option value="">Select</option>
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
                    <div class="card-footer border-top py-3">
                        <a href="{{ url('master/user') }}" class="btn btn-secondary btn-sm">Cancel</a>
                        <button type="submit" name="submitButton" class="btn btn-primary btn-sm">Submit</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /FormValidation -->
    </div>
</div>
 
<script type="text/javascript">
    var existingRoleId = <?php echo json_encode($data->role); ?>;
    var existingUserLocationId = <?php echo json_encode($data->user_location_id); ?>;
    var existingUserDepartmentId = <?php echo json_encode($data->department_id); ?>;
    var existingUserDivisionId = <?php echo json_encode($data->division_id); ?>;

    $(document).ready(function(){
        requestSelectAjax({
                'url' : '{{ url("setting/role/select") }}',
                'data': [],
                'optionType' : 'role',
                'type': 'GET'
        });
        requestSelectAjax({
            'url' : '{{ url("setting/general/select?type=user_location_id") }}',
            'data': [],
            'optionType' : 'user_location',
            'type': 'GET'
        });
        requestSelectAjax({
            'url' : '{{ url("master/department/select") }}',
            'data': [],
            'optionType' : 'department',
            'type': 'GET'
        });

        $('#department_id').change(function() {
            requestSelectAjax({
                'url' : '{{ url("master/division/select?department_id='+$(this).val()+'") }}',
                'data': [],
                'optionType' : 'division',
                'type': 'GET'
            });
        });


        $('form').submit(function(event){
            // Memeriksa apakah kedua kolom memiliki nilai yang sama
            var password = $('#password').val();
            var confirm_password = $('#confirm_password').val();
            if (password != confirm_password) {
                toasMassage({'status':false, 'message':'Password dan konfirmasi password tidak cocok'})
                event.preventDefault();
            }
        })
    })

    function setDataSelect(optionType, response){
        var id = "";
        var existingId = "";
        if(optionType == 'role'){
            id = "#role_id";
            existingId = existingRoleId;
        }
        else if(optionType == 'user_location'){
            id = "#user_location_id";
            existingId = existingUserLocationId;
        } else if (optionType == 'department') {
            id = "#department_id";
            existingId = existingUserDepartmentId;
        } else if (optionType == 'division') {
            id = "#division_id";
            existingId = existingUserDivisionId;
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