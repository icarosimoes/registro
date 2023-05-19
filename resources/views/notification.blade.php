@extends('adminlte::page')
@section('content')
@section('plugins.Datatables', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Notificações</li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Notificações</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col">
                                    <a type="button" href="{{ route('function.create') }}" data-toggle="tooltip"
                                        data-placement="top" title="Nova Função"
                                        class="btn bg-gradient-secondary btn-sm float-right"><i class="fas fa-plus"></i>
                                        Nova Função</a>
                                </div>
                            </div>
                        </div>

                        <table name="DataTableUser" id="DataTableUser" class="table table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Link</th>
                                    <th class="text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach($notifications as $item)
                               <tr>
                                    <td>{{$item->id}}</td>
                                    <td></td>
                                    <td></td>
                               </tr>
                               @endforeach
                            </tbody>

                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>

        </div>
    </div>

    
      

      @section('plugins.scriptListFunction', true)
@endsection
