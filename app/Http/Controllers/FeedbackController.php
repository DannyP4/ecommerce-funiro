<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Product;
use App\Http\Requests\StoreFeedbackRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function index($productId)
    {
        $product = Product::with('category')->findOrFail($productId);
        $feedbacks = Feedback::where('product_id', $productId)
                            ->with('user')
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);
        
        // calculate average rating
        $averageRating = Feedback::where('product_id', $productId)->avg('rating');

        return view('customer.pages.feedbacks', compact('product', 'feedbacks', 'averageRating'));
    }

    public function store(StoreFeedbackRequest $request, $productId)
    {
        $product = Product::findOrFail($productId);

        if ($request->feedback_id) {
            $feedback = Feedback::where('feedback_id', $request->feedback_id)
                               ->where('user_id', Auth::id())
                               ->firstOrFail();
            
            $feedback->update([
                'comment' => $request->comment,
                'rating' => $request->rating
            ]);
            
            return redirect()->route('customer.feedbacks', $productId)->with('success', 'Feedback updated successfully!');
        }

        // Create new feedback
        Feedback::create([
            'user_id' => Auth::id(),
            'product_id' => $productId,
            'comment' => $request->comment,
            'rating' => $request->rating
        ]);

        return redirect()->route('customer.feedbacks', $productId)->with('success', 'Feedback submitted successfully!');
    }

    public function destroy($feedbackId)
    {
        $feedback = Feedback::where('feedback_id', $feedbackId)
                           ->where('user_id', Auth::id())
                           ->firstOrFail();
        
        $productId = $feedback->product_id;
        $feedback->delete();

        return redirect()->route('customer.feedbacks', $productId)->with('success', 'Feedback deleted successfully!');
    }
}
