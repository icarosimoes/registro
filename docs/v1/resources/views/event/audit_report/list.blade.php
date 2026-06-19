@extends('adminlte::page')
@section('content')
@section('plugins.Datatables', false)
<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-12">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active">Lista de Relatório de Auditoria</li>
            </ol>
        </div>
        <div class="col-md-12">
            <div class="card card-secondary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Relatório de Auditoria</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col">
                                <a type="button" href="{{ route('audit_report.create') }}" data-toggle="tooltip" data-placement="top" title="Novo Relatório" class="btn bg-gradient-secondary btn-sm float-lg-right"><i class="fas fa-plus-square"></i> Novo Relatório</a>
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
                                    <label for="search">Procurar</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="search" name="search" placeholder="">
                                    </div>
                                </div>
                            </div>
                            {{-- btn de pesquisa --}}
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="search"></label>
                                    <div class="input-group">
                                        <button style="margin-top:8px" id="btnSearch" class="btn btn-primary btn-block"><i class="fas fa-search"></i>
                                            Procurar</button>
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
                            @foreach ($auditReports as $item)
                            <tr>
                                <td>{{ $item->user->name }}</td>
                                <td>{{ $item->date ? (new DateTime($item->date))->format('d/m/Y'): '' }}</td>
                                <td>
                                    <a href="{{ route('audit_report.edit', $item->id) }}" data-toggle="tooltip" data-placement="top" title="Editar" class="btn btn-primary btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                    <a href="{{ route('audit_report.edit', $item->id).'?view=true' }}" data-toggle="tooltip" data-placement="top" title="Visualizar" class="btn btn-secondary btn-sm"><i class="fas fa-eye"></i></a>
                                    <button type="button" data-id="{{ $item->id }}" data-toggle="tooltip" data-placement="top" title="Excluir" class="btn btn-danger btn-sm btn_delete"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @endforeach
                            {{-- @endforeach --}}
                        </tbody>

                    </table>
                    <div style="margin-top:30px;width:100%;" class="row">
                        <div class="col">
                            <b>25 de {{ $auditReports->total() }} Registros</b>
                        </div>
                        <div class="col"></div>
                        <div class="col"></div>
                        <div class="col"></div>
                        <div class="col"></div>
                        <div class="col"></div>
                        <div class="col text-right">
                            {{ $auditReports->onEachSide(1)->links() }}
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>

    </div>
    <!-- Modal selecionar ocorrência-->
    <div class="modal fade" id="ModalDelete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> Excluir</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4> deseja realmente excluir?</h2>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-danger" data-dismiss="modal"> Não</button> --}}
                    <input type="hidden" id="id">
                    <button id="btnDelete" type="button" class="btn btn-danger"> Sim</button>
                </div>
            </div>
        </div>
    </div> <!-- / Modal selecionar ocorrência -->

</div>
@section('plugins.scriptAuditReportList', true)
@endsection
