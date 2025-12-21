@extends('layouts.admin')

@section('title', 'Dashboard - Admin')

@section('page-title', 'Dashboard')

@section('breadcrumb')
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ \App\Models\DisasterZone::count() }}</h3>
                <p>Zona Bencana</p>
            </div>
            <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <a href="{{ route('admin.disaster-zones.index') }}" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ \App\Models\EvacuationRoute::count() }}</h3>
                <p>Rute Evakuasi</p>
            </div>
            <div class="icon">
                <i class="fas fa-route"></i>
            </div>
            <a href="{{ route('admin.evacuation-routes.index') }}" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ \App\Models\EvacuationFacility::count() }}</h3>
                <p>Fasilitas Evakuasi</p>
            </div>
            <div class="icon">
                <i class="fas fa-building"></i>
            </div>
            <a href="{{ route('admin.evacuation-facilities.index') }}" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ \App\Models\AidDistributionPoint::count() }}</h3>
                <p>Titik Distribusi Bantuan</p>
            </div>
            <div class="icon">
                <i class="fas fa-hand-holding-heart"></i>
            </div>
            <a href="{{ route('admin.aid-distribution-points.index') }}" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Selamat Datang di Giscana!</h3>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <div class="mb-4">
                        <i class="fas fa-map-marked-alt fa-4x text-primary"></i>
                    </div>
                    <h4>Halo {{ Auth::user()->name }}!</h4>
                    <p class="text-muted">
                        Selamat datang di sistem informasi geografis untuk tanggap darurat bencana alam.
                    </p>
                    <a href="{{ route('map.index') }}" class="btn btn-primary">
                        <i class="fas fa-map"></i> Akses Peta Interaktif
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
