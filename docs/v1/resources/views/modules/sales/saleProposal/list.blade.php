@extends('adminlte::page')
@section('content')
@section('plugins.Datatables', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Lista de Propostas</li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Propostas</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="form-group">
                            <div class="card-footer">
                                <a type="button" href="{{ route('create.salesproposal') }}" data-toggle="tooltip"
                                    data-placement="top" title="Novo Faturamento"
                                    class="btn bg-gradient-secondary btn-sm"><i class="fas fa-plus-square"></i> Novo</a>
                            </div>
                        </div>

                        <table name="DataTableUser" id="DataTableUser" class="table table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Cliente</th>
                                    <th>Condição</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th>Total R$</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item['clients']->nome }}</td>
                                        <td>{{ $item['payment_methods']->description }}</td>
                                        <td>{{ (new DateTime($item->created_at))->format('d/m/Y') }}</td>
                                        <td>{{ $item['status_sale_proposals']->description }}</td>
                                        <td>{{ number_format($item->total, 2, ',', '.') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                @can('checkRouters', $route = 'edit.salesproposal')
                                                    <a href="{{ route('edit.salesproposal', ['id' => $item->id]) }}"
                                                        data-toggle="tooltip" data-placement="top" title="Editar"
                                                        class="btn btn-default"><i class="far fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('checkRouters', $route = 'edit.salesproposal')
                                                    <a href="{{ route('delete.salesproposal', ['id' => $item['id']]) }}"
                                                        data-toggle="tooltip" data-placement="top" title="Excluir"
                                                        class="btn btn-default"><i class="far fa-trash-alt"></i></a>
                                                @endcan
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
