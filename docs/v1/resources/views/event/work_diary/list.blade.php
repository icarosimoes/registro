@extends('adminlte::page')
@section('content')
@section('plugins.Select2', true)

@section('plugins.Datatables', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Lista Diário Obras </li>
                </ol>
            </div>

            
            <div class="col-md-12">
                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Diário de Obras</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col text-left">
                                    <a type="button" href="{{ route('work_diary.create') }}" data-toggle="tooltip"
                                        data-placement="top" title="Novo Departamento"
                                        class="btn bg-gradient-secondary btn-sm "><i class="fas fa-plus"></i>
                                        Novo Diário </a>
                                        
                                </div>
                                <div class="col text-right">
                                    <button type="button" id="filter" class="btn bg-gradient-info btn-sm "><i
                                            class="fas fa-filter"></i> Filtro </button>
                                </div>    
                            </div>
                        </div>
                        <form name="form" id="form" action="" enctype="multipart/form-data" method="GET">
                            <div class="callout callout-info" id="card_filter" style="display: none">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="Name">De</label>
                                            <input class="form-control" type="date" name="date_start" value="{{ @$filter['date_start'] }}">
                                        </div>   
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="Name">Até</label>
                                            <input class="form-control" type="date" name="date_end" value="{{ @$filter['date_end'] }}">
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
                                    <th>Data</th>
                                    <th class="text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($workDiary as $item)
                                    <tr >
                                        {{-- @foreach ($roles as $role) --}}
                                            <td width="50">{{ $item->id }}</td>
                                            <td >{{ date("d/m/Y",strtotime($item->date)) }}</td>
                                            <td class="text-right">
                                                <div class="btn-group-sm">
                                                    {{-- @can('checkRouters', $route =
                                                    'view.client') --}}
                                                    <a href="{{ route('work_diary.create').'?copy='.$item->id }}"
                                                        data-toggle="tooltip" data-placement="top" title="Copiar"
                                                        class="btn btn-success"><i class="fas fa-copy"></i></a> 
                                                    
                                                    <a href="{{ route('work_diary.show', [$item->id]) }}"
                                                        data-toggle="tooltip" data-placement="top" title="Visualizar"
                                                        class="btn  btn-secondary"><i class="fas fa-eye"></i></a> 
                                                    {{-- @endcan
                                                    --}}
                                                    {{-- @can('checkRouters', $route =
                                                    'edit.client') --}}
                                                    <a href="{{ route('work_diary.edit', [$item->id]) }}"
                                                        data-toggle="tooltip" data-placement="top" title="Editar"
                                                        class="btn btn-primary"><i class="fas fa-pencil-alt"></i></a>
                                                    {{-- @endcan
                                                    --}}
                                                    {{-- @can('checkRouters', $route =
                                                    'delete.client') --}}
                                                    <button  data-id="{{ $item->id }}"
                                                        data-toggle="tooltip" data-placement="top" title="Excluir"
                                                        class="btn btn-danger remove"><i class="fas fa-trash"></i></button> 
                                                    {{-- @endcan
                                                    --}}

                                                </div>
                                            </td>
                                    </tr>
                                @endforeach
                                {{-- @endforeach
                                --}}
                            </tbody>

                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>

        </div>
    </div>

    {{-- Modal delete --}}
    <div class="modal" tabindex="-1" id='modal_delete' >
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Excluir</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <p>Deseja realmente excluir ?</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
              <button id="btn_delete"  class="btn btn-danger">Deletar</button>
            </div>
          </div>
        </div>
      </div>
      

      @section('plugins.scriptListWorkDiary', true)
@endsection
