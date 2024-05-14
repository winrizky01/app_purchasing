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
                <td width="30%" rowspan="4" style="text-transform: uppercase; text-align:center; border:1px solid;">
                    <img src="{{ public_path('template/assets/img/meepo.png') }}" class="img-fluid" />
                </td>
                <td colspan="2" style="text-transform: uppercase; text-align:center; border:1px solid;"><strong>{{$data["title"]}} ({{$data["data"]->type_adjustment->name}})</strong></td>
            </tr>
            <tr>
                <td style="border:1px solid;">No</td>
                <td style="text-transform: uppercase; text-align:center; border:1px solid;">{{ $data["data"]->code }}</td>
            </tr>
            <tr>
                <td style="border:1px solid;">Tanggal</td>
                <td style="text-transform: uppercase; text-align:center; border:1px solid;">{{ $data["data"]->date }}</td>
            </tr>
            <tr>
                <td style="border:1px solid;">Lokasi</td>
                <td style="text-transform: uppercase; text-align:center; border:1px solid;">{{ $data["data"]->warehouse->name }}</td>
            </tr>
        </thead>
    </table>

    <table style="width: 100%; border:3px; font-size:12pt; margin-bottom:15px">
        <thead>
            <th style="text-align:center; border:1px solid; width:3%">No</th>
            <th style="text-align:center; border:1px solid;">Kode Barang</th>
            <th style="text-align:center; border:1px solid; width:10%">Nama Barang</th>
            <th style="text-align:center; border:1px solid; width:3%">Qty</th>
            <th style="text-align:center; border:1px solid; width:3%">Unit</th>
            <th style="text-align:center; border:1px solid;">Note</th>
        </thead>
        <tbody>
            @php
                $i = 1;
            @endphp
            @foreach ($data["data"]->detail as $item)
            <tr>
                <td style="text-align:center; border:1px solid;">{{ $i }}</td>
                <td style="text-align:center; border:1px solid;">{{ $item->product->code }}</td>
                <td style="text-align:center; border:1px solid;">{{ $item->product->name }}</td>
                <td style="text-align:center; border:1px solid;">{{ $item->qty }}</td>
                <td style="text-align:center; border:1px solid;">{{ $item->product->product_unit->name }}</td>
                <td style="text-align:center; border:1px solid;">{{ $item->notes }}</td>
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
            </tr>
            <tr>
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
            <th style="text-align:center; border:1px solid; width:20%">Status Document</th>
            <th style="text-align:center; border:1px solid;">{{ $data["data"]->document_status->name }}</th>
        </thead>
    </table>
    <table style="width: 100%; border:3px; font-size:12pt; margin-bottom:15px">
        <thead>
            <th style="text-align:center; border:1px solid; width:20%">Description</th>
            <th style="text-align:center; border:1px solid;">{{ $data["data"]->description }}</th>
        </thead>
    </table>

    <table style="width: 100%; font-size:12pt; margin-bottom:15px; border:none">
        <tr>
            <td style="text-align:center; border:1px solid;">Diajukan Oleh</td>
            <td style="text-align:center; border:none;" colspan="2"></td>
        </tr>
        <tr>
            <td style="text-align:center; border:1px solid; height:80px"></td>
            <td style="text-align:center; border:none;"></td>
            <td style="text-align:center; border:none;"></td>
        </tr>
        <tr>
            <td style="text-align:center; border:1px solid; width:50%">
                <span>{{ $data["signature"]["created"]["name"] }}</span><br/>
                <span>{{ $data["signature"]["created"]["role"] }}</span>
            </td>
            <td style="text-align:center; border:none;"></td>
            <td style="text-align:center; border:none;"></td>
        </tr>
    </table>

</body>

</html>