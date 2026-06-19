@extends('adminlte::page')
@section('content')
@section('plugins.JqueryMask', true)
@section('plugins.JqueryMaskMoney', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Nova Conta</li>
                    <li class="breadcrumb-item active"><a href="{{ route('list.account') }}">Contas</a></li>
                </ol>
            </div>
            <div class="col-md-8">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-2">
                            <form name="formAccount" id="formAccount" enctype="multipart/form-data" method="POST">
                                @csrf
                                <!-- {{ csrf_field() }} -->
                        </div> <!-- col-md3 -->
                        <div class="col-md-8">
                            <div class="card card-secondary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="far fa-file"></i> Nova Conta</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="Name">Descrição:</label>
                                        <input type="text" class="form-control" name="description" id="description"
                                            placeholder="" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="Name">Saldo:</label>
                                        <input type="text" class="form-control" name="balance" id="balance" placeholder=""
                                            required>
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
                                    <button type="submit" id="submit" name="submit" class="btn  bg-gradient-secondary float-right"><i
                                            class="far fa-save"></i> Salvar</button>
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
@section('plugins.scriptCreateAccount', true)
@endsection
