<?php

namespace App\Admin\Controllers;

use App\Models\ContactDetail;
use App\Models\Contact;
use App\Models\Agency;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ContactDetailController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'ContactDetail';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ContactDetail());

        $grid->column('id', __('ID'));
        $grid->column('name', __('Name'));
        $grid->column('email', __('Email'));
        $grid->column('telephone', __('Telephone'));
        $grid->filter(function($filter){

            // Remove the default id filter
            $filter->disableIdFilter();
        
            // Add a column filter
            $filter->equal('contact.agency_id', __('Agency'))->select(Agency::all()->pluck('agency_name','id'));

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
        $show = new Show(ContactDetail::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('name', __('Name'));
        $show->field('email', __('Email'));
        $show->field('telephone', __('Telephone'));
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
        $form = new Form(new ContactDetail());

        $form->disableEditingCheck();
        $form->disableCreatingCheck();
        $form->disableViewCheck();

        $form->select('contact_id', __('Agency'))->options(Agency::all()->pluck('agency_name','id'))->required();
        $form->text('name', __('Name'));
        $form->email('email', __('Email'));
        $form->text('telephone', __('Telephone'));

        return $form;
    }
}
