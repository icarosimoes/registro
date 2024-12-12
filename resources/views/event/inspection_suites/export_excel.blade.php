<table>
  <thead>
      <tr>
          <th colspan="5"  style="font-size: 15;"><b>{{ @$company->name }}</b></th>
      </tr>

      <tr>
          <th colspan="3" ><b>Relatório Inspeção de Suites</b></th>
      </tr>

      <tr>
          <th colspan="3" ><b>Descrição: {{$name}}</b></th>
          <th colspan="2" ><b>Exportação:{{ date('d/m/Y H:i:s') }}</b></th>
      </tr>

  </thead>
</table>


<table>
  <thead>
    <tr >
      <th style="text-align: center;background: lightgray"><b>ID</b></th>
      <th style="text-align: center;background: lightgray"><b>DATA</b></th>
      <th style="text-align: center;background: lightgray"><b>SUITE</b></th>
      <th style="text-align: center;background: lightgray"><b>INSPECIONADO POR</b></th>
      <th style="text-align: center;background: lightgray"><b>CAMAREIRA</b></th>
    </tr>
  </thead>
  <tbody>
    @foreach ($inspectionSuite as $item)
      <tr>
        {{-- @foreach ($roles as $role) --}}
        <td width="10" style="text-align: left">{{ $item->id }}</td>
        <td width="20">{{ date('d/m/Y', strtotime($item->date)) }}</td>
        <td width="20">{{ @$item->local->name }}</td>
        <td width="30">{{ @$item->user->name }}</td>
        <td width="30">{{ @$item->maid }}</td>
      </tr>
    @endforeach
  </tbody>
</table>
