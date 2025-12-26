<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\DeliveryInfo;
use App\Services\ShippingService;
use App\Services\VNPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\NewOrderPlaced;
use Illuminate\Support\Facades\Redis;
use App\Http\Requests\StoreDeliveryInfoRequest;

class CheckoutController extends Controller
{
    protected $shippingService;
    protected $vnpayService;

    public function __construct(ShippingService $shippingService, VNPayService $vnpayService)
    {
        $this->shippingService = $shippingService;
        $this->vnpayService = $vnpayService;
    }

    /**
     * Show delivery information form
     */
    public function deliveryInfo(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('customer.cart.index')->with('info', __('Your cart is empty.'));
        }

        $user = $request->user();
        
        // Pre-fill delivery info from user data
        $deliveryInfo = [
            'user_name' => $user->name ?? '',
            'email' => $user->email ?? '',
            'phone_number' => $user->phone_number ?? '',
            'country' => $user->country ?? '',
            'city' => $user->city ?? '',
            'district' => $user->district ?? '',
            'ward' => $user->ward ?? '',
        ];

        $totalQuantity = array_sum(array_map(function ($item) {
            return (int) ($item['quantity'] ?? 0);
        }, $cart));

        $totalPrice = array_reduce($cart, function ($carry, $item) {
            $quantity = (int) ($item['quantity'] ?? 0);
            $price = (float) ($item['price'] ?? 0);
            return $carry + ($quantity * $price);
        }, 0.0);

        $shippingInfo = $this->shippingService->getShippingInfo($totalPrice);

        return view('customer.pages.delivery-info', compact('cart', 'deliveryInfo', 'totalQuantity', 'totalPrice', 'shippingInfo'));
    }

    /**
     * Store delivery information in session and redirect to checkout
     */
    public function storeDeliveryInfo(StoreDeliveryInfoRequest $request)
    {
        // Store delivery info in session (for current order only)
        $request->session()->put('delivery_info', $request->validated());

        return redirect()->route('customer.checkout.create');
    }

    /**
     * Show the checkout page with cart summary
     */
    public function create(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('customer.cart.index')->with('success', __('Your cart is empty.'));
        }

        // Check if delivery info is provided
        $deliveryInfo = $request->session()->get('delivery_info');
        if (!$deliveryInfo) {
            return redirect()->route('customer.delivery.info')->with('info', __('Please provide delivery information first.'));
        }

        $totalQuantity = array_sum(array_map(function ($item) {
            return (int) ($item['quantity'] ?? 0);
        }, $cart));

        $totalPrice = array_reduce($cart, function ($carry, $item) {
            $quantity = (int) ($item['quantity'] ?? 0);
            $price = (float) ($item['price'] ?? 0);
            return $carry + ($quantity * $price);
        }, 0.0);

        $shippingInfo = $this->shippingService->getShippingInfo($totalPrice);

        return view('customer.pages.checkout', compact('cart', 'deliveryInfo', 'totalQuantity', 'totalPrice', 'shippingInfo'));
    }

    /**
     * Place order from session cart
     */
    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:cod,vnpay'
        ]);

        $cart = $request->session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('customer.cart.index')->with('info', __('Your cart is empty.'));
        }

        // Check if delivery info is provided
        $deliveryInfo = $request->session()->get('delivery_info');
        if (!$deliveryInfo) {
            return redirect()->route('customer.delivery.info')->with('info', __('Please provide delivery information first.'));
        }

        $paymentMethod = $request->input('payment_method');

        if ($paymentMethod === 'vnpay') {
            return $this->processVNPayPayment($request);
        }

        // Process COD payment directly
        return $this->processCODPayment($request);
    }

    private function processCODPayment(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $productIds = array_map('intval', array_keys($cart));

        // Load products keyed by primary key (product_id)
        $products = Product::whereIn('product_id', $productIds)->get()->keyBy('product_id');

        // Validate all items exist and have sufficient stock
        $computedTotal = 0.0;
        foreach ($cart as $pid => $item) {
            $pid = (int) $pid;
            $product = $products[$pid] ?? null;
            if (!$product) {
                return back()->with('error', __('A product in your cart is no longer available.'));
            }
            $quantity = (int) ($item['quantity'] ?? 0);
            if ($quantity <= 0) {
                return back()->with('error', __('Invalid item quantity in cart.'));
            }
            if (isset($product->stock) && $product->stock !== null && $product->stock < $quantity) {
                return back()->with('error', __('Insufficient stock for product: ') . $product->name);
            }
            $computedTotal += $quantity * (float) $product->price;
        }

        $shippingInfo = $this->shippingService->getShippingInfo($computedTotal);
        $finalTotal = $shippingInfo['total'];

        $userId = (int) $request->user()->getKey();
        $newOrderId = null;

        DB::transaction(function () use ($cart, $products, $computedTotal, $shippingInfo, $finalTotal, $userId, $request, &$newOrderId) {
            // Create order
            $order = Order::create([
                'customer_id' => $userId,
                'order_date' => now()->toDateString(),
                'total_cost' => $finalTotal, // Tổng tiền bao gồm phí ship
                'shipping_fee' => $shippingInfo['shipping_fee'], // Phí ship riêng biệt
                'status' => 'pending',
                'payment_method' => 'cod',
                'payment_status' => 'pending',
            ]);
            
            $newOrderId = $order->order_id;

            // Create delivery info if exists in session
            $deliveryInfo = $request->session()->get('delivery_info');
            if ($deliveryInfo) {
                DeliveryInfo::create([
                    'order_id' => $order->order_id,
                    'user_name' => $deliveryInfo['user_name'],
                    'email' => $deliveryInfo['email'],
                    'phone_number' => $deliveryInfo['phone_number'],
                    'country' => $deliveryInfo['country'],
                    'city' => $deliveryInfo['city'],
                    'district' => $deliveryInfo['district'],
                    'ward' => $deliveryInfo['ward'] ?? null,
                ]);
            }

            // Create items and decrement stock
            foreach ($cart as $pid => $item) {
                $pid = (int) $pid;
                $product = $products[$pid];
                $quantity = (int) $item['quantity'];

                OrderItem::create([
                    'order_id' => $order->getKey(),
                    'product_id' => $product->getKey(),
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);

                if (isset($product->stock) && $product->stock !== null) {
                    // Prevent race conditions by checking stock again and updating
                    $affected = Product::where('product_id', $product->getKey())
                        ->where('stock', '>=', $quantity)
                        ->decrement('stock', $quantity);
                    if ($affected === 0) {
                        throw new \RuntimeException('Insufficient stock during checkout.');
                    }
                }
            }
          
            // Clear cart and delivery info from session
            $request->session()->forget(['cart', 'delivery_info']);

            // Eager load the user relationship before dispatching the event
            $order->load('user');

            // Dispatch the event after the order is successfully created
            event(new NewOrderPlaced($order));
        });   
             
        return redirect()->route('customer.orders', ['highlight' => $newOrderId])->with('success', __('Order placed successfully.'));
    }

    private function processVNPayPayment(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $productIds = array_map('intval', array_keys($cart));

        // Load products
        $products = Product::whereIn('product_id', $productIds)->get()->keyBy('product_id');

        // Validate all items exist and have sufficient stock
        $computedTotal = 0.0;
        foreach ($cart as $pid => $item) {
            $pid = (int) $pid;
            $product = $products[$pid] ?? null;
            if (!$product) {
                return back()->with('error', __('A product in your cart is no longer available.'));
            }
            $quantity = (int) ($item['quantity'] ?? 0);
            if ($quantity <= 0) {
                return back()->with('error', __('Invalid item quantity in cart.'));
            }
            if (isset($product->stock) && $product->stock !== null && $product->stock < $quantity) {
                return back()->with('error', __('Insufficient stock for product: ') . $product->name);
            }
            $computedTotal += $quantity * (float) $product->price;
        }

        $shippingInfo = $this->shippingService->getShippingInfo($computedTotal);
        $finalTotal = $shippingInfo['total'];

        $userId = (int) $request->user()->getKey();
        $newOrderId = null;

        DB::transaction(function () use ($cart, $products, $computedTotal, $shippingInfo, $finalTotal, $userId, $request, &$newOrderId) {
            $order = Order::create([
                'customer_id' => $userId,
                'order_date' => now()->toDateString(),
                'total_cost' => $finalTotal,
                'shipping_fee' => $shippingInfo['shipping_fee'],
                'status' => 'pending',
                'payment_method' => 'vnpay',
                'payment_status' => 'pending',
            ]);
            
            $newOrderId = $order->order_id;

            // Create delivery info
            $deliveryInfo = $request->session()->get('delivery_info');
            if ($deliveryInfo) {
                DeliveryInfo::create([
                    'order_id' => $order->order_id,
                    'user_name' => $deliveryInfo['user_name'],
                    'email' => $deliveryInfo['email'],
                    'phone_number' => $deliveryInfo['phone_number'],
                    'country' => $deliveryInfo['country'],
                    'city' => $deliveryInfo['city'],
                    'district' => $deliveryInfo['district'],
                    'ward' => $deliveryInfo['ward'] ?? null,
                ]);
            }

            foreach ($cart as $pid => $item) {
                $pid = (int) $pid;
                $product = $products[$pid];
                $quantity = (int) $item['quantity'];

                OrderItem::create([
                    'order_id' => $order->getKey(),
                    'product_id' => $product->getKey(),
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);
            }
        });

        // Generate VNPay payment URL
        $orderInfo = "Thanh toan don hang #" . $newOrderId;
        $ipAddress = $request->ip();
        
        $paymentData = $this->vnpayService->createPaymentUrl($newOrderId, $finalTotal, $orderInfo, $ipAddress);
        
        // Save transaction reference to order
        Order::where('order_id', $newOrderId)->update([
            'transaction_id' => $paymentData['txn_ref']
        ]);

        // Store order ID in session for callback verification
        $request->session()->put('vnpay_order_id', $newOrderId);

        // Redirect to VNPay
        return redirect($paymentData['url']);
    }

    public function vnpayReturn(Request $request)
    {
        $inputData = $request->all();
        
        // Verify checksum
        if (!$this->vnpayService->verifyReturnUrl($inputData)) {
            return redirect()->route('customer.orders')->with('error', __('Invalid payment response.'));
        }

        // Get transaction info
        $transactionInfo = $this->vnpayService->getTransactionInfo($inputData);
        $orderId = $transactionInfo['order_id'];
        
        // Verify order exists and belongs to current user
        $order = Order::where('order_id', $orderId)
            ->where('customer_id', $request->user()->getKey())
            ->first();

        if (!$order) {
            return redirect()->route('customer.orders')->with('error', __('Order not found.'));
        }

        // Check if payment is successful
        $isSuccess = $this->vnpayService->isSuccessful($transactionInfo['response_code']);

        DB::transaction(function () use ($order, $transactionInfo, $isSuccess, $request) {
            if ($isSuccess) {
                // Payment successful - update order and decrement stock
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'approved',
                    'vnpay_data' => json_encode($transactionInfo),
                ]);

                // Decrement product stock
                $orderItems = OrderItem::where('order_id', $order->order_id)->get();
                foreach ($orderItems as $item) {
                    $affected = Product::where('product_id', $item->product_id)
                        ->where('stock', '>=', $item->quantity)
                        ->decrement('stock', $item->quantity);
                    
                    if ($affected === 0) {
                        throw new \RuntimeException('Insufficient stock for product ID: ' . $item->product_id);
                    }
                }

                // Clear cart from session
                $request->session()->forget(['cart', 'delivery_info', 'vnpay_order_id']);

                // Load user and dispatch event
                $order->load('user');
                event(new NewOrderPlaced($order));

            } else {
                // Payment failed
                $order->update([
                    'payment_status' => 'failed',
                    'status' => 'cancelled',
                    'vnpay_data' => json_encode($transactionInfo),
                ]);
            }
        });

        if ($isSuccess) {
            return redirect()->route('customer.orders', ['highlight' => $orderId])
                ->with('success', __('Payment successful. Your order has been placed.'));
        } else {
            $errorMessage = $this->vnpayService->getResponseMessage($transactionInfo['response_code']);
            return redirect()->route('customer.orders', ['highlight' => $orderId])
                ->with('error', __('Payment failed: ') . $errorMessage);
        }
    }
} 
