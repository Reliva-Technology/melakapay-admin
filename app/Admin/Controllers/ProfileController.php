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

        $grid->column('id', __('ID'));
        $grid->column('user_id', __('User ID'));
        $grid->column('id_type', __('ID type'));
        $grid->column('id_no', __('ID No.'));
        $grid->column('address', __('Address'));
        $grid->column('address2', __('Address2'));
        $grid->column('postcode', __('Postcode'));
        $grid->column('city', __('City'));
        $grid->column('state', __('State'));
        $grid->column('phone_no', __('Phone No.'));
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

        $show->field('id', __('ID'));
        $show->field('user_id', __('User ID'));
        $show->field('id_type', __('ID type'));
        $show->field('id_no', __('ID No.'));
        $show->field('address', __('Address Line 1'));
        $show->field('address2', __('Address Line 2'));
        $show->field('postcode', __('Postcode'));
        $show->field('city', __('City'));
        $show->field('state', __('State'));
        $show->phone_no('Phone Number');
        $show->divider();
        $show->company_name('Company Name');
        $show->roc_no('Company ROC Number');
        $show->company_address1('Company Address Line 1');
        $show->company_address2('Company Address Line 2');
        $show->company_city('Company City');
        $show->company_postcode('Company Postcode');
        $show->company_state('Company State');
        $show->divider();
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

        $form->column(1/2, function ($form) {

            $form->text('user_id', __('User ID'))->disable();
            $form->text('id_type', __('ID type'));
            $form->text('id_no', __('ID Number'));
            $form->text('phone_no', __('Phone Number'));
            $form->textarea('address', __('Address'));
            $form->textarea('address2', __('Address2'));
            $form->number('postcode', __('Postcode'));
            $form->text('city', __('City'));
            $form->text('state', __('State'));
        });

        $form->column(1/2, function ($form) {

            $form->text('company_name', __('Company Name'));
            $form->text('roc_no', __('ROC No'));
            $form->textarea('company_address1', __('Company Address Line 1'));
            $form->textarea('company_address2', __('Company Address Line 2'));
            $form->number('company_postcode', __('Company Postcode'));
            $form->text('company_city', __('Company City'));
            $form->text('company_state', __('Company State'));
        });

        return $form;
    }
}
