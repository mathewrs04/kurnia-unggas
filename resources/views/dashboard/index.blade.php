@extends('layouts.app')
@section('content_title', 'Dashboard')
@section('content')
<div class="card">
    <div class="card-body">
        Welcome to Kurnia Unggas, <strong>{{ auth()->user()->name }}</strong>!
    </div>
</div>
@endsection
