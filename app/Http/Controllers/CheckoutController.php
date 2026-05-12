<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\PaymentMethod;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function __construct(protected CartService $cart) {}

    public function index()
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your bag is empty.');
        }

        $paymentMethods = PaymentMethod::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('checkout', [
            'items'          => $this->cart->get(),
            'subtotal'       => $this->cart->subtotal(),
            'paymentMethods' => $paymentMethods,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'email'             => 'required|email|max:255',
            'phone'             => 'nullable|string|max:20',
            'address_line1'     => 'required|string|max:255',
            'city'              => 'required|string|max:100',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'payment_reference' => 'nullable|string|max:255',
            'notes'             => 'nullable|string|max:1000',
        ]);

        if ($this->cart->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your bag is empty.');
        }

        $items = $this->cart->get();

        try {
            DB::transaction(function () use ($request, $items) {

                // 1. Upsert customer
                $customer = Customer::firstOrCreate(
                    ['email' => $request->email],
                    [
                        'first_name' => $request->first_name,
                        'last_name'  => $request->last_name,
                        'phone'      => $request->phone,
                    ]
                );

                // 2. Get pending status
                $pendingStatus = OrderStatus::where('name', 'Pending')->firstOrFail();

                $subtotal = $this->cart->subtotal();

                // 3. Create order
                $order = Order::create([
                    'customer_id'       => $customer->id,
                    'status_id'         => $pendingStatus->id,
                    'payment_method_id' => $request->payment_method_id,
                    'payment_reference' => $request->payment_reference,
                    'subtotal'          => $subtotal,
                    'shipping_cost'     => 0,
                    'total'             => $subtotal,
                    'shipping_address'  => [
                        'line1'   => $request->address_line1,
                        'city'    => $request->city,
                        'country' => 'Lesotho',
                    ],
                    'notes' => $request->notes,
                ]);

                // 4. Create items and decrement stock atomically
                foreach ($items as $variantId => $item) {
                    $affected = ProductVariant::where('id', $variantId)
                        ->where('stock_quantity', '>=', $item['quantity'])
                        ->decrement('stock_quantity', $item['quantity']);

                    if ($affected === 0) {
                        throw new \RuntimeException(
                            "Sorry, {$item['product_name']} ({$item['size']}, {$item['color']}) is out of stock."
                        );
                    }

                    OrderItem::create([
                        'order_id'   => $order->id,
                        'variant_id' => $variantId,
                        'quantity'   => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'line_total' => $item['unit_price'] * $item['quantity'],
                    ]);
                }

                // 5. Clear cart
                $this->cart->clear();

                // 6. Store order ID for thank you page
                session(['last_order_id' => $order->id]);
            });

        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('checkout.success');
    }

    public function success()
    {
        $orderId = session('last_order_id');
        if (! $orderId) return redirect()->route('home');

        $order = Order::with([
            'items.variant.product',
            'items.variant.size',
            'items.variant.color',
            'paymentMethod',
            'status',
        ])->findOrFail($orderId);

        return view('checkout-success', compact('order'));
    }
}