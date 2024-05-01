@extends('layout.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">{{ $title }}</span>
        </h4>

        <div class="row">
            <div class="col-md mb-4 mb-md-2">
                <div class="accordion mt-3" id="accordion">

                    @php
                        $i=1;
                    @endphp
                    @foreach ($data as $item)
                    <div class="card accordion-item">
                        <h2 class="accordion-header" id="heading{{$i}}">
                            <button type="button" 
                                    class="accordion-button collapsed" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#collapse{{$i}}" 
                                    aria-expanded="false" 
                                    aria-controls="collapse{{$i}}">
                                    Revision {{ $i }} By
                                {{ $item->revisiedBy->name }} At {{ date("d-m-Y H:i:s", strtotime($item->revisied_at)) }}
                            </button>
                        </h2>
                        <div id="collapse{{$i}}" class="accordion-collapse collapse" aria-labelledby="heading{{$i}}" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-md-6 mb-2">Material Request Type : </div>
                                    <div class="col-md-6 mb-2">Remark : {{ $item["remark"]["name"] }}</div>
                                    <div class="col-md-6 mb-2">Document Photo : </div>
                                    <div class="col-md-6 mb-2">Document PDF : </div>
                                    <div class="col-md-6 mb-2">Justification : {{ $item["justification"] }}</div>

                                    <div class="col-md-12">
                                        <div class="divider">
                                            <div class="divider-text">Material Request Detail</div>
                                        </div>
                                    </div>
        
                                    <div class="col-md-12 mb-2">
                                        <table class="table">
                                            <thead>
                                                <th style="text-align: center;">Category</th>
                                                <th style="text-align: center;">Product Name</th>
                                                <th style="text-align: center;">Unit</th>
                                                <th style="text-align: center;">Qty</th>
                                                <th style="text-align: center;">Notes</th>
                                            </thead>
                                            <tbody>
                                                @foreach ($item["detail"] as $dt)
                                                    <tr>
                                                        <td style="text-align: center;">{{ $dt["product"]["product_category"]["name"] }}</td>
                                                        <td style="text-align: center;">{{ $dt["product"]["name"] }}</td>
                                                        <td style="text-align: center;">{{ $dt["product"]["product_unit"]["name"] }}</td>
                                                        <td style="text-align: center;">{{ $dt["qty"] }}</td>
                                                        <td style="text-align: center;">{{ $dt["notes"] != null ? $dt["notes"] : "-" }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="divider">
                                            <div class="divider-text">Material Request Detail</div>
                                        </div>
                                    </div>
        
                                </div>
                            </div>
                        </div>
                    </div>
                    @php
                        $i++;
                    @endphp
                    @endforeach

                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-footer">
                <a href="{{ url('inventory/material-request') }}" class="btn btn-secondary btn-sm">Cancel</a>
            </div>
        </div>
@endsection
