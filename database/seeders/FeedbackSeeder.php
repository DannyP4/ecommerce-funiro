<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Feedback;
use App\Models\User;
use App\Models\Product;

class FeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $products = Product::limit(3)->get();

        if (!$user || $products->count() == 0) {
            echo "Need users and products to create feedbacks.\n";
            return;
        }

        $feedbacks = [
            [
                'user_id' => $user->id,
                'product_id' => $products[0]->product_id,
                'comment' => 'Sản phẩm rất chất lượng, giao hàng nhanh. Tôi rất hài lòng với dịch vụ.',
                'rating' => 5
            ],
            [
                'user_id' => $user->id,
                'product_id' => $products[1]->product_id ?? $products[0]->product_id,
                'comment' => 'Sản phẩm đẹp, chắc chắn. Giá cả hợp lý. Sẽ mua thêm lần sau.',
                'rating' => 4
            ],
            [
                'user_id' => $user->id,
                'product_id' => $products[2]->product_id ?? $products[0]->product_id,
                'comment' => 'Thiết kế đẹp mắt, chất lượng tốt. Nhân viên tư vấn nhiệt tình.',
                'rating' => 5
            ]
        ];

        foreach ($feedbacks as $feedbackData) {
            Feedback::firstOrCreate(
                [
                    'user_id' => $feedbackData['user_id'],
                    'product_id' => $feedbackData['product_id']
                ],
                $feedbackData
            );
        }

        echo "Seeded " . count($feedbacks) . " feedbacks.\n";
    }
}
