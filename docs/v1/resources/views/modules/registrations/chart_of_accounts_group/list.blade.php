@extends('adminlte::page')
@section('content')
@section('plugins.Datatables', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Grupo Plano de Contas</li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Grupo Plano de Contas</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="form-group">
                            <div class="card-footer">
                                <a type="button" href="{{ route('new.Chart_of_accounts_group') }}" data-toggle="tooltip" data-placement="top"
                                    title="Novo Grupo" class="btn bg-gradient-secondary btn-sm"><i
                                        class="fas fa-plus-square"></i> Novo</a>
                            </div>
                        </div>

                        <table name="DataTableUser" id="DataTableUser" class="table table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nome</th>
                                    <th style="width: 20%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        {{-- @foreach ($roles as $role) --}}
                                            <td>{{  substr($item->code, 0, 2).'-'.substr($item->code, 2,2)  }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    @can('checkRouters', $route = 'edit.Chart_of_accounts_group')
                                                      <a href="{{ route('edit.Chart_of_accounts_group',['id' => $item['id']]) }}" data-toggle="tooltip" data-placement="top" title="Editar" class="btn btn-default"><i class="fas fa-pencil-alt"></i></a>
                                                    @endcan
                                                    @can('checkRouters', $route = 'edit.Chart_of_accounts')
                                                     <a href="{{ route('list.Chart_of_accounts',['id' => $item['id']]) }}" data-toggle="tooltip" data-placement="top" title="Listar Planos" class="btn btn-default"><i class="fas fa-list"></i></a>
                                                    @endcan
                                                    @can('checkRouters', $route = 'delete.Chart_of_accounts_group')
                                                      <a href="{{ route('delete.Chart_of_accounts_group',['id' => $item['id']]) }}" data-toggle="tooltip" data-placement="top" title="Excluir" class="btn btn-default"><i class="fas fa-trash"></i></a>
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
