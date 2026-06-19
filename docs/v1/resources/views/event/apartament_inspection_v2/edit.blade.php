@extends('adminlte::page')
@section('content')
@section('plugins.Select2', true)
@section('plugins.JqueryMask', true)
<div class="container">
  <div class="row justify-content-center">
    <div class="col-sm-12">
      <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active"><a href="{{ route('check_suite.index') }}">Lista de Conferências</a>
        </li>
        <li class="breadcrumb-item active">Editar Vistoria</li>
      </ol>
    </div>
    <div class="col-md-12">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-2">
            <form name="form" id="form" enctype="multipart/form-data" method="POST">
              @csrf
          </div> <!-- col-md3 -->
          <div class="col-md-12">
            <div class="card card-secondary card-outline">
              <div class="card-header">
                <h3 class="card-title">Editar Vistoria v2</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <div class="card-body">
                <input type="hidden" id="apartment_inspection_id" value="{{ $apartment_inspection->id }}">
                <div class="row">
                  <div class="col">
                    <div class="form-group">
                      <label for="Name">Propriétario</label>
                      <input type="text" class="form-control" id="owner" placeholder=""
                        value="{{ $apartment_inspection->owner }}" required>
                    </div>
                  </div>
                  <div class="col">
                    <div class="form-group">
                      <label for="Name">Unidade</label>
                      <input type="text" class="form-control" value="{{ $apartment_inspection->unit }}"
                        id="unit" placeholder="" required>
                    </div>
                  </div>


                  <div class="col">
                    <div class="form-group">
                      <label for="Name">Inspecionado por</label>
                      <input type="text" class="form-control" value="{{ $apartment_inspection->inspected_by }}"
                        id="inspected_by" placeholder="" required>
                    </div>
                  </div>
                  <div class="col">
                    <div class="form-group">
                      <label for="Name">Data</label>
                      <input type="date" class="form-control"
                        value="{{ explode(' ', $apartment_inspection->inspection_date)[0] }}" id="inspection_date"
                        placeholder="" required>
                    </div>
                  </div>

                </div>
                <div class="row">
                                    <div class="col-3">
                                        <div class="d-flex align-items-center">
                                            <select data-value="{{ $apartment_inspection->type_unit }}" class="form-control" id="type_unit" name="type_unit">
                                            </select>
                                            <button type="button" class="btn btn-secondary ml-2" id="addTypeUnit">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                 
                                </div>
                <div class="row" style="margin-top:20px; mx-0">
                                  <div class="col">
                                    <button type="button" class="btn btn-primary btn-sm" id="add_group"> <i class="fas fa-plus"></i> Adicionar Área</button>

                                  </div>
                                </div>
                <div class="row">
                  <input type="hidden" value="{{ $apartment_inspection->apartment_inspection_items }}" id="items">
                  <table style="font-size: 13px" class="table table-sm ">
                    <thead>
                      <tr>
                        <td>ÁREA VISTORIADA</td>
                        <td>SERVIÇOS</td>
                        <td>ITENS DE VERIFICAÇÃO</td>
                        <td>AVALIAÇÃO</td>
                        <td>OBSERVAÇÕES</td>
                        <td></td>
                        <td>REGISTRO</td>
                      </tr>
                    </thead>
                    {{-- DOIS QUARTOS --}}
                    <tbody id="apartment_items" >

                      
                    </tbody>
                  </table>

                </div>
                <div class="row">
                  <div class="col">
                    <label for="Name">Observaçôes</label>
                    <div class="form-group">

                      <textarea class="form-control " name="" id="obs" rows="5">{{ $apartment_inspection->observation }}</textarea>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <label for="Name">Status da Vistoria</label>
                    <div class="form-group">

                      <input type="radio" required name="status_conf" id="status1"
                        {{ $apartment_inspection->approved == 'yes' ? 'checked' : '' }} value="liberado">
                      <label for="status1">VISTORIADA E APROVADA</label>
                      <input type="radio" required class="ml-5" name="status_conf" id="status2"
                        {{ $apartment_inspection->approved == 'not' ? 'checked' : '' }} value="bloqueado">
                      <label for="status2">VISTORIADA E REPROVADA</label>
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
                <button type="submit" id="submit" name="submit" class="btn btn-secondary float-lg-right"><i
                    class="fas fa-save"></i> Salvar</button>
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

{{-- modal novo grupo --}}
<div class="modal fade" id="modal_add_group">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Adicionar Novo Grupo</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
                @csrf
                <div class="modal-body">
                  <input type="text" class="form-control" id="name_new_group" placeholder="Insira um novo grupo">
                    
                    <div class="overlay-wrapper">
                        <div class="d-none overlay loading_attach">
                            <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                            <div class="text-bold pt-2">Carregando...</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between text-right">
                    <button type="button" id="btn_add_new_group" class="btn btn-secondary"><i class="fas fa-save"></i>
                        Save</button>
                </div>
            
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


{{-- modal tipos de unidade --}}
<div class="modal fade" id="modal_type_unit">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tipos De Unidade</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="formFileDownload" id="formFileDownload" enctype="multipart/form-data" method="POST">
                @csrf
                <div class="modal-body">
                  <input type="text" class="form-control" id="new_type_unit" placeholder="Insira um novo tipo de unidade">
                    
                    <div class="overlay-wrapper">
                        <div class="d-none overlay loading_attach">
                            <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                            <div class="text-bold pt-2">Carregando...</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between text-right">
                    <button type="button" id="btn_save_type_unit" class="btn btn-secondary"><i
                            class="fas fa-save"></i>
                        Save</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

{{-- modal de anexos --}}
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
            <input type="hidden" class="form-control" id="apartment_inspection_item_id">
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
            <div class="d-none overlay loading_attach">
              <i class="fas fa-3x fa-sync-alt fa-spin"></i>
              <div class="text-bold pt-2">Carregando...</div>
            </div>
          </div>
        </div>

        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal"><i
              class="fas fa-sign-out-alt"></i> Fechar</button>
          <button type="button" id="btn_send_attach" class="btn btn-secondary"><i class="fas fa-save"></i>
            Save</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>


<!-- Modal selecionar ocorrência-->
<div class="modal fade" id="ModalSelectOcurrence" tabindex="-1" role="dialog"
  aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Selecione um Registro</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Registros</label>
          <input type="hidden" id="register_ref" name="register_ref">
          <select class="form-control  isdfdOccurence" id="idOccurence" name="userRegistered"
            style="width: 100%;">
            {{-- @foreach ($ocurrences as $ocurrence)
                        <option value="{{ $ocurrence->id }}">{{ "Código: ".$ocurrence->id." - ".$ocurrence->title }}
                        </option>
                    @endforeach --}}
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="javascript:window.open('{{ route('occurrence.create') }}', '_blank');"
          class="btn btn-info float-left" data-dismiss="modal"><i class="fas fa-plus"></i> Novo Registro</button>
        {{-- <button type="button" data-toggle='modal' data-target='#ModalNewOcurrence' class="btn btn-info float-left"
                data-dismiss="modal"><i class="fas fa-plus"></i> Novo Registro</button> --}}
        <button type="button" id="buttonOccurrence" name="buttonOccurrence"
          class="btn btn-primary float-md-right buttonOccurrence"><i class="fas fa-hand-pointer"></i>
          Selecionar</button>
      </div>
    </div>
  </div>
</div> <!-- / Modal selecionar ocorrência -->

@section('plugins.scriptUpdateApartmentInspectV2', true)
@endsection
