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

    .sub_title {
      background: lightgray;
      text-align: center
    }
  </style>
</head>

<body>
  <table>

    <tbody>
      <tr>
        <td class="top41" style="width:500px">
          <b>AERO</b>
        </td>
        <td class="top2d">
          <b>Exportação:</b> {{ (new DateTime())->format('d/m/Y H:i:s') }}
        </td>
      </tr>
      <tr>
        <td>Relatório de Vistorias de Suites</td>
        <td></td>
      </tr>
      <tr>
        <td><b>Descrição:</b> {{ $description }}</td>
        <td></td>
      </tr>
    </tbody>
  </table>
  <h3 class="sub_title">Data e Local</h3>
  {{-- <hr style="margin: 0px"> --}}
  <table border="1" width="100%">
    <thead>
      <tr>
        <th width='30' style="text-align: center; vertical-align: middle;">ID</th>
        <th width='50' style="text-align: center; vertical-align: middle;">DATA</th>
        <th width='50' style="text-align: center; vertical-align: middle;">SUITE</th>
        <th width='100' style="text-align: center; vertical-align: middle;">INSPECIONADO POR</th>
        <th width='15' style="text-align: center; vertical-align: middle;">CAMAREIRA</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($inspection_suite as $item)
        <tr>

          <td >{{ $item->id }}</td>
          <td style="text-align: center;">{{ date('d/m/Y', strtotime($item->date)) }}</td>
          <td style="text-align: center;">{{ @$item->local->name }}</td>
          <td >{{ @$item->user->name }}</td>
          <td>{{ @$item->maid }}</td>

        </tr>
      @endforeach
    </tbody>
  </table>

</body>

</html>
