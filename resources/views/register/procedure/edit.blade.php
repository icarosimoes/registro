@extends('adminlte::page')
@section('content')
@section('plugins.Select2', true)
@section('plugins.JqueryMask', true)
<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-12">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('procedure.index') }}">Lista de Procedimentos</a>
                </li>
                <li class="breadcrumb-item active">Editar Procedimento</li>
            </ol>
        </div>
        <div class="col-md-12">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-2">
                        <form name="form" id="formSector" enctype="multipart/form-data" method="POST">
                            @csrf
                            <!-- {{ csrf_field() }} -->
                    </div> <!-- col-md3 -->
                    <div class="col-md-12">
                        <div class="card card-secondary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Editar Procedimento</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label for="Name">Procedimento</label>
                                            <input type="text" class="form-control" id="name"
                                                value="{{ $procedure->name }}"placeholder="" required>
                                            <input type="hidden" id="id" value="{{ $procedure->id }}">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label for="Name">Link</label>
                                            <input type="text" class="form-control" id="link" value="{{ $procedure->link }}"placeholder="">
                                        </div>
                                    </div>
                                     <div class="col-3">
                                        <div class="form-group">
                                           
                                            <button data-id="{{ $procedure->id }}" type="button"  class="btn btn-secondary md mt-4">Anexo <i
                                                            class="fas fa-download"></i></button>
                                           
                                        </div>
                                    </div> 

                                </div>

                                <div class="overlay-wrapper">
                                    <div class="d-none overlay">
                                        <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                                        <div class="text-bold pt-2">Carregando...</div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" id="submit" name="submit"
                                    class="btn btn-secondary float-lg-right"><i class="fas fa-save"></i> Salvar</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
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

@section('plugins.scriptUpdateProcedure', true)
@endsection
