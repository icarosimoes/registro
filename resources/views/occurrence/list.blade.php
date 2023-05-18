@extends('adminlte::page')
@section('content')
@section('plugins.Select2', true)
@section('plugins.Datatables', true)
<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-12">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active">Lista de Registros</li>
            </ol>
        </div>
        <div class="col-md-12">
            <div class="card card-secondary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Lista de Registros</h3>
                    <a data-toggle="modal" data-target="#export-pdf"
                        class="btn btn-warning btn-flat btn-sm float-right mb-0"><i class="fas fa-file-export"></i>
                        Exportar</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col">
                                <a type="button" href="{{ route('occurrence.create') }}" data-toggle="tooltip"
                                    data-placement="top" title="Novo Registro"
                                    class="btn bg-gradient-secondary btn-sm "><i class="fas fa-plus"></i>
                                    Novo Registro</a>
                            </div>
                            <div class="col text-right">
                                <button type="button" id="filter" class="btn bg-gradient-info btn-sm "><i
                                        class="fas fa-filter"></i> Filtro </button>
                            </div>
                        </div>
                    </div>
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ $errors->first() }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form name="form" id="form" action="" enctype="multipart/form-data" method="GET">
                        <div class="callout callout-info" id="card_filter" style="display: none">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="Name">Status</label>
                                        <select class="form-control" name="status" id="status">
                                            <option value="0">Todos</option>
                                            <option value="1">Em Aberto</option>
                                            <option value="2">Em Andamento</option>
                                            <option value="3">Fechado</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="Name">Local</label>
                                        <select class="form-control" name="local" id="local">

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="Name">Departamento</label>
                                        <select class="form-control" name="sector" id="sector">

                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col text-right">
                                    <button type="submit" class="btn btn-sm btn-info btn-flat"><i
                                            class="fas fa-search"></i> Aplicar</button>
                                </div>
                            </div>
                        </div>

                    </form>
                    <table name="DataTableUser" id="DataTableUser" class="table table-striped table-sm table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th width="42%">Title</th>
                                <th>Status</th>
                                <th>Local</th>
                                <th>Departamento</th>
                                <th>Prazo</th>
                                <th>Atualizado em</th>
                                <th class="w-20">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    {{-- @foreach ($roles as $role) --}}
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->title }}</td>
                                    <td>
                                        @if ($item->status == 1)
                                            <span class="badge bg-info">{{ 'Em Aberto' }}</span>
                                        @elseif($item->status == 2)
                                            <span class="badge bg-warning">{{ 'Em Andamento' }}</span>
                                        @elseif($item->status == 3)
                                            <span class="badge bg-success">{{ 'Fechado' }}</span>
                                        @endif
                                    </td>
                                    <td>{{ @$item->local->name }}</td>
                                    <td>{{ @$item->sector->name }}</td>
                                    <td>{{ (new DateTime($item->deadline))->format('d/m/Y') }}</td>
                                    <td>{{ (new DateTime($item->updated_at))->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="btn-group-sm">
                                            {{-- @can('checkRouters', $route = 'view.client') --}}

                                            <a href="{{ route('occurrence.view', ['id' => $item['id']]) }}"
                                                data-toggle="tooltip" data-placement="top" title="Visualizar"
                                                type="button" class="btn btn-default btn-circle"><i
                                                    class="fas fa-eye"></i></a>
                                            {{-- @endcan --}}
                                            {{-- @can('checkRouters', $route = 'edit.client') --}}
                                            @if ($item->status == 1 || $item->status == 2)
                                                <a href="{{ route('occurrence.edit', ['id' => $item['id']]) }}"
                                                    data-toggle="tooltip" data-placement="top" title="Editar"
                                                    class="btn btn-primary"><i class="fas fa-pencil-alt"></i></a>
                                            @endif

                                            {{-- @endcan --}}
                                            {{-- @can('checkRouters', $route = 'delete.client') --}}
                                            <a href="{{ route('occurrence.delete', ['id' => $item['id']]) }}"
                                                data-toggle="tooltip" data-placement="top" title="Excluir"
                                                class="btn btn-danger"><i class="fas fa-trash"></i></a>
                                            {{-- @endcan --}}
                                            <button class="btn btn-info" data-toggle="tooltip" data-placement="top"
                                                title="Criado em: {{ (new DateTime($item->created_at))->format('d/m/Y H:i:s') }} Por: {{ @$item->createdBy->name }}  | Atualizado em: {{ (new DateTime($item->updated_at))->format('d/m/Y H:i:s') }} Por: {{ @$item->updatedBy->name }} "><i
                                                    class="fas fa-info-circle"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            {{-- @endforeach --}}
                        </tbody>

                    </table>
                </div>
                <!-- /.card-body -->
            </div>
        </div>

    </div>
</div>

<!-- Modal export pdf-->
<div class="modal fade" id="export-pdf" tabindex="-1" role="dialog" aria-labelledby="export-pdf"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="export-pdf">Por favor, insira uma descrição ao arquivo.</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="inputName" class="form-group">
                    <label for="titleExport">Nome</label>
                    <input type="text" class="form-control" name="titleExport" id="titleExport">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i
                        class="far fa-times-circle"></i> Sair</button>
                <button type="button" id="btnNext" target="_blank" class="btn btn-primary">Continuar <i
                        class="fas fa-forward"></i></button>
                <a type="button" href="" id="btnExport" target="_blank" class="btn btn-primary d-none"><i
                        class="fas fa-file-export"></i> Exportar</a>
            </div>
        </div>
    </div>
</div>




@section('plugins.scriptListOccurrence', true)
@endsection
