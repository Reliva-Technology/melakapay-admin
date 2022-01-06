<?php

namespace App\Admin\Controllers;

use App\Models\CarianPersendirian;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Carbon\Carbon as Carbon;

class CarianPersendirianController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'CarianPersendirian';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CarianPersendirian());

        $grid->column('id', __('ID'));
        $grid->column('bil_paparan', __('Bil. Paparan'));
        $grid->column('id_hakmilik', __('ID Hakmilik'));
        $grid->column('id_portal_transaksi', __('ID Portal'));
        $grid->column('tarikh', __('Tarikh'));
        $grid->column('user_id', __('User ID'));
        $grid->created_at()->display(function ($created) {
            return Carbon::parse($created)->diffForHumans();
        });
        $grid->updated_at()->display(function ($updated) {
            return Carbon::parse($updated)->diffForHumans();
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(CarianPersendirian::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('bil_paparan', __('Bil paparan'));
        $show->field('id_hakmilik', __('Id hakmilik'));
        $show->field('id_portal_transaksi', __('Id portal transaksi'));
        $show->field('tarikh', __('Tarikh'));
        $show->field('user_id', __('User id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CarianPersendirian());

        $form->number('bil_paparan', __('Bil paparan'));
        $form->text('id_hakmilik', __('Id hakmilik'));
        $form->text('id_portal_transaksi', __('Id portal transaksi'));
        $form->text('tarikh', __('Tarikh'));
        $form->number('user_id', __('User id'));

        return $form;
    }
}
