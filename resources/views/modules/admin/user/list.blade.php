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
                      <th>Imagem</th>
                      <th>Ações</th>
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
                           <a class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="top" title="Editar" href="{{route('edit.users',['id' => $user->id])}}"><i class="fas fa-pencil-alt"></i></a>
                          {{-- @endcan --}}
                          {{-- @can('checkRouters', $route = 'delete.users') --}}
                          <a class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="top" title="Excluir" href="{{ route('delete.users', ['id' => $user->id]) }}"><i class="fas fa-trash"></i></a>
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
@endsection


