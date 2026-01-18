<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-person-circle"></i> Account Menu</h5>
    </div>
    <div class="list-group list-group-flush">
        <a href="{{ route('account.index') }}"
           class="list-group-item list-group-item-action {{ ($active ?? '') === 'dashboard' ? 'active' : '' }}">
            <i class="bi bi-house"></i> Dashboard
        </a>
        <a href="{{ route('account.orders.index') }}"
           class="list-group-item list-group-item-action {{ ($active ?? '') === 'orders' ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i> My Orders
        </a>
        <a href="{{ route('account.wishlist.index') }}"
           class="list-group-item list-group-item-action {{ ($active ?? '') === 'wishlist' ? 'active' : '' }}">
            <i class="bi bi-heart"></i> Wishlist
        </a>
        <a href="{{ route('account.addresses.index') }}"
           class="list-group-item list-group-item-action {{ ($active ?? '') === 'addresses' ? 'active' : '' }}">
            <i class="bi bi-geo-alt"></i> Addresses
        </a>
        <a href="{{ route('profile.edit') }}"
           class="list-group-item list-group-item-action {{ ($active ?? '') === 'profile' ? 'active' : '' }}">
            <i class="bi bi-gear"></i> Account Settings
        </a>
    </div>
</div>
