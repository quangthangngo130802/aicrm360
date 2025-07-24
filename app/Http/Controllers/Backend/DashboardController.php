<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\CustomerCare;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function dashboard()
    {

        $user = Auth::user();
        if ($user->is_admin == 0) {
            $today = Carbon::today();
            $tomorrow = Carbon::tomorrow();
            $startDate = Carbon::tomorrow();
            $endDate = Carbon::now()->endOfWeek();
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();

            //KH chăm sóc hôm nay
            $customerCareCount = CustomerCare::whereBetween('care_date', [$today, $tomorrow])
            ->where('user_id', $user->id)
            ->count();

            // Đếm lịch hôm nay của chính user
            $appointmentCount = Appointment::whereBetween('scheduled_at', [$today, $tomorrow])
                ->where('user_id', $user->id)
                ->count();

            // Đếm lịch còn lại trong tuần (sau hôm nay)
            $appointmentNextCount = Appointment::whereBetween('scheduled_at', [$startDate, $endDate])
                ->where('user_id', $user->id)
                ->count();

            // Đếm khách hàng có lịch trong tuần thuộc user
            $customerCount = Customer::whereHas('appointments', function ($query) use ($startOfWeek, $endOfWeek, $user) {
                $query->whereBetween('scheduled_at', [$startOfWeek, $endOfWeek])
                    ->where('user_id', $user->id);
            })->count();

            // Lấy 5 lịch sắp diễn ra thuộc user
            $appointmentNext = Appointment::whereBetween('scheduled_at', [$startDate, $endDate])
                ->where('user_id', $user->id)
                ->orderBy('scheduled_at')
                ->limit(5)
                ->get();

            // Lấy khách có lịch hôm nay của user
            $customerNow = Customer::whereHas('appointments', function ($query) use ($today, $tomorrow, $user) {
                $query->whereBetween('scheduled_at', [$today, $tomorrow])
                    ->where('user_id', $user->id);
            })->get();


            $customers = Customer::whereHas('appointments', function ($query) use ($today, $tomorrow, $user) {
                $query->whereBetween('scheduled_at', [$today, $tomorrow])
                ->where('user_id', $user->id);;
            })->get();

            return view('backend.dashboardUser', compact('appointmentCount', 'appointmentNextCount', 'customerCount', 'appointmentNext',  'customerNow', 'customers', 'customerCareCount'));
        }
        $filter = 'today';
        return view('backend.dashboard', [
            'customerSummary' => $this->customerSummary($filter),
            'orderCount'      => $this->orderCount($filter),
            'orderSum'        => $this->orderSum($filter),
            'userCount'       => $this->userCount($filter),
        ]);
    }

    public function filterDashboard(Request $request)
    {
        $filter = $request->input('filter', 'today');
        $from = $request->input('from');
        $to = $request->input('to');

        return response()->json([
            'customerSummary' => $this->customerSummary($filter, $from, $to),
            'orderCount'      => $this->orderCount($filter, $from, $to),
            'orderSum'        => $this->orderSum($filter, $from, $to),
            'userCount'       => $this->userCount($filter, $from, $to),
        ]);
    }


    public function customerSummary($filter, $from = null, $to = null)
    {
        [$start, $end] = $this->parseDateRange($filter, $from, $to);
        $query = Customer::whereBetween('created_at', [$start, $end]);

        $user = Auth::user();
        if ($user->is_admin != 1) {
            $query->where('user_id', $user->id);
        }

        $customer = $query->count();
        return compact('customer');
    }

    public function orderCount($filter, $from = null, $to = null)
    {
        [$start, $end] = $this->parseDateRange($filter, $from, $to);
        $query = Order::whereBetween('created_at', [$start, $end]);

        $user = Auth::user();
        if ($user->is_admin != 1) {
            $query->where('user_id', $user->id);
        }

        return $query->count();
    }


    public function orderSum($filter, $from = null, $to = null)
    {
        [$start, $end] = $this->parseDateRange($filter, $from, $to);
        $query = Order::whereBetween('created_at', [$start, $end]);

        $user = Auth::user();
        if ($user->is_admin != 1) {
            $query->where('user_id', $user->id);
        }

        return $query->sum('total_amount');
    }


    public function userCount($filter, $from = null, $to = null)
    {
        [$start, $end] = $this->parseDateRange($filter, $from, $to);
        $user = Auth::user();

        $query = User::whereBetween('created_at', [$start, $end]);

        if ($user->is_admin != 1) {
            $query->where('id', $user->id); // Chỉ đếm chính nó
        }

        return $query->count();
    }





    private function parseDateRange($filter, $from = null, $to = null)
    {
        $now = Carbon::now();
        $start = Carbon::today();
        $end = $now;

        switch ($filter) {
            case 'yesterday':
                $start = Carbon::yesterday()->startOfDay();
                $end = Carbon::yesterday()->endOfDay();
                break;
            case 'last_7_days':
                $start = $now->copy()->subDays(6)->startOfDay();
                $end = $now->endOfDay();
                break;
            case 'custom':
                $start = Carbon::parse($from)->startOfDay();
                $end = Carbon::parse($to)->endOfDay();
                break;
        }

        return [$start, $end];
    }
}
