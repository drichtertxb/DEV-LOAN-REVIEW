@extends('layouts.app')


@section('heading')
@endsection

@section('sidebar')

@endsection


@section('content')

@yield('sidebar')

<overview :data='{{ json_encode($data) }}'>

</overview>

@endsection
