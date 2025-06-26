<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = auth()->user()->cartItems()->with('product')->get();
        return view('frontend.product.cart', compact('cartItems'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = auth()->user();
        $productId = $request->product_id;

        $cartItem = $user->cartItems()->where('product_id', $productId)->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $request->quantity);
        } else {
            $user->cartItems()->create([
                'product_id' => $productId,
                'quantity' => $request->quantity,
            ]);
        }

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function updateQuantity(Request $request)
    {
        $cart = Cart::where('id', $request->id)->where('user_id', auth()->id())->firstOrFail();
        $quantity = (int) $request->quantity;
        if ($quantity < 1) return response()->json(['confirm_delete' => true]);
        $cart->update(['quantity' => $quantity]);
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        Cart::where('id', $id)->where('user_id', auth()->id())->delete();
        return back()->with('success', 'Produk dihapus dari keranjang.');
    }
}
