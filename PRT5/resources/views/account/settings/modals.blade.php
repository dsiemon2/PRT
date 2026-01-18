{{-- Edit Name Modal --}}
<div class="modal fade" id="editNameModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--prt-brown); color: white;">
                <h5 class="modal-title"><i class="bi bi-person"></i> Edit Name</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editNameForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="first_name" value="{{ $user->first_name }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name" value="{{ $user->last_name }}" required>
                        </div>
                    </div>
                    <div id="editNameMessage"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitEditName()">
                    <i class="bi bi-check-circle"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Edit Email Modal --}}
<div class="modal fade" id="editEmailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--prt-brown); color: white;">
                <h5 class="modal-title"><i class="bi bi-envelope"></i> Edit Email</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editEmailForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password (for security)</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div id="editEmailMessage"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitEditEmail()">
                    <i class="bi bi-check-circle"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Edit Phone Modal --}}
<div class="modal fade" id="editPhoneModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--prt-brown); color: white;">
                <h5 class="modal-title"><i class="bi bi-telephone"></i> Edit Phone</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editPhoneForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" name="phone" value="{{ $user->phone }}" placeholder="(555) 123-4567">
                    </div>
                    <div id="editPhoneMessage"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitEditPhone()">
                    <i class="bi bi-check-circle"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Change Password Modal --}}
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--prt-brown); color: white;">
                <h5 class="modal-title"><i class="bi bi-lock"></i> Change Password</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" class="form-control" name="new_password" required>
                        <small class="text-muted">Minimum 8 characters</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" name="new_password_confirmation" required>
                    </div>
                    <div id="changePasswordMessage"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitChangePassword()">
                    <i class="bi bi-check-circle"></i> Change Password
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Add Address Modal --}}
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--prt-brown); color: white;">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Add New Address</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addAddressForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Address Type</label>
                        <select class="form-select" name="address_type" required>
                            <option value="billing">Billing</option>
                            <option value="shipping">Shipping</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address Line 1</label>
                        <input type="text" class="form-control" name="address_line1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address Line 2 (Optional)</label>
                        <input type="text" class="form-control" name="address_line2">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" name="city" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">State</label>
                            <input type="text" class="form-control" name="state" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Zip Code</label>
                            <input type="text" class="form-control" name="zip_code" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone (Optional)</label>
                        <input type="tel" class="form-control" name="phone">
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_default" id="setDefaultAddress">
                        <label class="form-check-label" for="setDefaultAddress">Set as default address</label>
                    </div>
                    <div id="addressFormMessage"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitAddressForm()">
                    <i class="bi bi-check-circle"></i> Save Address
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Add Card Modal --}}
<div class="modal fade" id="addCardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--prt-brown); color: white;">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Add Payment Card</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Card management is handled securely. Contact support to update payment methods.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Add Gift Card Modal --}}
<div class="modal fade" id="addGiftCardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--prt-brown); color: white;">
                <h5 class="modal-title"><i class="bi bi-gift"></i> Add Gift Card</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addGiftCardForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Gift Card Code</label>
                        <input type="text" class="form-control" name="card_code" placeholder="Enter gift card code" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">PIN (if required)</label>
                        <input type="text" class="form-control" name="pin" placeholder="Enter PIN">
                    </div>
                    <div id="giftCardFormMessage"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitGiftCardForm()">
                    <i class="bi bi-check-circle"></i> Add Gift Card
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Modal form submissions
async function submitEditName() {
    const form = document.getElementById('editNameForm');
    const formData = new FormData(form);
    const messageDiv = document.getElementById('editNameMessage');

    try {
        const response = await fetch('{{ route("account.settings.name") }}', { method: 'POST', body: formData });
        const data = await response.json();

        if (data.success) {
            messageDiv.innerHTML = '<div class="alert alert-success">Name updated!</div>';
            setTimeout(() => location.reload(), 1000);
        } else {
            messageDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
        }
    } catch (error) {
        messageDiv.innerHTML = '<div class="alert alert-danger">An error occurred</div>';
    }
}

async function submitEditEmail() {
    const form = document.getElementById('editEmailForm');
    const formData = new FormData(form);
    const messageDiv = document.getElementById('editEmailMessage');

    try {
        const response = await fetch('{{ route("account.settings.email") }}', { method: 'POST', body: formData });
        const data = await response.json();

        if (data.success) {
            messageDiv.innerHTML = '<div class="alert alert-success">Email updated!</div>';
            setTimeout(() => location.reload(), 1000);
        } else {
            messageDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
        }
    } catch (error) {
        messageDiv.innerHTML = '<div class="alert alert-danger">An error occurred</div>';
    }
}

async function submitEditPhone() {
    const form = document.getElementById('editPhoneForm');
    const formData = new FormData(form);
    const messageDiv = document.getElementById('editPhoneMessage');

    try {
        const response = await fetch('{{ route("account.settings.phone") }}', { method: 'POST', body: formData });
        const data = await response.json();

        if (data.success) {
            messageDiv.innerHTML = '<div class="alert alert-success">Phone updated!</div>';
            setTimeout(() => location.reload(), 1000);
        } else {
            messageDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
        }
    } catch (error) {
        messageDiv.innerHTML = '<div class="alert alert-danger">An error occurred</div>';
    }
}

async function submitChangePassword() {
    const form = document.getElementById('changePasswordForm');
    const formData = new FormData(form);
    const messageDiv = document.getElementById('changePasswordMessage');

    try {
        const response = await fetch('{{ route("account.settings.password") }}', { method: 'POST', body: formData });
        const data = await response.json();

        if (data.success) {
            messageDiv.innerHTML = '<div class="alert alert-success">Password changed!</div>';
            setTimeout(() => location.reload(), 1000);
        } else {
            messageDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
        }
    } catch (error) {
        messageDiv.innerHTML = '<div class="alert alert-danger">An error occurred</div>';
    }
}

async function submitAddressForm() {
    const form = document.getElementById('addAddressForm');
    const formData = new FormData(form);
    const messageDiv = document.getElementById('addressFormMessage');

    try {
        const response = await fetch('{{ route("account.settings.address.store") }}', { method: 'POST', body: formData });
        const data = await response.json();

        if (data.success) {
            messageDiv.innerHTML = '<div class="alert alert-success">Address saved!</div>';
            setTimeout(() => location.reload(), 1000);
        } else {
            messageDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
        }
    } catch (error) {
        messageDiv.innerHTML = '<div class="alert alert-danger">An error occurred</div>';
    }
}

async function submitGiftCardForm() {
    const form = document.getElementById('addGiftCardForm');
    const messageDiv = document.getElementById('giftCardFormMessage');
    messageDiv.innerHTML = '<div class="alert alert-info">Gift card functionality coming soon!</div>';
}
</script>
@endpush
