@extends('adminlte::page')
@section('content')
@section('plugins.Select2', true)
@section('plugins.JqueryMaskMoney', true)
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
                    <form name="form" id="form" class="col-12" enctype="multipart/form-data" method="POST">
                        <div class="col-md-2">
                            @csrf
                        </div> <!-- col-md3 -->
                        <div class="col-md-12">
                            <div class="card card-secondary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">FREQUÊNCIA ADMINISTRAÇÃO</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>FUNÇÃO</th>
                                                        <th width="100">TOTAL</th>
                                                        <th width="100">AUSENTE</th>
                                                        <th width="100">EFETIVO</th>
                                                        <th>OBSERVAÇÕES</th>
                                                        <th width="70"></th>
                                                    </tr>

                                                </thead>
                                                <tbody id="body_frequency_adm">
                                                    

                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="6">
                                                            {{-- <button type="button"
                                                                class="btn btn-default col-12  " >Adicionar</button> --}}
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>

                                            
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
                                <div class="card-footer text-center" style="cursor: pointer" id="btn_add_frequency">
                                    <i class="far fa-plus-square"></i>&nbsp;&nbsp;<a id="addItemTopic"
                                        href="javascript:">Adicionar Novo Item</a>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card card-secondary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">FREQUÊNCIA PRODUÇÃO </h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            
                                            <table class="table table-sm mt-2">
                                                <thead>
                                                    <tr>
                                                        
                                                        <th>FUNÇÃO</th>
                                                        <th width="100">TOTAL</th>
                                                        <th width="100">AUSENTE</th>
                                                        <th width="100">EFETIVO</th>
                                                        <th>OBSERVAÇÕES</th>
                                                        <th width="70"></th>
                                                    </tr>

                                                </thead>
                                                <tbody id="body_frequency_prod">
                                                                                                       
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="6">
                                                            {{-- <button  type="button" class="btn btn-default col-12" >Adicionar</button> --}}
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
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
                                <div class="card-footer text-center" style="cursor: pointer" id="btn_add_frequency_prod">
                                    <i class="far fa-plus-square"></i>&nbsp;&nbsp;<a id="addItemTopic"
                                        href="javascript:">Adicionar Novo Item</a>
                                </div>

                                

                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card card-secondary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">SUB-EMPREITEIROS</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>EMPRESA</th>
                                                        <th>FUNÇÃO</th>
                                                        <th width='100'>TOTAL</th>
                                                        <th width='100'>AUSENTE</th>
                                                        <th width='100'>EFETIVO</th>
                                                        <th>OBSERVAÇÕES</th>
                                                        <th width='70' ></th>
                                                    </tr>

                                                </thead>
                                                <tbody id="body_sub" >
                                                    
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td >
                                                            
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>

                                           
                                        </div>
                                    </div>

                                    <div class="overlay-wrapper">
                                        <div class="d-none overlay">
                                            <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                                            <div class="text-bold pt-2">Carregando...</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-center" style="cursor: pointer" id="btn_add_sub">
                                    <i class="far fa-plus-square"></i>&nbsp;&nbsp;<a id="addItemTopic"
                                        href="javascript:">Adicionar Novo Item</a>
                                </div>
                                <!-- /.card-body -->

                                {{-- <div class="card-footer">
                                <button type="submit" id="submit" name="submit"
                                    class="btn btn-secondary float-lg-right"><i class="fas fa-save"></i> Salvar</button>
                            </div> --}}

                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card card-secondary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">EQUIPAMENTOS</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>FORNECEDOR</th>
                                                        <th>DESCRIÇÃO</th>
                                                        <th>INÍCIO</th>
                                                        <th>DEVOLUÇÃO</th>
                                                        <th>SERVIÇO</th>
                                                        <th width='70' ></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="body_equipament">
                                                    
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td >
                                                            
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>

                                           
                                        </div>
                                    </div>

                                    <div class="overlay-wrapper">
                                        <div class="d-none overlay">
                                            <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                                            <div class="text-bold pt-2">Carregando...</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-center" style="cursor: pointer" id="btn_add_equipament">
                                    <i class="far fa-plus-square"></i>&nbsp;&nbsp;<a id="addItemTopic"
                                        href="javascript:">Adicionar Novo Item</a>
                                </div>
                              
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card card-secondary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">ATIVIDADES</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>SETOR</th>
                                                        <th>EQUIPE</th>
                                                        <th width="100">REGISTRO</th>
                                                        <th>DESCRIÇÃO</th>
                                                        <th width="200" >ANEXO</th>
                                                        <th width='70'></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="body_activity">
                                   
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td >
                                                            
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>

                                           
                                        </div>
                                    </div>

                                    <div class="overlay-wrapper">
                                        <div class="d-none overlay">
                                            <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                                            <div class="text-bold pt-2">Carregando...</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-center" style="cursor: pointer" id="btn_add_activity">
                                    <i class="far fa-plus-square"></i>&nbsp;&nbsp;<a id="addItemTopic"
                                        href="javascript:">Adicionar Novo Item</a>
                                </div>
                              
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card card-secondary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">OBSERVAÇÕES</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>SETOR</th>
                                                        <th>DESCRIÇÃO</th>
                                                        <th width="100">REGISTRO</th>
                                                        <th>OBSERVAÇÕES</th>
                                                        <th width="70" ></th>
                                                                                                  
                                                    </tr>
                                                </thead>
                                                <tbody id='body_obs'>
                                                    
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td >
                                                            
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>

                                           
                                        </div>
                                    </div>

                                    <div class="overlay-wrapper">
                                        <div class="d-none overlay">
                                            <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                                            <div class="text-bold pt-2">Carregando...</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-center" style="cursor: pointer" id="btn_add_obs">
                                    <i class="far fa-plus-square"></i>&nbsp;&nbsp;<a id="addItemTopic"
                                        href="javascript:">Adicionar Novo Item</a>
                                </div>
                              
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <a href="{{route('work_diary.index')}}" class="btn btn-secondary mb-2">Voltar</a> 
                            <button  id='btn_submit' type="submit" class="btn btn-success float-right"><i
                                    class="far fa-save"></i>&nbsp;&nbsp;Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>




@section('plugins.scriptCreateWorkDiary', true)
@endsection
