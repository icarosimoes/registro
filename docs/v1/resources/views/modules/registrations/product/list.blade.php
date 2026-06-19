@extends('adminlte::page')
@section('content')
@section('plugins.Datatables', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active"><i class="fas fa-file-alt"></i> <a
                            href="{{ route('list.group.product') }}">Grupo de Produtos</a></li>
                    <li class="breadcrumb-item active"><i class="fas fa-file-alt"></i> Lista de Produtos</li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Adicionar Produtos</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-2">
                                    <a type="button" href="{{ route('new.product') }}" data-toggle="tooltip"
                                        data-placement="top" title="Adicionar Produto"
                                        class="btn bg-gradient-secondary btn-sm"><i class="fas fa-plus-square"></i>
                                        Novo</a>
                                </div>
                            </div>
                        </div>

                        <table name="DataTableUser" id="DataTableUser" class="table table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Descrição</th>
                                    <th>Unidade</th>
                                    <th>Preço de Venda</th>
                                    <th>Custo Unitário</th>
                                    <th>MC</th>
                                    <th>MC%</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td>{{ $item->code }}</td>
                                        <td>{{ $item->description }}</td>
                                        <td>{{ $item['units']->description }}</td>
                                        <td>
                                            {{ "R$ " . number_format($item->sale_price, 2, ',', '.') }}</td>
                                        <td>
                                            {{ "R$ " . number_format($item->unit_cost, 2, ',', '.') }}</td>
                                        <td>
                                            {{ "R$ " . number_format($item->contribution_margin, 2, ',', '.') }}</td>
                                        <td>
                                            {{ "R$ " . number_format($item->costs_contribution_margin_percent, 2, ',', '.') }}
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                @can('checkRouters', $route = 'edit.product')
                                                    <a href="{{ route('edit.product', ['id' => $item->id]) }}"
                                                        data-toggle="tooltip" data-placement="top" title="Editar Produto"
                                                        class="btn btn-default"><i class="far fa-edit"></i></a>
                                                @endcan
                                                @can('checkRouters', $route = 'delete.product')
                                                    <a href="{{ route('delete.product', ['id' => $item->id]) }}"
                                                        data-toggle="tooltip" data-placement="top" title="Remover Produto"
                                                        class="btn btn-default"><i class="fas fa-trash-alt"></i></a>
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
