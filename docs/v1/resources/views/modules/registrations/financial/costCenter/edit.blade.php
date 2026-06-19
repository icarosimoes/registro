@extends('adminlte::page')
@section('content')
@section('plugins.Select2', true)
@section('plugins.JqueryMask', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Editar Centro de custo</li>
                    <li class="breadcrumb-item active"><a href="{{ route('list.cost_center') }}">Lista de Centro de custo</a></li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-2">
                            <form name="formCostCenterEdit" id="formCostCenterEdit" enctype="multipart/form-data"
                                method="POST">
                                @csrf
                                <!-- {{ csrf_field() }} -->
                        </div> <!-- col-md3 -->
                        <div class="col-md-8">
                            <div class="card card-secondary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="far fa-file"></i> Editar Centro de custo</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="Name">Código:</label>
                                                <input type="text" maxlength="2" minlength="2" class="form-control"
                                                    value="{{ $data['code'] }}" name="code" id="code" placeholder=""
                                                    required>
                                                <div name="msgInput" id="msgInput"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="Name">Nome:</label>
                                                <input type="text" class="form-control" value="{{ $data['name'] }}"
                                                    name="name" id="name" placeholder="" required>
                                                <input type="hidden" name="id" id="id" value="{{ $data['id'] }}">
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
                                        class="btn  bg-gradient-secondary float-right"><i
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
@section('plugins.scriptCreateCostCenter', true)
@endsection
