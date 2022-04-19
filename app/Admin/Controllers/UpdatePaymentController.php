<?php

namespace App\Admin\Controllers;

use App\Models\UpdatePayment;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UpdatePaymentController extends AdminController
{
    protected $title = 'Cron Update Payment Log';

    protected function grid()
    {
        $grid = new Grid(new UpdatePayment());
        $grid->model()->orderBy('id', 'desc');

        return $grid;
    }

    protected function detail($id)
    {
        $show = new Show(UpdatePayment::findOrFail($id));
        return $show;
    }

}
