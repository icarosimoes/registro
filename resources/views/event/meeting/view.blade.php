@extends('adminlte::page')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('occurrence.list') }}">Lista de Registros</a></li>
                    <li class="breadcrumb-item active">Visualizar Registro</li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-2">
                            <form name="formOccurrenceEdit" id="formOccurrenceEdit" enctype="multipart/form-data" method="POST">
                                @csrf
                                <!-- {{ csrf_field() }} -->
                        </div> <!-- col-md3 -->
                        <div class="col-md-12">
                            <div class="card card-secondary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Visualizar Registro</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="Name">Titulo</label>
                                        <p>{{ $data->title }}</p>  
                                    </div>
                                    <div class="form-group">
                                        <label for="Name">Descrição</label>
                                        <p>{{ $data->description }}</p>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="Name">Tipo</label>                                                
                                                    <p>{{ $data['type_occurrences']->description }}</p>
                                            </div>
                                        </div>
                                       <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="Name">Prazo</label>
                                            <p>{{ (new DateTime($data->deadline))->format('d/m/Y') }}</p>
                                        </div>
                                       </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="Name">Destinatário</label>
                                                <p>{{ $receiver->name }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="Name">Observações</label>
                                       <p>{{ $data->comments }}</p>
                                    </div>
                                </div>
                                <!-- /.card-body -->
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
