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
                                <div class="col text-right">
                                <button type="button" id="filter" class="btn bg-gradient-info btn-sm "><i
                                        class="fas fa-filter"></i> Filtro </button>
                            </div>
                                    <!-- <a type="button" href="{{ route('function.create') }}" data-toggle="tooltip"
                                        data-placement="top" title="Nova Função"
                                        class="btn bg-gradient-secondary btn-sm float-right"><i class="fas fa-plus"></i>
                                        Nova Função</a> -->
                                </div>
                            </div>
                        </div>
                        <form name="form" id="form" action="" enctype="multipart/form-data" method="GET">
                        <div class="callout callout-info" id="card_filter" style="display: none">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="Name">Status</label>
                                        <select class="form-control" name="checked" id="checked">
                                            <option value="not">Não Visto</option>
                                            <option value="yes">Visto</option>
                                            <option value="all">Todos</option>
                                            
                                        </select>
                                    </div>
                                </div>
                                

                            </div>
                            <div class="row">
                                <div class="col text-right">
                                    <button type="submit" class="btn btn-sm btn-info btn-flat"><i
                                            class="fas fa-search"></i> Aplicar</button>
                                </div>
                            </div>
                        </div>

                    </form>
                        <table name="DataTableUser" id="DataTableUser" class="table table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Menssagem</th>
                                    <th>Link</th>
                                    <th class="text-right">Visto</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach($notifications as $item)
                               <tr>
                                    <td>{{$item->id}}</td>
                                    <td>{{$item->msg}}</td>
                                    @if( $item->occurrence_id)
                                    <td><a href="{{route('occurrence.edit',[$item->occurrence_id]).'?notification='.$item->id}}">Registro {{$item->occurrence_id}}</a>  </td>
                                    @endif
                                    @if( $item->meeting_id)
                                    <td><a href="{{route('meeting.edit',[$item->meeting_id]).'?notification='.$item->id}}">Reunião {{$item->meeting_id}}</a>  </td>
                                    @endif
                                    @if($item->checked == 'yes')
                                    <td class="text-right"> <input disabled  type="checkbox" checked name="checked"></td>
                                    @else
                                    <td class="text-right"> <input disabled  type="checkbox"  name="checked"></td>
                                    @endif    
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

    
      

      @section('plugins.scriptNotification', true)
@endsection
