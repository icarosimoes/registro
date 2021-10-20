@extends('adminlte::page')
@section('content')
@section('plugins.Select2', true)
@section('plugins.JqueryMask', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('occurrence.list') }}">Lista de Registros</a></li>
                    <li class="breadcrumb-item active">Novo Registro</li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-2">
                            <form name="formOccurrence" id="formOccurrence" enctype="multipart/form-data" method="POST">
                                @csrf
                                <!-- {{ csrf_field() }} -->
                        </div> <!-- col-md3 -->
                        <div class="col-md-12">
                            <div class="card card-secondary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Novo Registro</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="Name">Titulo</label>
                                        <input type="text" class="form-control" name="title" id="title" placeholder=""
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label for="Name">Descrição</label>
                                       <textarea class="form-control" name="description" id="description" cols="30" rows="5" required></textarea>
                                    </div>
                                    <div class="row">
                                       <div class="col">
                                        <div class="form-group">
                                            <label for="Name">Prazo</label>
                                            <input type="date" class="form-control" name="deadline" id="deadline" placeholder=""
                                            required>
                                        </div>
                                       </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="Name">Destinatário</label>
                                                <select class="form-control select2" name="receiver" id="receiver" required>
                                                    @foreach ($users as $user)
                                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                        @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div> <!-- end row-->
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Participantes</label>
                                            <div class="select2-purple">
                                              <select class="select2" id="participants" name="participants" multiple="multiple" data-placeholder="Selecione 1 ou mais participantes" data-dropdown-css-class="select2-purple" style="width: 100%;">
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                              </select>
                                            </div>
                                          </div>
                                       </div>
                                    <div class="form-group">
                                        <label for="Name">Observações</label>
                                       <textarea class="form-control" name="comments" id="comments" cols="30" rows="5"></textarea>
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
                                        class="btn btn-secondary float-lg-right"><i
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
@section('plugins.scriptCreateOccurrence', true)
@endsection
