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
        <li class="breadcrumb-item active">Nova Vistoria</li>
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
                <h3 class="card-title">Nova Vistoria</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <div class="card-body">

                <div class="row">
                  <div class="col">
                    <div class="form-group">
                      <label for="Name">Propriétario</label>
                      <input type="text" class="form-control" id="date" placeholder="" required>
                    </div>
                  </div>
                  <div class="col">
                    <div class="form-group">
                      <label for="Name">Unidade</label>
                      <input type="text" class="form-control" id="date" placeholder="" required>
                    </div>
                  </div>


                  <div class="col">
                    <div class="form-group">
                      <label for="Name">Inspecionado por</label>
                      <input type="text" class="form-control" id="date" placeholder="" required>
                    </div>
                  </div>
                  <div class="col">
                    <div class="form-group">
                      <label for="Name">Data</label>
                      <input type="date" class="form-control" id="date" placeholder="" required>
                    </div>
                  </div>

                </div>
                <div class="row">
                  <table style="font-size: 13px" class="table table-sm ">
                    <thead>
                      <tr>
                        <td>ÁREA VISTORIADA</td>
                        <td>SERVIÇOS</td>
                        <td>ITENS DE VERIFICAÇÃO</td>
                        <td>AVALIAÇÃO</td>
                        <td></td>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td rowspan="8" style="background: gray">SUITE</td>
                        <td>
                          PISOS
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, ESQUADRO, NÍVEL, REJUNTAMENTO E LIMPEZA
                        </td>

                        <td>
                          <input type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="">

                            <option value="sim">SIM</option>
                            <option value="nao">NÃO</option>
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          RODAPÉ
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, ESQUADRO, NÍVEL, REJUNTAMENTO E LIMPEZA
                        </td>

                        <td>
                          <input type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="">

                            <option value="sim">SIM</option>
                            <option value="nao">NÃO</option>
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          PINTURA DE PAREDES E TETOS
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, ESQUADRO, NÍVEL, REJUNTAMENTO E LIMPEZA
                        </td>

                        <td>
                          <input type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="">

                            <option value="sim">SIM</option>
                            <option value="nao">NÃO</option>
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          ESQUADRIAS DE ALUMÍNIO
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, ESQUADRO, NÍVEL, REJUNTAMENTO E LIMPEZA
                        </td>

                        <td>
                          <input type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="">

                            <option value="sim">SIM</option>
                            <option value="nao">NÃO</option>
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          PORTAS DE MADEIRAS
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, ESQUADRO, NÍVEL, REJUNTAMENTO E LIMPEZA
                        </td>
                        <td>
                          <input type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="">

                            <option value="sim">SIM</option>
                            <option value="nao">NÃO</option>
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          TOMADAS E INTERRUPTORES
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, ESQUADRO, NÍVEL, REJUNTAMENTO E LIMPEZA
                        </td>
                        <td>
                          <input type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="">

                            <option value="sim">SIM</option>
                            <option value="nao">NÃO</option>
                          </select>
                        </td>
                      </tr>
											<tr>
                        <td>
                          FORRO DE GESSO
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, ESQUADRO, NÍVEL, REJUNTAMENTO E LIMPEZA
                        </td>
                        <td>
                          <input type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="">

                            <option value="sim">SIM</option>
                            <option value="nao">NÃO</option>
                          </select>
                        </td>
                      </tr>
											<tr>
                        <td>
                          AR CONDICIONADO
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, ESQUADRO, NÍVEL, REJUNTAMENTO E LIMPEZA
                        </td>
                        <td>
                          <input type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="">

                            <option value="sim">SIM</option>
                            <option value="nao">NÃO</option>
                          </select>
                        </td>
                      </tr>



                    </tbody>
                  </table>

                </div>
                <div class="row">
                  <div class="col">
                    <label for="Name">Observaçôes</label>
                    <div class="form-group">

                      <textarea class="form-control " name="" id="obs" rows="5"></textarea>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <label for="Name">Status da Vistoria</label>
                    <div class="form-group">

                      <input type="radio" required name="status_conf" id="status1" value="liberado">
                      <label for="status1">VISTORIADA E APROVADA</label>
                      <input type="radio" required class="ml-5" name="status_conf" id="status2"
                        value="bloqueado">
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


<!-- Modal selecionar ocorrência-->
<div class="modal fade" id="ModalSelectOcurrence" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
  aria-hidden="true">
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
          <select class="form-control  isdfdOccurence" id="idOccurence" name="userRegistered" style="width: 100%;">
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

@section('plugins.scriptCreateCheckSuite', true)
@endsection
