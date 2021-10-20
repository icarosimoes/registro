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
                            href="{{ route('list.contract.recurrent.instament', ['id' => $contract_recurrent->id]) }}">Lista
                            de Contratos
                            Recorrentes</a></li>
                    <li class="breadcrumb-item active">Compromissar Despesa</li>

                </ol>
            </div>
            <div class="col-md-8">
                <div class="container-fluid">
                    <div class="card card-secondary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Compromissar Despesa</h3>
                        </div>
                        <form name="formCommitment" id="formCommitment" enctype="multipart/form-data" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Nº Nota Fiscal</label>
                                    <input type="text" name="fiscal_note" id="fiscal_note" class="form-control" required>
                                    <input type="hidden" name="id" id="id" value="{{ $data->id }}">
                                    <input type="hidden" name="contract_recurrents_id" id="contract_recurrents_id" value="{{ $contract_recurrent->id }}">
                                </div>
                                <div class="form-group">
                                    <label>Data da Emissão</label>
                                    <input type="date" name="emission_date" id="emission_date" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Forma de Pagamento</label>
                                    <select class="form-control" name="payment_methods_id" id="payment_methods_id" required>
                                        @foreach ($getPaymentMethods as $item)
                                            <option value="{{ $item->id }}">{{ $item->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Vencimento</label>
                                    <input type="date" name="due_date" id="due_date" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Valor Compromissado</label>
                                    <input type="text" name="commitment_value" id="commitment_value" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Anexo</label>
                                    <input type="file" name="file" id="file" class="form-control" required>
                                </div>
                                <button type="submit" id="submit" name="submit" class="btn btn-secondary float-lg-right"><i
                                        class="fas fa-save"></i>
                                    Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    @section('plugins.scriptCommitmentContractInstallments', true)
@endsection
