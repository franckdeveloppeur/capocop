@extends('layouts._layout')
@section('body')
<livewire:product-detail :slug="$slug" />

@endsection
@section('headItems')
<title>{{ $slug ?? 'Produit' }} - Détails</title>
@endsection