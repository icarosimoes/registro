@extends('adminlte::page')
@section('content')
@section('plugins.Datatables', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Conta</li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Contas</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="form-group">
                            <div class="card-footer">
                                <a type="button" href="{{ route('new.account') }}" data-toggle="tooltip" data-placement="top"
                                    title="Novo" class="btn bg-gradient-secondary btn-sm"><i
                                        class="fas fa-plus-square"></i> Novo</a>
                            </div>
                        </div>

                        <table name="DataTableUser" id="DataTableUser" class="table table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Descrição</th>
                                    <th>Saldo</th>
                                    <th style="width:  8.33%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>                                        
                                            <td>{{ $item->id }}</td>
                                            <td>{{ $item->description }}</td>
                                            <td>{{ "R$ ".number_format($item->balance, 2, ',','.') }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    @can('checkRouters', $route = 'edit.account')
                                                      <a href="{{ route('edit.account',['id' => $item['id']]) }}" data-toggle="tooltip" data-placement="top" title="Editar" class="btn btn-default"><i class="fas fa-pencil-alt"></i></a>
                                                    @endcan
                                                    @can('checkRouters', $route = 'delete.account')
                                                      <a href="{{ route('delete.account',['id' => $item['id']]) }}" data-toggle="tooltip" data-placement="top" title="Excluir" class="btn btn-default"><i class="fas fa-trash"></i></a>
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
