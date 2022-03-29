<?php

namespace App\Admin\Controllers;

use App\Models\EmailLog;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class EmailLogController extends AdminController
{
    protected $title = 'Email Log';

    protected function grid()
    {
        $grid = new Grid(new EmailLog());
        $grid->model()->orderBy('id', 'desc');

        $grid->column('date', __('Date'));
        $grid->column('from', __('From'));
        $grid->column('to', __('To'));
        $grid->column('subject', __('Subject'));
        
        $grid->filter(function($filter){
            // Remove the default id filter
            $filter->disableIdFilter();
        
            // Add a column filter
            $filter->like('to', __('Receipient E-mail'));
            $filter->between('date', 'Date Sent')->date();
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
        $show = new Show(EmailLog::findOrFail($id));

        $show->field('date', __('Date'));
        $show->field('from', __('From'));
        $show->field('to', __('To'));
        $show->field('subject', __('Subject'));
        $show->field('body', __('Body'))->unescape();

        $show->panel()->tools(function ($tools) {
            $tools->disableEdit();
            $tools->disableDelete();
        });

        return $show;
    }

    public function print($id)
    {
        $result = EmailLog::find($id);

        $attachments = explode("\n\n", $result['attachments']);

        foreach ($attachments as $i => $attachment) {

            $data = explode("\r\n\r\n", $attachment);
            $headersData = str_replace(";\r\n", ';', $data[0]);
            $headersData = explode("\r\n", $headersData);

            $headers = [];
            foreach ($headersData as $item) {
                $header = explode(':', $item);
                $headers[$header[0]] = $header[1];
            }

            $content = $data[1];

            $data = \Response::make(base64_decode($content, true), 200, $headers);
        }

        header('Content-Type: application/pdf');
        header('Content-disposition: attachment;filename='.$id.'.pdf');
        echo $data;
    }
}
