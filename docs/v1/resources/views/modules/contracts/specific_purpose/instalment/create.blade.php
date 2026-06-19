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
                    <li class="breadcrumb-item active"><a
                            href="{{ route('list.contract.specific_purpose.instament', ['id' => $contract_id]) }}">Lista de
                            Contratos</a></li>
                    <li class="breadcrumb-item active">Adicionar Parcelas Previstas</li>

                </ol>
            </div>
            <div class="col-md-8">
                <div class="container-fluid">
                    <div class="card card-secondary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Nova Despesa</h3>
                        </div>
                        <div class="card-body">
                            <form name="formNewExpenditure" id="formNewExpenditure" enctype="multipart/form-data"
                                method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>Dia para pagamento</label>
                                    <input type="date" name="planned_date" id="planned_date" class="form-control" required>
                                    <input type="hidden" name="contract_specific_purposes_id" id="contract_specific_purposes_id"
                                        value="{{ $contract_id->id }}">
                                </div>
                                <div class="form-group">
                                    <label>Forma de Pagamento</label>
                                    <select name="payment_methods_id" id="payment_methods_id" class="form-control select"
                                        required>
                                        @foreach ($getPaymentMethods as $payment_method)
                                            <option value="{{ $payment_method->id }}">
                                                {{ $payment_method->description }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Valor Previsto Mensal</label>
                                    <input type="text" name="monthly_value" id="monthly_value" class="form-control"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label>Quantidade de Meses Previstos</label>
                                    <input type="text" name="expected_month_amount" id="expected_month_amount"
                                        class="form-control" required>
                                </div>
                                <div id="preview">
                                    <table id="DataTableUser" class="table table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Parcela</th>
                                                <th>Valor</th>
                                            </tr>
                                        </thead>
                                        <tbody id="trPreview"></tbody>
                                    </table>
                                </div>
                                <button type="submit" id="submit" name="submit" class="btn btn-secondary float-lg-right"><i
                                        class="fas fa-save"></i>
                                    Salvar</button>
                                <button type="button" name="previewButton" id="previewButton"
                                    class="btn btn-info float-lg-left"><i class="fa fa-list-ol" aria-hidden="true"></i>
                                    Visualizar Parcelas</button>
                        </div>
                        </form>
                    </div>
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
@section('plugins.scriptCreateContractSpecificPurposeInstallments', true)
@endsection
