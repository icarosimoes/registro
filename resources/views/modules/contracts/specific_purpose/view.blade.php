@extends('adminlte::page')

@section('content')
@section('plugins.Select2', true)
@section('plugins.Datatables', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('list.contract.specificPurpose') }}">Lista de Contratos</a></li>
                    <li class="breadcrumb-item active">Visualizar Contrato Propósito específico</li>

                </ol>
            </div>
            <div class="col-md-12">
                <div class="container-fluid">
                    <div class="card card-secondary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Visualizar Contrato Propósito específico</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label>Fornecedor:</label>
                                        <p>{{ $contract_recurrent['suppliers']->fantasy_name }}</p>
                                        {{-- <select name="suppliers_id" id="suppliers_id" class="form-control select2">
                                            <option selected value="{{ $contract_recurrent->suppliers_id }}">{{ $contract_recurrent['suppliers']->fantasy_name }}</option>
                                            @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->fantasy_name }}</option>
                                            @endforeach 
                                        </select> --}}
                                    </div>
                                </div>
                                <div class="col">
                                    <label>Centro de Custo:</label>
                                    <p>{{ $contract_recurrent['cost_centers']->name }}</p>
                                    {{-- <select name="cost_centers_id" id="cost_centers_id" class="form-control select2">
                                        <option selected value="{{ $contract_recurrent->cost_centers_id }}">{{ $contract_recurrent['cost_centers']->name }}</option>
                                        @foreach ($costCenters as $costCenter)
                                           <option value="{{ $costCenter->id }}">{{ $costCenter->name }}</option>
                                        @endforeach
                                        
                                    </select> --}}
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Plano de Contas:</label>
                                        <p>{{ $contract_recurrent['chart_of_accounts']->name }}</p>
                                        {{-- <select name="chart_of_accounts_id" id="chart_of_accounts_id" class="form-control select2">
                                            <option value="{{ $contract_recurrent->chart_of_accounts_id }}">{{ $contract_recurrent['chart_of_accounts']->name }}</option>
                                            @foreach ($chartOfAccounts as $chartOfAccount)
                                               <option value="{{ $chartOfAccount->id }}">{{ $chartOfAccount->name }}</option>
                                            @endforeach
                                        </select> --}}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                
                                <div class="col">
                                    <div class="form-group">
                                        <label>Início:</label>
                                        <p>{{ (new DateTime($contract_recurrent->start_period))->format("d/m/Y") }}</p>
                                        {{-- <input type="date" value="{{ $contract_recurrent->start_period }}" name="start_period" id="start_period" class="form-control"> --}}
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Término:</label>
                                        <p>{{ (new dateTime($contract_recurrent->end_period))->format("d/m/Y") }}</p>
                                        {{-- <input type="date" value="{{ $contract_recurrent->end_period }}" name="end_period" id="end_period" class="form-control"> --}}
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Forma de Pagamento:</label>
                                        <p>{{ $contract_recurrent['payment_methods']->description }}</p>
                                        {{-- <select name="payment_methods_id" id="payment_methods_id"
                                            class="form-control select2" required>
                                            <option value="{{ $contract_recurrent->payment_methods_id }}">{{ $contract_recurrent['payment_methods']->description }}</option>
                                            @foreach ($paymentMethods as $paymentMethod)
                                                <option value="{{ $paymentMethod->id }}">
                                                    {{ $paymentMethod->description }}</option>
                                            @endforeach
                                        </select> --}}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label>Objeto:</label>
                                        <p>{{ $contract_recurrent->object }}</p>
                                        {{-- <textarea class="form-control" name="object" id="object" cols="30" rows="5">{{ $contract_recurrent->object }}</textarea> --}}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label>Preço:</label>
                                        <p>{{ "R$ ".number_format($contract_recurrent->price, 2, ",",".") }}</p>
                                        {{-- <input type="text" value="{{ $contract_recurrent->price }}" name="price" id="price" class="form-control"> --}}
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Dia do vencimento:</label>
                                        <p>{{ $contract_recurrent->day_of_maturities }}</p>
                                        {{-- <input type="date" value="{{ }}" name="day_of_maturities" id="day_of_maturities" class="form-control"> --}}
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
                                        <label>Indice de Reajuste:</label>
                                        <p>{{ $contract_recurrent->readjustment_index }}</p>
                                        {{-- <input type="text" value="{{ $contract_recurrent->readjustment_index }}" name="readjustment_index" id="readjustment_index" class="form-control"> --}}
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Rescisão Antecipada:</label>
                                        <p>{{ $contract_recurrent->early_termination }}</p>
                                        {{-- <input type="text" value="{{ $contract_recurrent->early_termination }}" name="early_termination" id="early_termination" class="form-control"> --}}
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Multa por Rescisão Antecipada:</label>
                                        <p>{{ $contract_recurrent->early_termination_penalty }}</p>
                                        {{-- <input type="text" name="" value="{{ $contract_recurrent->early_termination_penalty }}" id="early_termination_penalty" class="form-control"> --}}
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
                                        <label>Banco:</label>
                                        <p>{{ $contract_recurrent->bank }}</p>
                                        {{-- <input type="text" value="{{ $contract_recurrent->bank }}" name="bank" id="bank" class="form-control"> --}}
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Agência:</label>
                                        <p>{{ $contract_recurrent->agency }}</p>
                                        {{-- <input type="text" value="{{ $contract_recurrent->agency }}" name="agency" id="agency" class="form-control"> --}}
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Conta:</label>
                                        <p>{{ $contract_recurrent->account }}</p>
                                        {{-- <input type="text" name="account" value="{{ $contract_recurrent->account }}" id="account" class="form-control"> --}}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label>Observação:</label>
                                        <p>{{ $contract_recurrent->observation }}</p>
                                        {{-- <textarea class="form-control" name="observation" id="observation" cols="30" rows="5">{{ $contract_recurrent->observation }}</textarea> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card card-default">
                        <div class="card-header">
                            <h3 class="card-title">Parcelas Compromissadas</h3>
                        </div>
                        <div class="card-body">
                            <table name="DataTableUser" id="DataTableUser" class="table table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Nota Fiscal</th>
                                    <th>Vencimento</th>
                                    <th>Valor Compromissado</th>
                                    <th>Status</th>
                                    <th>Data Pagamento</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($installments as $installment)
                                    <tr>
                                        <td>{{ $installment->fiscal_note }}</td>
                                        <td>{{ (new DateTime($installment->due_date))->format("d/m/Y") }}</td>
                                        <td>{{ "R$ ".number_format($installment->commitment_value, 2,",",".") }}</td>
                                        <td>
                                        @if ($installment->status_id == 1)
                                            {{ "Compromissado" }}
                                        @endif
                                        </td>
                                        <td>{{ (new DateTime($installment->due_date))->format("d/m/Y") }}</td>
                                        <td>
                                            <a href="{{ route('specific_purpose.installments.download',['id' => $installment->id]) }}" class="btn btn-sm btn-default"><i class="fas fa-download"></i>
                                                Anexo</a>
                                        </td>
                                    </tr>
                                    @endforeach
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
