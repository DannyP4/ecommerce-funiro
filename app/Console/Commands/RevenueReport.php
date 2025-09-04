<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class RevenueReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:daily-revenue {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily revenue report to all admins';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // get the date from command argument or use today's date
            $date = $this->argument('date') ? Carbon::parse($this->argument('date')) : Carbon::today();

            $this->info("Generating daily revenue report for {$date->format('Y-m-d')}...");

            // get delivered orders for the specified date
            $orders = Order::with(['user', 'orderItems.product'])
                ->where('status', 'delivered')
                ->where(function($query) use ($date) {
                    $query->whereDate('updated_at', $date)
                          ->orWhere(function($subQuery) use ($date) {
                              $subQuery->whereDate('created_at', $date)
                                       ->where('status', 'delivered');
                          });
                })
                ->orderBy('updated_at', 'desc')
                ->get();

            // calculate total revenue
            $totalRevenue = $orders->sum('total_cost');
            $totalOrders = $orders->count();

            $this->info("Found {$totalOrders} delivered orders with total revenue: " . number_format($totalRevenue) . " VNĐ");

            // get all admin users and still active
            $admins = User::where('role_id', Role::ADMIN)
                ->where('is_activate', true)
                ->get();
            
            if ($admins->isEmpty()) {
                $this->warn('No admin users found to send the report to.');
                return Command::SUCCESS;
            }

            $this->info("Sending report to {$admins->count()} admin(s)...");

            // send email to all admins
            foreach ($admins as $admin) {
                try {
                    Mail::send('emails.revenue-report', [
                        'reportDate' => $date,
                        'orders' => $orders,
                        'totalRevenue' => $totalRevenue,
                        'totalOrders' => $totalOrders,
                        'adminName' => $admin->name ?? $admin->user_name
                    ], function ($message) use ($admin, $date) {
                        $message->to($admin->email, $admin->name ?? $admin->user_name)
                                ->subject(__('Daily Revenue Report') . ' - ' . $date->format('d/m/Y'));
                    });

                    $this->info("Report sent successfully to {$admin->email}");
                } catch (\Exception $e) {
                    $this->error("Failed to send report to {$admin->email}: " . $e->getMessage());
                }
            }

            $this->info('Daily revenue report command completed successfully!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Failed to generate daily revenue report: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
