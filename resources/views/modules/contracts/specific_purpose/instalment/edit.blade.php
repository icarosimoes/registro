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
                            href="{{ route('list.contract.specific_purpose.instament', ['id' => $contract->id]) }}">Lista de
                            Contratos</a></li>
                    <li class="breadcrumb-item active">Editar Parcelas Previstas</li>

                </ol>
            </div>
            <div class="col-md-8">
                <div class="container-fluid">
                    <div class="card card-secondary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Editar Parcela</h3>
                        </div>
                        <div class="card-body">
                            <form name="formNewExpenditureEdit" id="formNewExpenditureEdit" enctype="multipart/form-data"
                                method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>Dia para pagamento</label>
                                    <input type="date" value="{{ $data->planned_date }}" name="planned_date" id="planned_date" class="form-control" required>
                                    <input type="hidden" name="contract_specific_purposes_id" id="contract_specific_purposes_id"
                                        value="{{ $contract->id }}">
                                        <input type="hidden" name="id" id="id"
                                        value="{{ $data->id }}">
                                </div>
                                <div class="form-group">
                                    <label>Forma de Pagamento</label>
                                    <select name="payment_methods_id" id="payment_methods_id" class="form-control select"
                                        required>
                                        <option selected value="{{ $data->payment_methods_id }}">{{ $data['payment_methods']->description }}</option>
                                        @foreach ($getPaymentMethods as $payment_method)
                                            <option value="{{ $payment_method->id }}">
                                                {{ $payment_method->description }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Valor Previsto Mensal</label>
                                    <input type="text" value="{{ number_format($data->monthly_value, 2, ",",".") }}" name="monthly_value" id="monthly_value" class="form-control"
                                        required>
                                </div>
                                
                                <button type="submit" id="submit" name="submit" class="btn btn-secondary float-lg-right"><i
                                        class="fas fa-save"></i>
                                    Salvar</button>
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
@section('plugins.scriptUpdateContractSpecificPurposeInstallments', true)
@endsection
