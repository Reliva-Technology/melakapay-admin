<?php

namespace App\Admin\Controllers;

use App\Models\Contact;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Models\Agency;

class ContactController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Contact';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Contact());

        $grid->column('id', __('ID'));
        $grid->column('agency.agency_name', __('Agency'));
        $grid->column('address', __('Address'));
        $grid->column('email', __('E-mail'));
        $grid->column('telephone', __('Telephone'));
        $grid->column('ordering', __('Ordering'));

        $grid->filter(function($filter){

            // Remove the default id filter
            $filter->disableIdFilter();
        
            // Add a column filter
            $filter->equal('agency_id', __('Agency'))->select(Agency::all()->pluck('agency_name','id'));

        });

        $grid->actions(function ($actions) {
            $actions->disableDelete();
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
        $show = new Show(Contact::findOrFail($id));

        $show->field('agency.agency_name', __('Agency'));
        $show->field('address', __('Address'));
        $show->field('email', __('E-mail'));
        $show->field('telephone', __('Telephone'));
        $show->field('ordering', __('Ordering'));

        $show->panel()->tools(function ($tools) {
            $tools->disableDelete();
        });

        $show->contactDetails('Contact Details', function ($detail) {

            $detail->setResource('/contact-details');
        
            $detail->name();
            $detail->email();
            $detail->telephone();
        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Contact());

        $form->select('agency_id', __('Agency'))->options(Agency::all()->pluck('agency_name','id'))->required();
        $form->textarea('address', __('Address'));
        $form->email('email', __('E-mail'));
        $form->text('telephone', __('Telephone'));
        $form->number('ordering', __('Ordering'));

        return $form;
    }
}
