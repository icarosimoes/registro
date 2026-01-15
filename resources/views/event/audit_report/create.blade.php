@extends('adminlte::page')
@section('content')
@section('plugins.Select2', true)
@section('plugins.JqueryMask', true)
@section('plugins.JqueryMaskMoney', true)
@section('plugins.LoaderTemp', true)
<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-12">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('audit_report.index') }}">Lista de Relatório de
                        Auditoria</a></li>
                <li class="breadcrumb-item active">Novo Relatório de Auditoria</li>
            </ol>
        </div>
        <div class="col-md-12">
            <div class="container-fluid">
                {{-- <div id="alertError" class="alert alert-danger alert-dismissible fade show d-none" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> --}}
                {{-- <div class="row"> --}}
                <form name="formShiftReport" id="formShiftReport" enctype="multipart/form-data" method="POST">
                    {{-- cabeçalho --}}
                    <div class="col-md-12">
                        <div class="card card-secondary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Novo Relatório de Turno</h3>
                            </div>
                            <div class="card-body">
                              <div class="row form-group">
                                  <div class="col-3">
                                      <label for="Name">Data:</label>
                                      <input type="date" class="form-control" name="date" id="date" >
                                  </div>
                              </div>
                                
                              <div class="row form-group">

                                    <div class="col">
                                        <label for="Name">Ocupação</label>
                                        <input class="form-control mask_float" value="0,00" type="text" name="ocupacao" id="occupation">
                                    </div>
                                    <div class="col">
                                        <label for="Name">Diaria média:</label>
                                        <input type="text" class="form-control mask_integer" value="0" name="diaria_medias"
                                            id="average_daily" placeholder="">
                                    </div>
                                    <div class="col">
                                        <label for="Name">Hóspedes:</label>
                                        <input type="text" class="form-control mask_integer" value="0" name="inputQuantity"
                                            id="guests">
                                    </div>
                                    <div class="col">
                                        <label for="Name">UH'S:</label>
                                        <input class="form-control mask_integer" value="0" type="text" name="uhs" id="uh">
                                    </div>
                                    <div class="col">
                                        <label for="Name">Manutenção:</label>
                                        <input type="text" class="form-control mask_integer" value="0" name="manutencao" id="maintenance_apartment">
                                    </div>
                                    <div class="col">
                                        <label for="Name">Limpeza:</label>
                                        <input type="text" class="form-control mask_integer" value="0" name="limpeza" id="cleaning">
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>{{-- end cabeçalho --}}

                    {{-- SHOW COM GARANTIA --}}
                    <div class="col-md-12">
                        <div class="card card-secondary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">NO SHOW COM GARANTIA: </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <table name="DataTableUser" id="DataTableUser" class="table table-sm tablenotstyle">
                                        <thead>
                                            <tr>
                                                <th>Nº Reserva</th>
                                                <th>Empresa/Agencia/Titular</th>
                                                <th>Nome do Pax</th>
                                                {{-- <th class="w-20">Ações</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody id="table1">

                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div style="cursor:poiter" class="card-footer text-center" id="addTable1">
                                <i class="far fa-plus-square"></i>&nbsp;&nbsp;<a 
                                    href="javascript:">Adicionar Novo
                                    Item</a>

                            </div>
                        </div>
                    </div>{{-- end FREQUÊNCIA --}}

                    {{-- EXTRA --}}
                    <div class="col-md-12">
                        <div class="card card-secondary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">CORTESIA / PERMUTA / USO DA CASA: </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <table name="DataTableUser" id="DataTableUser" class="table table-sm tablenotstyle">
                                        <thead>
                                            <tr>
                                                <th>Empresa / Agencia / Titular </th>
                                                <th>Nome do Pax </th>
                                                {{-- <th class="w-20">Ações</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody id="table2">
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div style="cursor:pointer" class="card-footer text-center" id="addTable2">
                                <i class="far fa-plus-square"></i>&nbsp;&nbsp;<a 
                                    href="javascript:">Adicionar Novo
                                    Item</a>

                            </div>
                        </div>
                    </div>{{-- end extra --}}

                    {{-- MANUTENÇÃO --}}
                    <div class="col-md-12">
                        <div class="card card-secondary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">NO SHOW SEM GARANTIA:</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <table name="DataTableUser" id="DataTableUser" class="table table-sm tablenotstyle">
                                        <thead>
                                            <tr>
                                                <th>Nº Reserva</th>
                                                <th>Empresa/Agencia/Titular</th>
                                                <th>Nome do Pax</th>

                                            </tr>
                                        </thead>
                                        <tbody id="table3">
                                            {{-- <tr>
                                                        <td><input type="text" class="form-control form-control-sm"></td>
                                                        <td>
                                                            <select name="" class="form-control form-control-sm" id="">
                                                                <option value="">BLOQUEADO</option>
                                                                <option value="">DISPONÍVEL</option>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" class="form-control form-control-sm"></td>
                                                        <td><input type="text" class="form-control form-control-sm"></td>
                                                        <td>
                                                            <a href="#" data-toggle="tooltip" data-placement="top" title="Excluir"
                                                            class="btn btn-sm btn-default"><i class="fas fa-trash"></i></a>
                                                            <a href="#" data-toggle="tooltip" data-placement="top" title="Selecionar Registro"
                                                            class="btn btn-sm btn-default"><i class="fas fa-filter"></i></a>
                                                            <small class="badge badge-success"><i class="far fa-registered"></i> 25</small>
                                                        </td>
                                                    </tr> --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div style="cursor:pointer" class="card-footer text-center" id="addTable3">
                                <i class="far fa-plus-square"></i>&nbsp;&nbsp;<a 
                                    href="javascript:">Adicionar Novo
                                    Item</a>

                            </div>
                        </div>
                    </div>{{-- end extra --}}
                    <div class="col-md-12">
                        <div class="card card-secondary card-outline">
                            <div class="card-body">

                                <label for="Name">Observação</label>
                                <textarea class="form-control" rows="3" name="observation" id="obs">
                          
                          </textarea>
                                <div class="form-group">
                                    <label for="Name">A&B:</label>
                                    <input class="form-control" type="text" id="AB">
                                </div>
                                <div class="form-group">
                                    <label for="Name">RECEPÇÃO:</label>
                                    <input class="form-control" type="text" id="reception">
                                </div>
                                <div class="form-group">
                                    <label for="Name">RESERVAS:</label>
                                    <input class="form-control" type="text" id="reservations">
                                </div>
                                <div class="form-group">
                                    <label for="Name">GOVERNANÇA:</label>
                                    <input class="form-control" type="text" id="governance">
                                </div>
                                <div class="form-group">
                                    <label for="Name">MANUTENÇÃO:</label>
                                    <input class="form-control" id="maintenance" type="text" ">
                                </div>
                                <div class="form-group">
                                    <label for="Name">TI:</label>
                                    <input class="form-control" type="text" id="ti"> 
                                </div>
                                <div class="form-group">
                                    <label for="Name">SEGURANÇA:</label>
                                    <input class="form-control" type="text" id="security">
                                </div>
                            </div>
                        </div>


                        <div class="col-sm-12">
                            <a href="#" class="btn btn-secondary mb-2">Cancelar</a>
                            <button type="button" id="btn_save" class="btn btn-success float-right"><i
                                    class="far fa-save"></i>&nbsp;&nbsp;Salvar</button>
                        </div>


                </form>


            </div>
            <div class="overlay-wrapper">
                <div class="d-none overlay">
                    <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                    <div class="text-bold pt-2">Carregando...</div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@section('plugins.scriptAuditReportCreate', true)
@endsection
