@extends('adminlte::page')
@section('content')
@section('plugins.Select2', true)
@section('plugins.JqueryMask', true)
<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-12">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('check_suite.index') }}">Lista de Conferência </a></li>
                <li class="breadcrumb-item active">Editar conferência das suites</li>
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
                                <h3 class="card-title">Editar conferência das suites</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="Name">Data</label>
                                            <input type="datetime-local" class="form-control" id="date" placeholder="" value="{{$inspectionSuite->date}}"
                                                required>
                                            <input type="hidden" id="check_suite_id" value="{{ $inspectionSuite->id }}">    
                                            <input type="text" id="inspection_suite_items" value="{{ json_encode($inspectionSuite->inspection_suite_items) }}">    
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="Name">Suite</label>
                                            <select class="form-control" name="" id="local" required >
                                               <option value="{{$inspectionSuite->local->id }}">{{ $inspectionSuite->local->id.' - '.$inspectionSuite->local->name }}</option>
                                                
                                            </select>
                                            {{-- <input type="text" class="form-control" id="suite" value="{{$inspectionSuite->suite}}" placeholder="" --}}
                                                
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="Name">Inspecionado por</label>
                                            <select class="form-control" name="" id="user" required >
                                                <option value="{{$inspectionSuite->user->id }}">{{ $inspectionSuite->user->id.' - '.$inspectionSuite->user->name }}</option>
                                                 
                                             </select>
                                            {{-- <input type="text" class="form-control" id="inspected_by" placeholder="" required value="{{$inspectionSuite->inspected_by}}"> --}}
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="Name">Camareira</label>
                                            <input class="form-control" type="text" value="{{$inspectionSuite->maid}}" id="maid">
                                            {{-- <input type="text" class="form-control" id="inspected_by" placeholder="" required value="{{$inspectionSuite->inspected_by}}"> --}}
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <table style="font-size: 13px" class="table table-sm ">
                                        <thead>
                                            <tr>
                                                <td width='50'>ITEM</td>
                                                <td  width='450'>CONFERÊNCIA DAS SUÍTES</td>
                                                <td width='100'>AVALIAÇÃO</td>
                                                <td width='500'>REGISTRO</td>
                                                <td></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td><p>LIGAR O AR CONDICIONADO NO INÍCIO DA VISTORIA. FUNCIONAMENTO PLENO ?</p></td>
                                                <td>
                                                    <select required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a type="button" data-item="1" class="btn btn-sm btn-secondary filter "><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-1">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                                
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>FUNCIONAMENTO PLENO DAS FECHADURAS (PRINCIPALMENTE MAÇANETAS) ?</td>
                                                <td>
                                                    <select  required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a data-item="2" class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-2">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td>FUNCIONAMENTO PLENO DAS PORTA (PRINCIPALMENTE NA PARTE INFERIOR) ?</td>
                                                <td>
                                                    <select required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a  data-item="3" class="btn btn-sm btn-secondary filter "><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-3">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>4</td>
                                                <td>FUNCIONAMENTO PLENO DAS DOBRADIÇAS ?</td>
                                                <td>
                                                    <select required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a data-item="4" class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-4">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>5</td>
                                                <td>OS PISOS E REJUNTE ESTÃO SEM PRESENÇA DE MANCHAS, RACHADURAS OU REJUNTE SOLTANDO?</td>
                                                <td>
                                                    <select  required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a data-item="5" class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-5">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>6</td>
                                                <td>PINTURA DAS PAREDES, PORTAS, AO REDOR DE TOMADAS E INTERRUPTORES  ESTÃO SEM MANCHAS, BURACOS, INFILTRAÇÃO ?</td>
                                                <td>
                                                    <select required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a data-item="6" class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-6">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>7</td>
                                                <td>FUNCIONAMENTO PLENO DE TODA ILUMINAÇÃO (TROCA DAS LÂMPADAS E REATORES) ?</td>
                                                <td>
                                                    <select required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a data-item="7" class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-7">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>8</td>
                                                <td>FUNCIONAMENTO PLENO E LIMPEZA DOS INTERRUPTORES E TOMADAS ?</td>
                                                <td>
                                                    <select required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a  data-item="8"class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-8">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>9</td>
                                                <td>FUNCIONAMENTO PLENO DA TELEVISÃO E DOS CANAIS ?</td>
                                                <td>
                                                    <select required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a data-item="9" class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-9">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>10</td>
                                                <td>FUNCIONAMENTO PLENO DO PAINEL DE CONTROLE ( MANUTENÇÃO, TROCA DE BOTÕES OU MANUTENÇÃO NA INSTALAÇÃO ELÉTRICA) ?</td>
                                                <td>
                                                    <select required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a data-item="10" class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-10">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>11</td>
                                                <td>FUNCIONAMENTO PLENO E LIMPEZA DO TELEFONE ?</td>
                                                <td>
                                                    <select required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a data-item="11" class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-11">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>12</td>
                                                <td>FUNCIONAMENTO PLENO DA CAMPAINHA?</td>
                                                <td>
                                                    <select required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a data-item="12" class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-12">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>13</td>
                                                <td>BOM ESTADO DE CONSERVAÇÃO E ASPECTO VISUAL DA ROUPA DE CAMA ? </td>
                                                <td>
                                                    <select required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a data-item="13" class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-13">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>14</td>
                                                <td>BOM ESTADO DE  CONSERVAÇÃO E ASPECTO VISUAL DOS MOBILIÁRIOS?</td>
                                                <td>
                                                    <select required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a data-item="14" class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-14">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>15</td>
                                                <td>BOM ESTADO  DE CONSERVAÇÃO DO PAPEL DE PAREDE ? ( INDICAR QUANDO HOUVER NECESSIDADE DA TROCA)  </td>
                                                <td>
                                                    <select required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a data-item="15" class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-15">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>16</td>
                                                <td>BOM ESTADO DE CONSERVAÇÃO E  USO DAS CORTINAS ? </td>
                                                <td>
                                                    <select required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a data-item="16" class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-16">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>17</td>
                                                <td>BOM ESTADO DE CONSERVAÇÃO E  USO DO ESPELHO ?	</td>
                                                <td>
                                                    <select required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a data-item="17" class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-17">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>18</td>
                                                <td>ESTÁ OCORRENDO INFILTRAÇÃO OU CONDENSAÇÃO DO AR CONDICIONADO ?</td>
                                                <td>
                                                    <select required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a data-item="18" class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-18">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>19</td>
                                                <td>APÓS TODAS AS CONFERÊNCIAS, DESLIGAR O AR CONDICIONADO E ANALISAR SEU DESEMPENHO.  FUNCIONAMENTO PLENO ?</td>
                                                <td>
                                                    <select required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a data-item="19" class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-19">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>20</td>
                                                <td>FUNCIONAMENTO PLENO DO AQUECIMENTO DA ÁGUA DO CHUVEIRO E DA HIDROMASSAGEM?	</td>
                                                <td>
                                                    <select required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a  data-item="20"class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-20">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>21</td>
                                                <td>FEITA LIMPEZA DOS CHUVEIROS?</td>
                                                <td>
                                                    <select required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a data-item="21" class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-21">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>22</td>
                                                <td>ACABAMENTO DO FORRO SEM PROBLEMAS (PRINCIPALMENTE ONDE TEM UMIDADE)?</td>
                                                <td>
                                                    <select required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a data-item="22" class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-22">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>23</td>
                                                <td>BOM ESTADO DE CONSERVAÇÃO DAS SABONETEIRAS, LIXEIRA, REGISTROS, TORNEIRAS E DOS DEMAIS METAIS DO BANHEIRO?</td>
                                                <td>
                                                    <select required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a data-item="23" class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-23">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>24</td>
                                                <td>CORRETO FUNCIONAMENTO DOS RALOS?</td>
                                                <td>
                                                    <select required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a data-item="24" class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-24">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>25</td>
                                                <td>CORRETO FUNCIONAMENTO DA DESCARGA DOS VASOS SANITÁRIOS?</td>
                                                <td>
                                                    <select required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a data-item="25" class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-25">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>26</td>
                                                <td>VEDAÇÃO AO REDOR DOS VASOS SANITÁRIOS?</td>
                                                <td>
                                                    <select required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a data-item="26" class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-26">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>27</td>
                                                <td>INFILTRAÇÃO OU MANCHAS DE MOFO NOS BANHEIROS ?</td>
                                                <td>
                                                    <select  required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a data-item="27" class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-27">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr>
                                            {{-- <tr>
                                                <td>28</td>
                                                <td>SECADOR FUNCIONANDO?	</td>
                                                <td>
                                                    <select required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input  data-item="28"type="hidden" name="occurrences_id" id="item-28">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>29</td>
                                                <td>AS LOUÇAS E METAIS DO BANHEIRO ESTÃO LIMPOS E EM BOM USO?	</td>
                                                <td>
                                                    <select required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a data-item="29" class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-29">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered" >0</i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>30</td>
                                                <td>BANHEIRO AROMATIZADO ?</td>
                                                <td>
                                                    <select required class="form-control form-control-sm" name="item" id="">
                                                        
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="register"></td>
                                                <td class="">
                                                    <a data-item="30" class="btn btn-sm btn-secondary filter"><i class="fas fa-filter"></i></a>
                                                    <input type="hidden" name="occurrences_id" id="item-30">
                                                    <a class="btn btn-sm btn-success d-none show_occurence_id "><i class="far fa-registered">0</i></a>
                                                </td>
                                            </tr> --}}
                                            
                                        </tbody>
                                    </table>

                                </div>
                                <div class="row">
                                    <div class="col">
                                        <label for="Name">Observaçôes</label>
                                        <div class="form-group">
                                            
                                            <textarea class="form-control " name="" id="obs"  rows="5" >{{$inspectionSuite->obs}}</textarea>
                                        </div>  
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <label for="Name">Status da Conferência</label>
                                        <div class="form-group">
                                            
                                            <input type="radio" {{$inspectionSuite->status=='liberado'?'checked':''}} required name="status_conf" id="status1" value="liberado">    
                                            <label for="status1">CONFERIDA E LIBERADA</label>
                                            <input type="radio" {{$inspectionSuite->status=='liberado'?'':'checked'}} required class="ml-5" name="status_conf" id="status2" value="bloqueado">                                            
                                            <label for="status2" >CONFERIDA E BLOQUEADA</label>                                        
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
                <select class="form-control  isdfdOccurence" id="idOccurence"
                    name="userRegistered" style="width: 100%;">
                    {{-- @foreach ($ocurrences as $ocurrence)
                        <option value="{{ $ocurrence->id }}">{{ "Código: ".$ocurrence->id." - ".$ocurrence->title }}
                        </option>
                    @endforeach --}}
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="javascript:window.open('{{ route('occurrence.create') }}', '_blank');" class="btn btn-info float-left"
                data-dismiss="modal"><i class="fas fa-plus"></i> Novo Registro</button>
            {{-- <button type="button" data-toggle='modal' data-target='#ModalNewOcurrence' class="btn btn-info float-left"
                data-dismiss="modal"><i class="fas fa-plus"></i> Novo Registro</button> --}}
            <button type="button" id="buttonOccurrence" name="buttonOccurrence"
                class="btn btn-primary float-md-right buttonOccurrence"><i class="fas fa-hand-pointer"></i> Selecionar</button>
        </div>
    </div>
</div>
</div> <!-- / Modal selecionar ocorrência -->

@section('plugins.scriptUpdateInspectionSuite', true)
@endsection
