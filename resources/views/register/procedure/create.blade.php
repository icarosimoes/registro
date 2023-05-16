@extends('adminlte::page')
@section('content')
@section('plugins.Select2', true)
@section('plugins.JqueryMask', true)
<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-12">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('function.index') }}">Lista de Procedimento</a></li>
                <li class="breadcrumb-item active">Novo Procedimento</li>
            </ol>
        </div>
        <div class="col-md-12">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-2">
                        <form name="form" id="form" enctype="multipart/form-data" method="POST">
                            @csrf
                        
                    </div> <!-- col-md3 -->
                    <div class="col-md-12">
                        <div class="card card-secondary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Novo Procedimento</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label for="Name">Procedimento</label>
                                            <input type="text" class="form-control"  id="name"
                                                placeholder="" required>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label for="Name">Link</label>
                                            <input type="text" class="form-control"  id="link"
                                                placeholder="" >
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label for="Name">Anexo</label>
                                            <input type="file" class="form-control"  id="file"
                                                placeholder="" >
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
                                    class="btn btn-secondary float-lg-right"><i class="fas fa-save"></i> Salvar</button>
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
@section('plugins.scriptCreateProcedure', true)
@endsection
