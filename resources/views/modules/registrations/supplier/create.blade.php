@extends('adminlte::page')
@section('content')
@section('plugins.Select2', true)
@section('plugins.JqueryMask', true)

    {{-- @section('plugins.JqueryValidate', true) --}}
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Novo Fornecedor</li>
                    <li class="breadcrumb-item active"><a href="{{ route('list.supplier') }}">Lista de Fornecedores</a></li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-2">
                            <form name="formSupplier" id="formSupplier" enctype="multipart/form-data" method="POST">
                                @csrf
                                <!-- {{ csrf_field() }} -->
                        </div> <!-- col-md3 -->
                        <div class="col-md-12">
                            <div class="card card-secondary">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="far fa-file"></i> Fornecedor</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="Name">CNPJ:</label>
                                                <div class="input-group">
                                                    <input class="form-control" id="cnpj" name="cnpj" maxlength="18"
                                                        minlength="18" type="text" class="form-control" required>
                                                    <div id="messageCNPJ" class="invalid-feedback">
                                                        CNPJ inválido, insira um CNPJ válido para continuar.
                                                    </div>
                                                    <span id="btnConsultCNPJ" class="input-group-append">
                                                        <button type="button" class="btn btn-secondary btn-flat"><i
                                                                class="fas fa-search"></i></button>
                                                    </span>
                                                </div>

                                            </div>
                                            <div class="form-group">
                                                <label for="Name">Razão Social:</label>
                                                <input type="text" class="form-control" name="company_name"
                                                    id="company_name" placeholder="" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="Name">Inscrição Municipal:</label>
                                                <input type="text" class="form-control" name="municipal_registration"
                                                    id="municipal_registration" placeholder="" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="Name">Endereço:</label>
                                                <input type="text" class="form-control" name="address" id="address"
                                                    placeholder="" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="Name">Cidade:</label>
                                                <input type="text" class="form-control" name="city" id="city" placeholder=""
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="Name">Nome Fantasia:</label>
                                                <input type="text" class="form-control" name="fantasy_name"
                                                    id="fantasy_name" placeholder="" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="Name">Inscrição Estadual:</label>
                                                <input type="text" class="form-control" name="state_registration"
                                                    id="state_registration" placeholder="" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="Name">Estado:</label>
                                                <input type="text" class="form-control" name="state" id="state"
                                                    placeholder="" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="Name">CEP:</label>
                                                <input type="text" class="form-control" name="cep" id="cep" placeholder=""
                                                    required>
                                            </div>
                                            <div class="form-group">
                                                <label for="Name">Email:</label>
                                                <input type="text" class="form-control" name="email" id="email"
                                                    placeholder="" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Grupo de insumos:</label>
                                                <div class="select2-purple">
                                                    <select class="select2" multiple="multiple" name="input_group"
                                                        id="input_group" data-placeholder="Selecionar grupo(s)"
                                                        data-dropdown-css-class="select2-purple" style="width: 100%;">
                                                        @foreach ($input_group as $item)
                                                            <option value="{{ $item->id }}">{{ $item->description }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
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
                            </div>
                            <div class="card card-secondary">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-phone"></i> Contatos</h3>
                                </div>
                                <div class="card-body" id="dep">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <label>Nome</label>
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Função</label>
                                        </div>
                                        <div class="col-sm-2">
                                            <label>Telefone</label>
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Email</label>
                                        </div>
                                    </div>

                                    <div class="row with-border mailbox-controls dep_fc">
                                        <div class="col-sm-3">
                                            <input type="text" name="contact_name[]" id="contact_name[]"
                                                class="form-control form-control-sm" required>
                                        </div>
                                        <div class="col-sm-3">
                                            <input type="text" name="contact_occupation[]" id="contact_occupation[]"
                                                class="form-control form-control-sm" required>
                                        </div>
                                        <div class="col-sm-2">
                                            <input type="text" name="contact_telephone[]" id="contact_telephone[]"
                                                class="form-control form-control-sm contact_telephone" required>
                                        </div>
                                        <div class="col-sm-3">
                                            <input type="text" name="contact_email[]" id="contact_email[]"
                                                class="form-control form-control-sm" required>
                                        </div>
                                        <div class="col-sm-0">
                                            <button disabled type="button" data-toggle="tooltip" data-placement="top"
                                                title="Remover Contato" class="btn btn-block btn-danger btn-sm remove"><i
                                                    class="fas fa-trash-alt"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="button" name="add_dependency" id="add_dependency"
                                        class="btn btn-default"><i class="fas fa-address-book"></i>
                                        Adicionar Contatos</button>
                                    <button type="submit" id="submit" name="submit"
                                        class="btn  bg-gradient-secondary float-right"><i class="far fa-save"></i>
                                        Salvar</button>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@section('plugins.scriptCreateSupplier', true)
@endsection
