@extends('adminlte::page')
@section('content')
@section('plugins.Datatables', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('list.contract.specificPurpose') }}">Lista de Contratos</a></li>
                    <li class="breadcrumb-item active">Despesas Previstas</li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Despesas Previstas</h3>
                    </div>
                    <div style="padding: 2%" class="card">
                        <div class="row">
                            <div class="col">
                                <h3 class="card-title"><b>Fornecedor:
                                    </b>{{ $contractSpecificPurpose['suppliers']->fantasy_name }}</h3>
                            </div>
                            <div class="col">
                                <h3 class="card-title"><b>ID: </b>{{ $contractSpecificPurpose->id }}</h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <h3 class="card-title"><b>Centro de Custo:
                                    </b>{{ $contractSpecificPurpose['chart_of_accounts']->name }}</h3>
                            </div>
                            <div class="col">
                                <h3 class="card-title"><b>Plano de Contas:
                                    </b>{{ $contractSpecificPurpose['cost_centers']->name }}</h3>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col">
                                    <a type="button"
                                        href="{{ route('create.contract.specific_purpose.instament', ['id' => $contractSpecificPurpose->id]) }}"
                                        data-toggle="tooltip" data-placement="top" title="Adicionar Despesas Previstas"
                                        class="btn btn-secondary btn-sm"><i class="fas fa-plus-square"></i> Adicionar
                                        Despesas Previstas
                                    </a>
                                </div>
                            </div>
                        </div>
                        @if (session('delete'))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <h5><i class="icon fas fa-check"></i> Sucesso!</h5>
                                Registro removido com sucesso.
                            </div>
                        @endif

                        <table name="DataTableUser" id="DataTableUser" class="table table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Data Prevista</th>
                                    <th>Forma de Pagamento</th>
                                    <th>Valor Previsto</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($instalments as $item)
                                    <tr>
                                        <td>
                                            @if ($item->status_id == 0)
                                                {{ 'Previsto' }}
                                            @endif
                                        </td>
                                        <td>{{ (new DateTime($item->planned_date))->format('d/m/Y') }}</td>
                                        <td>{{ $item['payment_methods']->description }}</td>
                                        <td>{{ 'R$ ' . number_format($item->monthly_value, 2, ',', '.') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('edit.contract.specific_purpose.instament', ['contract_id' => $contractSpecificPurpose->id, 'id' => $item->id]) }}"
                                                    class="btn btn-default" data-toggle="tooltip" data-placement="top"
                                                    title="Editar"><i class="far fa-edit"></i></a>
                                                <a href="{{ route('create.contract.specific_purpose.instament.commitment', ['contract_id' => $contractSpecificPurpose->id,'id' => $item->id]) }}"
                                                    class="btn btn-default" data-toggle="tooltip" data-placement="top"
                                                    title="Compromissar"><i class="fa fa-check" aria-hidden="true"></i></a>
                                                <a class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="top"
                                                    title="Excluir"
                                                    href="{{ route('delete.contract.specific_purpose.installments', ['id' => $item->id, 'contract_id' => $contractSpecificPurpose->id]) }}"><i
                                                        class="fas fa-trash"></i></a>
                                                <a href="#" data-toggle="tooltip" data-placement="top"
                                                    title="Atualizando em {{ (new DateTime($item->created_at))->format('d/m/Y H:i:s') }}"
                                                    class="btn btn-default"><i class="fa fa-info"
                                                        aria-hidden="true"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>

        </div>
    </div>

@endsection
