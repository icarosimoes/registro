@extends('adminlte::page')
@section('content')
@section('plugins.Datatables', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Lista de Procedimentos</li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Procedimentos</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col">
                                    <a type="button" href="{{ route('procedure.create') }}" data-toggle="tooltip"
                                        data-placement="top" title="Novo Procedimento"
                                        class="btn bg-gradient-secondary btn-sm float-right"><i class="fas fa-plus"></i>
                                        Novo Procedimento</a>
                                </div>
                            </div>
                        </div>

                        <table name="DataTableUser" id="DataTableUser" class="table table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Name</th>
                                    <th>Link</th>
                                    <th class="text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr >
                                        {{-- @foreach ($roles as $role) --}}
                                            <td width="100">{{ $item->id }}</td>
                                            <td width="400">{{ $item->name }}</td>
                                            <td >{{ $item->link }}</td>
                                            <td class="text-right" >
                                                <div class="btn-group-sm">
                                                    <button data-id="{{ $item->id }}" data-toggle="tooltip" data-placement="top" title="Download"
                                                        class="btn btn-secondary md"><i class="fas fa-download"></i></button> 
                                                    {{-- @can('checkRouters', $route =
                                                    'view.client') --}}
                                                    <a href="{{ route('procedure.show', [$item->id]) }}"
                                                        data-toggle="tooltip" data-placement="top" title="Visualizar"
                                                        class="btn btn-default"><i class="fas fa-eye"></i></a> 
                                                    {{-- @endcan
                                                    --}}
                                                    {{-- @can('checkRouters', $route =
                                                    'edit.client') --}}
                                                    <a href="{{ route('procedure.edit', [$item->id]) }}"
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
      

      <!-- Modal -->
<div class="modal fade" id="anexo">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Lista de Anexos</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="formFileDownload" id="formFileDownload" enctype="multipart/form-data" method="POST">
                @csrf
                <div class="modal-body">

                    <div class="form-group">
                        <label>Insira uma descrição para o arquivo</label>
                        <input type="text" class="form-control" name="name" id="name" required>
                        <input type="hidden" class="form-control" id="procedure_id_atach">
                    </div>
                    <div class="form-group">
                        <label>Selecione o arquivo</label>
                        <input type="file" class="form-control" name="file" id="file" required>
                    </div>

                    <table class="table table-striped table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Descrição</th>
                                <th>Data</th>
                                <th style="width: 1%">Download</th>
                            </tr>
                        </thead>
                        <tbody id="bodyFile"></tbody>
                    </table>
                    <div class="overlay-wrapper">
                        <div class="d-none overlay">
                            <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                            <div class="text-bold pt-2">Carregando...</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i
                            class="fas fa-sign-out-alt"></i> Fechar</button>
                    <button type="submit" class="btn btn-secondary"><i class="fas fa-save"></i>
                        Save</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!-- Modal exclir anexo -->
<div class="modal " id="deleteFile" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Atenção</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <h5>Deseja realmente excluir o anexo?</h5>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" id="cancelDeleteTheFile" ><i class="fas fa-ban"></i> Cancelar</button>
          <button id="deleteTheFile" type="button" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Sim</button>
        </div>
      </div>
    </div>
  </div>
</div>

      @section('plugins.scriptListProcedure', true)
@endsection
