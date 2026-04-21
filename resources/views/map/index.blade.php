@extends('layouts.landing')

@section('title', 'Peta Interaktif - Giscana')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 bg-blue-600">
    <div class="mb-6 text-center text-white"></div>
    @include('map.partials.interactive', ['mapLayout' => 'landing'])
</div>
@endsection
