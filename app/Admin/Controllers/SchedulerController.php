<?php

namespace App\Admin\Controllers;

use App\Models\Scheduler;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Models\Agency;
use App\Models\Service;
use Illuminate\Http\Request;
use DB;

class SchedulerController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Scheduler';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Scheduler());

        $grid->column('id', __('ID'));
        $grid->column('agency.agency_name', __('Agency'));
        $grid->column('service.category', __('Service'));
        $grid->column('status', __('Status'))->using(['0' => 'No', '1' => 'Yes', '2' => 'Temporary']);
        $grid->column('start_at', __('Start'));
        $grid->column('end_at', __('End'));

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
        $show = new Show(Scheduler::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('agency_id', __('Agency'));
        $show->field('service_id', __('Service'));
        $show->field('notice', __('Notice'));
        $show->field('status', __('Status'));
        $show->field('start_at', __('Start'));
        $show->field('end_at', __('End'));
        $show->field('created_at', __('Created'));
        $show->field('updated_at', __('Updated'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Scheduler());

        $form->column(1/2, function ($form) {
            $form->select('agency_id', __('Agency'))->options(Agency::all()->pluck('agency_name','id'))->load('service', '/api/service')->required();
            $form->select('service', __('Service'));
            $form->datetime('start_at', __('Start at'))->default(date('Y-m-d H:i:s'));
            $form->datetime('end_at', __('End at'))->default(date('Y-m-d H:i:s'));
        });
        $form->column(1/2, function ($form) {
            $form->textarea('notice', __('Notice'));
            $form->radio('status', __('Status'))->options(['0' => 'No', '1' => 'Yes', '2' => 'Temporary'])->default('0')->stacked();
        });

        return $form;
    }

    public function service(Request $request)
    {
        $agency_id = $request->get('q');
        return Service::where('agency_id', $agency_id)->get(['id', DB::raw("CONCAT(category,':',sub_category) as text")]);
    }

}
