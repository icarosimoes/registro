@extends('adminlte::page')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Visualizar Cliente</li>
                    <li class="breadcrumb-item active"><a href="{{ route('list.client') }}">Lista de Clientes</a></li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-2"></div> <!-- col-md3 -->
                        <div class="col-md-12">
                            <div class="card card-secondary">
                                <div class="card-header">
                                    <h3 class="card-title">Visualizar Cliente</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="Name">Nome:</label>
                                                <input type="text" class="form-control" value="{{ $data->nome }}"
                                                    name="nome" id="nome" placeholder="" disabled>
                                                <input type="hidden" name="id" id="id" value="{{ $data->id }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="Name">Email:</label>
                                                <input type="text" class="form-control" value="{{ $data->email }}"
                                                    name="email" id="email" placeholder="" disabled>
                                            </div>
                                            <div class="form-group">
                                                <label for="Name">CPF/CNPJ:</label>
                                                <input type="text" class="form-control" value="{{ App\Http\Controllers\Register\ClientController::formatCnpjCpf($data->cpf_cnpj) }}"
                                                    name="cpf_cnpj" id="cpf_cnpj" placeholder="" disabled>
                                            </div>
                                            <div class="form-group">
                                                <label for="Name">Inscrição Estadual:</label>
                                                <input type="number" class="form-control"
                                                    value="{{ $data->inscricaoEstadual }}" name="inscricaoEstadual"
                                                    id="inscricaoEstadual" placeholder="" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="Name">Inscrição Municipal:</label>
                                                <input type="number" class="form-control"
                                                    value="{{ $data->inscricaoMunicipal }}" name="inscricaoMunicipal"
                                                    id="inscricaoMunicipal" placeholder="" disabled>
                                            </div>
                                            <div class="form-group">
                                                <label for="Name">CEP:</label>
                                                <input type="text" class="form-control" value="{{ $data->cep }}" name="cep"
                                                    id="cep" placeholder="" disabled>
                                            </div>
                                            <div class="form-group">
                                                <label for="Name">Endereço:</label>
                                                <input type="text" class="form-control" value="{{ $data->endereco }}"
                                                    name="endereco" id="endereco" placeholder="" disabled>
                                            </div>
                                            <div class="form-group">
                                                <label for="Name">Telefone:</label>
                                                <input type="text" class="form-control" value="{{ App\Http\Controllers\Register\ClientController::formatTelefone($data->telefone) }}"
                                                    name="telefone" id="telefone" placeholder="" disabled>
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
                                    <a type="button" href="{{ route('list.client') }}"
                                        class="btn  bg-gradient-secondary">Voltar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
