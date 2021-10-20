@extends('adminlte::page')
@section('content')
@section('plugins.Datatables', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('list.billing') }}">Lista de Faturamentos</a></li>
                    <li class="breadcrumb-item active">Lista de Parcelas</li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Faturamento</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label>Cliente</label>
                                    <input disabled class="form-control" type="text" name="client" id="client" value="{{ $saleProposal['saleProposal']['clients']->nome }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="Name">Nota Fiscal</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">nº</span>
                                        </div>
                                        <input disabled type="text" name="fiscal_note" id="fiscal_note"
                                            class="form-control" value="{{ $billing->fiscal_note }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="Name">Data da Emissão</label>
                                    <input disabled type="date" class="form-control" name="emission_date"
                                        id="emission_date" value="{{ $billing->emission_date }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Proposta</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">Produto</th>
                                    <th scope="col">Plano de contas</th>
                                    <th scope="col">Unidade</th>
                                    <th scope="col">Quantidade</th>
                                    <th scope="col" >Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($saleProposal['saleProposalProducts'] as $item)
                                    <tr>
                                        <td>{{ $item['products']->description }}</td>
                                        <td>{{ $item['chart_of_accounts']->name }}</td>
                                        <td>{{ $item->product_unit }}</td>
                                        <td>{{ $item->amount }}</td>
                                        <td>{{ "R$ ".number_format($item->total, 2, ',','.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{-- <div style="padding:1%">
                            <strong class="float-right saleProposalTotal">Total: </strong>
                            <input type="hidden" name="" id="" value="">
                        </div> --}}
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Parcelas</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="form-group">
                            {{-- <div class="card-footer">
                                    <a type="button" href="{{ route('create.installments') }}" data-toggle="tooltip"
                                    data-placement="top" title="Nova Parcela"
                                    class="btn bg-gradient-secondary btn-sm"><i class="fas fa-plus-square"></i> Novo</a>
                            </div> --}}
                        </div>

                        <table class="table table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Vencimento</th>
                                    <th>Preço</th>
                                    <th>Método de Pagamento</th>
                                    <th>Data Pagamento</th>
                                    <th>Total</th>
                                    {{-- <th class="w-25">Ações</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($parcels as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ date('d-m-Y', strtotime($item->expiration_date)) }}</td>
                                        <td>{{ "R$ ".number_format($item->price, 2, ',','.') }}</td>
                                        <td>{{ $item['payment_methods']->description }}</td>
                                        <td>{{ date('d-m-Y', strtotime($item->date_deadline)) }}</td>
                                        <td>{{ "R$ ".number_format($item->total_price, 2, ',','.')}}</td>
                                        {{-- <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('edit.installments', ['id' => $item->id]) }}" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="Editar"><i class="fas fa-pencil-alt"></i></a>
                                                <a href="{{ route('delete.installments', ['id' => $item->id]) }}" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Excluir"><i class="fas fa-trash"></i></a>
                                            </div>
                                        </td> --}}
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                        
                    </div>
                    <!-- /.card-body -->
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Processamento</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="Name">Valor Da Venda</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">R$</span>
                                        </div>
                                        <input disabled type="text" name="acumulative_billing"
                                            id="acumulative_billing" value="{{ number_format($billing->acumulative_billing, 2,',','.') }}" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="Name">Soma das parcelas</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">R$</span>
                                        </div>
                                        <input disabled type="text" value="{{ number_format($billing->total, 2,',','.') }}" name="total" id="total" class="form-control"
                                            required>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="Name">Saldo</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">R$</span>
                                        </div>
                                        <input disabled type="text" value="{{ number_format($billing->balance, 2,',','.') }}" name="balance" id="balance"
                                            class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
