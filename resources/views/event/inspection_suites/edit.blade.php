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
                                <input type="hidden" value="{{$inspectionSuite->inspection_suite_items}}">
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
                                            <input type="hidden" id="inspection_suite_items" value="{{ json_encode($inspectionSuite->inspection_suite_items) }}">    
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
                                        <tbody id="itens_inspection_suite">
                                           {{-- Itens carregados via javascript --}} 
                                            
                                        </tbody>
                                    </table>

                                </div>
                                <div class="row mb-4" >
                                  <div class="col">
                                    <button type="button" class="btn btn-primary" id="add_inspection_item"><i class="fas fa-plus mr-1"></i>Adicionar Item</button>
                                  </div>
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

<!-- Modal confirmar deleção-->
<div class="modal fade" id="ModalConfirmDelete" tabindex="-1" role="dialog" aria-labelledby="modalConfirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmDeleteLabel">Excluir item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <input type="text" id="deleteItemIndex" class="d-none">
            <div class="modal-body">
                Tem certeza que deseja excluir este item?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Excluir</button>
            </div>
        </div>
    </div>
</div>
<!-- / Modal confirmar deleção -->

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
