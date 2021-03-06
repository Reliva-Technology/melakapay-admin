<?php

namespace App\Admin\Controllers;

use App\Models\Video;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class VideoController extends AdminController
{
    protected $title = 'Video';

    protected function grid()
    {
        $grid = new Grid(new Video());

        $grid->column('id', __('ID'))->sortable();
        $grid->column('title', __('Title'))->sortable();
        $grid->column('description', __('Description'));
        $grid->column('file_url', __('Video ID'))->help('Only paste the video ID here');

        return $grid;
    }

    protected function detail($id)
    {
        $show = new Show(Video::findOrFail($id));

        $show->field('title', __('Title'));
        $show->field('description', __('Description'));
        $show->file_url();

        return $show;
    }

    protected function form()
    {
        $form = new Form(new Video());

        $form->disableEditingCheck();
        $form->disableCreatingCheck();
        $form->disableViewCheck();

        $form->text('title', __('Title'))->help('Separate by | for dual language title');
        $form->textarea('description', __('Description'))->help('Separate by | for dual language description');
        $form->text('file_url', __('Video ID'))->help('Only paste the video ID here');

        return $form;
    }
}
