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
