@extends('layouts._layout')
@section('body')

@include('components.index-custom-components-section-1')
@include('components.index-custom-components-section-2')

<livewire:recent-products />
@include('components.index-custom-components-section-3')

<!-- best seller -->
@include('components.best-ventes')
@endsection
@section('headItems')
<title>Capocop-Shop — Homepage</title>
@endsection