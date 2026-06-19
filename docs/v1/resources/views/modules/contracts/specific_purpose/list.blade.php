@extends('adminlte::page')
@section('content')
@section('plugins.Datatables', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Contratos para propósitos específicos</li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Contratos para propósitos específicos</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col">
                                    <a type="button" href="{{ route('create.contract.specificPurpose') }}" data-toggle="tooltip"
                                        data-placement="top" title="Novo Contrato Recorrente"
                                        class="btn btn-secondary btn-sm"><i class="fas fa-plus-square"></i> Novo</a>
                                    {{-- btn btn-block btn-secondary btn-flat
                                    --}}
                                </div>
                            </div>
                        </div>

                        {{-- Modal --}}
                        <div class="modal fade" id="modal-default">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Lista de Anexos</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form name="formContractFileDownload" id="formContractFileDownload" enctype="multipart/form-data" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Descrição</label>
                                                <input type="text" class="form-control" name="name" id="name" required>
                                                <input type="hidden" class="form-control" name="contract_specificPurpose_id" id="contract_specificPurpose_id">
                                            </div>
                                            <div class="form-group">
                                                <label>Selecione o arquivo</label>
                                                <input type="file" class="form-control" name="file" id="file" required>
                                            </div>

                                            <table class="table table-striped table-sm table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Descrição</th>
                                                        <th style="width: 2%">Download</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="bodyFile"></tbody>
                                            </table>
                                            <div class="overlay-wrapper">
                                                <div class="d-none overlay">
                                                  <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                                                  <div class="text-bold pt-2">Carregando...</div>
                                                </div>
                                            </div>
                                        </div>
                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-default" data-dismiss="modal"><i
                                                class="fas fa-sign-out-alt"></i> Fechar</button>
                                        <button type="submit" class="btn btn-secondary"><i class="fas fa-save"></i>
                                            Save</button>
                                    </div>
                                </form>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                        <!-- /.modal -->

                        <table name="DataTableUser" id="DataTableUser" class="table table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Plano de Contas</th>
                                    <th>Fornecedor</th>
                                    <th>Período</th>
                                    <th>Valor</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contracts as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item['chart_of_accounts']->name }}</td>
                                        <td>{{ $item['suppliers']->fantasy_name }}</td>
                                        <td>{{ (new DateTime($item->start_period))->format('d/m/Y') . ' á ' . (new DateTime($item->end_period))->format('d/m/Y') }}
                                        </td>
                                        <td>{{ number_format($item->price, 2, ',', '.') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                @can('checkRouters', $route = 'view.contract.specificPurpose')
                                                    <a href="{{ route('view.contract.specificPurpose', ['id' => $item->id]) }}"
                                                        class="btn btn-default" data-toggle="tooltip" data-placement="top"
                                                        title="Visualizar"><i class="fas fa-eye"></i></a>
                                                @endcan

                                                <a data-id="{{ $item->id }}" class="btn btn-default md" data-toggle="tooltip" data-placement="top"
                                                    title="Anexo"><i class="fas fa-download"></i></a>
                                                    @can('checkRouters', $route = 'edit.contract.specificPurpose')
                                                        <a href="{{ route('edit.contract.specificPurpose', ['id' => $item->id]) }}"
                                                            class="btn btn-default" data-toggle="tooltip" data-placement="top"
                                                            title="Editar Contratos Recorrentes"><i class="far fa-edit"></i></a>
                                                    @endcan
                                                    @can('checkRouters', $route = 'list.contract.specific_purpose.instament')
                                                        <a href="{{ route('list.contract.specific_purpose.instament', ['id' => $item->id]) }}"
                                                            data-toggle="tooltip" data-placement="top" title="Parcelas Previstas"
                                                            class="btn btn-default"><i class="fas fa-list"></i></a>
                                                    @endcan

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
@section('plugins.createContractSpecificPurposeFiles', true)
@endsection
