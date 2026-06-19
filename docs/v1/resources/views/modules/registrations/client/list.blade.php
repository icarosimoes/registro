@extends('adminlte::page')
@section('content')
@section('plugins.Datatables', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Lista de Clientes</li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Clientes</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="form-group">
                            <div class="card-footer">
                                <a type="button" href="{{ route('new.client') }}" data-toggle="tooltip" data-placement="top"
                                    title="Novo Cliente" class="btn bg-gradient-secondary btn-sm"><i
                                        class="fas fa-plus-square"></i> Novo</a>
                            </div>
                        </div>

                        <table name="DataTableUser" id="DataTableUser" class="table table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>CPF/CNPJ</th>
                                    <th>Telefone</th>
                                    <th class="w-20">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        {{-- @foreach ($roles as $role) --}}
                                            <td>{{ $item->nome }}</td>
                                            <td>{{ $item->email }}</td>
                                            <td>{{ App\Http\Controllers\Register\ClientController::formatCnpjCpf($item->cpf_cnpj) }}</td>
                                            <td>{{ App\Http\Controllers\Register\ClientController::formatTelefone($item->telefone) }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    @can('checkRouters', $route = 'view.client')
                                                      <a href="{{ route('view.client', ['id' => $item['id']]) }}" data-toggle="tooltip" data-placement="top" title="Visualizar" class="btn btn-default"><i class="fas fa-eye"></i></a>
                                                    @endcan
                                                    @can('checkRouters', $route = 'edit.client')
                                                      <a href="{{ route('edit.client',['id' => $item['id']]) }}" data-toggle="tooltip" data-placement="top" title="Editar" class="btn btn-default"><i class="fas fa-pencil-alt"></i></a>
                                                    @endcan
                                                    @can('checkRouters', $route = 'delete.client')
                                                      <a href="{{ route('delete.client',['id' => $item['id']]) }}" data-toggle="tooltip" data-placement="top" title="Excluir" class="btn btn-default"><i class="fas fa-trash"></i></a>
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
