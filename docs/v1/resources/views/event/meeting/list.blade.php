@extends('adminlte::page')
@section('content')
@section('plugins.Datatables', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Lista de Reuniões</li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Reuniões</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col">
                                    <a type="button" href="{{ route('meeting.create') }}" data-toggle="tooltip"
                                        data-placement="top" title="Nova Reunião"
                                        class="btn bg-gradient-secondary btn-sm float-right"><i class="fas fa-plus"></i>
                                        Nova Reunião</a>
                                </div>
                            </div>
                        </div>

                        <table name="DataTableUser" id="DataTableUser" class="table table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Criado Por</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                    <th  class="text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        {{-- @foreach ($roles as $role) --}}
                                            <td>{{ $item['users']->name }}</td>
                                            <td>
                                                @if ($item->status == 1)
                                                <span class="badge badge-success">Em Aberto</span>
                                                @elseif($item->status == 2)
                                                <span class="badge badge-warning">Convocada</span>
                                                @elseif($item->status == 3)
                                                <span class="badge badge-danger">Realizado</span>
                                                @endif
                                            </td>
                                            <td >{{ (new DateTime($item->created_at))->format('d/m/Y H:i:s') }}</td>
                                            <td class="text-right">
                                                <div class="btn-group-sm">
                                                    {{-- @can('checkRouters', $route =
                                                    'view.client') --}}
                                                    <a href="{{ route('meeting.view', ['id' => $item['id']]) }}"
                                                        data-toggle="tooltip" data-placement="top" title="Visualizar"
                                                        class="btn btn-default"><i class="fas fa-eye"></i></a> 
                                                    {{-- @endcan --}}
                                                    {{-- --}} 
                                                    {{-- @can('checkRouters', $route =
                                                    'edit.client') --}}
                                                    <a href="{{ route('meeting.edit', ['id' => $item['id']]) }}"
                                                        data-toggle="tooltip" data-placement="top" title="Editar"
                                                        class="btn btn-info"><i class="fas fa-pencil-alt"></i></a>
                                                    {{-- @endcan
                                                    --}}
                                                    {{-- @can('checkRouters', $route =
                                                    'delete.client') --}}
                                                    <a href="{{ route('meeting.delete', ['id' => $item['id']]) }}"
                                                        data-toggle="tooltip" data-placement="top" title="Excluir"
                                                        class="btn btn-danger"><i class="fas fa-trash"></i></a>
                                                    {{-- @endcan
                                                    --}}

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
