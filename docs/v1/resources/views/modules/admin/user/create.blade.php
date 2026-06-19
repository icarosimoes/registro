@extends('adminlte::page')

@section('content')
@section('plugins.Select2', true)
<div class="container">
  <div class="row justify-content-center">
        <div class="col-sm-12">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
            <li class="breadcrumb-item active">Novo Usuário</li>
            <li class="breadcrumb-item active"><a href="{{route('list.users')}}">Lista de Usuários</a></li>
          </ol>
        </div>
      <div class="col-md-12">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                  <form name="formUser" id="formUser" enctype="multipart/form-data" method="POST">
                    @csrf <!-- {{ csrf_field() }} -->
                    <div class="card card-secondary card-outline">
                        <div class="card-body box-profile">
                          <div class="text-center">
                            <img id="imgphoto" name="imgphoto" style="width:130px;heigth:130px" class="profile-user-img img-fluid img-circle" src="{{asset('img/user_default.png')}}" alt="User profile picture">
                          </div>
                          {{-- <h3 class="profile-username text-center">{{ mb_strimwidth(ucwords(Auth::user()->name), 0, 20, "...")}}</h3>
                          <p class="text-muted text-center"><b>Perfil: </b>Administrador</p> --}}
                          <div class="form-group">
                            <label for="exampleInputFile"></label>
                            <div class="input-group">
                              <div class="custom-file">
                                <input type="file" class="custom-file-input" name="photo" id="photo">
                                <label class="custom-file-label" for="exampleInputFile">Selecionar</label>
                              </div>
                              {{-- <div class="input-group-append">
                                <span class="input-group-text" id="">Upload</span>
                              </div> --}}
                            </div>
                          </div>
                        </div>
                        <!-- /.card-body -->
                      </div>
                </div> <!-- col-md3 -->
                <div class="col-md-9">
                    <div class="card card-secondary card-outline">
                        <div class="card-header">
                          <h3 class="card-title">Novo Usuário</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                          <div class="card-body">
                           <div class="form-group">
                              <label for="Name">Nome:</label>
                              <input type="text" class="form-control" name="name" id="name" placeholder="ex: Marcos Farias" required>
                            </div>
                            <div class="form-group">
                              <label for="exampleInputEmail1">Email:</label>
                              <input type="email" class="form-control" name="email" id="email" placeholder="ex: exemplo@hotmail.com" required>
                            </div>
                            <div class="form-group">
                              <label for="exampleInputPassword1">Senha:</label>
                              <input type="password" class="form-control" name="password" id="password" required>
                            </div>
                            
                            <div class="form-group">
                              <label>Perfil:</label>
                              <select class="form-control select2" name="profile" id="profile">
                                @foreach ($profiles as $profile)
                                <option value="{{ $profile->id }}">{{ $profile->name }}</option>
                                @endforeach
                              </select>
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
</div>
@section('plugins.scriptCreateUser', true)
@endsection
