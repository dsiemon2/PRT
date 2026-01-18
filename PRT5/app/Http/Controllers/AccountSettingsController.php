<?php

namespace App\Http\Controllers;

use App\Models\UserAddress;
use App\Models\UserPaymentMethod;
use App\Models\UserGiftCard;
use App\Models\UserDevice;
use App\Models\UserDeliveryPreference;
use App\Models\UserNotificationPreference;
use App\Services\BrandingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AccountSettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get addresses
        $addresses = UserAddress::where('user_id', $user->id)
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();

        // Get payment methods
        $paymentMethods = UserPaymentMethod::where('user_id', $user->id)
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();

        // Get gift cards
        $giftCards = UserGiftCard::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderByDesc('added_at')
            ->get();

        // Get devices
        $devices = UserDevice::where('user_id', $user->id)
            ->orderByDesc('is_current')
            ->orderByDesc('last_seen')
            ->get();

        // Get delivery preferences
        $deliveryPrefs = UserDeliveryPreference::where('user_id', $user->id)->first();

        // Get notification preferences
        $notifPrefs = UserNotificationPreference::where('user_id', $user->id)->first();

        // Get admin notification settings from branding service
        $brandingService = app(BrandingService::class);
        $adminSettings = $brandingService->getNotificationSettings();

        return view('account.settings', compact(
            'user',
            'addresses',
            'paymentMethods',
            'giftCards',
            'devices',
            'deliveryPrefs',
            'notifPrefs',
            'adminSettings'
        ));
    }

    public function updateName(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        $user->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Name updated successfully']);
        }

        return back()->with('success', 'Name updated successfully');
    }

    public function updateEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'password' => 'required|current_password',
        ]);

        $user = auth()->user();
        $user->update(['email' => $validated['email']]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Email updated successfully']);
        }

        return back()->with('success', 'Email updated successfully');
    }

    public function updatePhone(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'nullable|string|max:20',
        ]);

        $user = auth()->user();
        $user->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Phone updated successfully']);
        }

        return back()->with('success', 'Phone updated successfully');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = auth()->user();
        $user->update(['password' => Hash::make($validated['new_password'])]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Password changed successfully']);
        }

        return back()->with('success', 'Password changed successfully');
    }

    // Address methods
    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'address_type' => 'required|in:billing,shipping',
            'full_name' => 'required|string|max:200',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:50',
            'zip_code' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['is_default'] = $request->boolean('is_default');

        // If setting as default, unset other defaults of same type
        if ($validated['is_default']) {
            UserAddress::where('user_id', auth()->id())
                ->where('address_type', $validated['address_type'])
                ->update(['is_default' => false]);
        }

        UserAddress::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Address saved']);
        }

        return back()->with('success', 'Address saved');
    }

    public function setDefaultAddress(Request $request)
    {
        $validated = $request->validate([
            'address_id' => 'required|exists:user_addresses,id',
        ]);

        $address = UserAddress::where('id', $validated['address_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Unset other defaults of same type
        UserAddress::where('user_id', auth()->id())
            ->where('address_type', $address->address_type)
            ->update(['is_default' => false]);

        $address->update(['is_default' => true]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Default address updated']);
        }

        return back()->with('success', 'Default address updated');
    }

    public function deleteAddress(Request $request)
    {
        $validated = $request->validate([
            'address_id' => 'required|exists:user_addresses,id',
        ]);

        UserAddress::where('id', $validated['address_id'])
            ->where('user_id', auth()->id())
            ->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Address deleted']);
        }

        return back()->with('success', 'Address deleted');
    }

    // Delivery preferences
    public function saveDeliveryPrefs(Request $request)
    {
        $data = [
            'user_id' => auth()->id(),
            'door_to_door' => $request->boolean('door_to_door'),
            'weekend_delivery' => $request->boolean('weekend_delivery'),
            'signature_required' => $request->boolean('signature_required'),
            'leave_with_neighbor' => $request->boolean('leave_with_neighbor'),
            'authority_to_leave' => $request->boolean('authority_to_leave'),
            'weekday_time' => $request->input('weekday_time'),
            'weekend_time' => $request->input('weekend_time'),
            'vacation_mode' => $request->boolean('vacation_mode'),
            'vacation_start' => $request->input('vacation_start'),
            'vacation_end' => $request->input('vacation_end'),
            'vacation_instructions' => $request->input('vacation_instructions'),
            'special_instructions' => $request->input('special_instructions'),
            'backup_location' => $request->input('backup_location'),
        ];

        UserDeliveryPreference::updateOrCreate(
            ['user_id' => auth()->id()],
            $data
        );

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Delivery preferences saved']);
        }

        return back()->with('success', 'Delivery preferences saved');
    }

    // Notification preferences
    public function saveNotificationPrefs(Request $request)
    {
        $data = [
            'user_id' => auth()->id(),
            'delivery_email' => $request->boolean('delivery_email'),
            'delivery_sms' => $request->boolean('delivery_sms'),
            'delivery_push' => $request->boolean('delivery_push'),
            'promo_email' => $request->boolean('promo_email'),
            'promo_sms' => $request->boolean('promo_sms'),
            'promo_push' => $request->boolean('promo_push'),
            'payment_email' => $request->boolean('payment_email'),
            'payment_sms' => $request->boolean('payment_sms'),
            'payment_push' => $request->boolean('payment_push'),
            'security_email' => $request->boolean('security_email'),
            'security_sms' => $request->boolean('security_sms'),
            'security_push' => $request->boolean('security_push'),
        ];

        UserNotificationPreference::updateOrCreate(
            ['user_id' => auth()->id()],
            $data
        );

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Notification preferences saved']);
        }

        return back()->with('success', 'Notification preferences saved');
    }
}
