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
                    <li class="breadcrumb-item active">Editar Proposta</li>
                    <li class="breadcrumb-item active"><a href="{{ route('list.salesproposal') }}">Lista de Propostas</a>
                    </li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-2">
                            <form name="formSaleProposalEdit" id="formSaleProposalEdit" enctype="multipart/form-data" method="POST">
                                @csrf
                                <!-- {{ csrf_field() }} -->
                        </div> <!-- col-md3 -->
                        <div class="col-md-12">
                            <div class="card card-secondary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Editar Proposta de Venda</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Cliente:</label>
                                                <select class="form-control select2" name="clients_id" id="clients_id">
                                                  <option selected value="{{ $clientID->id }}">{{ $clientID->nome }}</option> 
                                                   @foreach ($client as $item)
                                                        <option value="{{ $item->id }}">{{ $item->nome }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="id" id="id" value="{{ $data->id }}">
                                                <input type="hidden" name="productCollection" id="productCollection" value="{{ $productCollection }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Centro de custos:</label>
                                                <select class="form-control select2" name="cost_centers_id"
                                                    id="cost_centers_id" required>
                                                    <option selected value="{{ $costCenterID->id }}">{{ $costCenterID->name }}</option> 
                                                    @foreach ($costCenter as $item)
                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Status:</label>
                                                <select class="form-control select2" name="status_sale_proposals_id"
                                                    id="status_sale_proposals_id" disabled required>
                                                    <option selected value="{{ $status_sale_proposalsID->id }}">{{ $status_sale_proposalsID->description }}</option>
                                                    @foreach ($status_sale_proposals as $item)
                                                        <option value="{{ $item->id }}">{{ $item->description }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-check">
                                              <input class="form-check-input" type="checkbox" id="gridCheck1">
                                              <label class="form-check-label" for="gridCheck1">
                                                <p class="text-success">APROVAR?</p>
                                              </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div> {{-- end card card-default --}}

                            <!-- Modal product-->
                            <div class="modal fade" id="modal_search_product" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Consultar Produtos</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                                <strong>Atenção!</strong> Selecione o <strong>Plano de Conta</strong> e o
                                                <strong>Produto</strong> para compor sua lista.
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Plano de Contas:</label>
                                                    <select class="form-control select2" style="width: 100%"
                                                        name="chartOfAccounts_search_selected"
                                                        id="chartOfAccounts_search_selected">
                                                        @foreach ($chartOfAccounts as $item)
                                                            <option value="{{ $item->id }}">
                                                                {{ substr($item->code, 0, 2) . '-' . substr($item->code, 2, 2) . ' | ' . $item->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Produtos:</label>
                                                    <select class="form-control select2" style="width: 100%"
                                                        name="product_search_selected" id="product_search_selected">
                                                        @foreach ($product as $item)
                                                            <option value="{{ $item->id }}">
                                                                {{ substr($item->code, 0, 2) . '-' . substr($item->code, 2, 4) . ' | ' . $item->description }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            {{-- <button type="button"
                                                class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                            --}}
                                            <button type="button" data-dismiss="modal" name="add_dependency_product"
                                                id="add_dependency_product" class="btn btn-primary">Continuar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card card-default" id="add_products">
                                <div class="card-header">
                                    <h3 class="card-title">Produtos*</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body" id="dep_product">
                                    <div class="row" id="dep_title_product">
                                        <div class="col-sm-3">
                                            <label>Plano de Contas</label>
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Produto</label>
                                        </div>
                                        <div class="col-sm-1">
                                            <label>Unid</label>
                                        </div>
                                        <div class="col-sm-2">
                                            <label>P Unitário</label>
                                        </div>
                                        <div class="col-sm-1">
                                            <label>Quant</label>
                                        </div>
                                        <div class="col-sm-1">
                                            <label>SubTotal</label>
                                        </div>
                                        <div class="col-sm-0">
                                            <label></label>
                                        </div>
                                    </div>
                                         {{--  --}}
                                </div>

                                <div class="card-footer">
                                    <button type="button" data-toggle='modal' data-target='#modal_search_product'
                                        class="btn btn-outline-secondary btn-sm"><i class="fas fa-plus"></i> Adicionar
                                        Produtos</button>
                                </div>

                            </div>


                            <div class="card card-default">
                                <div class="card-header">
                                    <h3 class="card-title">Processamento</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Validade</label>
                                                <input type="date" class="form-control" value="{{ $data->expiration_time }}" id="expiration_time"
                                                    placeholder="expiration_time" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Prazo de Entrega</label>
                                                <input type="date" class="form-control" value="{{ $data->deadline }}" id="deadline" placeholder="deadline"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Forma de pagamento:</label>
                                                <select class="form-control select2" name="payment_methods_id"
                                                    id="payment_methods_id" required>
                                                    @foreach ($payment_methods as $item)
                                                        <option value="{{ $item->id }}">{{ $item->description }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Parcelas</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">Nº</div>
                                                    </div>
                                                    <input type="number" value="{{ $data->installments }}" class="form-control" name="installments"
                                                        id="installments" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Desconto</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">R$</div>
                                                    </div>
                                                    <input type="text" class="form-control" value="{{ number_format($data->discount, 2, ",",".") }}" name="discount" id="discount"
                                                        required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Total</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">R$</div>
                                                    </div>
                                                    <input type="text" value="{{ number_format($data->total, 2, ",",".") }}" name="total" class="form-control" id="total"
                                                        required>
                                                        <button style="margin-left:5%" id="reload_sum" type="button" class="btn btn-light"><i class="fas fa-sync-alt"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" id="submit" name="submit"
                                            class="btn btn-secondary float-lg-right"><i class="fas fa-save"></i>
                                            Salvar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@section('plugins.scriptEditSaleProposal', true)
@endsection
