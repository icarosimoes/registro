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
                <li class="breadcrumb-item active"><a href="{{ route('work_diary.index') }}">Lista Diario de Obras</a>
                </li>
                <li class="breadcrumb-item active">Editar Diário de Obras</li>
            </ol>
        </div>

        <input id="id" type="hidden" value="{{ $workDiary->id }}">
        <input id="load_shift_time" type="hidden" value="{{ $workDiary->work_diary_shift_time }}">
        <input id="load_frequency_adm" type="hidden" value="{{ $workDiary->work_diary_frequency_adm }}">
        <input id="load_frequency_prod" type="hidden" value="{{ $workDiary->work_diary_frequency_prod }}">
        <input id="load_sub" type="hidden" value="{{ $workDiary->work_diary_sub }}">
        <input id="load_equipament" type="hidden" value="{{ $workDiary->work_diary_equipament }}">
        <input id="load_activity" type="hidden" value="{{ $workDiary->work_diary_activity }}">
        <input id="load_obs" type="hidden" value="{{ $workDiary->work_diary_obs }}">


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
                                    <h3 class="card-title">TURNO/TEMPO</h3>
                                    <a data-toggle="modal" data-target="#export-pdf"
                                        class="btn btn-warning btn-flat btn-sm float-right mb-0"><i
                                            class="fas fa-file-export"></i>
                                        Exportar</a>

                                </div>
                                <!-- /.card-header -->

                                @php
                                    $percentages = [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100];                                    
                                @endphp
                                <!-- form start -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th width='200'></th>
                                                        <th width='200'>CÉU LIMPO</th>
                                                        <th width='200'>NUBLADO</th>
                                                        <th width='200'>CHUVA</th>
                                                        <th width='200'>IMPRATICÁVEL</th>
                                                    <tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>MANHÃ</td>
                                                        <td>
                                                            <select class="form-control form-control-sm morning ">
                                                                @foreach ($percentages as $percentege)
                                                                    <option value="{{ $percentege }}">
                                                                        {{ $percentege }}%</option>
                                                                @endforeach
                                                                <option value="N">N.A</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-control form-control-sm morning">
                                                                @foreach ($percentages as $percentege)
                                                                    <option value="{{ $percentege }}">
                                                                        {{ $percentege }}%</option>
                                                                @endforeach
                                                                <option value="N">N.A</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-control form-control-sm morning">
                                                                @foreach ($percentages as $percentege)
                                                                    <option value="{{ $percentege }}">
                                                                        {{ $percentege }}%</option>
                                                                @endforeach
                                                                <option value="N">N.A</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-control form-control-sm morning">
                                                                @foreach ($percentages as $percentege)
                                                                    <option value="{{ $percentege }}">
                                                                        {{ $percentege }}%</option>
                                                                @endforeach
                                                                <option value="N">N.A</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>TARDE</td>
                                                        <td>
                                                            <select class="form-control form-control-sm afternoon">
                                                                @foreach ($percentages as $percentege)
                                                                    <option value="{{ $percentege }}">
                                                                        {{ $percentege }}%</option>
                                                                @endforeach
                                                                <option value="N">N.A</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-control form-control-sm afternoon">
                                                                @foreach ($percentages as $percentege)
                                                                    <option value="{{ $percentege }}">
                                                                        {{ $percentege }}%</option>
                                                                @endforeach
                                                                <option value="N">N.A</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-control form-control-sm afternoon">
                                                                @foreach ($percentages as $percentege)
                                                                    <option value="{{ $percentege }}">
                                                                        {{ $percentege }}%</option>
                                                                @endforeach
                                                                <option value="N">N.A</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-control form-control-sm afternoon">
                                                                @foreach ($percentages as $percentege)
                                                                    <option value="{{ $percentege }}">
                                                                        {{ $percentege }}%</option>
                                                                @endforeach
                                                                <option value="N">N.A</option>
                                                            </select>
                                                        </td>

                                                    </tr>
                                                    <tr>
                                                        <td>NOITE</td>
                                                        <td>
                                                            <select class="form-control form-control-sm night">
                                                                @foreach ($percentages as $percentege)
                                                                    <option value="{{ $percentege }}">
                                                                        {{ $percentege }}%</option>
                                                                @endforeach
                                                                <option value="N">N.A</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-control form-control-sm night">
                                                                @foreach ($percentages as $percentege)
                                                                    <option value="{{ $percentege }}">
                                                                        {{ $percentege }}%</option>
                                                                @endforeach
                                                                <option value="N">N.A</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-control form-control-sm night">
                                                                @foreach ($percentages as $percentege)
                                                                    <option value="{{ $percentege }}">
                                                                        {{ $percentege }}%</option>
                                                                @endforeach
                                                                <option value="N">N.A</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-control form-control-sm night">
                                                                @foreach ($percentages as $percentege)
                                                                    <option value="{{ $percentege }}">
                                                                        {{ $percentege }}%</option>
                                                                @endforeach
                                                                <option value="N">N.A</option>
                                                            </select>
                                                        </td>
                                                    </tr>

                                                </tbody>

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
                                {{-- <div class="card-footer text-center" style="cursor: pointer" id="btn_add_frequency">
                                    <i class="far fa-plus-square"></i>&nbsp;&nbsp;<a id="addItemTopic"
                                        href="javascript:">Adicionar Novo Item</a>
                                </div> --}}

                            </div>
                        </div>

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
                                                        <th></th>
                                                    </tr>

                                                </thead>
                                                <tbody id="body_frequency_adm">


                                                </tbody>
                                                <tfoot>

                                                    <tr>
                                                        <td></td>
                                                        <th id="sumTotalAdm">0</th>
                                                        <th id="sumAbsentAdm">0</th>
                                                        <th id="sumEffectiveAdm">0</th>
                                                        <td></td>
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
                                                        <th></th>
                                                    </tr>

                                                </thead>
                                                <tbody id="body_frequency_prod">

                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td></td>
                                                        <th id="sumTotalProd">0</th>
                                                        <th id="sumAbsentProd">0</th>
                                                        <th id="sumEffectiveProd">0</th>
                                                        <td></td>
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
                                <div class="card-footer text-center" style="cursor: pointer"
                                    id="btn_add_frequency_prod">
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
                                                        <th></th>
                                                    </tr>

                                                </thead>
                                                <tbody id="body_sub">

                                                </tbody>
                                                <tfoot>

                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <th id="sumTotalSub">0</th>
                                                        <th id="sumAbsentSub">0</th>
                                                        <th id="sumEffectiveSub">0</th>
                                                        <td></td>
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
                                                        <th></th>

                                                    </tr>
                                                </thead>
                                                <tbody id="body_equipament">

                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td>

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
                                                        <th>DESCRIÇÃO</th>
                                                        <th width="200">ANEXO</th>
                                                        <th width="50"></th>
                                                        <th width="100"></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="body_activity">

                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td>

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
                                                        <th></th>

                                                    </tr>
                                                </thead>
                                                <tbody id='body_obs'>

                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td>

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
                            <a href="#" class="btn btn-secondary mb-2">Cancelar</a>
                            <button type="submit" class="btn btn-success float-right"><i
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

<!-- Modal export pdf-->
<div class="modal fade" id="export-pdf" tabindex="-1" role="dialog" aria-labelledby="export-pdf"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="export-pdf">Por favor, insira uma descrição ao arquivo.</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="inputName" class="form-group">
                    <label for="titleExport">Nome</label>
                    <input type="text" class="form-control" name="titleExport" id="titleExport">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i
                        class="far fa-times-circle"></i> Sair</button>
                <button type="button" id="btnNext" target="_blank" class="btn btn-primary">Continuar <i
                        class="fas fa-forward"></i></button>
                <a type="button" href="" id="btnExport" target="_blank" class="btn btn-primary d-none"><i
                        class="fas fa-file-export"></i> Exportar</a>
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
                <button type="button"
                    onclick="javascript:window.open('{{ route('occurrence.create') }}', '_blank');"
                    class="btn btn-info float-left" data-dismiss="modal"><i class="fas fa-plus"></i> Novo
                    Registro</button>
                {{-- <button type="button" data-toggle='modal' data-target='#ModalNewOcurrence' class="btn btn-info float-left"
                data-dismiss="modal"><i class="fas fa-plus"></i> Novo Registro</button> --}}
                <button type="button" id="buttonOccurrence" name="buttonOccurrence"
                    class="btn btn-primary float-md-right buttonOccurrence"><i class="fas fa-hand-pointer"></i>
                    Selecionar</button>
            </div>
        </div>
    </div>
</div> <!-- / Modal selecionar ocorrência -->


@section('plugins.scriptUpdateWorkDiary', true)
@endsection
