<?php

namespace App\Admin\Controllers;

use App\Models\Profile;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ProfileController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Profile';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Profile());

        $grid->column('id', __('Id'));
        $grid->column('user_id', __('User id'));
        $grid->column('id_type', __('Id type'));
        $grid->column('id_no', __('Id no'));
        $grid->column('address', __('Address'));
        $grid->column('address2', __('Address2'));
        $grid->column('postcode', __('Postcode'));
        $grid->column('city', __('City'));
        $grid->column('state', __('State'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('deleted_at', __('Deleted at'));

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
        $show = new Show(Profile::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        $show->field('id_type', __('Id type'));
        $show->field('id_no', __('Id no'));
        $show->field('address', __('Address'));
        $show->field('address2', __('Address2'));
        $show->field('postcode', __('Postcode'));
        $show->field('city', __('City'));
        $show->field('state', __('State'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('deleted_at', __('Deleted at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Profile());

        $form->number('user_id', __('User id'));
        $form->text('id_type', __('Id type'));
        $form->text('id_no', __('Id no'));
        $form->textarea('address', __('Address'));
        $form->textarea('address2', __('Address2'));
        $form->number('postcode', __('Postcode'));
        $form->text('city', __('City'));
        $form->text('state', __('State'));

        return $form;
    }
}
