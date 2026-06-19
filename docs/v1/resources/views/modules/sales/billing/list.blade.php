@extends('adminlte::page')
@section('content')
@section('plugins.Datatables', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Lista de Faturamentos</li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Faturamentos</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="form-group">
                            <div class="card-footer">
                                <a type="button" href="{{ route('create.billing') }}" data-toggle="tooltip"
                                    data-placement="top" title="Novo Faturamento"
                                    class="btn bg-gradient-secondary btn-sm"><i class="fas fa-plus-square"></i> Novo</a>
                            </div>
                        </div>
                        <table name="DataTableUser" id="DataTableUser" class="table table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Emissão</th>
                                    <th>Código Proposta</th>
                                    <th>Valor</th>
                                    <th>Parcelas</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($billing as $item)
                                    <tr>
                                        <td>{{ $item['id'] }}</td>
                                        <td>{{ $item['client'] }}</td>
                                        <td>{{ date('d-m-Y', strtotime($item['emission_date'])) }}</td>
                                        <td>{{ $item['sale_proposals_id'] }}</td>
                                        <td>{{ "R$ " . number_format($item['total'], 2, ',', '.') }}</td>
                                        <td>{{ $item['parcels'] }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                {{-- <a href="" class="btn btn-light"><i
                                                        class="fas fa-eye"></i> Visualizar</a>
                                                --}}
                                                @can('checkRouters', $route = 'list.installments')
                                                    <a href="{{ route('list.installments', ['id' => $item['id']]) }}"
                                                        class="btn btn-default" data-toggle="tooltip" data-placement="top"
                                                        title="Visualizar"><i class="fas fa-eye"></i></a>
                                                @endcan
                                                {{-- @can('checkRouters', $route =
                                                'billing.download') --}}
                                                <a href="{{ route('billing.download', ['id' => $item['id']]) }}"
                                                    class="btn btn-default" data-toggle="tooltip" data-placement="top"
                                                    title="Anexos"><i class="fas fa-download"></i></a>
                                                {{-- @endcan --}}
                                                @can('checkRouters', $route = 'delete.billing')
                                                    <a href="{{ route('delete.billing', ['id' => $item['id']]) }}"
                                                        class="btn btn-default" data-toggle="tooltip" data-placement="top"
                                                        title="Excluir"><i class="far fa-trash-alt"></i></a>
                                                @endcan

                                            </div>
                                        </td>
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
