@extends('adminlte::page')
@section('content')
@section('plugins.Datatables', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Lista de Conferências de suítes</li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Conferências de Suites</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col">
                                    <a type="button" href="{{ route('check_suite.create') }}" data-toggle="tooltip"
                                        data-placement="top" title="Novo Departamento"
                                        class="btn bg-gradient-secondary btn-sm float-right"><i class="fas fa-plus"></i>
                                        Nova Conferência</a>
                                </div>
                            </div>
                        </div>

                        <table name="DataTableUser" id="DataTableUser" class="table table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Date</th>
                                    <th>Suite</th>
                                    <th>Inspecionado por</th>
                                    <th class="text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr >
                                        {{-- @foreach ($roles as $role) --}}
                                            <td width="50">{{ $item->id }}</td>
                                            <td >{{ $item->date }}</td>
                                            <td >{{ $item->suite }}</td>
                                            <td >{{ $item->inspected_by }}</td>
                                            <td class="text-right">
                                                <div class="btn-group-sm">
                                                    {{-- @can('checkRouters', $route =
                                                    'view.client') --}}
                                                    <a href="{{ route('local.show', [$item->id]) }}"
                                                        data-toggle="tooltip" data-placement="top" title="Visualizar"
                                                        class="btn btn-default"><i class="fas fa-eye"></i></a> 
                                                    {{-- @endcan
                                                    --}}
                                                    {{-- @can('checkRouters', $route =
                                                    'edit.client') --}}
                                                    <a href="{{ route('check_suite.edit', [$item->id]) }}"
                                                        data-toggle="tooltip" data-placement="top" title="Editar"
                                                        class="btn btn-primary"><i class="fas fa-pencil-alt"></i></a>
                                                    {{-- @endcan
                                                    --}}
                                                    {{-- @can('checkRouters', $route =
                                                    'delete.client') --}}
                                                    <button  data-id="{{ $item->id }}"
                                                        data-toggle="tooltip" data-placement="top" title="Excluir"
                                                        class="btn btn-danger remove"><i class="fas fa-trash"></i></button> 
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

    {{-- Modal delete --}}
    <div class="modal" tabindex="-1" id='modal_delete' >
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Excluir</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <p>Deseja realmente excluir ?</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
              <button id="btn_delete"  class="btn btn-danger">Deletar</button>
            </div>
          </div>
        </div>
      </div>
      

      @section('plugins.scriptListLocal', true)
@endsection
