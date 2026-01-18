@extends('layouts.admin')

@section('title', 'Gift Card Details')

@section('content')
@php
    $code = $giftCard['code'] ?? 'N/A';
    $initialBalance = $giftCard['initial_balance'] ?? $giftCard['amount'] ?? 0;
    $currentBalance = $giftCard['balance'] ?? $giftCard['current_balance'] ?? 0;
    $redeemed = $initialBalance - $currentBalance;
    $isExpired = isset($giftCard['expires_at']) && strtotime($giftCard['expires_at']) < time();
    $isUsed = $currentBalance <= 0;
    $isVoided = ($giftCard['status'] ?? '') == 'voided';
    $statusText = $isVoided ? 'Voided' : ($isExpired ? 'Expired' : ($isUsed ? 'Used' : 'Active'));
    $statusClass = $isVoided ? 'inactive' : ($isExpired ? 'pending' : ($isUsed ? 'inactive' : 'active'));
@endphp

<div class="page-header">
    <h1>Gift Card {{ $code }}</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.giftcards') }}">Gift Cards</a></li>
            <li class="breadcrumb-item active">{{ $code }}</li>
        </ol>
    </nav>
</div>

@if($giftCard)
<div class="row g-4">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Balance Info -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="icon primary">
                        <i class="bi bi-gift"></i>
                    </div>
                    <div class="value">${{ number_format($initialBalance, 2) }}</div>
                    <div class="label">Initial Balance</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="icon success">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div class="value">${{ number_format($currentBalance, 2) }}</div>
                    <div class="label">Current Balance</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="icon info">
                        <i class="bi bi-bag-check"></i>
                    </div>
                    <div class="value">${{ number_format($redeemed, 2) }}</div>
                    <div class="label">Total Redeemed</div>
                </div>
            </div>
        </div>

        <!-- Transaction History -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Transaction History</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Balance After</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($giftCard['transactions'] ?? [] as $transaction)
                        <tr>
                            <td>{{ isset($transaction['created_at']) ? date('M d, Y', strtotime($transaction['created_at'])) : 'N/A' }}</td>
                            <td>
                                @if(($transaction['type'] ?? '') == 'redemption')
                                <span class="badge bg-danger">Redemption</span>
                                @elseif(($transaction['type'] ?? '') == 'purchase')
                                <span class="badge bg-success">Purchase</span>
                                @elseif(($transaction['type'] ?? '') == 'refund')
                                <span class="badge bg-warning">Refund</span>
                                @elseif(($transaction['type'] ?? '') == 'adjustment')
                                <span class="badge bg-info">Adjustment</span>
                                @else
                                <span class="badge bg-secondary">{{ ucfirst($transaction['type'] ?? 'Unknown') }}</span>
                                @endif
                            </td>
                            <td class="{{ ($transaction['amount'] ?? 0) < 0 ? 'text-danger' : 'text-success' }}">
                                {{ ($transaction['amount'] ?? 0) >= 0 ? '+' : '' }}${{ number_format(abs($transaction['amount'] ?? 0), 2) }}
                            </td>
                            <td>${{ number_format($transaction['balance_after'] ?? 0, 2) }}</td>
                            <td>
                                @if($transaction['order_id'] ?? null)
                                <a href="{{ route('admin.orders.detail', $transaction['order_id']) }}">#{{ $transaction['order_number'] ?? $transaction['order_id'] }}</a>
                                @else
                                {{ $transaction['note'] ?? 'Initial purchase' }}
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No transactions found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Personal Message -->
        @if($giftCard['message'] ?? null)
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Gift Message</h5>
            </div>
            <div class="card-body">
                <div class="p-3 bg-light rounded">
                    <p class="mb-1"><strong>From:</strong> {{ $giftCard['sender_name'] ?? 'Anonymous' }}</p>
                    <p class="mb-1"><strong>To:</strong> {{ $giftCard['recipient_name'] ?? 'Recipient' }}</p>
                    <hr>
                    <p class="mb-0 fst-italic">"{{ $giftCard['message'] }}"</p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Status & Actions -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Status & Actions</h5>
            </div>
            <div class="card-body">
                <p><strong>Status:</strong> <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></p>
                <p><strong>Created:</strong> {{ isset($giftCard['created_at']) ? date('M d, Y', strtotime($giftCard['created_at'])) : 'N/A' }}</p>
                <p><strong>Expires:</strong> {{ isset($giftCard['expires_at']) ? date('M d, Y', strtotime($giftCard['expires_at'])) : 'N/A' }}</p>
                <hr>
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary" {{ $isUsed || $isExpired || $isVoided ? 'disabled' : '' }}><i class="bi bi-envelope"></i> Resend Email</button>
                    <button class="btn btn-outline-secondary" {{ $isExpired || $isVoided ? 'disabled' : '' }}><i class="bi bi-plus-circle"></i> Adjust Balance</button>
                    <button class="btn btn-outline-danger" {{ $isUsed || $isExpired || $isVoided ? 'disabled' : '' }}><i class="bi bi-x-circle"></i> Void Card</button>
                </div>
            </div>
        </div>

        <!-- Gift Card Code -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Gift Card Code</h5>
            </div>
            <div class="card-body text-center">
                <h3 class="font-monospace mb-3">{{ $code }}</h3>
                <button class="btn btn-sm btn-outline-primary" onclick="navigator.clipboard.writeText('{{ $code }}')">
                    <i class="bi bi-clipboard"></i> Copy Code
                </button>
            </div>
        </div>

        <!-- Purchaser Info -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Purchaser</h5>
            </div>
            <div class="card-body">
                <p><strong>{{ $giftCard['sender_name'] ?? $giftCard['purchaser_name'] ?? 'Unknown' }}</strong></p>
                <p><i class="bi bi-envelope me-2"></i> {{ $giftCard['purchaser_email'] ?? 'N/A' }}</p>
                @if($giftCard['purchase_order_id'] ?? null)
                <p><strong>Purchase Order:</strong> <a href="{{ route('admin.orders.detail', $giftCard['purchase_order_id']) }}">#{{ $giftCard['purchase_order_number'] ?? $giftCard['purchase_order_id'] }}</a></p>
                @endif
            </div>
        </div>

        <!-- Recipient Info -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Recipient</h5>
            </div>
            <div class="card-body">
                <p><strong>{{ $giftCard['recipient_name'] ?? 'Unknown' }}</strong></p>
                <p><i class="bi bi-envelope me-2"></i> {{ $giftCard['recipient_email'] ?? $giftCard['email'] ?? 'N/A' }}</p>
                @if($giftCard['delivered_at'] ?? $giftCard['activated_at'] ?? null)
                <p><strong>Delivered:</strong> {{ date('M d, Y g:i A', strtotime($giftCard['delivered_at'] ?? $giftCard['activated_at'])) }}</p>
                @endif
            </div>
        </div>

        <!-- Design -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Card Design</h5>
            </div>
            <div class="card-body text-center">
                <div class="bg-light rounded p-4 mb-2">
                    <i class="bi bi-gift" style="font-size: 3rem; color: var(--prt-brown);"></i>
                    <p class="mb-0 mt-2">{{ $giftCard['design_template'] ?? 'Standard Theme' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="alert alert-warning">Gift card not found</div>
@endif
@endsection
