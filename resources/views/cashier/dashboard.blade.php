@extends('layouts.app')
@section('title', 'Cashier workspace')
@section('content')
<div class="container">
    <div class="cashier-welcome"><span class="eyebrow">READY FOR SERVICE</span><h1>Welcome back, {{ auth()->user()->name }}.</h1><p class="text-muted">Everything you need for the next order. Let’s keep the counter moving.</p><div class="cashier-actions"><a class="cashier-action" href="{{ route('orders.create') }}"><i class="fas fa-cash-register" aria-hidden="true"></i><div><h2>Take a new order</h2><p>Browse the menu, build an order, and complete checkout.</p></div><span>Open point of sale <i class="fas fa-arrow-right ms-2"></i></span></a><a class="cashier-action" href="{{ route('orders.history') }}"><i class="fas fa-receipt" aria-hidden="true"></i><div><h2>Find a receipt</h2><p>Review previous orders, check payment details, and print receipts.</p></div><span>View order history <i class="fas fa-arrow-right ms-2"></i></span></a></div></div>
</div>
@endsection
