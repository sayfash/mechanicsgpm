@extends('layouts.app')

@section('content')
    <!-- Auth Gate (Login & Forgot Password) -->
    @include('pages.auth.gate')

    <!-- Mechanic Workspace -->
    @include('pages.mechanic.workspace')

    <!-- Shop Admin Workspace -->
    @include('pages.shop-admin.workspace')

    <!-- Super Admin Workspace -->
    @include('pages.super-admin.workspace')
@endsection
