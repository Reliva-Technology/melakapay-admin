<?php

namespace App\Admin\Controllers;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Models\AgencyService;
use Carbon\Carbon;

class AgencyServiceController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Agency API';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new AgencyService);

        $grid->column('id', __('ID'))->sortable();
        $grid->column('agency.agency_name', __('Agency'))->sortable();
        $grid->column('host_url', __('URL'))->sortable();
        $grid->column('api_url', __('API'));
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
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(AgencyService::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('agency.agency_name', __('Agency'));
        $show->field('host_url', __('URL'));
        $show->field('api_url', __('API'));
        $show->field('parameters', __('Required parameter'));
        $show->field('api_username', __('API Username'));
        $show->field('api_password', __('API Password'));

        $show->panel()->tools(function ($tools) {
            $tools->disableDelete();
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
        $form = new Form(new AgencyService);

        $form->disableEditingCheck();
        $form->disableCreatingCheck();
        $form->disableViewCheck();

        $form->display('id', __('ID'));
        $form->text('host_url', __('URL'));
        $form->text('api_url', __('API'));
        $form->text('parameters', __('Required parameter'));
        $form->text('api_username', __('API Username'));
        $form->text('api_password', __('API Password'));

        return $form;
    }
}