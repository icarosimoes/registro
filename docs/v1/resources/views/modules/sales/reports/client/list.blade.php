@extends('adminlte::page')
@section('content')
@section('plugins.Datatables', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Relatório ABC Clientes</li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Relatório ABC Clientes</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col">
                                    <a type="button" target="blank" href="{{ route('sale.report.client') }}" data-toggle="tooltip" data-placement="top"
                                        title="Exportar Arquivo" class="btn bg-gradient-secondary btn-sm"><i
                                            class="fas fa-file-pdf"></i> Exportar PDF</a>
                                </div>
                            </div>
                        </div>
                        <form name="form" id="form" action="" enctype="multipart/form-data" method="GET">
                            <div class="callout callout-info">
                                <h5>Filtros</h5>
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="Name">De</label>
                                            <input type="date" class="form-control" name="filter_date_params1"
                                                id="filter_date_params1" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="Name">Até</label>
                                        <div class="input-group">
                                            <input type="date" name="filter_date_params2" id="filter_date_params2"
                                                class="form-control">
                                            <span style="margin-left: 2%" class="input-group-append">
                                                <button type="submit" class="btn btn-info btn-flat"><i
                                                        class="fas fa-search"></i> Aplicar</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                              </div>
                            
                        </form>

                        <table name="DataTableUser" id="DataTableUser" class="table table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Faturamento</th>
                                    <th>Faturamento Acumulado</th>
                                    {{-- <th>% Acum</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clients as $item)
                                    <tr>
                                        <td>{{ $item->nome }}</td>
                                        <td>{{ 'R$ ' . number_format($item->total, 2, ',', '.') }}</td>
                                        <td>{{ 'R$ ' . number_format($item->acumulative_billing, 2, ',', '.') }}</td>
                                        {{-- <td>% 100</td> --}}
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

@endsection
