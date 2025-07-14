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
                      <input type="text" class="form-control" id="owner" placeholder="" required>
                    </div>
                  </div>
                  <div class="col">
                    <div class="form-group">
                      <label for="Name">Unidade</label>
                      <input type="text" class="form-control" id="unit" placeholder="" required>
                    </div>
                  </div>


                  <div class="col">
                    <div class="form-group">
                      <label for="Name">Inspecionado por</label>
                      <input type="text" class="form-control" id="inspected_by" placeholder="" required>
                    </div>
                  </div>
                  <div class="col">
                    <div class="form-group">
                      <label for="Name">Data</label>
                      <input type="date" class="form-control" id="inspection_date" placeholder="" required>
                    </div>
                  </div>

                </div>
                <div class="row">
                  <table style="font-size: 13px" class="table table-sm ">
                    <thead>
                      <tr >
                        <td>ÁREA VISTORIADA</td>
                        <td>SERVIÇOS</td>
                        <td>ITENS DE VERIFICAÇÃO</td>
                        <td>AVALIAÇÃO</td>
                        <td></td>
                      </tr>
                    </thead>
                    <tbody>
                      <tr style="background:#ececec" >
                        <td rowspan="8" style="background:#ececec">SUITE</td>
                        <td>
                          PISOS
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, ESQUADRO, NÍVEL, REJUNTAMENTO E LIMPEZA
                        </td>

                        <td>
                          <input data-ref='100'  type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-100">
                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='101' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-101">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input type="text" data-ref='102' style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-102">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='103' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-103">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='104' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-104">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='105' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-105">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='106' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-106">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='107' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-107">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
                        </td>
                      </tr>


											{{-- SANITARIO SUITE --}}
											 <tr style="background: lightgray">
                        <td rowspan="10" style="background: lightgray">SANITÁRIO SUITE</td>
                        <td>
                          PISOS
                        </td>
                        <td>
                          PLANEZA, HOMOGENEIDADE, ESQUADRO, NÍVEL, REJUNTAMENTO E LIMPEZA
                        </td>

                        <td>
                          <input data-ref='200' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-200">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='201' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-201">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='202' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-202">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='203' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-203">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='204' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-204">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='205' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-205">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='206' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-206">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='207' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-207">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='208' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-208">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='209' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-209">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='300' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-300">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='301' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-301">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input  data-ref='302' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-302">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='303' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-303">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='304' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-304">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='305' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-305">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='306' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-306">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='307' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-307">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='400' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-400">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='401' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-401">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='402' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-402">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='403' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-403">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='404' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-404">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input  data-ref='405' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-405">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='406' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-406">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='407' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-407">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='408' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-408">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='409' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-409">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='500' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-500">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='501' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-501">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='502' style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-502">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='503' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-503">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='504' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-504">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='505' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-505">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='506' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-506">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='600' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-600">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='601' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-601">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='602' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-602">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='603' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>

                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-603">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='604' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-604">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
                          </select>
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
                          <input data-ref='605' type="text" style="width: 100px" class="form-control form-control-sm"
                            name="register">
                        </td>
                        <td>
                          <select required class="form-control form-control-sm" name="item" id="approved-605">

                            <option value="yes">SIM</option>
                            <option value="not">NÃO</option>
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

@section('plugins.scriptCreateApartmentInspect', true)
@endsection
