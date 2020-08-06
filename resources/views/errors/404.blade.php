@extends('errors.base')

@section('content')
    <h2>{{$exception->getMessage()}}</h2>
@endsection
