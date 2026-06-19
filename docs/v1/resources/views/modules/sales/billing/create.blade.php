@extends('adminlte::page')
@section('content')
@section('plugins.Select2', true)
@section('plugins.JqueryMask', true)
@section('plugins.JqueryMaskMoney', true)
@section('plugins.JqueryValidate', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Novo Faturamento</li>
                    <li class="breadcrumb-item active"><a href="{{ route('list.billing') }}">Lista de Faturamento</a></li>
                </ol>
            </div>
            <div class="col-md-12">
                @if (session()->exists('message'))
                    <div class="alert alert-{{ session()->get('color') }} alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        @if (session()->get('color') === 'success')
                            <i class="icon fas fa-check"></i>
                        @else
                            <i class="icon fas fa-ban"></i>
                        @endif
                        {{ session()->get('message') }}
                    </div>
                @endif
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-2">
                            <form name="formBilling" id="formBilling" enctype="multipart/form-data" method="POST">
                                @csrf
                                <!-- {{ csrf_field() }} -->
                        </div> <!-- col-md3 -->
                        <div class="col-md-12">
                            <div class="card card-secondary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Novo Faturamento</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label>Proposta de Venda</label>
                                                <select class="form-control select2" name="sale_proposal_id"
                                                    id="sale_proposal_id">
                                                    <option value="0">Selecione uma proposta...</option>
                                                    @foreach ($sale_proposal as $item)
                                                        <option value="{{ $item->id }}">
                                                            {{ 'Código da proposta: ' . $item->id . ' | Cliente: ' . $item['clients']->nome }}
                                                        </option>
                                                    @endforeach
                                                </select>
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
                                                    <input type="text" name="fiscal_note" id="fiscal_note"
                                                        class="form-control" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="Name">Data da Emissão</label>
                                                <input type="date" class="form-control" name="emission_date"
                                                    id="emission_date" required>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="Name">Anexo</label>
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" name="file" id="file"
                                                        required>
                                                    <label class="custom-file-label" for="customFile">Selecione um
                                                        arquivo...</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="overlay-wrapper">
                                        <div class="d-none overlay">
                                            <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                                            <div class="text-bold pt-2">Carregando...</div>
                                        </div>
                                    </div>
                                    <div class="overlay-wrapper overlaySelectSaleProposal">
                                        <div class="d-none overlay">
                                            <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                                            <div class="text-bold pt-2">Carregando...</div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>

                            {{-- card sale proposal products --}}
                            <div class="card card-default" id="sale_proposalProducts">
                                <div class="card-header">
                                    <h3 class="card-title">Proposta de venda</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th scope="col">Produto</th>
                                                <th scope="col">Plano de contas</th>
                                                <th scope="col">Unidade</th>
                                                <th scope="col">Quantidade</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                    <div style="padding:1%">
                                        <strong class="float-right saleProposalTotal"></strong>
                                        <input type="hidden" name="" id="" value="">
                                    </div>
                                </div>

                            </div>
                            {{-- end | card sale proposal products
                            --}}

                            <!-- Modal product-->
                            <div class="modal fade" id="modal_search_payment_methods" tabindex="-1" role="dialog"
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
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Métodos de pagamento:</label>
                                                    <select class="form-control select2" style="width: 100%"
                                                        name="payment_methods_search_selected"
                                                        id="payment_methods_search_selected">
                                                        @foreach ($payment_methods as $item)
                                                            <option value="{{ $item->id }}">
                                                                {{ $item->description }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" data-dismiss="modal" name="add_dependency_parcels"
                                                id="add_dependency_parcels" class="btn btn-primary">Continuar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- CARD PARCELS --}}
                            <div class="card card-default" id="add_percels">
                                <div class="card-header">
                                    <h3 class="card-title">Adicionar Parcelas</h3>
                                </div>
                                <div class="card-header">
                                    <div class="row">
                                        <div class="p-3 bg-light col"><div class="text-secondary" style="margin-left:29%;">FATURAMENTO</div></div>
                                        <div class="p-3 bg-light col"><div class="text-secondary" style="margin-left:49%">A RECEBER</div></div>
                                    </div>
                                </div>

                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body" id="dep_parcels">
                                    <div class="row" id="dep_title_parcels">
                                        <div class="col-sm-1">
                                            <label>Parcela</label>
                                        </div>
                                        <div class="col-sm-2">
                                            <label>Vencimento</label>
                                        </div>
                                        <div class="col-sm-1">
                                            <label>Valor</label>
                                        </div>
                                        <div class="col-sm-2">
                                            <label>Forma Pagamento</label>
                                        </div>
                                        <div class="col-sm-1">
                                            <label>Prazo</label>
                                        </div>
                                        <div class="col-sm-1">
                                            <label>Taxa(%)</label>
                                        </div>
                                        <div class="col-sm-2">
                                            <label>Data Recebimento</label>
                                        </div>
                                        <div class="col-sm-1">
                                            <label>Valor</label>
                                        </div>
                                        <div class="col-sm-0">
                                            <label></label>
                                        </div>
                                    </div>

                                    {{-- <div
                                        class="row with-border mailbox-controls dep_fc_parcels">
                                        <div class="col-sm-1">
                                            <p>1/1</p>
                                        </div>
                                        <div class="col-sm-2">
                                            <input type="date" name="parcels_date" id="parcels_date"
                                                class="form-control form-control-sm" placeholder="">
                                        </div>
                                        <div class="col-sm-1">
                                            <input type="text" name="parcels_price" id="parcels_price"
                                                class="form-control form-control-sm" placeholder="">
                                        </div>
                                        <div class="col-sm-2">
                                            <select class="form-control form-control-sm" name="parcels_payment_methods"
                                                id="parcels_payment_methods">
                                                @foreach ($client as $item)
                                                    <option value="#">Dinheiro</option>
                                                    <option value="#">Cartão</option>
                                                    <option value="#">Cheque</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-1">
                                            <input type="text" name="parcels_deadline" id="parcels_deadline"
                                                class="form-control form-control-sm" placeholder="">
                                        </div>
                                        <div class="col-sm-1">
                                            <input type="text" name="parcels_tax" id="parcels_tax"
                                                class="form-control form-control-sm" placeholder="">
                                        </div>
                                        <div class="col-sm-2">
                                            <input type="date" name="parcels_date" id="parcels_date"
                                                class="form-control form-control-sm" placeholder="">
                                        </div>
                                        <div class="col-sm-1">
                                            <input type="text" name="parcels_price_total" id="parcels_price_total"
                                                class="form-control form-control-sm" placeholder="">
                                        </div>
                                        <div class="col-sm-0">
                                            <button type="button" data-toggle="tooltip" data-placement="top"
                                                title="Remover Produto" class="btn btn-block btn-danger btn-sm"><i
                                                    class="fas fa-trash-alt"></i></button>
                                        </div>
                                    </div> --}}
                                    <div class="overlay-wrapper">
                                        <div class="d-none overlay">
                                            <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                                            <div class="text-bold pt-2">Carregando...</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <button type="button" data-toggle='modal' data-target='#modal_search_payment_methods'
                                        class="btn btn-outline-secondary btn-sm"><i class="fas fa-plus"></i> Adicionar
                                        Parcela</button>
                                </div>

                            </div>
                            {{-- CARD ADD PARCELS --}}
                            <div class="card card-default" id="processing">
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
                                                        id="acumulative_billing" class="form-control" required>
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
                                                    <input disabled type="text" name="total" id="total" class="form-control"
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
                                                    <input disabled type="text" name="balance" id="balance"
                                                        class="form-control" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="overlay-wrapper">
                                        <div class="d-none overlay">
                                            <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                                            <div class="text-bold pt-2">Carregando...</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <button type="submit" id="submit" name="submit"
                                        class="btn btn-secondary float-lg-right"><i class="fas fa-save"></i>
                                        Salvar</button>
                                </div>
                                </form>
                            </div> {{-- end | card card-default
                            --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@section('plugins.scriptCreateBilling', true)
@endsection
