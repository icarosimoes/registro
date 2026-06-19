@extends('adminlte::page')
@section('content')
@section('plugins.Datatables', true)
<div class="container">
  <div class="row justify-content-center">
    <div class="col-sm-12">
      <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
        <li class="breadcrumb-item active">Configurações</li>
      </ol>
    </div>
      <div class="col-md-12">
           <div class="card card-secondary card-outline">
                <div class="card-header">
                  <h3 class="card-title">Configurações</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <div class="form-group">
                    {{-- <div class="card-footer">
                      <a type="button" href="{{ route('new.profile') }}" data-toggle="tooltip" data-placement="top" title="Novo Usuário" class="btn bg-gradient-secondary btn-sm"><i class="fas fa-plus-square"></i> Novo</a>
                    </div> --}}
                </div>
              
                  <table name="DataTableUser" id="DataTableUser" class="table table-striped table-sm table-hover">
                      <thead>
                        <tr>
                          <th>Formulário</th>
                          <th class="text-right">Permissões</th>
                          {{-- <th class="w-25">Ações</th> --}}
                        </tr>
                      </thead>
                    <tbody>
                    <tr>
                        @foreach ($forms as $item)
                          <td>
                            {{ $item->name }}
                          </td>
                          <td class="text-right">
                            <input {{ $item->active=='yes'?'checked':'' }} data-id="{{$item->id}}" type="checkbox" class="checkbox_active">
                          </td>
                          {{-- <td> --}}
                            {{-- <div class="btn-group btn-group-sm">
                              @can('checkRouters', $route = 'edit.profile')
                              <a href="{{route('edit.profile',['id' => $role->id])}}" data-toggle="tooltip" data-placement="top" title="Editar" class="btn btn-default"><i class="fas fa-pencil-alt"></i></a>
                              @endcan 
                              @can('checkRouters', $route = 'destroy.profile') 
                              <a href="{{ route('destroy.profile', ['id' => $role->id]) }}" data-toggle="tooltip" data-placement="top" title="Excluir" class="btn btn-default"><i class="fas fa-trash"></i></a>
                              @endcan 
                            </div> --}}
                          {{-- </td> --}}
                        </tr>
                      @endforeach
                    </tbody>
                    
                  </table>
                </div>
                <!-- /.card-body -->
              </div>
        </div>
 
    </div>
</div>
@section('plugins.scriptCreateConfig', true)
@endsection


