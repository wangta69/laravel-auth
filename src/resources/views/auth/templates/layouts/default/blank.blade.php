@extends('layouts.app')

@section('body_class', '')

@section('page')


    @yield('content')

@stop

@section('styles')
@parent

@endsection

@section('scripts')
@parent

@endsection