<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Feedback;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\StatusOrder;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Repositories\OrderRepository;
use App\Repositories\UserRepository;
use App\Repositories\FeedbackRepository;

class AdminController extends Controller
{
    private const PRODUCT_IMAGE_DIR = 'images/products';

    protected $categoryRepository;
    protected $productRepository;
    protected $orderRepository;
    protected $userRepository;
    protected $feedbackRepository;

    public function __construct(
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        OrderRepository $orderRepository,
        UserRepository $userRepository,
        FeedbackRepository $feedbackRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
        $this->feedbackRepository = $feedbackRepository;
    }

    public function dashboard()
    {
        $stats = [
            'total_users' => $this->userRepository->count(),
            'total_categories' => $this->categoryRepository->count(),
            'total_products' => $this->productRepository->count(),
            'total_orders' => $this->orderRepository->count(),
            'pending_orders' => $this->orderRepository->countByStatus('pending'),
            'total_feedbacks' => $this->feedbackRepository->count()
        ];
        
        return view('admin.pages.dashboard', compact('stats'));
    }

    public function dashboardStats()
    {
        $stats = [
            'total_users' => $this->userRepository->count(),
            'total_categories' => $this->categoryRepository->count(),
            'total_products' => $this->productRepository->count(),
            'total_orders' => $this->orderRepository->count(),
            'pending_orders' => $this->orderRepository->countByStatus('pending'),
            'total_feedbacks' => $this->feedbackRepository->count()
        ];
        
        return response()->json($stats);
    }

    public function getWeeklyChartData()
    {
        $days = [];
        $activeUsers = [];
        $orderedProducts = [];
        $newOrders = [];
        $newFeedbacks = [];
        $dailyRevenue = [];

        // get data for the last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days[] = $date->translatedFormat('M d');
            $dateString = $date->translatedFormat('Y-m-d');
            
            // the number of active users (who placed orders or left feedback ...)
            $usersWithOrders = User::whereHas('orders', function($query) use ($dateString) {
                $query->whereDate('order_date', $dateString);
            })->pluck('id');
            
            $usersWithFeedbacks = User::whereHas('feedbacks', function($query) use ($dateString) {
                $query->whereDate('created_at', $dateString);
            })->pluck('id');
            
            $activeUserCount = $usersWithOrders->merge($usersWithFeedbacks)->unique()->count();
            $activeUsers[] = $activeUserCount;
            
            // the number of products ordered in the day
            $orderedProductCount = OrderItem::whereHas('order', function($query) use ($dateString) {
                $query->whereDate('order_date', $dateString);
            })->sum('quantity');
            $orderedProducts[] = (int) $orderedProductCount;
            
            // the number of new orders in the day
            $newOrderCount = $this->orderRepository->countByDate($dateString);
            $newOrders[] = $newOrderCount;
            
            // the number of new feedbacks in the day
            $newFeedbackCount = $this->feedbackRepository->countByDate($dateString);
            $newFeedbacks[] = $newFeedbackCount;
            
            // daily revenue from delivered orders
            $revenue = $this->orderRepository->getRevenueByDate($dateString);
            $dailyRevenue[] = (float) $revenue;
        }

        $lastWeekStart = now()->subDays(6)->startOfDay();
        $lastWeekEnd = now()->endOfDay();
        
        // Count orders with payment_status='paid' in the last 7 days
        $paidOrders = Order::where('payment_status', 'paid')
            ->whereBetween('order_date', [$lastWeekStart, $lastWeekEnd])
            ->count();
            
        $totalOrdersThisWeek = $this->orderRepository->countByDateRange($lastWeekStart, $lastWeekEnd);

        $ratingDistribution = [];
        for ($rating = 1; $rating <= 5; $rating++) {
            $count = $this->feedbackRepository->countByRating($rating);
            $ratingDistribution[] = $count;
        }

        return response()->json([
            'days' => $days,
            'activeUsers' => $activeUsers,
            'orderedProducts' => $orderedProducts,
            'newOrders' => $newOrders,
            'newFeedbacks' => $newFeedbacks,
            'dailyRevenue' => $dailyRevenue,
            'paidOrders' => $paidOrders,
            'totalOrdersThisWeek' => $totalOrdersThisWeek,
            'ratingDistribution' => $ratingDistribution
        ]);
    }

    public function users()
    {
        $users = $this->userRepository->getAllUsers(10);
        $roles = Role::all(); // Lấy tất cả roles để tạo filter dropdown
        return view('admin.pages.users', compact('users', 'roles'));
    }

    public function storeUser(StoreUserRequest $request)
    {
        // only super admin can create users
        if (auth()->user()->email !== Role::SUPER_ADMIN) {
            return redirect()->route('admin.users')->with('error', 'Only the super admin can create new users.');
        }

        $validated = $request->validated();
        $this->userRepository->createUser($validated);
        return redirect()->route('admin.users')->with('success', 'User created successfully.');
    }

    public function updateUser(UpdateUserRequest $request, User $user)
    {
        // only super admin can update users
        if (auth()->user()->email !== Role::SUPER_ADMIN) {
            return redirect()->route('admin.users')->with('error', 'Only the super admin can edit users.');
        }

        // super admin cannot edit their own account
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')->with('error', 'You cannot edit your own account.');
        }

        $validated = $request->validated();
        $this->userRepository->updateUser($user->id, $validated);
        return redirect()->route('admin.users')->with('success', 'User updated successfully.');
    }

    public function deleteUser(User $user)
    {
        // only super admin can delete users
        if (auth()->user()->email !== Role::SUPER_ADMIN) {
            return response()->json([
                'success' => false,
                'message' => 'Only the super admin can delete users.'
            ], 403);
        }

        // Prevent deletion of current logged in user
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete your own account.'
            ], 403);
        }

        // only allow deletion of deactivated users
        if ($user->is_activate) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete active users. Please deactivate the user first.'
            ], 403);
        }

        $this->userRepository->deleteUser($user->id);
        return response()->json(['success' => true]);
    }

    public function searchUsers(Request $request)
    {
        $query = $request->input('query');
        $roleId = $request->input('role_id');
        
        $usersQuery = User::with('role');
        
        // search filter (name hoặc email)
        if (!empty($query)) {
            $usersQuery->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%");
            });
        }
        
        // role filter  
        if (!empty($roleId) && $roleId !== 'all') {
            $usersQuery->where('role_id', $roleId);
        }
        
        $users = $usersQuery->paginate(10);
        $roles = Role::all(); // Lấy tất cả roles để tạo filter dropdown
        
        // ensure that search parameters are kept in pagination links
        $users->appends($request->only(['query', 'role_id']));
        
        return view('admin.pages.users', compact('users', 'roles'));
    }

    public function categories()
    {
        // count products in each category
        $categories = $this->categoryRepository->getCategoriesWithProductCount(10);
        return view('admin.pages.categories', compact('categories'));
    }

    public function storeCategory(StoreCategoryRequest $request)
    {
        $validated = $request->validated();
        
        try {
            // if there is an image, validate it.
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $validated['image'] = $image;
            } else {
                $validated['image'] = null; // No image uploaded
            }
    
            $this->categoryRepository->createCategory($validated);
    
            return redirect()->route('admin.categories')->with('success', 'Category added successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withErrors(['image' => $e->getMessage()])->withInput();
        }
    }

    
    public function updateCategory(UpdateCategoryRequest $request, Category $category)
    {
        $validated = $request->validated();

        try {
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $validated['image'] = $image;
            } 
            $this->categoryRepository->updateCategory($category->category_id, $validated);
            return redirect()->route('admin.categories')->with('success', 'Category updated.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withErrors(['image' => $e->getMessage()])->withInput();
        }
    }

    public function deleteCategory(Category $category)
    {
        if (!$this->categoryRepository->deleteCategory($category->category_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with associated products.'
            ], 400);
        }

        return response()->json(['success' => true]);
    }

    public function products()
    {
        $products = $this->productRepository->getAllProducts(10);
        $categories = $this->categoryRepository->getAllCategories();
        return view('admin.pages.products', compact('products', 'categories'));
    }

    public function storeProduct(StoreProductRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image');
        }

        try {
            $this->productRepository->createProduct($validated);
            return redirect()->route('admin.products')->with('success', 'Product added successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withErrors(['image' => $e->getMessage()])->withInput();
        }
    }

    public function updateProduct(UpdateProductRequest $request, $productId)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image');
        }

        try {
            $this->productRepository->updateProduct($productId, $validated);
            return redirect()->route('admin.products')->with('success', 'Product updated successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withErrors(['image' => $e->getMessage()])->withInput();
        }
    }

    public function deleteProduct($productId)
    { 
        $this->productRepository->deleteProduct($productId);
        return response()->json(['success' => true]);
    }

    public function searchProducts(Request $request)
    {
        $query = $request->input('query');
        $categoryId = $request->input('category_id');
        
        $products = $this->productRepository->searchProducts($query, $categoryId, 10);
        $categories = $this->categoryRepository->getAllCategories();
        
        // ensure that search parameters are kept in pagination links
        $products->appends($request->only(['query', 'category_id']));
        
        return view('admin.pages.products', compact('products', 'categories'));
    }

    public function orders(Request $request)
    {
        $filters = [
            'status' => $request->input('status'),
            'from_date' => $request->input('from_date'),
            'to_date' => $request->input('to_date')
        ];
        
        $orders = $this->orderRepository->getFilteredOrders($filters, 10);

        return view('admin.pages.orders', compact('orders'));
    }

    public function feedbacks()
    {
        $feedbacks = $this->feedbackRepository->getAllFeedbacks(10);
        return view('admin.pages.feedbacks', compact('feedbacks'));
    }

    public function showFeedback(Feedback $feedback)
    {
        $feedback = $this->feedbackRepository->getFeedbackById($feedback->feedback_id);
        return response()->json([
            'feedback' => $feedback,
            'user' => $feedback->user,
            'product' => $feedback->product
        ]);
    }

    public function deleteFeedback(Feedback $feedback)
    {
        $this->feedbackRepository->deleteFeedback($feedback->feedback_id);
        return redirect()->route('admin.feedbacks')->with('success', 'Feedback deleted successfully!');
    }

    public function updateOrderStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $validated = $request->validated();

        // check if the order is already in a final state (delivered or cancelled)
        if (in_array($order->status, ['delivered', 'cancelled'])) {
            return redirect()->route('admin.orders')->with('error', __('Cannot change status of delivered or cancelled orders.'));
        }

        // define valid status transitions
        $validTransitions = [
            'pending' => ['approved', 'rejected'],
            'approved' => ['delivering'],
            'rejected' => [], // rejected orders cannot be changed
            'delivering' => ['delivered'],
            'delivered' => [], // delivered orders cannot be changed
            'cancelled' => [] // cancelled orders cannot be changed (but only customers can cancel)
        ];

        $currentStatus = $order->status;
        $newStatus = $validated['status'];

        if (!in_array($newStatus, $validTransitions[$currentStatus] ?? [])) {
            $errorMessage = match($currentStatus) {
                'pending' => __('From Pending status, you can only approve or reject the order.'),
                'approved' => __('From Approved status, you can only change to Delivering.'),
                'rejected' => __('Rejected orders cannot be changed.'),
                'delivering' => __('From Delivering status, you can only mark as Delivered.'),
                default => __('Invalid status transition.')
            };
            
            return redirect()->route('admin.orders')->with('error', $errorMessage);
        }

        DB::transaction(function () use ($order, $validated) {
            $oldStatus = $order->status;
            $newStatus = $validated['status'];
            
            if ($oldStatus === $newStatus) {
                return; // No change
            }
            
            $this->orderRepository->updateOrderStatus($order->order_id, $newStatus);

            StatusOrder::create([
                'action_type' => $newStatus,
                'date' => now(),
                'admin_id' => auth()->id(),
                'order_id' => $order->getKey(),
            ]);
        });

        $statusMessage = match($validated['status']) {
            'pending' => __('Order status changed to Pending'),
            'approved' => __('Order has been approved'),
            'rejected' => __('Order has been rejected'),
            'delivering' => __('Order is now being delivered'),
            'delivered' => __('Order has been marked as delivered'),
            default => __('Order status updated')
        };

        return redirect()->route('admin.orders')->with('success', $statusMessage);
    }

    public function showOrderDetails(Order $order)
    {
        $order = $this->orderRepository->getOrderById($order->order_id);
        
        return response()->json([
            'order' => $order,
            'customer_name' => $order->user->name ?? $order->user->user_name ?? __('N/A'),
            'order_items' => $order->orderItems->map(function ($item) {
                return [
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->quantity * $item->price,
                ];
            }),
        ]);
    }

    public function toggleUserActivation(User $user)
    {   
        // only super admin can toggle user activation
        if (auth()->user()->email !== Role::SUPER_ADMIN) {
            return response()->json([
                'success' => false,
                'message' => 'Only the super admin can activate/deactivate users.'
            ], 403);
        }

        // if the user is the currently logged in user, prevent deactivation
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot deactivate your own account.'
            ], 403);
        }

        if ($user->is_activate) {
            $this->userRepository->deactivateUser($user->id);
        } else {
            $this->userRepository->activateUser($user->id);
        }

        $user->refresh();
        $status = $user->is_activate ? 'activated' : 'deactivated';
        return response()->json([
            'success' => true,
            'message' => "User has been {$status} successfully.",
            'is_activate' => $user->is_activate
        ]);
    }
}
