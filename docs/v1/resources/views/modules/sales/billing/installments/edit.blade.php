@extends('adminlte::page')
@section('plugins.Select2', true)
@section('plugins.JqueryMask', true)
@section('plugins.JqueryMaskMoney', true)
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('list.billing') }}">Lista de Faturamentos</a>
                    <li class="breadcrumb-item active">Nova Parcela</li>
                    </li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-2">
                            <form name="formInstallmentsEdit" id="formInstallmentsEdit" enctype="multipart/form-data"
                                method="POST">
                                @csrf
                                <!-- {{ csrf_field() }} -->
                        </div> <!-- col-md3 -->
                        <div class="col-md-12">
                            <div class="card card-default">
                                <div class="card-header">
                                    <h3 class="card-title">Nova Parcela</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-sm-3">
                                            <label for="expiration_date">Vencimento</label>
                                            <input type="date" class="form-control" name="expiration_date"
                                                id="expiration_date" placeholder="Vencimento"
                                                value="{{ $data->expiration_date }}" required>
                                            <input type="hidden" name="id" id="id" value="{{ $data->id }}">
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <label for="price">Valor</label>
                                            <input disabled type="text" class="form-control" name="price" id="price"
                                                placeholder="Valor" value="{{ number_format($data->price, 2, ',', '.') }}"
                                                required>
                                        </div>
                                        <div class="form-group col-sm-5">
                                            <label>Método de Pagamento</label>
                                            <select disabled class="form-control select2"
                                                name="payment_methods_search_selected" id="payment_methods_search_selected"
                                                required>
                                                <option selected value="{{ $data->payment_methods_id }}">
                                                    {{ $data['payment_methods']->description }}
                                                </option>
                                                @foreach ($payment_methods as $item)
                                                    <option value="{{ $item->id }}">{{ $item->description }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-sm-3">
                                            <label for="deadline">Prazo</label>
                                            <input disabled type="text" class="form-control" name="deadline" id="deadline"
                                                placeholder="Prazo" value="{{ $data->deadline }}" required>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <label for="tax">Taxa</label>
                                            <input disabled type="text" class="form-control"
                                                value="{{ number_format($data->tax, 2, ',', '.') }}" name="tax" id="tax"
                                                placeholder="Taxa" required>
                                        </div>
                                        <div class="form-group col">
                                            <label for="date_deadline">Data Recebimento</label>
                                            <input disabled type="date" class="form-control"
                                                value="{{ $data->date_deadline }}" name="date_deadline" id="date_deadline"
                                                placeholder="Data de Recebimento" required>
                                        </div>
                                        <div class="form-group col-sm-2">
                                            <label for="total_price">Total</label>
                                            <input disabled type="text" class="form-control"
                                                value="{{ number_format($data->total_price, 2, ',', '.') }}"
                                                name="total_price" id="total_price" placeholder="Preço Total" required>
                                        </div>
                                        <div class="form-group col-sm-1">
                                            <button  name="reload_calculations" id="reload_calculations" type="button" class="btn btn-block btn-default btn-sm"><i
                                                    class="fas fa-retweet"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" id="submit" name="submit"
                                        class="btn btn-secondary float-lg-right"><i class="fas fa-save"></i>
                                        Salvar</button>
                                </div>

                            </div> {{-- end card card-default --}}
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@section('plugins.scriptCreateBillingParcels', true)
@endsection
