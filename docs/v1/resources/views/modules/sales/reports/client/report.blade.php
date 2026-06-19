<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- CSS Code: Place this code in the document's head (between the 'head' tags) -->
    <style>
        body {
            font-family: "source_sans_proregular", Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif;
        }
        table.GeneratedTable {
            width: 100%;
            background-color: #ffffff;
            border-collapse: collapse;
            border-width: 1px;
            border-color: #c0c3c4;
            border-style: solid;
            color: #000000;
        }

        table.GeneratedTable td,
        table.GeneratedTable th {
            border-width: 1px;
            border-color: #c0c3c4;
            border-style: solid;
            padding: 3px;
        }

        table.GeneratedTable thead {
            background-color: #dcdbd6;
        }

        .bodytitle{
            text-align: center;
        }
        p{
            font-size: 11px;
            line-height: 0.5;
        }

    </style>
</head>

<body>
    <div class="container">
        <div class="col-md-12">
            <div class="row">
                <div class="col-sm-2">
                        <b>Lauro de Freitas, {{ date('d/m/Y') }}</b><br />
                        <p>A(O) MARÉ CHEIA CONSTRUTORA</p>
                        <P>TEL: 71 9 9204-2643</P>
                        <p>Email: francisco@marecheiaconstrutora.com.br</p>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <h5 class="bodytitle">RELATÓRIO ABC CLIENTES</h5>
            <table class="GeneratedTable">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Faturamento</th>
                        <th>Faturamento Acumulado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clients as $item)
                        <tr>
                            <td>{{ $item->nome }}</td>
                            <td>{{ 'R$ ' . number_format($item->total, 2, ',', '.') }}</td>
                            <td>{{ 'R$ ' . number_format($item->acumulative_billing, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
