@extends('adminlte::page')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Novo Perfil</li>
                    <li class="breadcrumb-item active"><a href="{{ route('list.profile') }}">Lista de Perfis</a></li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-2">
                            <form name="formProfile" id="formUser" enctype="multipart/form-data" method="POST">
                                @csrf
                                <!-- {{ csrf_field() }} -->
                        </div> <!-- col-md3 -->
                        <div class="col-md-12">
                            <div class="card card-default">
                                <div class="card-header">
                                    <h3 class="card-title">Proposta de Venda</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                      <div class="col-sm-4">

                                      </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                  <button type="submit" id="submit" name="submit" class="btn bg-gradient-secondary">Registrar</button>
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
@endsection
