@extends('adminlte::page')

@section('content')
@section('plugins.Select2', true)
@section('plugins.JqueryMask', true)
@section('plugins.JqueryMaskMoney', true)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active"><i class="fas fa-file-alt"></i> <a href="{{ route('list.product', ['id' => $group_product->id ]) }}">Lista de Produtos</a></li>
                    <li class="breadcrumb-item active"><i class="fas fa-file-alt"></i> Editar Produto</li>
                    
                </ol>
            </div>
            <div class="col-md-12">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-2">
                            <form name="formProductEdit" id="formProductEdit" enctype="multipart/form-data" method="POST">
                                @csrf
                                <!-- {{ csrf_field() }} -->
                        </div> <!-- col-md3 -->
                        <div class="col-md-12">
                            <div class="card card-secondary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Editar Produto</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="Name">Grupo selecionado:</label>
                                                <input type="text" class="form-control is-valid" name="groups"
                                                    id="groups"
                                                    value="{{ $group_product->id . '-' . $group_product->description }}"
                                                    placeholder="" disabled>
                                                    <input type="hidden" class="form-control is-valid" name="group_products_id"
                                                    id="group_products_id" value="{{ $group_product->id }}">
                                                    <input type="hidden" class="form-control is-valid" name="id"
                                                    id="id" value="{{ $data->id }}">
                                                    <input type="hidden" name="array_inputs" id="array_inputs" value="{{ $getProductsInputs }}">
                                                    <input type="hidden" name="array_products" id="array_products" value="{{ $getProductProducts }}">
                                            </div>
                                        </div> 
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="Name">Código:</label>
                                                <input type="text" class="form-control" maxlength="7" minlength="7"
                                                    name="code" value="{{ $data->code }}" id="code" placeholder="" required>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label>Unidade:</label>
                                                <select class="form-control select2" name="units_id" id="units_id" required>
                                                  <option value="{{ $data['units']->id }}" selected>{{ $data['units']->description }}</option>  
                                                  @foreach ($units as $item)
                                                        <option value="{{ $item->id }}">{{ $item->description }}</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="Name">Descrição:</label>
                                                <input type="text" value="{{ $data->description }}" class="form-control" name="description" id="description"
                                                    placeholder="" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- INSUMOS --}}
                            <div class="card card-default" id="add_inputs">
                                <div class="card-header">
                                    <h3 class="card-title">Adicionar Insumos</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body" id="dep">
                                    <div class="row" id="labels_inputs">
                                        <div style="margin-left:1%;" class="col-sm-1">
                                            <label>Insumo</label>
                                        </div>
                                        <div class="col-sm-5">
                                            <label>Descrição</label>
                                        </div>
                                        <div class="col-sm-1">
                                            <label>UNID</label>
                                        </div>
                                        <div class="col-sm-1">
                                            <label>QUANT</label>
                                        </div>
                                        <div class="col-sm-1">
                                            <label>P UNIT</label>
                                        </div>
                                        <div class="col-sm-2">
                                            <label>Sub-Total</label>
                                        </div>
                                        <div class="col-sm-0">
                                            <label></label>
                                        </div>
                                    </div>


                                </div>
                                <!-- /.card-body  insumos-->

                                <div class="card-footer">
                                    <button type="button" data-toggle='modal' data-target='#modal_search_inputs'
                                        class="btn btn-outline-secondary btn-sm"><i class="fas fa-plus"></i> Adicionar
                                        Insumos</button>
                                </div>

                            </div> {{-- END - CARD-INSUMOS --}}

                            <!-- Modal -->
                            <div class="modal fade" id="modal_search_inputs" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Consultar Insumo</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col">
                                                    <div class="form-group">
                                                        <label>Insumos:</label>
                                                        <select class="form-control select2" style="width: 100%"
                                                            name="input_search_selected" id="input_search_selected">
                                                            @foreach ($inputs as $item)
                                                                <option value="{{ $item->id }}">
                                                                    {{ substr($item->code, 0, 2) . '-' . substr($item->code, 2, 4) . ' | ' . $item->description }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="modal-footer">
                                            {{-- <button type="button"
                                                class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                            --}}
                                            <button type="button" data-dismiss="modal" name="add_dependency_input"
                                                id="add_dependency_input" class="btn btn-primary">Selecionar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{--############################## START CARD-PRODUTOS
                            #############################--}}
                            <div class="card card-default" id="add_products">
                                <div class="card-header">
                                    <h3 class="card-title">Adicionar Produtos</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body" id="dep_product">
                                    <div class="row" id="labels_product">
                                        <div style="margin-left:1%;" class="col-sm-1">
                                            <label>Produto</label>
                                        </div>
                                        <div class="col-sm-5">
                                            <label>Descrição</label>
                                        </div>
                                        <div class="col-sm-1">
                                            <label>UNID</label>
                                        </div>
                                        <div class="col-sm-1">
                                            <label>QUANT</label>
                                        </div>
                                        <div class="col-sm-1">
                                            <label>P UNIT</label>
                                        </div>
                                        <div class="col-sm-2">
                                            <label>Sub-Total</label>
                                        </div>
                                        <div class="col-sm-0">
                                            <label></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <button type="button" data-toggle='modal' data-target='#modal_search_product'
                                        class="btn btn-outline-secondary btn-sm"><i class="fas fa-plus"></i> Adicionar
                                        Produtos</button>
                                </div>

                            </div>

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
                                            <div class="row">
                                                <div class="col">
                                                    <div class="form-group">
                                                        <label>Insumos:</label>
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

                                        </div>
                                        <div class="modal-footer">
                                            {{-- <button type="button"
                                                class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                            --}}
                                            <button type="button" data-dismiss="modal" name="add_dependency_product"
                                                id="add_dependency_product" class="btn btn-primary">Selecionar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- end modal --}}


                            {{--##################### CUSTOS - FINAL #########################--}}
                            <div class="card card-default">
                                <div class="card-header">
                                    <h3 class="card-title">Custos</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Preço de venda</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">R$</div>
                                                    </div>
                                                    <input type="text" value="{{ number_format($data->sale_price, 2,',','.')}}" name="costs_sale_price" id="costs_sale_price"
                                                        class="form-control" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Custo Unitário</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">R$</div>
                                                    </div>
                                                    <input type="text" value="{{ number_format($data->unit_cost, 2,',','.')}}" name="costs_unit_cost" id="costs_unit_cost"
                                                        class="form-control" disabled required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Margem de contribuição</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">R$</div>
                                                    </div>
                                                    <input type="text" value="{{ number_format($data->contribution_margin, 2,',','.')}}" name="costs_contribution_margin_real"
                                                    id="costs_contribution_margin_real" class="form-control" disabled
                                                    required>
                                                    <button style="margin-left:5%" id="reload_sum" name="reload_sum" type="button" class="btn btn-light"><i class="fas fa-sync-alt"></i></button>
                                                </div>
                                                <input type="hidden" value="{{ number_format($data->costs_contribution_margin_percent, 2,',','.') }}" name="costs_contribution_margin_percent"
                                                    id="costs_contribution_margin_percent" required>
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
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" id="submit" name="submit" class="btn btn-secondary float-right"><i
                                            class="fas fa-save"></i> Salvar</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@section('plugins.scriptEditProduct', true)
@endsection
