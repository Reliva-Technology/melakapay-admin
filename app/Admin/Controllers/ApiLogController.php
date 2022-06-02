<?php

namespace App\Admin\Controllers;

use App\Models\ApiLog;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Models\Agency;
use App\Models\User;

class ApiLogController extends AdminController
{
    protected $title = 'Api Logs';

    protected function grid()
    {
        $grid = new Grid(new ApiLog());
        $grid->model()->orderBy('id', 'desc');

        $grid->column('id', __('ID'));
        $grid->column('agency.agency_name', __('Agency'));
        $grid->column('api_name', __('API name'));
        $grid->column('search_id', __('Search ID'));
        $grid->column('created_at', __('Created at'));

        $grid->filter(function($filter){
            // Remove the default id filter
            $filter->disableIdFilter();
        
            // Add a column filter
            $filter->equal('search_id', __('Search ID'));
            $filter->equal('user_id', __('User'))->select(User::all()->pluck('name','id'));
            $filter->equal('agency_id', __('Agency'))->select(Agency::all()->pluck('agency_name','id'));
        });

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
        });

        $grid->disableCreateButton()->disableColumnSelector();

        return $grid;
    }

    protected function detail($id)
    {
        $show = new Show(ApiLog::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('user.name', __('User'));
        $show->field('agency.agency_name', __('Agency'));
        $show->field('api_name', __('API name'));
        $show->field('search_id', __('Search ID'));
        $show->field('request', __('API Request'))->json();
        $show->field('response', __('API Response'))->json();
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        $show->panel()->tools(function ($tools) {
            $tools->disableEdit();
            $tools->disableDelete();
        });

        return $show;
    }
}
