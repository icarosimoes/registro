@extends('adminlte::page')
@section('content')
@section('plugins.JqueryMask', true)
@section('plugins.JqueryValidate', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Editar Contatos</li>
                    <li class="breadcrumb-item active"><a href="{{ route('list.supplier') }}">Lista de Fornecedores</a></li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-2">
                            <form name="formContacts" id="formContacts" enctype="multipart/form-data" method="POST">
                                @csrf
                                <!-- {{ csrf_field() }} -->
                        </div> <!-- col-md3 -->
                        <div class="col-md-8">
                            <div class="card card-secondary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="far fa-file"></i> Contato</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="Name">Nome:</label>
                                        <input type="text" class="form-control" name="name"
                                            id="name" placeholder="" required>
                                            <input type="hidden" value="{{ $supplier_id }}" name="supplier_id" id="supplier_id">
                                    </div>
                                    <div class="form-group">
                                        <label for="Name">Função:</label>
                                        <input type="text" class="form-control" name="occupation"
                                            id="occupation" placeholder="" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="Name">Telephone:</label>
                                        <input type="text" class="form-control" name="telephone"
                                            id="telephone" placeholder="" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="Name">Email:</label>
                                        <input type="text" class="form-control" name="email"
                                            id="email" placeholder="" required>
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
