@extends('adminlte::page')
@section('content')
@section('plugins.Datatables', true)
<div class="container">
  <div class="row justify-content-center">
    <div class="col-sm-12">
      <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
        <li class="breadcrumb-item active">Lista de Usuários</li>
      </ol>
    </div>
      <div class="col-md-12">
           <div class="card card-secondary card-outline">
                <div class="card-header">
                  <h3 class="card-title">Lista de Usuários</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <div class="form-group">
                    <div class="card-footer">
                      <a type="button" href="{{ route('new.users') }}" data-toggle="tooltip" data-placement="top" title="Novo Usuário" class="btn bg-gradient-secondary btn-sm"><i class="fas fa-plus-square"></i> Novo</a>
                    </div>
                </div>
              
                  <table name="DataTableUser" id="DataTableUser" class="table table-striped table-sm table-hover projects">
                    <thead>
                    <tr>
                      <th>Nome</th>
                      <th>email</th>
                      <th class="text-center">Imagem</th>
                      <th class="text-center">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    
                    <tr>
                      @foreach ($users as $user)
                        <td>
                          <a>{{ $user->name }}</a><br>
                          <small><b>Perfil: </b> {{ $user->role['name'] }}</small>
                          </td>
                        <td>{{ $user->email }}</td>
                        <td style="text-align:center;">
                          <ul class="list-inline">
                            <li class="list-inline-item">
                              <img alt="Avatar" class="table-avatar" src="{{ url('/storage/'.$user->image) }}">
                            </li>
                          </ul> 
                        </td>
                        <td style="text-align:center;">
                          <div class="btn-group btn-group-sm">
                          {{-- @can('checkRouters', $route = 'edit.users') --}}
                           <a class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="Editar" href="{{route('edit.users',['id' => $user->id])}}"><i class="fas fa-pencil-alt"></i></a>
                          {{-- @endcan --}}
                          {{-- @can('checkRouters', $route = 'delete.users') --}}
                          <button class="btn btn-danger btn-sm remove" data-toggle="tooltip" data-id="{{ $user->id }}" data-placement="top" title="Excluir" ><i class="fas fa-trash"></i></button>
                          {{-- @endcan --}}
                        </div>
                        </td>
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

{{-- Modal delete --}}
<div class="modal" tabindex="-1" id='modal_delete' >
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Excluir</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Deseja realmente excluir ?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
        <a id="btn_delete" href="" data-id  class="btn btn-danger">Deletar</a>
      </div>
    </div>
  </div>
</div>

@section('plugins.scriptListUser', true)
@endsection


