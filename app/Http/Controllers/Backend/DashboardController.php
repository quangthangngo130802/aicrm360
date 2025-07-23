<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Customer;
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
            $appointmentCount = Appointment::whereBetween('scheduled_at', [$today, $tomorrow])->count();

            $startDate = Carbon::tomorrow();
            $endDate = Carbon::now()->endOfWeek();

            $appointmentNextCount = Appointment::whereBetween('scheduled_at', [$startDate, $endDate])->count();

            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();

            $customerCount = Customer::whereHas('appointments', function ($query) use ($startOfWeek, $endOfWeek) {
                $query->whereBetween('scheduled_at', [$startOfWeek, $endOfWeek]);
            })->count();

            $appointmentNext = Appointment::whereBetween('scheduled_at', [$startDate, $endDate])->limit(5)->get();  // lịch sắp diễn ra

            $customerNow = Customer::whereHas('appointments', function ($query) use ($today, $tomorrow) {
                $query->whereBetween('scheduled_at',  [$today, $tomorrow]);
            })->get();

            $customers = Customer::whereHas('appointments', function ($query) use ($today, $tomorrow) {
                $query->whereBetween('scheduled_at', [$today, $tomorrow]);
            })->get();

            return view('backend.dashboardUser', compact('appointmentCount', 'appointmentNextCount', 'customerCount', 'appointmentNext',  'customerNow','customers'));
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
