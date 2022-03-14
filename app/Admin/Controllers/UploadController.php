<?php

namespace App\Admin\Controllers;

use App\Models\Upload;
use App\Models\Agency;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UploadController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Upload';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Upload());

        $grid->column('id', __('ID'));
        $grid->column('agency.agency_name', __('Agency'));
        $grid->column('title', __('Title'));
        $grid->column('description', __('Description'));
        $grid->column('file_url', __('File'));

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
        $show = new Show(Upload::findOrFail($id));

        $show->field('title', __('Title'));
        $show->field('agency.agency_name', __('Agency'));
        $show->field('description', __('Description'));
        $show->file_url()->file();

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Upload());

        $form->disableEditingCheck();
        $form->disableCreatingCheck();
        $form->disableViewCheck();

        $form->select('agency_id', __('Agency'))->options(Agency::all()->pluck('agency_name','id'));
        $form->text('title', __('Title'))->help('Separate by | for dual language title');
        $form->textarea('description', __('Description'))->help('Separate by | for dual language description');
        $form->file('file_url', __('File'))->rules('mimes:pdf,png,jpg,jpeg')->removable();

        return $form;
    }
}
