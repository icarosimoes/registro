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
                    <li class="breadcrumb-item active">Insumos</li>
                    <li class="breadcrumb-item active"><a href="{{ route('list.input_group') }}">Grupo de Insumos</a></li>
                </ol>
            </div>
            <div class="col-md-12">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-2">
                            <form name="formInputs" id="formInputs" enctype="multipart/form-data" method="POST">
                                @csrf
                                <!-- {{ csrf_field() }} -->
                        </div> <!-- col-md3 -->
                        <div class="col-md-8">
                            <div class="card card-secondary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Novo Insumo</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <div class="card-body">
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="Name">Grupo Selecionado</label>
                                            <input type="text" class="form-control"
                                                value="{{ $input_group->code . ' - ' . $input_group->description }}"
                                                name="input_group_select" id="input_group_select" placeholder="" disabled>
                                            <input type="hidden" id="input_group" name="input_group"
                                                value="{{ $input_group->id }}">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="Name">Código</label>
                                            <input type="text" class="form-control" name="code" maxlength="7" minlength="7" id="code" placeholder=""
                                                required>
                                            <div name="msgInput" id="msgInput"></div>
                                        </div>

                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="Name">Descrição</label>
                                            <input type="text" class="form-control" name="description" id="description"
                                                placeholder="" required>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="inputState">Unidade</label>
                                            <select id="unit" name="unit" class="form-control">
                                                @foreach ($units as $item)
                                                    <option value="{{ $item->id }}">{{ $item->description }}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="Name">Custo Unitário</label>
                                            <input type="text" class="form-control" name="unit_cost" id="unit_cost"
                                                placeholder="" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Fornecedor:</label>
                                            <div class="select2-purple">
                                                <select class="select2" multiple="multiple" name="suppliers"
                                                    id="suppliers" data-placeholder="Selecionar Fornecedor(es)"
                                                    data-dropdown-css-class="select2-purple" style="width: 100%;">
                                                    @foreach ($suppliers as $item)
                                                        <option value="{{ $item->id }}">{{ $item->fantasy_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Observações</label>
                                        <textarea class="form-control" name="comments" id="comments"
                                            placeholder=""></textarea>
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
                                    <button type="submit" id="submit" name="submit"
                                        class="btn  bg-gradient-secondary float-right"><i class="far fa-save"></i>
                                        Salvar</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@section('plugins.scriptCreateInput', true)
@endsection
