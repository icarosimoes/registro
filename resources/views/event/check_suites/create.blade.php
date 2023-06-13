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
                <li class="breadcrumb-item active">Nova Conferência</li>
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
                                <h3 class="card-title">Nova Conferência</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="Name">Data</label>
                                            <input type="text" class="form-control" id="name" placeholder=""
                                                required>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="Name">Suite</label>
                                            <input type="text" class="form-control" id="name" placeholder=""
                                                required>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="Name">Inspecionado por</label>
                                            <input type="text" class="form-control" id="name" placeholder=""
                                                required>
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <table class="table table-sm ">
                                        <thead>
                                            <tr>
                                                <td>ITEM</td>
                                                <td>CONFERÊNCIA DAS SUÍTES</td>
                                                <td>AVALIAÇÃO</td>
                                                <td>REGISTRO</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>PORTA DA ENTRADA, Nº DO QUARTO E ARCO DA PORTA ESTÃO LIMPOS?</td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>PAVIMENTO ASPIRADO, PISO SEM MANCHAS, AROMATIZADO E REJUNTE LIMPO?</td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td>AS ESCADAS E GARAGEM ESTÃO LIMPAS?	</td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>4</td>
                                                <td>BOM FUNCIONAMENTO DAS LUZES, INTERRUPTORES E TOMADAS?</td>
                                                <td>
                                                    <select class="form-control" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>5</td>
                                                <td>TEMPERATURA AMENA DO QUARTO? LIGAR AR CONDICIONADO.</td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>6</td>
                                                <td>TELEFONE ESTÁ COM FUNCIONAMENTO PLENO E LIMPO?</td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>7</td>
                                                <td>SOFÁS, CAMAS E CABECEIRAS LIMPAS?</td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>8</td>
                                                <td>TELEVISÃO LIMPA E FUNCIONANDO?</td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>9</td>
                                                <td>MINI BAR LIMPO POR FORA E POR DENTRO?</td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>10</td>
                                                <td>MATERIAL DE INFORMAÇÃO (CARDÁPIOS) COMPLETOS E EM BOM ESTADO?</td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>11</td>
                                                <td>CONFERIU OS ITENS DO FRIGOBAR? </td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>12</td>
                                                <td>CONFERIU OS ITENS DO MINIBAR? </td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>13</td>
                                                <td>CONFERIU OS ITENS DO SEX SHOP? </td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>14</td>
                                                <td>CONFERIU OS UTENSÍLIOS QUE PRECISAM ESTAR NA SUÍTE?</td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>15</td>
                                                <td>02 TRAVESSEIROS COM FRONHAS E 02 LENÇÓIS? LIMPOS E EM BOM ESTADO?  </td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>16</td>
                                                <td>PRESENÇA DE SUJEIRAS, MANCHAS, DESBOTAMENTOS, BURACOS, INFILTRAÇÃO OU MUDANÇA NA COR DAS PINTURAS? </td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>17</td>
                                                <td>CONFERIU AS AUTOMATIZAÇÕES (PAINEL DE COMANDO)?	</td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>18</td>
                                                <td>OS MOBILIÁRIOS LIMPOS E EM BOM ESTADO DE USO? </td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>19</td>
                                                <td>O PAPEL DE PAREDE ESTÁ LIMPO E EM BOM ESTADO?</td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>20</td>
                                                <td>OS VIDROS ESTÃO LIMPOS E SEM DANOS?	</td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>21</td>
                                                <td>OS UTENSÍLIOS COMO BANDEJAS, COPOS, ESTÃO DEVIDAMENTE LIMPOS?</td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>22</td>
                                                <td>AS EMBALAGENS DOS COMESTÍVEIS ESTÃO LIMPAS, SEM POEIRA?	</td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>23</td>
                                                <td>LACRE DE HIGIENIZAÇÃO DO VASO SANITÁRIO CONFORME TREINAMENTO DA ABmoteis?	</td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>24</td>
                                                <td>ESPELHO LIMPO E EM BOM ESTADO?	</td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>25</td>
                                                <td>CONFERIR TEMPERATURA DA ÁGUA DOS CHUVEIROS E HIDROMASSAGEM?	</td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>26</td>
                                                <td>CONFERIU OS ITENS DE CONSUMO DO BANHEIRO?</td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>27</td>
                                                <td>2 TOALHAS DE BANHO E 1 TAPETE? LIMPOS E EM BOM ESTADO?	</td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>28</td>
                                                <td>SECADOR FUNCIONANDO?	</td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>29</td>
                                                <td>AS LOUÇAS E METAIS DO BANHEIRO ESTÃO LIMPOS E EM BOM USO?	</td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>30</td>
                                                <td>BANHEIRO AROMATIZADO?</td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="item" id="">
                                                        <option value="sim">SIM</option>
                                                        <option value="nao">NÃO</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                            
                                        </tbody>
                                    </table>

                                </div>
                                <div class="row">
                                    <div class="col">
                                        <label for="Name">Observaçôes</label>
                                        <div class="form-group">
                                            
                                            <textarea class="form-control " name="" id=""  rows="5"></textarea>
                                        </div>  
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <label for="Name">Status da Conferência</label>
                                        <div class="form-group">
                                            
                                            <input type="radio" name="status_conf" value="liberado">    
                                            <label for="">CONFERIDA E LIBERADA</label>
                                            <input type="radio" class="ml-5" name="status_conf" value="bloqueado">                                            
                                            <label for="" >CONFERIDA E BLOQUEADA</label>                                        
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
@section('plugins.scriptCreateLocal', true)
@endsection
