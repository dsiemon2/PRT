<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">First Name <span class="text-danger">*</span></label>
        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
               value="{{ old('first_name', $address->first_name ?? '') }}" required>
        @error('first_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Last Name <span class="text-danger">*</span></label>
        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
               value="{{ old('last_name', $address->last_name ?? '') }}" required>
        @error('last_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Address Line 1 <span class="text-danger">*</span></label>
    <input type="text" name="address_line1" class="form-control @error('address_line1') is-invalid @enderror"
           value="{{ old('address_line1', $address->address_line1 ?? '') }}" required>
    @error('address_line1')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Address Line 2 (Optional)</label>
    <input type="text" name="address_line2" class="form-control @error('address_line2') is-invalid @enderror"
           value="{{ old('address_line2', $address->address_line2 ?? '') }}">
    @error('address_line2')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-5 mb-3">
        <label class="form-label">City <span class="text-danger">*</span></label>
        <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
               value="{{ old('city', $address->city ?? '') }}" required>
        @error('city')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">State <span class="text-danger">*</span></label>
        <select name="state" class="form-select @error('state') is-invalid @enderror" required>
            <option value="">Select State</option>
            @php
                $states = ['AL','AK','AZ','AR','CA','CO','CT','DE','FL','GA','HI','ID','IL','IN','IA','KS','KY','LA','ME','MD','MA','MI','MN','MS','MO','MT','NE','NV','NH','NJ','NM','NY','NC','ND','OH','OK','OR','PA','RI','SC','SD','TN','TX','UT','VT','VA','WA','WV','WI','WY'];
                $currentState = old('state', $address->state ?? '');
            @endphp
            @foreach($states as $state)
                <option value="{{ $state }}" {{ $currentState === $state ? 'selected' : '' }}>{{ $state }}</option>
            @endforeach
        </select>
        @error('state')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">ZIP Code <span class="text-danger">*</span></label>
        <input type="text" name="postal_code" class="form-control @error('postal_code') is-invalid @enderror"
               value="{{ old('postal_code', $address->postal_code ?? '') }}" required>
        @error('postal_code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Phone (Optional)</label>
    <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
           value="{{ old('phone', $address->phone ?? '') }}">
    @error('phone')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-check mb-4">
    <input type="checkbox" name="is_default" class="form-check-input" id="is_default" value="1"
           {{ old('is_default', $address->is_default ?? false) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_default">Set as default address</label>
</div>

<div class="d-flex justify-content-between">
    <a href="{{ route('account.addresses.index') }}" class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="Cancel and return to addresses">
        <i class="bi bi-arrow-left"></i> Cancel
    </a>
    <button type="submit" class="btn btn-primary" data-bs-toggle="tooltip" title="Save this address">
        <i class="bi bi-check-lg"></i> Save Address
    </button>
</div>
