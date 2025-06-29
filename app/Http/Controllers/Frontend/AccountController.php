<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Review;
use App\Services\RajaOngkirService;

class AccountController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load('addresses');
        $orders = $user->orders()->with('orderItems.product')->latest()->get();
        $reviews = $user->reviews()->with('product')->latest()->get();
        return view('frontend.account.index', compact('user', 'orders', 'reviews'));
    }

    // ===================== PROFILE =====================
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        if ($request->name !== $user->name) {
            if ($user->updated_name_at && now()->diffInDays($user->updated_name_at) < 30) {
                return back()->withErrors(['name' => 'Nama hanya bisa diubah satu kali dalam sebulan.']);
            }
            $user->updated_name_at = now();
            $user->name = $request->name;
        }

        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak cocok']);
        }

        $user->password = bcrypt($request->new_password);
        $user->save();

        Auth::logout();
        return redirect()->route('login')->with('success', 'Password berhasil diubah. Silakan login kembali.');
    }

    public function uploadPicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|max:2048',
        ]);

        $user = auth()->user();

        if ($user->profile_picture) {
            Storage::disk('public')->delete('profile/' . $user->profile_picture);
        }

        $filename = $request->file('profile_picture')->store('profile', 'public');
        $user->profile_picture = basename($filename);
        $user->save();

        return back()->with('success', 'Foto profil berhasil diunggah.');
    }

    public function deletePicture()
    {
        $user = auth()->user();

        if ($user->profile_picture) {
            Storage::disk('public')->delete('profile/' . $user->profile_picture);
            $user->profile_picture = null;
            $user->save();
        }

        return back()->with('success', 'Foto profil berhasil dihapus.');
    }

    // ===================== ADDRESS =====================
    public function addAddress(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:100',
            'destination_id' => 'required|string', // ID dari RajaOngkir API
            'destination_name' => 'required|string|max:255', // Nama lengkap destinasi
            'address' => 'required|string',
            'zip' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
        ]);

        // Ambil detail destinasi dari RajaOngkir untuk validasi
        $rajaOngkir = app(RajaOngkirService::class);
        $destinationDetail = $rajaOngkir->getDestinationDetail($validated['destination_id']);

        if (!$destinationDetail) {
            return back()->withErrors(['destination_id' => 'Destinasi tidak valid.']);
        }

        // Parsing nama destinasi untuk mendapatkan kota dan provinsi
        $destinationParts = $this->parseDestinationName($validated['destination_name']);

        auth()->user()->addresses()->create([
            'label' => $validated['label'],
            'destination_id' => $validated['destination_id'], // ID untuk RajaOngkir
            'province_id' => $destinationParts['province_id'] ?? null, // Legacy compatibility
            'province' => $destinationParts['province'] ?? '',
            'city_id' => $destinationParts['city_id'] ?? null, // Legacy compatibility
            'city' => $destinationParts['city'] ?? '',
            'destination_name' => $validated['destination_name'], // Nama lengkap dari RajaOngkir
            'address' => $validated['address'],
            'zip' => $validated['zip'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ]);

        return back()->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function updateAddress(Request $request, $id)
    {
        $address = auth()->user()->addresses()->findOrFail($id);

        $validated = $request->validate([
            'label' => 'required|string|max:100',
            'destination_id' => 'required|string',
            'destination_name' => 'required|string|max:255',
            'address' => 'required|string',
            'zip' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
        ]);

        // Validasi destinasi
        $rajaOngkir = app(RajaOngkirService::class);
        $destinationDetail = $rajaOngkir->getDestinationDetail($validated['destination_id']);

        if (!$destinationDetail) {
            return back()->withErrors(['destination_id' => 'Destinasi tidak valid.']);
        }

        $destinationParts = $this->parseDestinationName($validated['destination_name']);

        $address->update([
            'label' => $validated['label'],
            'destination_id' => $validated['destination_id'],
            'province_id' => $destinationParts['province_id'] ?? null,
            'province' => $destinationParts['province'] ?? '',
            'city_id' => $destinationParts['city_id'] ?? null,
            'city' => $destinationParts['city'] ?? '',
            'destination_name' => $validated['destination_name'],
            'address' => $validated['address'],
            'zip' => $validated['zip'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ]);

        return back()->with('success', 'Alamat berhasil diperbarui.');
    }

    public function deleteAddress($id)
    {
        $address = auth()->user()->addresses()->findOrFail($id);
        $address->delete();

        return back()->with('success', 'Alamat berhasil dihapus.');
    }

    public function getAddressData($id)
    {
        $address = auth()->user()->addresses()->findOrFail($id);
        return response()->json($address);
    }

    private function parseDestinationName($destinationName)
    {
        $parts = array_map('trim', explode(',', $destinationName));

        return [
            'city' => $parts[0] ?? '',
            'province' => $parts[1] ?? '',
            'city_id' => null,
            'province_id' => null
        ];
    }

    // ===================== ORDERS =====================
    public function ordersInProgress()
    {
        $orders = auth()->user()->orders()
            ->where('status', 'paid')
            ->latest()
            ->with('orderItems.product')
            ->get();

        return view('frontend.account.order', compact('orders'));
    }

    public function orderHistory()
    {
        $orders = Order::with('orderItems.product')
            ->where('user_id', auth()->id())
            ->where('status', 'completed')
            ->latest()
            ->get();

        return view('frontend.account.history', compact('orders'));
    }

    public function markOrderAsCompleted(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if (!in_array($order->status, ['paid', 'processing'])) {
            return back()->with('error', 'Pesanan tidak bisa ditandai sebagai selesai.');
        }

        $order->update([
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);

        return back()->with('success', 'Pesanan telah ditandai sebagai selesai.');
    }

    // ===================== REVIEWS =====================
    public function reviewForm(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $products = $order->orderItems()->with('product')->get();

        return view('frontend.account.review-modal', compact('order', 'products'));
    }

    public function submitReview(Request $request, Order $order)
    {
        $request->validate([
            'reviews.*.product_id' => 'required|exists:products,id',
            'reviews.*.rating' => 'required|integer|min:1|max:5',
            'reviews.*.comment' => 'nullable|string',
        ]);

        foreach ($request->reviews as $reviewData) {
            Review::updateOrCreate(
                ['user_id' => auth()->id(), 'product_id' => $reviewData['product_id']],
                ['rating' => $reviewData['rating'], 'comment' => $reviewData['comment']]
            );
        }

        return redirect()->route('account.orders.history')->with('success', 'Ulasan berhasil disimpan!');
    }

    public function myReviews()
    {
        $reviews = auth()->user()->reviews()->with('product')->latest()->get();
        return view('frontend.account.my-reviews', compact('reviews'));
    }
}
