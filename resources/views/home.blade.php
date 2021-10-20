@extends('adminlte::page')

@section('content')
@section('plugins.Chartjs', true) 
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Dashboard</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Dashboard v1</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<div class="container">
  {{-- init: alert access permission --}}
    @php
        if (isset($errorPermission) && $errorPermission == true) {
          echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                  <strong>Atenção!</strong> Você não tem permissão para o acesso desejado, contate um administrador do sistema e solicite o acesso.
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
                </div>';
        }
    @endphp
  {{-- end: alert access permission --}}
    
      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-info">
                <div class="inner">
                  <h3>{{ $totalOccurrence }}</h3>
  
                  <p>Total de Registros</p>
                </div>
                <div class="icon">
                  <i class="ion ion-clipboard"></i>
                </div>
                {{-- <a href="#" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a> --}}
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-danger">
                <div class="inner">
                  <h3>{{ $totalOccurrenceOpen }}</h3>
  
                  <p>Registros Abertos</p>
                </div>
                <div class="icon">
                  <i class="ion ion-close-circled"></i>
                </div>
                {{-- <a href="#" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a> --}}
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-success">
                <div class="inner">
                  <h3>{{ $totalOccurrenceClosed }}</h3>
  
                  <p>Registros Fechados</p>
                </div>
                <div class="icon">
                  <i class="ion ion-checkmark"></i>
                </div>
                {{-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> --}}
              </div>
            </div>
          </div>
          <h3 class="mt-4 mb-2">Gerenciamento</h3>
          <div class="row">
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-warning">
                <div class="inner">
                  <h3>{{ $totalUsers }}</h3>
  
                  <p>Usuários Registrados</p>
                </div>
                <div class="icon">
                  <i class="ion ion-person-add"></i>
                </div>
                {{-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> --}}
              </div>
            </div>
          </div>
        </div>
      </section>  
    
</div>

@section('plugins.scriptDashboard', true) 
@endsection
