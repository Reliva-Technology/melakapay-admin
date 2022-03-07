<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use App\Models\Transaction;
use DB;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('Dashboard')
            ->description('Overview of MelakaPay')
            ->row(function (Row $row) {

                $row->column(3, function (Column $column) {
                    $column->append(HomeController::paymentMode());
                });
                
                
            })
            ->row(function (Row $row) {
                $row->column(6, function (Column $column) {
                    $column->append(HomeController::transaction());
                });
                $row->column(6, function (Column $column) {
                    $column->append(HomeController::visitor());
                });
            });
    }

    public function paymentMode()
    {
        $method = Transaction::select(DB::raw('count(payment_type) as count, payment_type'))
            ->groupBy('payment_type')
            ->get()
            ->pluck('count', 'payment_type')
            ->toArray();
        $doughnut = view('admin.charts.payment-mode', compact('method'));
        return new Box('Payment Mode', $doughnut);
    }

    public function visitor()
    {
        $visitor = DB::table('visitors')
            ->select(DB::raw('count(id) as count, date'))
            ->groupBy('date')
            ->orderBy('id', 'desc')
            ->limit(30)
            ->get()
            ->pluck('count', 'date')
            ->toArray();
        $bar = view('admin.charts.user', compact('visitor'));
        return new Box('Visitor (last 30 days)', $bar);
    }

    public function transaction()
    {
        $transaction = DB::table('transaction_details')
            ->select(DB::raw('count(id) as count, agency'))
            ->where('agency','LIKE', '%-app')
            ->where('agency','not LIKE', '%|%')
            ->groupBy('agency')
            ->orderBy('id', 'desc')
            ->get()
            ->pluck('count', 'agency')
            ->toArray();
        $bar = view('admin.charts.transaction', compact('transaction'));
        return new Box('Transaction by Agency', $bar);
    }

    public function about(Content $content)
    {
        return $content
            ->title('About')
            ->description('About this app')
            ->row(Dashboard::title())
            ->row(function (Row $row) {

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::environment());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::extensions());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::dependencies());
                });
            });
    }
}
