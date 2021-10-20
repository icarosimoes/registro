@extends('adminlte::page')

@section('content')
@section('plugins.Select2', true)
<div class="container">
  <div class="row justify-content-center">
        <div class="col-sm-12">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
            <li class="breadcrumb-item active">Novo Perfil</li>
            <li class="breadcrumb-item active"><a href="{{route('list.profile')}}">Lista de Perfis</a></li>
          </ol>
        </div>
      <div class="col-md-12">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">Permissões</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0" style="height: 300px;">
                      <table class="table table-head-fixed text-nowrap">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Nome</th>
                            <th>Módulo</th>
                          </tr>
                        </thead>
                        <tbody>
                          <form name="formPermission" id="formPermission" enctype="multipart/form-data" method="POST">
                          <tr>
                            @foreach ($permission as $item)
                            <td>
                              
                              <div class="form-check">
                                  <input type="checkbox" name="permission[]" id="permission[]" class="form-check-input" value="{{ $item->id }}">
                              </div>
                            </td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->module['name'] }}</td>
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                    <!-- /.card-body -->
                    <div class="overlay-wrapper">
                      <div class="d-none overlay">
                        <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                        <div class="text-bold pt-2">Carregando...</div>
                      </div>
                  </div>
                  </div>
                  <!-- /.card -->
                  <div class="card-footer">
                    <button type="submit" class="btn btn-info float-right"><i class="fas fa-share"></i> Adicionar</button>
                  </div>
                </form>
                </div>

                <div class="col-12" style="margin-top: 15px">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">Lista de Permissões Atribuidas</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0" style="height: 300px;">
                      <table class="table table-head-fixed text-nowrap">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Módulo</th>
                            <th>Ações</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            @foreach ($acls as $acl)
                            <td>{{ $acl->id }}</td>
                            <td>{{ $acl->name }}</td>
                            <td>{{ $acl->module['name'] }}</td>
                            <td><a class="btn btn-danger btn-sm" href="{{ route('permission.remove', ['id' => $acl->id]) }}"><i class="fas fa-trash"></i></a></td>
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                    <!-- /.card-body -->
                  </div>
                  <!-- /.card -->
                </div>
            </div>
        </div>
    </div>
      </div>
  </div>
</div>
@section('plugins.scriptCreatePermission', true)
@endsection
