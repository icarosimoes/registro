@extends('adminlte::page')
@section('plugins.JqueryMask', true)
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('list.Chart_of_accounts_group') }}">Grupo Plano de
                            Contas</a></li>
                    <li class="breadcrumb-item active">Novo Plano de Contas</li>
                </ol>
            </div>
            <div class="col-md-8">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-2">
                            <form name="formChartOfAccounts" id="formChartOfAccounts" enctype="multipart/form-data"
                                method="POST">
                                @csrf
                                <!-- {{ csrf_field() }} -->
                        </div> <!-- col-md3 -->
                        <div class="col-md-12">
                            <div class="card card-secondary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="far fa-file"></i> Novo Plano de Contas</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="Name">Grupo selecionado:</label>
                                        <input type="text" class="form-control is-valid"
                                            value="{{ $group->id . ' - ' . $group->name }}" placeholder="" disabled="">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="Name">Código:</label>
                                                <input type="text" maxlength="5" minlength="5" class="form-control"
                                                    name="code" id="code" placeholder="" required>
                                                <div name="msgInput" id="msgInput"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="Name">Descrição:</label>
                                                <input type="text" class="form-control" name="name" id="name" placeholder=""
                                                    required>
                                                <input type="hidden" value="{{ $group->id }}" name="group_id" id="group_id">
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
                                    <button type="submit" id="submit" name="submit" class="btn btn-secondary float-right"><i
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
@section('plugins.scriptCreateChartOfAccounts', true)
@endsection
