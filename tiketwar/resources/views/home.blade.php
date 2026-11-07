@extends('layouts.main') 
  
@section('title', 'Beranda') 
  
@section('content') 
    <img src="{{ asset('images/banner.jpg') }}" class="img-fluid rounded mb-4" alt="Banner konser 
TiketWar"> 
  
    <div class="hero text-center mb-4"> 
        <h1 class="display-5 fw-bold">Selamat Datang di TiketWar</h1> 
        <p class="lead">Tempat war tiket konser paling gercep!</p> 
        <a href="{{ route('konser.index') }}" class="btn btn-light btn-lg">Lihat Daftar Konser</a> 
    </div> 

    <div class="row text-center mt-4">
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Konser</h5>
                    <h2>{{ $jumlahKonser }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Promotor</h5>
                    <h2>{{ $jumlahPromotor }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Sisa Tiket</h5>
                    <h2>{{ $totalSisa }}</h2>
                </div>
            </div>
        </div>
    </div>
@endsection 