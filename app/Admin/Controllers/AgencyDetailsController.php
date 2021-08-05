<?php

namespace App\Admin\Controllers;

use App\Models\AgencyDetails;
use App\Models\Agency;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class AgencyDetailsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Agency Details';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new AgencyDetails());

        $grid->column('id', __('ID'))->sortable();
        $grid->column('agency.agency_name', __('Agency'))->sortable();
        $grid->column('logo', __('Logo'))->image();
        $grid->filter(function($filter){

            // Remove the default id filter
            $filter->disableIdFilter();
        
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
        $show = new Show(AgencyDetails::findOrFail($id));

        $show->field('agency.agency_name', __('Agency'));
        $show->field('description', __('Description'));
        $show->logo()->image();
        $show->field('url', __('URL'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new AgencyDetails());

        $form->select('agency_id', __('Agency'))->options(Agency::all()->pluck('agency_name','id'))->required();
        $form->summernote('description', __('Description'));
        $form->image('logo', __('Logo'));
        $form->url('url', __('URL'));

        return $form;
    }
}
