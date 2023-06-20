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
                    <li class="breadcrumb-item active">Editar Reunião</li>
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
                                    <h3 class="card-title">Editar reunião</h3>
                                </div><!-- /.card-header -->

                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-3">
                                            <input value="{{ $meeting->datetime }}" id="datetime" class="form-control" type="datetime-local"  required>
                                        </div>
                                        <div class="col-3">
                                            <input value="{{ $meeting->local }}" id="local" class="form-control" type="text" required>   
                                        </div>
                                        
                                    </div>
                                </div>
                                {{-- <div class="card-footer text-center">
                                    <i class="far fa-plus-square"></i>&nbsp;&nbsp;<a id="addItemTopic"
                                        href="javascript:">Adicionar Novo Item</a>
                                </div> --}}
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="card card-secondary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Pauta</h3>
                                </div><!-- /.card-header -->

                                <div class="card-body">
                                    <div class="card-body table-responsive p-0">
                                        <table class="table table-sm table-striped table-valign-middle tablenotstyle">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th style="width: 10%"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyItemTopic">
                                               <input type="hidden" id="meeting_subjects" name="meeting_subjects" value="{{ $meeting_subjects }}">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                {{-- <div class="card-footer text-center">
                                    <i class="far fa-plus-square"></i>&nbsp;&nbsp;<a id="addItemTopic"
                                        href="javascript:">Adicionar Novo Item</a>
                                </div> --}}
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
                                                <input type="hidden" id="meeting_id" name="meeting_id" value="{{ $meeting->id }}">
                                                <input type="hidden" id="meeting_registered_participants" name="meeting_registered_participants" value="{{ $meeting_registered_participants }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="card-header">
                                            <h3 class="card-title">Convidados</h3>
                                        </div>

                                        <div class="card-body">
                                            <div class="row" id="invited_users">
                                                <input type="hidden" id="meeting_invited_participants" name="meeting_invited_participants" value="{{ $meeting_invited_participants }}">
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


                        {{-- CARD ASSUNTOS ABORDADOS --}}
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
                                            <input type="hidden" id="meeting_topics_covereds" name="meeting_topics_covereds" value="{{ $meeting_topics_covereds }}">
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="card-footer text-center">
                                    <i class="far fa-plus-square"></i>&nbsp;&nbsp;<a id="addItemTopics_covered"
                                        href="javascript:">Adicionar Novo Item</a>
                                </div> --}}
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <a href="#" class="btn btn-secondary mb-2">Cancelar</a>
                            <button type="submit" class="btn btn-success float-right"><i
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

            <!-- Modal adicionar participante-->
            <div class="modal fade" id="ModalAddParticipant" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
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
            <form name="formAddParticipantGuest" id="formAddParticipantGuest" enctype="multipart/form-data" method="POST">
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
@section('plugins.scriptUpdateMeeting', true)
@endsection
