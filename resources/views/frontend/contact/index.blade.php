@extends('frontend.layouts.app')

@section('content')
    <h1>{{ $translation->title }}</h1>

    {!! $translation->content !!}
@endsection
