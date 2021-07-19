<?php

namespace App\Admin\Controllers;

use App\Models\Favourite;
use App\Models\Agency;
use App\Models\User;
use App\Models\Service;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class FavouriteController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Favourite';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Favourite());

        $grid->column('agency.agency_name', __('Agency'))->sortable();
        $grid->column('account_no', __('Account no'));
        $grid->column('nric', __('NRIC'));
        $grid->column('bill_payment_type', __('Payment Type'));
        $grid->column('account_type', __('Account Type'));
        $grid->column('search_value', __('Search Value'));
        $grid->column('search_type', __('Search Type'));
        $grid->column('holder_name', __('Holder Name'));
        $grid->column('user.name', __('User Name'));

        $grid->filter(function($filter){

            // Remove the default id filter
            $filter->disableIdFilter();
        
            // Add a column filter
            $filter->like('search_value', 'Search Value');
            $filter->like('account_no', 'Account No');
            $filter->like('holder_name', 'Account Holder Name');
            $filter->equal('agency_id', __('Agency'))->select(Agency::all()->pluck('agency_name','id'));
            $filter->equal('bill_payment_type', __('Payment Type'))->select(Service::all()->pluck('category','category'));
        
        });

        $grid->actions(function ($actions) {
            $actions->disableEdit();
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
        $show = new Show(Favourite::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('user.name', __('User Name'));
        $show->field('agency.agency_name', __('Agency'));
        $show->field('account_no', __('Account no'));
        $show->field('nric', __('NRIC'));
        $show->field('bill_payment_type', __('Payment Type'));
        $show->field('holder_name', __('Account Holder Name'));
        $show->field('account_type', __('Account Type'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Favourite());

        $form->text('account_no', __('Account no'));
        $form->text('bill_name', __('Bill name'));
        $form->number('user_id', __('User id'));
        $form->text('holder_name', __('Holder name'));
        $form->text('holder_idno', __('Holder idno'));
        $form->text('id_type', __('Id type'));
        $form->text('agency_id', __('Agency id'));
        $form->text('acc_typ', __('Acc typ'));
        $form->textarea('address', __('Address'));
        $form->text('address2', __('Address2'));
        $form->text('postcode', __('Postcode'));
        $form->text('city', __('City'));
        $form->text('state', __('State'));
        $form->datetime('modified', __('Modified'))->default(date('Y-m-d H:i:s'));

        return $form;
    }
}
