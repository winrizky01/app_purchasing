<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>

    <title>{{$data["title"]}}</title>

    <style>
        .table-responsive-with-width {
            width: 100%;
            overflow-x: auto;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            white-space: nowrap;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body style="">
    <table id="header" style="width: 100%; border:3px; font-size:12pt; margin-bottom:15px">
        <thead>
            <tr>
                <td width="30%" rowspan="6" style="text-transform: uppercase; text-align:center; border:1px solid;">aa</td>
            </tr>
            <tr>
                <td colspan="2" style="text-transform: uppercase; text-align:center; border:1px solid;"><strong>Material Request</strong></td>
            </tr>
            <tr>
                <td style="border:1px solid;">No</td>
                <td style="text-transform: uppercase; text-align:center; border:1px solid;">{{ $data["data"]->code }}</td>
            </tr>
            <tr>
                <td style="border:1px solid;">Date / Mont / Years</td>
                <td style="text-transform: uppercase; text-align:center; border:1px solid;">{{ date("d/m/Y", strtotime($data["data"]->request_date)) }}</td>
            </tr>
            <tr>
                <td style="text-transform: uppercase; text-align:center; border:1px solid;"><strong>Dilaporkan Oleh User</strong></td>
                <td style="text-transform: uppercase; text-align:center; border:1px solid;"><strong>Department / Division</strong></td>
            </tr>
            <tr>
                <td style="text-transform: uppercase; text-align:center; border:1px solid;">{{ $data["data"]->createdBy->name }}</td>
                <td style="text-transform: uppercase; text-align:center; border:1px solid;">{{ $data["data"]->department->name }} / {{ $data["data"]->division->name }}</td>
            </tr>
        </thead>
    </table>

    <table style="width: 100%; border:3px; font-size:12pt; margin-bottom:15px">
        <thead>
            <th style="text-align:center; border:1px solid; width:3%">No</th>
            <th style="text-align:center; border:1px solid; width:10%">Category</th>
            <th style="text-align:center; border:1px solid;">Material</th>
            <th style="text-align:center; border:1px solid; width:10%">Part Number</th>
            <th style="text-align:center; border:1px solid;">Fungsi / Kegunaan</th>
            <th style="text-align:center; border:1px solid; width:3%">Qty</th>
            <th style="text-align:center; border:1px solid; width:3%">Unit</th>
            <th style="text-align:center; border:1px solid;">Note</th>
        </thead>
        <tbody>
            @php
                $i = 1;
            @endphp
            @foreach ($data["data"]->material_request_details as $item)
            <tr>
                <td style="text-align:center; border:1px solid;">{{ $i }}</td>
                <td style="text-align:center; border:1px solid;">{{ $item->product->product_category->name }}</td>
                <td style="text-align:center; border:1px solid;">{{ $item->product->name }}</td>
                <td style="text-align:center; border:1px solid;">{{ $item->product->part_number }}</td>
                <td style="text-align:center; border:1px solid;">{{ $item->product->description }}</td>
                <td style="text-align:center; border:1px solid;">{{ $item->qty }}</td>
                <td style="text-align:center; border:1px solid;">{{ $item->product->product_unit->name }}</td>
                <td style="text-align:center; border:1px solid;">
                    @php
                        $machine = "";
                        if($item->product->product_category->name == "Sparepart"){
                            foreach ($item->product->product_machine as $mch) {
                                $machine = $machine.$mch->machine->name.", ";
                            }
                        }
                        $machine = rtrim($machine, ", ");
                    @endphp
                    {{ $item->product->product_category->name == "Sparepart" ? "Untuk Mesin ". $machine." - " : '' }} {{$item->product->notes}}
                </td>
            </tr>
            @php
                $i++;
            @endphp
            @endforeach
            <tr>
                <td style="text-align:center; border:1px solid; height:40px"></td>
                <td style="text-align:center; border:1px solid; height:40px"></td>
                <td style="text-align:center; border:1px solid; height:40px"></td>
                <td style="text-align:center; border:1px solid; height:40px"></td>
                <td style="text-align:center; border:1px solid; height:40px"></td>
                <td style="text-align:center; border:1px solid; height:40px"></td>
                <td style="text-align:center; border:1px solid; height:40px"></td>
                <td style="text-align:center; border:1px solid; height:40px"></td>
            </tr>
            <tr>
                <td style="text-align:center; border:1px solid; height:40px"></td>
                <td style="text-align:center; border:1px solid; height:40px"></td>
                <td style="text-align:center; border:1px solid; height:40px"></td>
                <td style="text-align:center; border:1px solid; height:40px"></td>
                <td style="text-align:center; border:1px solid; height:40px"></td>
                <td style="text-align:center; border:1px solid; height:40px"></td>
                <td style="text-align:center; border:1px solid; height:40px"></td>
                <td style="text-align:center; border:1px solid; height:40px"></td>
            </tr>
        </tbody>
    </table>

    <table style="width: 100%; border:3px; font-size:12pt; margin-bottom:15px">
        <thead>
            <th style="text-align:center; border:1px solid; width:20%">Justification</th>
            <th style="text-align:center; border:1px solid;">{{ $data["data"]->justification }}</th>
        </thead>
    </table>
    <table style="width: 100%; border:3px; font-size:12pt; margin-bottom:15px">
        <thead>
            <th style="text-align:center; border:1px solid; width:20%">Remark</th>
            <th style="text-align:center; border:1px solid;">{{ $data["data"]->remark->name }}</th>
        </thead>
    </table>
    <table style="width: 100%; border:3px; font-size:12pt; margin-bottom:15px">
        <thead>
            <th style="text-align:center; border:1px solid; width:20%">Status Document</th>
            <th style="text-align:center; border:1px solid;">{{ $data["data"]->document_status->name }}</th>
        </thead>
    </table>

    <table style="width: 100%; border:3px; font-size:12pt; margin-bottom:15px">
        <tr>
            <td style="text-align:center; border:1px solid;">Diajukan Oleh</td>
            <td style="text-align:center; border:1px solid;" colspan="2">Mengetahui</td>
        </tr>
        <tr>
            <td style="text-align:center; border:1px solid; height:80px"></td>
            <td style="text-align:center; border:1px solid; height:80px"></td>
            <td style="text-align:center; border:1px solid; height:80px"></td>
        </tr>
        <tr>
            <td style="text-align:center; border:1px solid;">
                <span>{{ $data["signature"]["created"]["name"] }}</span><br/>
                <span>{{ $data["signature"]["created"]["role"] }}</span>
            </td>
            <td style="text-align:center; border:1px solid;">
                <span>{{ count($data["signature"]["riviwed"]) > 0 ? $data["signature"]["riviwed"]["name"] : "" }}</span><br/>
                <span>{{ count($data["signature"]["riviwed"]) > 0 ? $data["signature"]["riviwed"]["role"] : "" }}</span><br/>
            </td>
            <td style="text-align:center; border:1px solid;">
                <span>{{ count($data["signature"]["approved"]) > 0 ? $data["signature"]["approved"]["name"] : "" }}</span><br/>
                <span>{{ count($data["signature"]["approved"]) > 0 ? $data["signature"]["approved"]["role"] : "" }}</span><br/>
            </td>
        </tr>
    </table>
</body>

</html>