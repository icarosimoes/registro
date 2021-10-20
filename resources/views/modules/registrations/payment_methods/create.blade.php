@extends('adminlte::page')
@section('content')
@section('plugins.JqueryMask', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Novo Método de pagamento</li>
                    <li class="breadcrumb-item active"><a href="{{ route('list.payment_methods') }}">Método de pagamento</a>
                    </li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-2">
                            <form name="formPaymentMethods" id="formPaymentMethods" enctype="multipart/form-data"
                                method="POST">
                                @csrf
                                <!-- {{ csrf_field() }} -->
                        </div> <!-- col-md3 -->
                        <div class="col-md-8">
                            <div class="card card-secondary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="far fa-file"></i> Novo método de pagamento</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="Name">Descrição:</label>
                                            <input type="text" class="form-control" name="description" id="description"
                                                placeholder="" required>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="Name">Taxa:</label>
                                            <input type="text" class="form-control" maxlength="3" minlength="1" name="tax" id="tax" placeholder=""
                                                required>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="Name">Prazo:</label>
                                            <input type="text" class="form-control" maxlength="4" minlength="1" name="deadline" id="deadline"
                                                placeholder="" required>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="Name">Conta:</label>
                                            <select id="accounts_id" name="accounts_id" class="form-control">
                                                @foreach ($account as $item)
                                                    <option value="{{ $item->id }}">{{ $item->description }}</option>
                                                @endforeach

                                            </select>
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
@section('plugins.scriptCreatePaymentMethods', true)
@endsection
