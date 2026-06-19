<table>
  <thead>
    <tr>
      <th colspan="5" style="font-size: 15;"><b>{{ @$company->name }}</b></th>
    </tr>

    <tr>
      <th colspan="3"><b>Relatório de Turno</b></th>
    </tr>

    <tr>
      <th colspan="3"><b>Descrição: {{ @$name }}</b></th>
      <th colspan="2"><b>Exportação:{{ date('d/m/Y H:i:s') }}</b></th>
    </tr>

  </thead>
</table>

<table>
  <thead>
    <tr>
      <td colspan="2"></td>
    </tr>
    <tr>
      <th style="text-align: center;background: gray;color:black;font-weight:bold;font-size: 14px;"colspan="5">
       Relatório de Turno </th>
    </tr>
    
  </thead>
  <tbody>
    <tr>
      <td width="25"><b>Início:</b></td>
      <td colspan="2" width="25" style="text-align: center">{{ (new DateTime($shifit_report['shiftReport']['beginning']))->format('d/m/Y H:i:s') }}</td>
      <td width="25"><b>Término:</b></td>
      <td  width="25" style="text-align: center">{{ (new DateTime($shifit_report['shiftReport']['end']))->format('d/m/Y H:i:s') }}</td>
    </tr>
    <tr>
      <td width="25"><b>Supervisor:</b></td>
      <td colspan="2" width="25" style="text-align: center">{{ @$shifit_report['shiftReport']['supervisor'] }}</td>
      <td width="25"><b>Retorno de clientes:</b></td>
      <td  width="25" style="text-align: center">{{ @$shifit_report['shiftReport']['return_of_customers'] }}</td>
    </tr>
    <tr>
      <td width="25"><b>Quantidade de Entrada:</b></td>
      <td colspan="2" width="25" style="text-align: center">{{ @$shifit_report['shiftReport']['inputQuantity'] }}</td>
      <td width="25"><b>Quantidade de Saída:</b></td>
      <td  width="25" style="text-align: center">{{ @$shifit_report['shiftReport']['outputQuantity'] }}</td>
    </tr>
  </tbody>
  <tfoot>
    <tr>
      <td style="background: gray;" colspan="5"></td>
    </tr>
    
  </tfoot>

</table>




<table>
  <thead>
    <tr>
      <td colspan="5"></td>
    </tr>
    <tr>
      <th style="text-align: center;background: gray;color:black;font-weight:bold;font-size: 14px;"colspan="5">
        Frequência</th>
    </tr>
    <tr>
      <th colspan="2" style="background: lightgray"><b>Funcionário</b></th>
      <th colspan="3" style="background: lightgray"><b>Função</b></th>
    </tr>
  </thead>
  <tbody>
    @foreach ($shifit_report['frequency'] as $frequency)
      <tr>
        <td colspan="2">{{ @$frequency['employee'] }}</td>
        <td colspan="2">{{ @$frequency['func']['name'] }}</td>
      </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td style="background: gray;" colspan="5"></td>
    </tr>
    
  </tfoot>
</table>

<table>
  <thead>
    <tr>
      <td colspan="5"></td>
    </tr>
    <tr>
      <th style="text-align: center;background: gray;color:black;font-weight:bold;font-size: 14px;"colspan="5">Extra
      </th>
    </tr>
    <tr>
      <th colspan="2" style="background: lightgray"><b>Mão de obra extra</b></th>
      <th colspan="3" style="background: lightgray"><b>Motivo</b></th>
    </tr>
  </thead>
  <tbody>
    @foreach ($shifit_report['extra'] as $extra)
      <tr>
        <td colspan="2">{{ @$extra['extrawork'] }}</td>
        <td colspan="2">{{ @$extra['reasons'] }}</td>
      </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td style="background: gray;" colspan="5"></td>
    </tr>
    
  </tfoot>
</table>


<table>
  <thead>
    <tr>
      <td colspan="5"></td>
    </tr>
    <tr>
      <th style="text-align: center;background: gray;color:black;font-weight:bold;font-size: 14px;"colspan="5">
        Manutenção</th>
    </tr>
    <tr>
      <th style="background: lightgray"><b>Local</b></th>
      <th style="background: lightgray"><b>Status</b></th>
      <th style="background: lightgray"><b>Motivo</b></th>
      <th style="background: lightgray"><b>Providência</b></th>
      <th style="background: lightgray"><b>Registro</b></th>
    </tr>
  </thead>
  <tbody>
    @foreach ($shifit_report['maintenence'] as $maintenence)
      <tr>
        <td>{{ @$maintenence['local']['id'] }} - {{ @$maintenence['local']['name'] }}</td>
        <td>{{ @$maintenence['status'] }}</td>
        <td>{{ @$maintenence['reason'] }}</td>
        <td>{{ @$maintenence['providence'] }}</td>
        <td style="text-align: center">{{ @$maintenence['occurrences_id'] }}</td>
      </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td style="background: gray;" colspan="5"></td>
    </tr>
    
  </tfoot>
</table>

<table>
  <thead>
    <tr>
      <td colspan="5"></td>
    </tr>
    <tr>
      <th style="text-align: center;background: gray;color:black;font-weight:bold;font-size: 14px;"colspan="5">
        Reclamação do cliente</th>
    </tr>
    <tr>
      <th colspan="2" style="background: lightgray"><b>Reclamação do cliente</b></th>
      <th colspan="2" style="background: lightgray"><b>Providências</b></th>
      <th style="background: lightgray"><b>Registro</b></th>
    </tr>
  </thead>
  <tbody>
    @foreach ($shifit_report['customer_comp'] as $customer_comp)
      <tr>
        <td colspan="2">{{ @$customer_comp['problem'] }}</td>
        <td colspan="2">{{ @$customer_comp['providence'] }}</td>
        <td style="text-align: center">{{ @$customer_comp['occurrences_id'] }}</td>
      </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td style="background: gray;" colspan="5"></td>
    </tr>
    
  </tfoot>
</table>

<table>
  <thead>
    <tr>
      <td colspan="5"></td>
    </tr>
    <tr>
      <th style="text-align: center;background: gray;color:black;font-weight:bold;font-size: 14px;"colspan="5">
        Reclamação do cliente</th>
    </tr>
    <tr>
      <th colspan="4" style="background: lightgray"><b>Registros</b></th>
      <th style="background: lightgray"><b>Registro</b></th>
    </tr>
  </thead>
  <tbody>
    @foreach ($shifit_report['comments'] as $comments)
      <tr>
        <td colspan="4">{{ @$comments['comments'] }}</td>
        <td style="text-align: center">{{ @$comments['occurrences_id'] }}</td>
      </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td style="background: gray;" colspan="5"></td>
    </tr>
    
  </tfoot>
</table>
