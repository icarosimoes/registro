@extends('adminlte::page')
@section('content')
@section('plugins.Datatables', false)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Lista de Relatório de Turno</li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Relatório de Turno</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col">
                                    <a type="button" href="{{ route('shiftreport.create') }}" data-toggle="tooltip"
                                        data-placement="top" title="Novo Relatório"
                                        class="btn bg-gradient-secondary btn-sm float-lg-right"><i
                                            class="fas fa-plus-square"></i> Novo Relatório</a>
                                </div>
                            </div>
                        </div>

                        @if (isset($error))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ $error }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <form action="" method="GET">
                          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="search">Pesquisar</label>
                <div class="input-group">
                  <input type="text" class="form-control" id="search" name="search"
                    placeholder="">
                </div>
              </div>
            </div>
            {{-- btn de pesquisa --}}
            <div class="col-md-2">
              <div class="form-group">
                <label for="search"></label>
                <div class="input-group">
                  <button style="margin-top:8px"  id="btnSearch" class="btn btn-primary btn-block"><i
                      class="fas fa-search"></i> Pesquisar</button>
                </div>
              </div>
            </div>
</form>



                        <table name="DataTableUser" id="DataTableUser" class="table table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Criado Por</th>
                                    {{-- <th>Status</th> --}}
                                    <th>Data</th>
                                    <th style="width:  15%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        {{-- @foreach ($roles as $role) --}}
                                        <td>{{ @$item['users']->name }}</td>
                                        {{-- <td>
                                            @if ($item->status == 1)
                                                {{ 'Aberto' }}
                                            @endif
                                        </td> --}}
                                        <td>{{ (new DateTime($item->created_at))->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="btn-group-sm float-left">
                                                {{-- @can('checkRouters', $route = 'view.client') --}}
                                                {{-- {{ route('view.client', ['id' => $item['id']]) }} --}}
                                                <a  id="ModalView" data-toggle="tooltip" data-placement="top" title="Visualizar" data-id="{{ $item->id }}" class="btn btn-default"><i
                                                            class="fas fa-eye"></i></a>
                                                {{-- @endcan --}}
                                                {{-- @can('checkRouters', $route = 'edit.client') --}}
                                                <a href="{{ route('shiftreport.edit', ['id' => $item->id]) }}"
                                                    data-toggle="tooltip" data-placement="top" title="Editar"
                                                    class="btn btn-info"><i class="fas fa-pencil-alt"></i></a>
                                                {{-- @endcan --}}
                                                {{-- @can('checkRouters', $route = 'delete.client') --}}
                                                <a href="{{ route('shiftreport.delete', ['id' => $item->id]) }}"
                                                    data-toggle="tooltip" data-placement="top" title="Excluir"
                                                    class="btn btn-danger"><i class="fas fa-trash"></i></a>
                                                {{-- @endcan --}}

                                            </div>
                                            @if (Auth::user()->isAdmin == 1)
                                                <div class="form-group float-right">
                                                    <div
                                                        class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                                        @if ($item->viewed == 1)
                                                            <input checked data-toggle="tooltip" data-placement="top"
                                                                title="Visto" type="checkbox" name="tested"
                                                                data-id="{{ $item->id }}" class="custom-control-input tested"
                                                                id="visto-{{ $item->id }}">
                                                        @else
                                                            <input data-toggle="tooltip" data-placement="top" title="Visto"
                                                                type="checkbox" name="tested" data-id="{{ $item->id }}"
                                                                class="custom-control-input tested"
                                                                id="visto-{{ $item->id }}">
                                                        @endif
                                                        <label class="custom-control-label"
                                                            for="visto-{{ $item->id }}"></label>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                {{-- @endforeach --}}
                            </tbody>
                            
                        </table>
                        <div style="margin-top:30px;width:100%;" class="row">
                          <div class="col">
                            <b>25 de {{ $data->total() }} Registros</b>
                          </div>
                          <div class="col"></div>
                          <div class="col">
                            {{ $data->onEachSide(1)->links() }}
                          </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>

        </div>
        <!-- Modal selecionar ocorrência-->
        <div class="modal fade" id="ModalViewId" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-eye"></i> Visualizar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="insertView" class="overflow-auto">
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info"
                        data-dismiss="modal"><i class="far fa-eye-slash"></i> Voltar</button>
                    {{-- <button type="button" id="buttonOccurrence" name="buttonOccurrence"
                        class="btn btn-primary buttonOccurrence">Selecionar</button> --}}
                </div>
            </div>
        </div>
    </div> <!-- / Modal selecionar ocorrência -->

    </div>
@section('plugins.scriptShiftReportList', true)
@endsection
