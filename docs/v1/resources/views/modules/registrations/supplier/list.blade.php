@extends('adminlte::page')
@section('content')
@section('plugins.Datatables', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Lista de Fornecedores</li>
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
                        <h3 class="card-title">Lista de Fornecedores</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="form-group">
                            <div class="card-footer">
                                <a type="button" href="{{ route('new.supplier') }}" data-toggle="tooltip"
                                    data-placement="top" title="Novo Fornecedor" class="btn bg-gradient-secondary btn-sm"><i
                                        class="fas fa-plus-square"></i> Novo</a>
                            </div>
                        </div>

                        <table name="DataTableUser" id="DataTableUser" class="table table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Nome Fantasia</th>
                                    <th>Razão Social</th>
                                    <th>CNPJ</th>
                                    <th class="w-20">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item) 
                                    <tr>
                                            <td>{{ $item->fantasy_name }}</td>
                                            <td>{{ $item->company_name }}</td>
                                            <td>{{ App\Http\Controllers\Register\SupplierController::formatCnpjCpf($item->cnpj) }}
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    @can('checkRouters', $route = 'edit.supplier')
                                                    <a href="{{ route('edit.supplier', ['id' => $item['id']]) }}"
                                                        data-toggle="tooltip" data-placement="top" title="Editar"
                                                        class="btn btn-default"><i class="fas fa-pencil-alt"></i></a>
                                                    @endcan
                                                    @can('checkRouters', $route = 'contacts.supplier')
                                                    <a href="{{ route('contacts.supplier', ['id' => $item['id']]) }}"
                                                        data-toggle="tooltip" data-placement="top" title="Agenda Contatos"
                                                        class="btn btn-default"><i class="fas fa-tty"></i></a>
                                                    @endcan
                                                    @can('checkRouters', $route = 'delete.supplier')
                                                    <a href="{{ route('delete.supplier', ['id' => $item['id']]) }}"
                                                        data-toggle="tooltip" data-placement="top" title="Excluir"
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
