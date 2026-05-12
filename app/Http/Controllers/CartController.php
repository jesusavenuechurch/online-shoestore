<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(protected CartService $cart) {}

    public function index()
    {
        return view('cart', [
            'items'    => $this->cart->get(),
            'subtotal' => $this->cart->subtotal(),
            'count'    => $this->cart->count(),
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity'   => 'integer|min:1|max:10',
        ]);

        $this->cart->add($request->variant_id, $request->quantity ?? 1);

        return back()->with('success', 'Added to bag!');
    }

    public function update(Request $request, int $variantId)
    {
        $request->validate(['quantity' => 'required|integer|min:0']);
        $this->cart->update($variantId, $request->quantity);
        return back();
    }

    public function remove(int $variantId)
    {
        $this->cart->remove($variantId);
        return back()->with('success', 'Item removed.');
    }
}