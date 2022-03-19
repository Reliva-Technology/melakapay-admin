<?php

namespace App\Admin\Controllers;

use App\Models\Ebayar;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Admin\Actions\User\AllowLogin;

class EbayarController extends AdminController
{
    protected $title = 'eBayar User';

    protected function grid()
    {
        $grid = new Grid(new Ebayar);
        $grid->model()->orderBy('id', 'desc');

        $grid->column('id', __('eBayar ID'));
        $grid->column('melakapay.id', __('MelakaPay ID'))->label();
        $grid->column('profile.full_name', __('Name'));
        $grid->column('profile.email', __('E-mail'));
        $grid->column('username', __('IC/Passport No'));

        $grid->filter(function($filter){
        
            // Add a column filter
            $filter->like('profile.full_namename', __('Name'));
            $filter->like('profile.email', __('E-mail Address'));
            $filter->like('username', __('IC/Passport No.'));
            $filter->like('profile.phone_no', __('Phone No.'));
        
        });

        $grid->actions(function ($actions) {
            $actions->disableEdit();
            $actions->disableDelete();
            $actions->add(new AllowLogin);
        });

        $grid->disableCreateButton();

        return $grid;
    }

    protected function detail($id)
    {
        $show = new Show(Ebayar::findOrFail($id));

        $show->panel()->style('primary')->title('User details');

        $show->field('id', __('User ID'));
        $show->field('profile.full_name', __('Name'));
        $show->field('profile.email', __('Email'));
        $show->field('username', __('Username'));
        $show->field('password', __('Password'));

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableDelete();
            });

        $show->profile('Profile Information', function ($profile) {
        
            $profile->id_type('ID Type');
            $profile->id_no('IC/Passport No.');
            $profile->address('Address Line 1');
            $profile->address2('Address Line 2');
            $profile->postcode();
            $profile->city();
            $profile->state();
            $profile->phone_no('Phone Number');
            $profile->divider('Company Information');
            $profile->company_name('Company Name');
            $profile->roc_no('Company ROC Number');
            $profile->company_address1('Company Address Line 1');
            $profile->company_address2('Company Address Line 2');
            $profile->company_city('Company City');
            $profile->company_postcode('Company Postcode');
            $profile->company_state('Company State');

            $profile->panel()
                ->tools(function ($tools) {
                    $tools->disableEdit();
                    $tools->disableDelete();
                });
        });

        return $show;
    }
}
