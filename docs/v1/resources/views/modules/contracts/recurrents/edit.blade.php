@extends('adminlte::page')

@section('content')
@section('plugins.Select2', true)
@section('plugins.JqueryMask', true)
@section('plugins.JqueryMaskMoney', true)
@section('plugins.Datatables', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('list.contract.recurrent') }}">Lista de Contratos
                            Recorrentes</a></li>
                    <li class="breadcrumb-item active">Editar Contrato Recorrente</li>

                </ol>
            </div>
            <div class="col-md-12">
                <div class="container-fluid">
                    <div class="card card-secondary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Editar Contrato Recorrente</h3>
                        </div>
                        <form name="formContractEdit" id="formContractEdit" enctype="multipart/form-data" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Fornecedor</label>
                                            <select name="suppliers_id" id="suppliers_id" class="form-control select2">
                                                <option selected value="{{ $contract_recurrent->suppliers_id }}">
                                                    {{ $contract_recurrent['suppliers']->fantasy_name }}</option>
                                                @foreach ($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}">{{ $supplier->fantasy_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="id" id="id" value="{{ $contract_recurrent->id }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Centro de Custo</label>
                                        <select name="cost_centers_id" id="cost_centers_id" class="form-control select2">
                                            <option selected value="{{ $contract_recurrent->cost_centers_id }}">
                                                {{ $contract_recurrent['cost_centers']->name }}</option>
                                            @foreach ($costCenters as $costCenter)
                                                <option value="{{ $costCenter->id }}">{{ $costCenter->name }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Plano de Contas</label>
                                            <select name="chart_of_accounts_id" id="chart_of_accounts_id"
                                                class="form-control select2">
                                                <option value="{{ $contract_recurrent->chart_of_accounts_id }}">
                                                    {{ $contract_recurrent['chart_of_accounts']->name }}</option>
                                                @foreach ($chartOfAccounts as $chartOfAccount)
                                                    <option value="{{ $chartOfAccount->id }}">{{ $chartOfAccount->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col">
                                        <div class="form-group">
                                            <label>Início</label>
                                            <input type="date" value="{{ $contract_recurrent->start_period }}"
                                                name="start_period" id="start_period" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Término</label>
                                            <input type="date" value="{{ $contract_recurrent->end_period }}"
                                                name="end_period" id="end_period" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Forma de Pagamento</label>
                                            <select name="payment_methods_id" id="payment_methods_id"
                                                class="form-control select2" required>
                                                <option value="{{ $contract_recurrent->payment_methods_id }}">
                                                    {{ $contract_recurrent['payment_methods']->description }}</option>
                                                @foreach ($paymentMethods as $paymentMethod)
                                                    <option value="{{ $paymentMethod->id }}">
                                                        {{ $paymentMethod->description }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Objeto</label>
                                            <textarea class="form-control" name="object" id="object" cols="30"
                                                rows="5">{{ $contract_recurrent->object }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Preço</label>
                                            <input type="text" value="{{ number_format($contract_recurrent->price, 2, ",",".") }}" name="price"
                                                id="price" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Dia do vencimento</label>
                                            <input type="text" value="{{ $contract_recurrent->day_of_maturities }}"
                                                name="day_of_maturities" id="day_of_maturities" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="card card-dafault">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label>Indice de Reajuste</label>
                                        <input type="text" value="{{ $contract_recurrent->readjustment_index }}"
                                            name="readjustment_index" id="readjustment_index" class="form-control">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Rescisão Antecipada</label>
                                        <input type="text" value="{{ $contract_recurrent->early_termination }}"
                                            name="early_termination" id="early_termination" class="form-control">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Multa por Rescisão Antecipada</label>
                                        <input type="text" name=""
                                            value="{{ $contract_recurrent->early_termination_penalty }}"
                                            id="early_termination_penalty" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-dafault">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label>Banco</label>
                                        <input type="text" value="{{ $contract_recurrent->bank }}" name="bank" id="bank"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Agência</label>
                                        <input type="text" value="{{ $contract_recurrent->agency }}" name="agency"
                                            id="agency" class="form-control">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Conta</label>
                                        <input type="text" name="account" value="{{ $contract_recurrent->account }}"
                                            id="account" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label>Observação</label>
                                        <textarea class="form-control" name="observation" id="observation" cols="30"
                                            rows="5">{{ $contract_recurrent->observation }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" id="submit" name="submit" class="btn btn-secondary float-lg-right"><i
                                    class="fas fa-save"></i>
                                Salvar</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
@section('plugins.scriptUpdateContract', true)
@endsection
