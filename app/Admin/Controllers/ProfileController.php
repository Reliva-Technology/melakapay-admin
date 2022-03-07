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
        $grid->model()->orderBy('id', 'desc');

        $grid->column('id', __('ID'));
        $grid->column('user_id', __('User ID'));
        $grid->column('id_type', __('ID type'));
        $grid->column('id_no', __('ID No.'));
        $grid->column('full_name', __('Full Name'));

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
        $show->field('full_name', __('Full Name'));
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

            $form->text('user_id', __('User ID'));
            $form->text('id_type', __('ID type'));
            $form->text('id_no', __('ID Number'));
            $form->text('full_name', __('Full Name'));
            $form->text('phone_no', __('Phone Number'));
            $form->textarea('address', __('Address'))->rules('nullable');
            $form->textarea('address2', __('Address2'))->rules('nullable');
            $form->number('postcode', __('Postcode'))->rules('nullable');
            $form->text('city', __('City'))->rules('nullable');
            $form->text('state', __('State'))->rules('nullable');
        });

        $form->column(1/2, function ($form) {

            $form->text('company_name', __('Company Name'))->rules('nullable');
            $form->text('roc_no', __('ROC No'))->rules('nullable');
            $form->textarea('company_address1', __('Company Address Line 1'))->rules('nullable');
            $form->textarea('company_address2', __('Company Address Line 2'))->rules('nullable');
            $form->number('company_postcode', __('Company Postcode'))->rules('nullable');
            $form->text('company_city', __('Company City'))->rules('nullable');
            $form->text('company_state', __('Company State'))->rules('nullable');
        });

        return $form;
    }
}
