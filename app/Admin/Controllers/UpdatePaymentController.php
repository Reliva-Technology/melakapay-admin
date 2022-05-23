<?php

namespace App\Admin\Controllers;

use App\Models\UpdatePayment;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Admin\Actions\UpdatePayment\UpdatePaymentStatus;

class UpdatePaymentController extends AdminController
{
    protected $title = 'Cron Update Payment Log';

    protected function grid()
    {
        $grid = new Grid(new UpdatePayment());
        $grid->model()->orderBy('id', 'desc');

        $grid->column('eps_id', __('EPS ID'));
        $grid->column('transaction_id', __('Transaction ID'));
        $grid->column('eps_status', __('Status'))->using(['0' => 'Attempt Payment', '1' => 'Successful', '2' => 'Failed', '3' => 'Pending']);
        $grid->column('response', __('Response'));

        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new UpdatePaymentStatus());
        });

        return $grid;
    }

    protected function detail($id)
    {
        $show = new Show(UpdatePayment::findOrFail($id));
        return $show;
    }

}
