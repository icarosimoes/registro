@extends('adminlte::page')
@section('content')
@section('plugins.JqueryMask', true)
@section('plugins.JqueryMaskMoney', true)
@section('plugins.Select2', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('list.contract.recurrent') }}">Lista de Contratos
                            Recorrentes</a></li>
                    <li class="breadcrumb-item active">Novo Contrato Recorrente</li>

                </ol>
            </div>
            <div class="col-md-12">
                <div class="container-fluid">
                    <div class="card card-secondary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Novo Contrato Recorrente</h3>
                        </div>
                        <form name="formContract" id="formContract" enctype="multipart/form-data" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Fornecedor</label>
                                            <select name="suppliers_id" id="suppliers_id" class="form-control select2" required>
                                                @foreach ($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}">{{ $supplier->fantasy_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Centro de Custo</label>
                                        <select name="cost_centers_id" id="cost_centers_id" class="form-control select2" required>
                                            @foreach ($cost_centers as $cost_center)
                                                <option value="{{ $cost_center->id }}">{{ $cost_center->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Plano de Contas</label>
                                            <select name="chart_of_accounts_id" id="chart_of_accounts_id"
                                                class="form-control select2" required>
                                                @foreach ($chart_of_accounts as $chart_of_account)
                                                    <option value="{{ $chart_of_account->id }}">
                                                        {{ $chart_of_account->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Início</label>
                                            <input type="date" name="start_period" id="start_period" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Término</label>
                                            <input type="date" name="end_period" id="end_period" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Forma de Pagamento</label>
                                            <select name="payment_methods_id" id="payment_methods_id"
                                                class="form-control select2" required>
                                                @foreach ($payment_methods as $payment_method)
                                                    <option value="{{ $payment_method->id }}">
                                                        {{ $payment_method->description }}</option>
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
                                                rows="5"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Preço</label>
                                            <input type="text" name="price" id="price" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Dia do vencimento</label>
                                            <input type="text" name="day_of_maturities" id="day_of_maturities"
                                                class="form-control" required>
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
                                        <input type="text" name="readjustment_index" id="readjustment_index"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Rescisão Antecipada</label>
                                        <input type="text" name="early_termination" id="early_termination"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Multa por Rescisão Antecipada</label>
                                        <input type="text" name="early_termination_penalty" id="early_termination_penalty"
                                            class="form-control">
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
                                        <input type="text" name="bank" id="bank" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Agência</label>
                                        <input type="text" name="agency" id="agency" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Conta</label>
                                        <input type="text" name="account" id="account" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label>Observação</label>
                                        <textarea class="form-control" name="observation" id="observation" cols="30"
                                            rows="5"></textarea>
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
                <div class="overlay-wrapper">
                    <div class="d-none overlay">
                      <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                      <div class="text-bold pt-2">Carregando...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@section('plugins.scriptCreateContract', true)
@endsection
