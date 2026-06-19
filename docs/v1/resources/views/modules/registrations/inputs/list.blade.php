@extends('adminlte::page')
@section('content')
@section('plugins.Datatables', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Lista de Insumos</li>
                </ol>
            </div>
            <div class="col-md-12">
                @if (session('alert'))
                    <div class="alert alert-warning alert-dismissible fade show">
                        {{ session('alert') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-list"></i> Lista de Insumos</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="form-group">
                            <div class="card-footer">
                                <a type="button" href="{{ route('new.inputs') }}" data-toggle="tooltip" data-placement="top"
                                    title="Novo Insumo" class="btn bg-gradient-secondary btn-sm"><i
                                        class="fas fa-plus-square"></i> Novo</a>
                            </div>
                        </div>

                        <table name="DataTableUser" id="DataTableUser" class="table table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Descrição</th>
                                    <th>Unidade</th>
                                    <th>Custo Unitário</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td>{{ substr($item->code, 0, 2).'-'.substr($item->code, 2, 4) }}</td>
                                        <td>{{ $item->description }}</td>
                                        <td>{{ $item['units']->description }}</td>
                                        <td>{{ "R$ ".number_format($item->unit_cost, 2, ",", ".") }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                {{-- @can('checkRouters', $route =
                                                'view.client') --}}
                                                {{-- <a
                                                    href="{{ route('view.client', ['id' => $item['id']]) }}"
                                                    data-toggle="tooltip" data-placement="top" title="Visualizar"
                                                    class="btn btn-secondary"><i class="fas fa-eye"></i></a>
                                                --}}
                                                {{-- @endcan --}}
                                                @can('checkRouters', $route = 'edit.inputs')
                                                <a href="{{ route('edit.inputs', ['id' => $item['id']]) }}"
                                                    data-toggle="tooltip" data-placement="top" title="Editar"
                                                    class="btn btn-default"><i class="fas fa-pencil-alt"></i></a>
                                                @endcan
                                                @can('checkRouters', $route = 'delete.inputs')
                                                <a href="{{ route('delete.inputs', ['id' => $item['id']]) }}" data-toggle="tooltip" data-placement="top" title="Excluir"
                                                    class="btn btn-default"><i class="fas fa-trash"></i></a>
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
