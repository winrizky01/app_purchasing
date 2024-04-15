@extends('layout.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-4">
            <div class="col-12">
                <div id="alert"></div>

                <div class="card">
                    <h5 class="card-header">{{ $title }}</h5>
                    <div class="card-body">
                        <form id="form" action="{{ url('setting/role/update').'/'.$data->id }}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-md-6">
                                <label class="form-label" for="name">Role Name</label>
                                <input type="text" id="name" class="form-control" placeholder="John Doe"
                                    name="name" value="{{ $data->name }}" required />
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="status">Status</label>
                                <select id="status" name="status" class="form-select select2" data-allow-clear="true"
                                    required>
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

                            <div class="col-md-12">
                                <div class="divider">
                                    <div class="divider-text">Role Detail</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header" style="border-bottom: 1px solid #434968">Menu Parent</div>
                                    <div class="card-body mt-3" style="overflow-y:scroll; height:350px; max-height:400px;"
                                        id="parent"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header" style="border-bottom: 1px solid #434968">Menu Children</div>
                                    <div class="card-body mt-3" style="overflow-y:scroll; height:350px; max-height:400px;"
                                        id="children"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header" style="border-bottom: 1px solid #434968">Menu Sub Children</div>
                                    <div class="card-body mt-3" style="overflow-y:scroll; height:350px; max-height:400px;"
                                        id="subChildren"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary btn-sm" type="button" id="addRole">Add Role</button>
                            </div>
                            <div class="col-md-12">
                                <div class="divider">
                                    <div class="divider-text">List Role Detail</div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <table class="table" id="listRoleDetail">
                                    <thead>
                                        <th>Parent</th>
                                        <th>Children</th>
                                        <th>Sub Children</th>
                                        <th>Action</th>
                                    </thead>
                                    <tbody>    
                                        @foreach ($data->detail as $dtl)
                                            <tr id="{{ $dtl->parent->id }} - {{ count($dtl->children) > 0 ? $dtl->children[0]['id'] : "" }} - {{ count($dtl->subchildren) > 0 ? $dtl->subchildren[0]['id'] : "" }}">
                                                <input type="hidden" name="role_detail[]" value="{{ $dtl->parent->id }} - {{ count($dtl->children) > 0 ? $dtl->children[0]['id'] : "" }} - {{ count($dtl->subchildren) > 0 ? $dtl->subchildren[0]['id'] : "" }}"/>
                                                <td>{{$dtl->parent->displayname}}</td>
                                                <td>{{count($dtl->children) > 0 ? $dtl->children[0]['displayname'] : ""}}</td>
                                                <td>{{count($dtl->subchildren) > 0 ? $dtl->subchildren[0]['displayname'] : ""}}</td>
                                                <td><button type="button" class="btn btn-danger btn-sm deleteListRole"><i class="fa fa-trash"></i></button></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <div class="divider">
                                    <div class="divider-text">List Role Detail</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <a href="{{ url('setting/role') }}" class="btn btn-secondary btn-sm">Cancel</a>
                                <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /FormValidation -->
        </div>
    </div>

    <script type="text/javascript">
        var modeMenu; // global variable mode menu
        var parent      = "";
        var parent_id   = "";
        var children    = "";
        var children_id = "";
        var subChildren = "";
        var subChildren_id = "";

        $(document).ready(function() {
            modeMenu = 'parent';

            // init menu
            ajaxRequest({
                'url' : '{{ url("setting/menu/parent/select") }}',
                'data': [],
                'type': 'GET',
            });
            $('body').on('click', '.parent', function() {
                var id = $(this).val();
                modeMenu = 'children';

                children        = '';
                children_id     = '';
                subChildren     = '';
                subChildren_id  = '';
                $('#children').empty();

                var param = {
                    'url' :'{{url("setting/menu/children/select?parent_id=")}}'+id, 
                    'data': [], 
                    'type': 'GET'
                };
                var response = ajaxRequest(param);
            });
            $('body').on('click', '.children', function() {
                var id = $(this).val();
                modeMenu = 'subchildren';

                subChildren     = '';
                subChildren_id  = '';
                $('#subChildren').empty();

                var param = {
                    'url' :'{{url("setting/menu/subchildren/select?children_id=")}}'+id, 
                    'data': [], 
                    'type': 'GET'
                };
                var response = ajaxRequest(param);
            });
            // role

            $('#addRole').click(function(){
                $('.parent').each(function(){
                    if($(this).is(':checked')){
                        parent = $('label[for='+$(this).attr('id')+']').text();
                        parent_id = $(this).val();
                    }
                });
                $('.children').each(function(){
                    if($(this).is(':checked')){
                        children = $('label[for='+$(this).attr('id')+']').text();
                        children_id = $(this).val();
                    }
                });
                $('.subChildren').each(function(){
                    if($(this).is(':checked')){
                        subChildren = $('label[for='+$(this).attr('id')+']').text();
                        subChildren_id = $(this).val();
                    }
                });

                if(parent == ""){
                    toasMassage({'status':false, 'message':'Opps, please choose menu parent!'})
                    return false;
                }

                if(children == ""){
                    $.ajax({
                        url : '{{url("setting/menu/children/select?parent_id=")}}'+parent_id,
                        data: [],
                        type: 'Get',
                        headers: {
                            'X-CRSF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'JSON',
                        success: function(rs) {
                            if(rs.results.length > 0){
                                toasMassage({'status':false, 'message':'Opps, please choose menu children!'})
                                return false;
                            }
                            else{
                                handleAddRoleDetail();
                            }
                        },
                    });
                }
                else if(subChildren == ""){
                    checkSubChildren();
                }
                else{
                    handleAddRoleDetail();
                }
            });
            $('body').on('click', '.deleteListRole', function(){
                $(this).closest('tr').remove();
            });

            $('form').submit(function(event) {
                alert();
            })
        })

        function checkSubChildren(){
            $.ajax({
                url : '{{url("setting/menu/subchildren/select?children_id=")}}'+children_id,
                data: [],
                type: 'Get',
                headers: {
                    'X-CRSF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'JSON',
                success: function(rs) {
                    if(rs.results.length > 0){
                        toasMassage({'status':false, 'message':'Opps, please choose menu sub children!'})
                        return false;
                    }
                    else{
                        handleAddRoleDetail();
                    }
                },
            });
        }

        function handleAddRoleDetail(){
            var tempAccess = parent_id + '-' + children_id + '-' + subChildren_id;

            var e = 0;
            $('#listRoleDetail tbody tr').each(function(){
                if($(this).attr('id') == tempAccess){
                    e++;
                }
            });

            if(e > 0){
                toasMassage({status:false, message:'Opps, role detail already exist!'});
                return false;
            }
            else{
                var html = '<tr id="'+tempAccess+'">'+
                        '<input type="hidden" name="role_detail[]" value="'+tempAccess+'">'+
                        '<td style="text-transform: capitalize">'+parent+'</td>'+
                        '<td style="text-transform: capitalize">'+children+'</td>'+
                        '<td style="text-transform: capitalize">'+subChildren+'</td>'+
                        '<td><button type="button" class="btn btn-danger btn-sm deleteListRole"><i class="fa fa-trash"></i></button></td>'+
                    '<tr>';

                $("#listRoleDetail tbody").append(html);
            }
        }

        function setData(data) {
            var dt = data.results
            for (var i = 0; i < dt.length; i++) {
                var html = '' +
                    '<div class="form-check custom-option custom-option-basic mb-2">' +
                        '<label class="form-check-label custom-option-content" style="padding:0.5em; padding-left:2.77em" for="' + dt[i].name + '">' +
                            '<input name="'+modeMenu+'" class="form-check-input '+modeMenu+'" type="radio" value="' + dt[i].id +'" id="' + dt[i].name + '">' +
                            '<span class="custom-option-header"><span class="h6 mb-0" style="text-transform:capitalize">' + dt[i].name + '</span></span>' +
                        '</label>' +
                    '</div>';

                $("#"+modeMenu).append(html);
            }
        }

        function ajaxRequest(param) {
            $.ajax({
                // beforeSend : function(){
                //     $('#overlay').css('display','flex');
                // },
                url: param['url'],
                data: param['data'],
                type: param['type'],
                headers: {
                    'X-CRSF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'JSON',
                success: function(rs) {
                    setData(rs);
                },
                // complete: function(){
                //     $('#overlay').css('display','none');
                // }
            });
        }
    </script>
@endsection
