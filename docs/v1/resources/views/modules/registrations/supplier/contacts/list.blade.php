@extends('adminlte::page')
@section('content')
@section('plugins.Datatables', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('list.supplier') }}">Lista Fornecedores</a></li>
                    <li class="breadcrumb-item active">Lista de Contatos</li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-tty"></i> Lista de Contatos</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="form-group">
                            <div class="card-footer">
                                <a type="button" href="{{ route('new.contacts') }}" data-toggle="tooltip" data-placement="top"
                                    title="Novo Fornecedor" class="btn bg-gradient-secondary btn-sm"><i
                                        class="fas fa-plus-square"></i> Novo</a>
                            </div>
                        </div>
                        <table name="DataTableUser" id="DataTableUser" class="table table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Função</th>
                                    <th>Telefone</th>
                                    <th>Email</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @foreach ($data as $item) --}}
                                    <tr>
                                        @foreach ($data as $item)
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->occupation }}</td>
                                            <td>{{ $item->telephone }}</td>
                                            <td>{{ $item->email }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    {{-- @can('checkRouters', $route = 'edit.supplier') --}}
                                                    <a href="{{ route('edit.contact',['id' => $item['id']]) }}" data-toggle="tooltip" data-placement="top" title="Editar" class="btn btn-default"><i class="fas fa-pencil-alt"></i></a>
                                                    {{-- @endcan --}}
                                                    {{-- @can('checkRouters', $route = 'delete.supplier') --}}
                                                    {{--  --}}
                                                    <a href="{{ route('delete.contact',['id' => $item['id']]) }}" data-toggle="tooltip" data-placement="top" title="Excluir" class="btn btn-default"><i class="fas fa-trash"></i></a>
                                                    {{-- @endcan --}}
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
