@extends('adminlte::page')
@section('content')
@section('plugins.Datatables', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Grupo de Insumos</li>
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
                        <h3 class="card-title">Grupo de Insumos</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="form-group">
                            <div class="card-footer">
                                <a type="button" href="{{ route('new.input_group') }}" data-toggle="tooltip" data-placement="top"
                                    title="Novo" class="btn bg-gradient-secondary btn-sm"><i
                                        class="fas fa-plus-square"></i> Novo</a>
                            </div>
                        </div>

                        <table name="DataTableUser" id="DataTableUser" class="table table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Descrição</th>
                                    <th style="width:  8.33%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        {{-- @foreach ($roles as $role) --}}
                                            <td>{{ substr($item->code, 0, 2) }}</td>
                                            <td>{{ $item->description }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    @can('checkRouters', $route = 'edit.input_group')
                                                      <a href="{{ route('edit.input_group',['id' => $item['id']]) }}" data-toggle="tooltip" data-placement="top" title="Editar" class="btn btn-default"><i class="fas fa-pencil-alt"></i></a>
                                                    @endcan
                                                    @can('checkRouters', $route = 'list.inputs')
                                                    <a href="{{ route('list.inputs',['id' => $item['id']]) }}" data-toggle="tooltip" data-placement="top" title="Lista de Insumos" class="btn btn-default"><i class="fas fa-list"></i></a>
                                                    @endcan
                                                    @can('checkRouters', $route = 'delete.input_group')
                                                      <a href="{{ route('delete.input_group',['id' => $item['id']]) }}" data-toggle="tooltip" data-placement="top" title="Excluir" class="btn btn-default"><i class="fas fa-trash"></i></a>
                                                    @endcan
                                                    
                                                </div>
                                            </td>
                                    </tr>
                                @endforeach
                                {{-- @endforeach
                                --}}
                            </tbody>

                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>

        </div>
    </div>

@endsection
