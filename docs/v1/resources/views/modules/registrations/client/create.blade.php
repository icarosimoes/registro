@extends('adminlte::page')
@section('content')
@section('plugins.Select2', true)
@section('plugins.JqueryMask', true)
@section('plugins.LoaderTemp', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Novo Cliente</li>
                    <li class="breadcrumb-item active"><a href="{{ route('list.client') }}">Lista de Clientes</a></li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-2">
                            <form name="formClient" id="formClient" enctype="multipart/form-data" method="POST">
                                @csrf
                                <!-- {{ csrf_field() }} -->
                        </div> <!-- col-md3 -->
                        <div class="col-md-12">
                            <div class="card card-secondary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Novo Cliente</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="Name">Nome:</label>
                                                <input type="text" class="form-control" name="nome" id="nome" placeholder=""
                                                    required>
                                            </div>
                                            <div class="form-group">
                                                <label for="Name">Email:</label>
                                                <input type="text" class="form-control" name="email" id="email"
                                                    placeholder="" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="Name">CPF/CNPJ:</label>
                                                <input type="text" class="form-control" name="cpf_cnpj" id="cpf_cnpj"
                                                    placeholder="" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="Name">Inscrição Estadual:</label>
                                                <input type="number" class="form-control" name="inscricaoEstadual"
                                                    id="inscricaoEstadual" placeholder="" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="Name">Inscrição Municipal:</label>
                                                <input type="number" class="form-control" name="inscricaoMunicipal"
                                                    id="inscricaoMunicipal" placeholder="" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="Name">CEP:</label>
                                                <input type="text" class="form-control" name="cep" id="cep" placeholder=""
                                                    required>
                                            </div>
                                            <div class="form-group">
                                                <label for="Name">Endereço:</label>
                                                <input type="text" class="form-control" name="endereco" id="endereco"
                                                    placeholder="" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="Name">Telefone:</label>
                                                <input type="text" class="form-control" name="telefone" id="telefone"
                                                    placeholder="" required>
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
@section('plugins.scriptCreateClient', true)
@endsection
