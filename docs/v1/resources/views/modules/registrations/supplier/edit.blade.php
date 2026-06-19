@extends('adminlte::page')
@section('content')
@section('plugins.Select2', true)
@section('plugins.JqueryMask', true)
@section('plugins.JqueryValidate', true)
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
                            <form name="formSupplierEdit" id="formSupplierEdit" enctype="multipart/form-data" method="POST">
                                @csrf
                                <!-- {{ csrf_field() }} -->
                        </div> <!-- col-md3 -->
                        <div class="col-md-12">
                            <div class="card card-secondary">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="far fa-file"></i> Editar Fornecedor</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="Name">CNPJ:</label>
                                                <div class="input-group">
                                                    <input class="form-control" value="{{ $data['cnpj'] }}" id="cnpj" name="cnpj" maxlength="18" minlength="18" type="text" class="form-control" required>
                                                    <div id="messageCNPJ" class="invalid-feedback">
                                                        CNPJ inválido, insira um CNPJ válido para continuar.
                                                      </div>
                                                    <span id="btnConsultCNPJ" class="input-group-append">
                                                        <button type="button" class="btn btn-secondary btn-flat"><i
                                                                class="fas fa-search"></i></button>
                                                    </span>
                                                </div>
                                                <input type="hidden" value="{{ $data['id'] }}" id="id" name="id">
                                            </div>
                                            <div class="form-group">
                                                <label for="Name">Razão Social:</label>
                                                <input type="text" class="form-control" name="company_name"
                                                value="{{ $data['company_name'] }}" id="company_name" placeholder="" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="Name">Inscrição Municipal:</label>
                                                <input type="text" class="form-control" name="municipal_registration"
                                                value="{{ $data['municipal_registration'] }}" id="municipal_registration" placeholder="" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="Name">Endereço:</label>
                                                <input type="text" class="form-control" name="address" value="{{ $data['address'] }}" id="address"
                                                    placeholder="" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="Name">Cidade:</label>
                                                <input type="text" class="form-control" name="city" value="{{ $data['city'] }}" id="city"
                                                    placeholder="" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="Name">Nome Fantasia:</label>
                                                <input type="text" class="form-control" name="fantasy_name"
                                                value="{{ $data['fantasy_name'] }}" id="fantasy_name" placeholder="" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="Name">Inscrição Estadual:</label>
                                                <input type="text" class="form-control" name="state_registration"
                                                value="{{ $data['state_registration'] }}" id="state_registration" placeholder="" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="Name">Estado:</label>
                                                <input type="text" class="form-control" name="state" value="{{ $data['state'] }}" id="state"
                                                    placeholder="" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="Name">CEP:</label>
                                                <input type="text" class="form-control" value="{{ $data['cep'] }}" name="cep" id="cep" placeholder=""
                                                    required>
                                            </div>
                                            <div class="form-group">
                                                <label for="Name">Email:</label>
                                                <input type="text" class="form-control" name="email" value="{{ $data['email'] }}" id="email"
                                                    placeholder="" required>
                                            </div>
                                            {{-- <div class="form-group">
                                                <label>Grupo de insumos:</label>
                                                <select name="input_group" id="input_group" class="form-control select2" style="width: 100%;">
                                                    @foreach ($input_group as $item)
                                                    <option value="{{ $item->id }}">{{ $item->description }}</option>
                                                    @endforeach
                                                </select>
                                            </div> --}}
                                            <div class="form-group">
                                                <label>Grupo de insumos:</label>
                                                <div class="select2-purple">
                                                    <select class="select2" multiple="multiple" name="input_group"
                                                        id="input_group" data-placeholder="Selecionar grupo(s)"
                                                        data-dropdown-css-class="select2-purple" style="width: 100%;">
                                                        @foreach ($input_group_seletecd as $item)
                                                            <option value="{{ $item->input_groups_id }}" selected>{{ $item['input_groups']->description }}</option>
                                                        @endforeach
                                                        @foreach ($input_group as $item)
                                                            <option value="{{ $item->id }}">{{ $item->description }}</option>
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
                                <div class="card-footer">
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
    </div>
@section('plugins.scriptCreateSupplier', true)
@endsection
