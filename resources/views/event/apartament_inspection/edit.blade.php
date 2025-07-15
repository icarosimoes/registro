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
                <h3 class="card-title">Editar Vistoria</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <div class="card-body">
                <input type="hidden" id="apartment_inspection_id" value="{{$apartment_inspection->id}}">
                <div class="row">
                  <div class="col">
                    <div class="form-group">
                      <label for="Name">Propriétario</label>
                      <input type="text" class="form-control" id="owner" placeholder="" value="{{$apartment_inspection->owner}}" required>
                    </div>
                  </div>
                  <div class="col">
                    <div class="form-group">
                      <label for="Name">Unidade</label>
                      <input type="text" class="form-control" value="{{$apartment_inspection->unit}}" id="unit" placeholder="" required>
                    </div>
                  </div>


                  <div class="col">
                    <div class="form-group">
                      <label for="Name">Inspecionado por</label>
                      <input type="text" class="form-control" value="{{$apartment_inspection->inspected_by}}" id="inspected_by" placeholder="" required>
                    </div>
                  </div>
                  <div class="col">
                    <div class="form-group">
                      <label for="Name">Data</label>
                      <input type="date" class="form-control" value="{{explode(' ',$apartment_inspection->inspection_date)[0]}}" id="inspection_date" placeholder="" required>
                    </div>
                  </div>

                </div>
                <div class="row">
                    <input type="hidden" value="{{$apartment_inspection->apartment_inspection_items}}" id="items"> 
                  <table style="font-size: 13px" class="table table-sm ">
                    <thead>
                      <tr>
                        <td>ÁREA VISTORIADA</td>
                        <td>SERVIÇOS</td>
                        <td>ITENS DE VERIFICAÇÃO</td>
                        <td>AVALIAÇÃO</td>
                        <td>OBSERVAÇÕES</td>
                        <td></td>
                      </tr>
                    </thead>
                    <tbody>
                      <tr style="background:#ececec">
                        <td rowspan="8" style="background: ">SUITE</td>
                        <td>
                          PISOS
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, ESQUADRO, NÍVEL, REJUNTAMENTO E LIMPEZA
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-100">
                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='100'  id="appreciation-100" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-100" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>

                      </tr>
                      <tr style="background:#ececec">
                        <td>
                          RODAPÉ
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, ESQUADRO, NÍVEL, REJUNTAMENTO E LIMPEZA
                        </td>


                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-101">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='101' id="appreciation-101" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-101" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>

                      </tr>
                      <tr style="background:#ececec">
                        <td>
                          PINTURA DE PAREDES E TETOS
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, ESQUADRO, NÍVEL, REJUNTAMENTO E LIMPEZA
                        </td>


                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-102">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input type="text" data-ref='102' id="appreciation-102" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-102" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>

                      </tr>
                      <tr style="background:#ececec">
                        <td>
                          ESQUADRIAS DE ALUMÍNIO
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, ESQUADRO, NÍVEL, REJUNTAMENTO E LIMPEZA
                        </td>

                        
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-103">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='103' id="appreciation-103" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-103" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>

                      </tr>
                      <tr style="background:#ececec">
                        <td>
                          PORTAS DE MADEIRAS
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, ESQUADRO, NÍVEL, REJUNTAMENTO E LIMPEZA
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-104">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='104' id="appreciation-104" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-104" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>

                      </tr>
                      <tr style="background:#ececec">
                        <td>
                          TOMADAS E INTERRUPTORES
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, ESQUADRO, NÍVEL, REJUNTAMENTO E LIMPEZA
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-105">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='105' id="appreciation-105" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-105" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>

                      </tr>
											<tr style="background:#ececec">
                        <td>
                          FORRO DE GESSO
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, ESQUADRO, NÍVEL, REJUNTAMENTO E LIMPEZA
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-106">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='106' id="appreciation-106" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-106" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>

                      </tr>
											<tr style="background:#ececec">
                        <td>
                          AR CONDICIONADO
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, ESQUADRO, NÍVEL, REJUNTAMENTO E LIMPEZA
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-107">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='107'  id="appreciation-107"type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-107" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>


											{{-- SANITARIO SUITE --}}
											 <tr style="background:lightgray">
                        <td rowspan="10" style="background: lightgray">SANITÁRIO SUITE</td>
                        <td>
                          PISOS
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, ESQUADRO, NÍVEL, REJUNTAMENTO E LIMPEZA
                        </td>


                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-200">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='200' id="appreciation-200" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-200" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
                      <tr style="background:lightgray">
                        <td>
                          REVESTIMENTOS
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, ESQUADRO, REJUNTAMENTO E LIMPEZA 
                        </td>


                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-201">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='201' id="appreciation-201" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-201" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
                      <tr style="background:lightgray">
                        <td>
                          BANCADAS
                        </td>
                        <td>
                          FIXAÇÃO, NIVELAMENTO, ACABAMENTO, REJUNTAMENTO E LIMPEZA
                        </td>


                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-202">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='202' id="appreciation-202" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-202" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
                      <tr style="background:lightgray">
                        <td>
                          ESQUADRIAS DE ALUMÍNIO
                        </td>
                        <td>
                          FUNCIONAMENTO, VIDROS, ACESSÓRIOS, ACABAMENTO E LIMPEZA
                        </td>


                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-203">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='203' id="appreciation-203" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-203" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
                      <tr style="background:lightgray">
                        <td>
                          PORTAS DE MADEIRAS
                        </td>
                        <td>
                          FUNCIONAMENTO, FERRAGENS, PRESENÇA DE VÃOS, FIXAÇÃO E LIMPEZA
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-204">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='204' id="appreciation-204" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-204" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
                      <tr style="background:lightgray">
                        <td>
                          TOMADAS E INTERRUPTORES
                        </td>
                        <td>
                          FUNCIONAMENTO, FIXAÇÃO DOS ESPELHOS, ACABAMENTO E LIMPEZA
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-205">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='205' id="appreciation-205" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-205" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
											<tr style="background:lightgray">
                        <td>
                          FORRO DE GESSO
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, CANTOS E PINTURA
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-206">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='206' id="appreciation-206" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-206" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
											<tr style="background:lightgray">
                        <td>
                          LOUÇAS (VASO SANIT E LAVATÓRIO)
                        </td>
                        <td>
                          FIXAÇÃO, FUNCIONAMENTO, ACABAMENTO, LIMPEZA
                        </td>
                        
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-207">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='207' id="appreciation-207" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-207" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>

                      </tr>
											<tr style="background:lightgray">
                        <td>
                          METAIS (TORNEIRAS, CHUVEIRO E DUCHA)
                        </td>
                        <td>
                          FIXAÇÃO, FUNCIONAMENTO, ACABAMENTO, LIMPEZA
                        </td>
                        
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-208">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='208' id="appreciation-208" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-208" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
											<tr style="background:lightgray">
                        <td>
                          SIFÃO/ VÁLVULAS/ RALOS
                        </td>
                        <td>
                          FIXAÇÃO, FUNCIONAMENTO, ACABAMENTO
                        </td>
                        
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-209">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='209' id="appreciation-209" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-209" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>

										{{--QUARTO  --}}

										 <tr style="background:#ececec">
                        <td rowspan="8" style="background: #ececec">QUARTO</td>
                        <td>
                          PISOS
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, ESQUADRO, NÍVEL, REJUNTAMENTO E LIMPEZA 
                        </td>

                        

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-300">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='300' id="appreciation-300" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-300" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
                      <tr style="background:#ececec">
                        <td>
                          RODAPÉ
                        </td>
                        <td>
                          FIXAÇÃO, HOMOGENEIDADE, CANTOS,  MANCHAS, FALHAS E LIMPEZA
                        </td>

                        
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-301">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='301' id="appreciation-301" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-301" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
                      <tr style="background:#ececec">
                        <td>
                          PINTURA DE PAREDES E TETOS
                        </td>
                        <td>
                          HOMOGENEIDADE, ACABAMENTO E LIMPEZA 
                        </td>


                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-302">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input  data-ref='302' id="appreciation-302" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-302" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
                      <tr style="background:#ececec">
                        <td>
                          ESQUADRIAS DE ALUMÍNIO
                        </td>
                        <td>
                          FUNCIONAMENTO, VIDROS, ACESSÓRIOS, ACABAMENTO E LIMPEZA
                        </td>


                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-303">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='303' id="appreciation-303" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-303" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
                      <tr style="background:#ececec">
                        <td>
                          PORTAS DE MADEIRAS
                        </td>
                        <td>
                          FUNCIONAMENTO, FERRAGENS, PRESENÇA DE VÃOS, FIXAÇÃO E LIMPEZA
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-304">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='304' id="appreciation-304" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-304" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
                      <tr style="background:#ececec">
                        <td>
                          TOMADAS E INTERRUPTORES
                        </td>
                        <td>
                          FUNCIONAMENTO, FIXAÇÃO DOS ESPELHOS, ACABAMENTO E LIMPEZA
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-305">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='305' id="appreciation-305" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-305" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
											<tr style="background:#ececec">
                        <td>
                          FORRO DE GESSO
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, CANTOS E PINTURA
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-306">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='306' id="appreciation-306" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-306" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
											<tr style="background:#ececec">
                        <td>
                          AR CONDICIONADO
                        </td>
                        <td>
                          PONTO ELÉTRICO, PONTO DE DRENO E TUBULAÇÃO
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-307">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='307' id="appreciation-307" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-307" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
											{{-- SANITARIO SOCIAL --}}
											<tr style="background:lightgray">
                        <td rowspan="10" style="background: lightgray">SANITÁRIO SOCIAL</td>
                        <td>
                          PISOS
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, ESQUADRO, NÍVEL, REJUNTAMENTO E LIMPEZA
                        </td>


                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-400">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='400' id="appreciation-400" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-400" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
                      <tr style="background:lightgray">
                        <td>
                          REVESTIMENTOS
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, ESQUADRO, REJUNTAMENTO E LIMPEZA 
                        </td>


                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-401">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='401' id="appreciation-401" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-401" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
                      <tr style="background:lightgray">
                        <td>
                          BANCADAS
                        </td>
                        <td>
                          FIXAÇÃO, NIVELAMENTO, ACABAMENTO, REJUNTAMENTO E LIMPEZA
                        </td>


                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-402">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='402' id="appreciation-402" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-402" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
                      <tr style="background:lightgray">
                        <td>
                          ESQUADRIAS DE ALUMÍNIO
                        </td>
                        <td>
                          FUNCIONAMENTO, VIDROS, ACESSÓRIOS, ACABAMENTO E LIMPEZA
                        </td>


                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-403">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='403' id="appreciation-403" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-403" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
                      <tr style="background:lightgray">
                        <td>
                          PORTAS DE MADEIRAS
                        </td>
                        <td>
                          FUNCIONAMENTO, FERRAGENS, PRESENÇA DE VÃOS, FIXAÇÃO E LIMPEZA
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-404">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='404' id="appreciation-404" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-404" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
                      <tr style="background:lightgray">
                        <td>
                          TOMADAS E INTERRUPTORES
                        </td>
                        <td>
                          FUNCIONAMENTO, FIXAÇÃO DOS ESPELHOS, ACABAMENTO E LIMPEZA
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-405">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input  data-ref='405' id="appreciation-405" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-405" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
											<tr style="background:lightgray">
                        <td>
                          FORRO DE GESSO
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, CANTOS E PINTURA
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-406">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='406' id="appreciation-406" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-406" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
											<tr style="background:lightgray">
                        <td>
                          LOUÇAS (VASO SANIT E LAVATÓRIO)
                        </td>
                        <td>
                          FIXAÇÃO, FUNCIONAMENTO, ACABAMENTO, LIMPEZA
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-407">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='407' id="appreciation-407" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-407" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td> 
                      </tr>
											<tr style="background:lightgray">
                        <td>
                          METAIS (TORNEIRAS, CHUVEIRO E DUCHA)
                        </td>
                        <td>
                          FIXAÇÃO, FUNCIONAMENTO, ACABAMENTO, LIMPEZA
                        </td>
                        
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-408">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='408' id="appreciation-408" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-408" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
											<tr style="background:lightgray">
                        <td>
                          SIFÃO/ VÁLVULAS/ RALOS
                        </td>
                        <td>
                          FIXAÇÃO, FUNCIONAMENTO, ACABAMENTO
                        </td>
                        
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-409">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='409' id="appreciation-409" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-409" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
										{{-- SALA E VARANDA  --}}

											<tr style="background:#ececec">
                        <td rowspan="7" style="background: #ececec">SALA E VARANDA</td>
                        <td>
                          PISOS
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, ESQUADRO, NÍVEL, REJUNTAMENTO E LIMPEZA 
                        </td>

                        

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-500">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='500' id="appreciation-500" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-500" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
                      <tr style="background:#ececec">
                        <td>
                          RODAPÉ
                        </td>
                        <td>
                          FIXAÇÃO, HOMOGENEIDADE, CANTOS,  MANCHAS, FALHAS E LIMPEZA
                        </td>

                        
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-501">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='501' id="appreciation-501" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-501" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
                      <tr style="background:#ececec">
                        <td>
                          PINTURA DE PAREDES E TETOS
                        </td>
                        <td>
                          HOMOGENEIDADE, ACABAMENTO E LIMPEZA 
                        </td>


                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-502">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='502' id="appreciation-502" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-502" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
                      <tr style="background:#ececec">
                        <td>
                          ESQUADRIAS DE ALUMÍNIO
                        </td>
                        <td>
                          FUNCIONAMENTO, VIDROS, ACESSÓRIOS, ACABAMENTO E LIMPEZA
                        </td>


                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-503">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='503' id="appreciation-503" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-503" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
                      <tr style="background:#ececec">
                        <td>
                          PORTAS DE MADEIRAS
                        </td>
                        <td>
                          FUNCIONAMENTO, FERRAGENS, PRESENÇA DE VÃOS, FIXAÇÃO E LIMPEZA
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-504">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='504' id="appreciation-504" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-504" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
                      <tr style="background:#ececec">
                        <td>
                          TOMADAS E INTERRUPTORES
                        </td>
                        <td>
                          FUNCIONAMENTO, FIXAÇÃO DOS ESPELHOS, ACABAMENTO E LIMPEZA
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-505">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='505' id="appreciation-505" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-505" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
											<tr style="background:#ececec">
                        <td>
                          FORRO DE GESSO
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, CANTOS E PINTURA
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-506">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='506' id="appreciation-506" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-506" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
										{{-- COZINHA --}}
										<tr style="background:lightgray">
                        <td rowspan="10" style="background: lightgray">COZINHA</td>
                        <td>
                          PISOS
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, ESQUADRO, NÍVEL, REJUNTAMENTO E LIMPEZA 
                        </td>


                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-600">
                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='600' id="appreciation-600" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-600" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
                      <tr style="background:lightgray">
                        <td>
                          REVESTIMENTOS
                        </td>
                        <td>
                          FIXAÇÃO, HOMOGENEIDADE, CANTOS,  MANCHAS, FALHAS E LIMPEZA
                        </td>


                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-601">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='601' id="appreciation-601" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-601" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
                      <tr style="background:lightgray">
                        <td>
                          BANCADAS
                        </td>
                        <td>
                          HOMOGENEIDADE, ACABAMENTO E LIMPEZA 
                        </td>


                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-602">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='602' id="appreciation-602" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-602" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
                      <tr style="background:lightgray">
                        <td>
                          TOMADAS E INTERRUPTORES
                        </td>
                        <td>
                          FUNCIONAMENTO, VIDROS, ACESSÓRIOS, ACABAMENTO E LIMPEZA
                        </td>


                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-603">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='603' id="appreciation-603" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-603" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
                      <tr style="background:lightgray">
                        <td>
                          METAIS (TORNEIRAS, SIFÕES E RELOS)
                        </td>
                        <td>
                          FUNCIONAMENTO, FERRAGENS, PRESENÇA DE VÃOS, FIXAÇÃO E LIMPEZA
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-604">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='604' id="appreciation-604" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-604" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
                      <tr style="background:lightgray">
                        <td>
                          BANCADAS E CUBAS
                        </td>
                        <td>
                          FUNCIONAMENTO, FIXAÇÃO DOS ESPELHOS, ACABAMENTO E LIMPEZA
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-605">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                        <td>
                          <input data-ref='605' id="appreciation-605" type="text" style="width: 200px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <button type="button" id="attach-605" data-toggle="tooltip" data-placement="top" title="Anexos"
                            class="btn btn-secondary btn-sm attach"><i class="fas fa-download"></i></button>
                        </td>
                      </tr>
										
										

                    </tbody>
                  </table>

                </div>
                <div class="row">
                  <div class="col">
                    <label for="Name">Observaçôes</label>
                    <div class="form-group">

                      <textarea class="form-control " name="" id="obs" rows="5">{{$apartment_inspection->observation}}</textarea>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <label for="Name">Status da Vistoria</label>
                    <div class="form-group">

                      <input type="radio" required name="status_conf" id="status1"  {{ $apartment_inspection->approved=='yes'?'checked':'' }} value="liberado">
                      <label for="status1">VISTORIADA E APROVADA</label>
                      <input type="radio" required class="ml-5" name="status_conf" id="status2" {{ $apartment_inspection->approved=='not'?'checked':'' }}
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

@section('plugins.scriptUpdateApartmentInspect', true)
@endsection
