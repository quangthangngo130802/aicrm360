<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function dashboard()
    {

        $filter = 'today';
        return view('backend.dashboard', [
            'customerSummary' => $this->customerSummary($filter),
            'orderCount'      => $this->orderCount($filter ),
            'orderSum'        => $this->orderSum($filter ),
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

        $customer = Customer::whereBetween('created_at', [$start, $end])->count();

        return compact('customer');
    }

    public function orderCount($filter, $from = null, $to = null)
    {
        [$start, $end] = $this->parseDateRange($filter, $from, $to);

        return Order::whereBetween('created_at', [$start, $end])->count();
    }

    public function orderSum($filter, $from = null, $to = null)
    {
        [$start, $end] = $this->parseDateRange($filter, $from, $to);

        return Order::whereBetween('created_at', [$start, $end])->sum('total_amount');
    }

    public function userCount($filter, $from = null, $to = null)
    {
        [$start, $end] = $this->parseDateRange($filter, $from, $to);
        return User::where('is_admin', 0)->whereBetween('created_at', [$start, $end])->count();
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
