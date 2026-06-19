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
            font-size: 12px;
        }

        .table {
            border-collapse: collapse;
            font-size: 0.6em;
            font-family: sans-serif;
            width: 100%;
            border-collapse: collapse;
            white-space: nowrap;
            border-spacing: 0;
        }

        .table th {
            font-size: 11px;
            text-align: left;
            text-transform: uppercase;
            background: #474744;
            color: #ffffff;
        }

        .table td {
            padding: 6px 12px;
            border: 1px solid #d9d7ce;
        }

        .top1 {
            padding-top: 20px;
            padding-right: 500px;
        }

        .top2 {
            padding-top: 20px;
        }

        p {
            font-size: 12px;
            line-height: 0.5;
        }
    </style>
</head>

<body>
    <table>

        <tbody>
            <tr>
                <td class="top1">
                    <b>AERO</b><br />
                    <p>Relatório de Registros</p>
                    <p><b>Descrição:</b> {{ $name }}</p>
                </td>
                <td class="top2">
                    {{-- @if ($params)
                        <b>Período: </b> {{ $params }}<br />
                        @else
                        <b>Período: </b> Indefinido<br />
                    @endif --}}

                    <p><b>Exportação:</b> {{ (new DateTime())->format('d-m-Y H:i:s') }}</p>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="table">
        <thead>
            <tr>
                <th style="text-align: center; vertical-align: middle;">ID</th>
                <th style="text-align: center; vertical-align: middle;">Titulo</th>
                <th style="text-align: center; vertical-align: middle;">Status</th>
                <th style="text-align: center; vertical-align: middle;">Local</th>
                <th style="text-align: center; vertical-align: middle;">Departamento</th>
                <th style="text-align: center; vertical-align: middle;">Prazo</th>
                <th style="text-align: center; vertical-align: middle;">Atualizado em</th>
                

            </tr>
        </thead>
        <tbody>
            @php
                $subTotal = 0;
                $count = 1;
            @endphp
            @foreach ($data as $item)
                <tr>
                    <td>{{ $item->id }}</td> 
                    <td style="width: 200px" >{{ substr(trim($item->title),0,50) }}</td>
                    <td>
                        @if ($item->status == 1)
                            <span class="badge bg-info">{{ 'Em Aberto' }}</span>
                        @elseif($item->status == 2)
                            <span class="badge bg-warning">{{ 'Em Andamento' }}</span>
                        @elseif($item->status == 3)
                            <span class="badge bg-success">{{ 'Fechado' }}</span>
                        @endif
                    </td>
                    <td>{{ @$item->local->name }}</td>
                    <td>{{ @$item->sector->name }}</td>
                    <td>{{ (new DateTime($item->deadline))->format('d/m/Y') }}</td>
                    <td>{{ (new DateTime($item->updated_at))->format('d/m/Y') }}</td>
                    @php
                        $count++;
                        $subTotal += $item->price;
                    @endphp
                </tr>
            @endforeach
            {{-- <tr>
                <td colspan="4"></td>
                <td style="text-align: center; font-size: 11px;"><b>Total:</td>
                <td style="text-align: right; font-size: 11px;"></b>{{ "R$ " . number_format($subTotal, 2, ',', '.') }}
                </td>
            </tr> --}}
        </tbody>
    </table>
</body>

</html>
