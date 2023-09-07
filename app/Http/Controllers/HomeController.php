<?php

namespace App\Http\Controllers;

use App\Models\Activitylog;
use App\Models\Product;
use App\Models\Company_detail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Rating;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function load_count_ratings()
    {
        $order_ratings = Order::where('orders.status', 6)
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->join('ratings', 'orders.id', '=', 'ratings.order_id')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('order_items.id as order_item_id', 'order_items.*', 'orders.id as order_id', 'orders.order_id as order_track_id', 'products.id as prod_id', 'products.*', 'users.id as user_id', 'users.*', 'ratings.rate_star')
            ->orderBy('orders.id', 'DESC')
            ->limit(5)
            ->get();

        $allOrders = Order::all();

        // Count the total number of all items
        $totalItems = count($allOrders);

        // Deduct the limited query result of 5 items
        $remainingRatings = max(0, $totalItems - 5);

        return response()->json([
            'orderRatings' => $order_ratings,
            'countRating' => count($order_ratings),
        ], 200);
    }


    public function get_all_ratings()
    {
        $paginate = request('paginate', 10);
        $search = request('search', null);

        $query = Rating::join('order_items', 'ratings.order_id', '=', 'order_items.order_id')
            ->join('orders', 'ratings.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('order_items.id as order_item_id', 'order_items.*', 'products.id as prod_id', 'products.*', 'ratings.id as rating_id', 'ratings.*', 'orders.order_id as order_tracking_id')
            ->orderBy('order_items.created_at', 'DESC');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('products.title', 'LIKE', "%$search%");
            });
        }

        $ratings = $query->paginate($paginate);


        return response()->json(['ratings' => $ratings], 200);
    }

    public function activity_logs()
    {

        $paginate = request('paginate', 10);
        $search = request('search', null);

        $query = Activitylog::join('users', 'activitylogs.user_id', '=', 'users.id')
            ->select('activitylogs.created_at as logs_created', 'activitylogs.*', 'users.name')
            ->orderBy('activitylogs.created_at', 'DESC');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'LIKE', "%$search%");
            });
        }

        $logs = $query->paginate($paginate);


        return response()->json(['logs' => $logs], 200);
    }

    public function load_dashboard_data()
    {
        $totalCompleteSales = Order::where('status', 5)->sum('total_amount');
        $totalPendingSales = Order::where('status', 0)->sum('total_amount');
        $totalCustomers = User::where('role', 1)->count();
        $totalProducts = Product::count();

        return response()->json([
            'totalCompleteSales' => $totalCompleteSales,
            'totalPendingSales' => $totalPendingSales,
            'totalCustomers' => $totalCustomers,
            'totalProducts' => $totalProducts,
        ]);

    }

    public function complete_order_chart()
    {
        $selectedValue = request('selectedValue');
        $labels = [];
        $data = [];

        if ($selectedValue === '1') {
            $labels = [];
            $currentDay = Carbon::now('Asia/Manila')->startOfWeek();
            for ($day = 1; $day <= 7; $day++) {
                $labels[] = $currentDay->format('l');
                $currentDay->addDay();
            }
            $data = $this->getOrderDataForTimePeriod('weekly', $labels);

        } elseif ($selectedValue === '2') {
            $labels = [
                Carbon::now('Asia/Manila')->format('l'),
            ];
            $data = $this->getOrderDataForTimePeriod('today', $labels);
        } elseif ($selectedValue === '3') {
            $labels = [];
            $data = [];
            $currentMonth = Carbon::now('Asia/Manila')->startOfMonth();
            $weeksInMonth = $currentMonth->copy()->endOfMonth()->weekOfMonth;

            for ($week = 1; $week <= $weeksInMonth; $week++) {
                $weekStartDate = $currentMonth->copy()->startOfWeek();
                $weekEndDate = $currentMonth->copy()->endOfWeek();

                $weekTotal = Order::where('status', 5)
                    ->whereYear('updated_at', $currentMonth->year)
                    ->whereMonth('updated_at', $currentMonth->month)
                    ->whereBetween('updated_at', [$weekStartDate, $weekEndDate])
                    ->sum('total_amount');

                $labels[] = 'Week ' . $week;
                $data[] = $weekTotal;

                $currentMonth->addWeek();
            }


        } elseif ($selectedValue === '4') {
            $labels = [];
            $currentYear = Carbon::now('Asia/Manila')->startOfYear();
            for ($month = 1; $month <= 12; $month++) {
                $labels[] = $currentYear->format('M');
                $currentYear->addMonth();
            }
            $data = $this->getOrderDataForTimePeriod('yearly', $labels);
        }

        if (empty($data)) {
            return response()->json([
                'labels' => [],
                'data' => [],
                'message' => 'No data available',
            ]);
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    private function getOrderDataForTimePeriod($timePeriod, $labels)
    {
        $totalAmounts = [];

        if ($timePeriod === 'today') {
            $totalAmounts = Order::where('status', 5)
                ->whereDate('updated_at', today())
                ->pluck('total_amount')
                ->toArray();

        } elseif ($timePeriod === 'weekly') {
            $totalAmounts = Order::selectRaw('SUM(total_amount) as total, DAYOFWEEK(updated_at) as day')
                ->where('status', 5)
                ->whereBetween('updated_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])
                ->groupBy('day')
                ->pluck('total', 'day')
                ->toArray();

            $totalAmounts = $this->fillMissingDays($totalAmounts, $labels);

        } elseif ($timePeriod === 'monthly') {
            $totalAmounts = Order::where('status', 5)
                ->whereYear('updated_at', now()->year)
                ->whereMonth('updated_at', now()->month)
                ->pluck('total_amount')
                ->toArray();
        } elseif ($timePeriod === 'yearly') {
            $currentYear = Carbon::now()->year;
            $months = range(1, 12);
            $monthlyData = Order::selectRaw('MONTH(updated_at) as month, SUM(total_amount) as total')
                ->where('status', 5)
                ->whereYear('updated_at', $currentYear)
                ->groupBy(DB::raw('MONTH(updated_at)'))
                ->pluck('total', 'month')
                ->toArray();

            foreach ($months as $month) {
                $totalAmounts[] = $monthlyData[$month] ?? 0;
            }
        }

        return $totalAmounts;
    }

    private function fillMissingDays($data, $labels)
    {
        $filledData = [];
        foreach ($labels as $label) {
            $dayOfWeek = Carbon::parse($label)->dayOfWeek + 1; // Adjust the dayOfWeek value to match database format
            $filledData[] = $data[$dayOfWeek] ?? 0;
        }
        return $filledData;
    }

    public function company_details()
    {

        $details = Company_detail::where('id', 1)->get();

        return response()->json(['details' => $details], 200);

    }

    public function update_company_details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|regex:/^09\d{9}$/',
            'address' => 'required|string',
            'gmail' => 'required|email',
            'facebook' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Fetch the company details from the database
        $companyDetails = Company_detail::where('id',1)->first();

        // Update the fields with the new values from the request
        $companyDetails->phone = $request->phone;
        $companyDetails->address = $request->address;
        $companyDetails->gmail = $request->gmail;
        $companyDetails->facebook = $request->facebook;
        $companyDetails->about = $request->description;

        // Save the updated details back to the database
        $companyDetails->save();

        // Return a success response
        return response()->json(['message' => 'Company details updated'], 200);
    }







}