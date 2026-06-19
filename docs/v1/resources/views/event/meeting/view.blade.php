@extends('adminlte::page')
@section('content')
@section('plugins.Select2', true)
@section('plugins.Datatables', true)
{{-- @section('plugins.JqueryMask', true) --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-12">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('meeting.list') }}">Lista de Reuniões</a>
                </li>
                <li class="breadcrumb-item active">Visualizar Reunião</li>
            </ol>
        </div>
        <div class="col-sm-12">
            <div id="alertError" class="alert alert-danger alert-dismissible fade show d-none" role="alert">

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="container-fluid">
                <!-- form start -->
                <form name="formMeetingEdit" id="formMeetingEdit" enctype="multipart/form-data" method="POST">

                    <div class="col-sm-12">
                        <div class="card card-secondary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Visualizar Reunião</h3>
                                <a data-toggle="modal" data-target="#export-pdf"
                                    class="btn btn-warning btn-flat btn-sm float-right mb-0"><i
                                        class="fas fa-file-export"></i>
                                    Exportar</a>
                            </div><!-- /.card-header -->

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-3">
                                        <label for="datetime">Data e Hora</label>
                                        <input value="{{ $meeting->datetime }}" id="datetime" class="form-control" disabled
                                            type="datetime-local" required>
                                    </div>
                                    <div class="col-3">
                                        <label for="local">Local</label>
                                        <input value="{{ $meeting->local }}" id="local" class="form-control" disabled
                                            type="text" required>
                                    </div>
                                    <div class="col-3">
                                        <label for="status">Status</label>
                                        <select name="" id="status" class="form-control" disabled
                                            data-value="{{ $meeting->status }}">
                                            <option value="1">Em Aberto</option>
                                            <option value="2">Convocado</option>
                                            <option value="3">Realizada</option>
                                        </select>
                                    </div>

                                </div>
                            </div>
                            {{-- <div class="card-footer text-center">
                                    <i class="far fa-plus-square"></i>&nbsp;&nbsp;<a id="addItemTopic"
                                        href="javascript:">Adicionar Novo Item</a>
                                </div> --}}
                        </div>
                    </div>
                    <div id="blocked">
                        <div class="col-sm-12">
                            <div class="card card-secondary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Pauta</h3>
                                </div><!-- /.card-header -->

                                <div class="card-body">
                                    <div class="card-body table-responsive p-0">
                                        <table class="table table-sm  table-valign-middle tablenotstyle">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th style="width: 10%"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyItemTopic">
                                                <input type="hidden" id="meeting_subjects" name="meeting_subjects"
                                                    value="{{ $meeting_subjects }}">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                {{-- <div class="card-footer text-center">
                                    <i class="far fa-plus-square"></i>&nbsp;&nbsp;<a id="addItemTopic"
                                        href="javascript:">Adicionar Novo Item</a>
                                </div>  --}}
                            </div>
                        </div>

                        {{-- CARD PARTICIPANTE E CONVIDADOS --}}
                        <div class="col-sm-12">
                            <div class="card card-secondary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Participantes</h3>
                                    {{-- <div class="card-tools">
                                        <span class="badge badge-danger">8 Participantes</span>
                                    </div> --}}
                                </div>
                                <!-- /.card-header -->
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="card-header">
                                            <h3 class="card-title">Cadastrados</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row" id="registered_users">
                                                <input type="hidden" id="meeting_id" name="meeting_id"
                                                    value="{{ $meeting->id }}">
                                                <input type="hidden" id="meeting_registered_participants"
                                                    name="meeting_registered_participants"
                                                    value="{{ $meeting_registered_participants }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="card-header">
                                            <h3 class="card-title">Convidados</h3>
                                        </div>

                                        <div class="card-body">
                                            <div class="row" id="invited_users">
                                                <input type="hidden" id="meeting_invited_participants"
                                                    name="meeting_invited_participants"
                                                    value="{{ $meeting_invited_participants }}">
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <!-- /.card-body -->
                                {{-- <div class="card-footer text-center">
                                    <i class="far fa-plus-square"></i>&nbsp;&nbsp;<a data-toggle="modal"
                                        data-target="#ModalAddParticipant" href="javascript:">Adicionar Novo
                                        Participantes</a>

                                </div> --}}
                                <!-- /.card-footer -->
                            </div>
                        </div>



                        {{-- INICIAR REUNIAO  --}}
                        <div class="col-sm-12">
                            <div class="card card-secondary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Iniciar reunião</h3>
                                </div><!-- /.card-header -->

                                <div class="card-body">
                                    <div class="row">
                                        <div class="col text-center">
                                            <button type="button"
                                                {{ $meeting->start_meeting == null ? '' : 'disabled' }}
                                                id='btn_start_meeting'
                                                class="btn btn-success">{{ $meeting->start_meeting == null ? 'Iniciar Reunião' : 'Reunião Iniciada: ' . date('d/m/Y - H:i', strtotime($meeting->start_meeting)) }}</button>
                                        </div>
                                    </div>
                                    <div id="list_meeting">
                                        @if ($meeting->start_meeting)

                                            @foreach ($meeting_subjects as $subjects)
                                                <div class="row mt-3">
                                                    <div class="col">
                                                        <label for="">ATA</label>
                                                        <input class="form-control" type="text" readonly
                                                            value="{{ $subjects->subject }}">
                                                    </div>
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col">
                                                        <label for="">DELIBERAÇÃO</label>
                                                        <textarea data-id="{{ $subjects->id }}" class="form-control obs_subject" name="" cols="30"
                                                            rows="5">{{ $subjects->obs_subject }}</textarea>
                                                    </div>
                                                </div>
                                            @endforeach


                                            @foreach ($meeting_new_subjects as $subjects)
                                                <div id="{{ $subjects->id }}">
                                                    <div class="row mt-3">
                                                        <div class="col">
                                                            <label for="">
                                                                Nova Pauta</label>
                                                            <div class="input-group">

                                                                <input class="form-control new_subject" type="text"
                                                                    value="{{ $subjects->subject }}">
                                                                <div class="input-group-append">
                                                                    <button data-id="{{ $subjects->id }}"
                                                                        class="btn btn-secondary btn-sm trash_subject"
                                                                        type="button"><i
                                                                            class="fas fa-trash"></i></button>
                                                                </div>
                                                            </div>


                                                        </div>
                                                        {{-- <div class="col-1"><button class="btn btn-sm btn-secondary"></button>
                                                </div> --}}
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col">
                                                            <label for="">Observações</label>
                                                            <textarea data-id="{{ $subjects->id }}" class="form-control obs_new_subject" name="" cols="30"
                                                                rows="5">{{ $subjects->obs_subject }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach



                                    </div>
                                    <div>


                                    </div>
                                    <div class="row mt-2">
                                        <div class="col">
                                            {{-- <div class="card-footer text-center" style="cursor: pointer"
                                            id="add_new_subject">
                                            <i class="far fa-plus-square"></i>&nbsp;&nbsp;<a>Adicionar Novos
                                                Assuntos</a>
                                        </div> --}}
                                        </div>
                                    </div>
                                    @endif

                                </div>

                            </div>
                        </div>

                        {{-- CARD ASSUNTOS ABORDADOS --}}
                        @if ($meeting->start_meeting)
                            <div class="col-sm-12">
                                <div class="card card-secondary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Assuntos Abordados</h3>
                                    </div>
                                    <!-- /.card-header -->
                                    <!-- form start -->
                                    <div class="card-body">
                                        <div class="card-body table-responsive p-0">
                                            <div id="divRowtopics_covered" class="row">
                                                <input type="hidden" id="meeting_topics_covereds"
                                                    name="meeting_topics_covereds"
                                                    value="{{ $meeting_topics_covereds }}">
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="card-footer text-center">
                                <i class="far fa-plus-square"></i>&nbsp;&nbsp;<a id="addItemTopics_covered"
                                    href="javascript:">Adicionar Novo Item</a>
                            </div> --}}
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-sm-12">
                        <a id='btn_back' href="{{ route('meeting.list') }}" class="btn btn-secondary mb-2">Voltar</a>
                        {{-- <button type="submit" class="btn btn-success float-right"><i --}}
                        {{-- class="far fa-save"></i>&nbsp;&nbsp;Salvar</button>  --}}
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

        <!-- Modal adicionar participante-->
        <div class="modal fade" id="ModalAddParticipant" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Selecione a opção desejada</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row d-flex justify-content-center">

                            <a data-toggle="modal" id="usersRegistered" data-target="#ModalAddParticipantRegistered"
                                class="btn btn-app bg-success">
                                <i class="fas fa-users"></i> Usuário Cadastrado
                            </a>
                            <a data-toggle="modal" id="usersGuest" data-target="#ModalAddParticipantGuest"
                                class="btn btn-app bg-secondary">
                                <i class="fas fa-users"></i> Usuário Participante
                            </a>

                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- / ModalAddParticipant -->


        <!-- Modal selecionar participante cadastrado-->
        <div class="modal fade" id="ModalAddParticipantRegistered" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Selecione o usuáio desejado</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Usuário</label>
                            <select class="form-control select2" id="userRegistered" name="userRegistered"
                                style="width: 100%;">
                                @foreach ($usersRegistered as $userRegistered)
                                    <option value="{{ $userRegistered->id }}">{{ $userRegistered->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" id="buttonUserRegistered" name="buttonUserRegistered"
                            class="btn btn-primary">Selecionar</button>
                    </div>
                </div>
            </div>
        </div> <!-- / ModalAddParticipantRegistered -->


        <!-- Modal selecionar participante convidado-->
        <form name="formAddParticipantGuest" id="formAddParticipantGuest" enctype="multipart/form-data"
            method="POST">
            <div class="modal fade" id="ModalAddParticipantGuest" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Cadastrar Participante Convidado</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Nome</label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                            <div class="form-group">
                                <label>Telefone</label>
                                <input type="text" class="form-control" id="telephone" name="telephone">
                            </div>
                            <div class="form-group">
                                <label>Função</label>
                                <input type="text" class="form-control" id="profession" name="profession">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Selecionar</button>
                        </div>
                    </div>
                </div>
            </div> <!-- / ModalAddParticipantGuest -->
        </form>

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
                            <select class="form-control select2 idOccurence" id="idOccurence" name="userRegistered"
                                style="width: 100%;">
                                @foreach ($ocurrences as $ocurrence)
                                    <option value="{{ $ocurrence->id }}">{{ $ocurrence->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" id="buttonOccurrence" name="buttonOccurrence"
                            class="btn btn-primary buttonOccurrence">Selecionar</button>
                    </div>
                </div>
            </div>
        </div> <!-- / Modal selecionar ocorrência -->
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

@section('plugins.scriptViewMeeting', true)
@endsection
