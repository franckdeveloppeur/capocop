@extends('layouts._layout')
@section('body')
    <livewire:order-confirmation :orderId="$orderId" />
@endsection
@section('headItems')
<title>Confirmation de commande - Capocop</title>
@endsection

