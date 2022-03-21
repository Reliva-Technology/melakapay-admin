<?php

namespace App\Admin\Controllers;

use App\Models\User;
use App\Models\Agency;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Admin\Actions\User\ResetPassword;
use App\Admin\Actions\User\SetUsernameAsPassword;
use App\Admin\Actions\User\ImpersonateUser;
use Carbon\Carbon as Carbon;
use Encore\Admin\Widgets\Box;
use DB;
use App\Admin\Actions\Transaction\GetTransactionFromEpic;

class UserController extends AdminController
{
    protected $title = 'User';

    protected function grid()
    {
        $grid = new Grid(new User());
        $grid->model()->orderBy('id', 'desc');

        $grid->column('id', __('ID'));
        $grid->column('name', __('Name'));
        $grid->column('email', __('E-mail'));
        $grid->column('username', __('IC'));
        $grid->column('profile.phone_no', __('Phone No.'));
        $grid->updated_at()->display(function ($updated) {
            return Carbon::parse($updated)->diffForHumans();
        });

        $grid->filter(function($filter){
        
            // Add a column filter
            $filter->like('name', __('Name'));
            $filter->like('email', __('E-mail Address'));
            $filter->like('username', __('IC/Passport No.'));
            $filter->like('profile.phone_no', __('Phone No.'));
            $filter->between('created_at', 'Registration Date')->date();
        
        });

        $grid->actions(function ($actions) {
            $actions->add(new ResetPassword);
            $actions->add(new SetUsernameAsPassword);
            $actions->add(new ImpersonateUser);
            $actions->disableDelete();
        });

        $grid->disableCreateButton();

        return $grid;
    }

    protected function detail($id)
    {
        $show = new Show(User::findOrFail($id));

        $show->panel()->style('primary')->title('User details');

        $show->field('id', __('User ID'));
        $show->field('name', __('Name'));
        $show->field('email', __('Email'));
        $show->field('username', __('Username'));
        $show->field('email_verified_at', __('Email verified at'));
        $show->field('password', __('Password'));
        $show->divider();
        $show->field('remember_token', __('Remember token'));
        $show->field('api_token', __('Api token'));
        $show->field('device_token', __('Device token'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableDelete();
            });

        $show->profile('Profile Information', function ($profile) {

            $profile->setResource('/profiles');
        
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

        $show->transaction('Transaction', function ($transaction) {

            $transaction->setResource('/manage/admin/transactions');
        
            $transaction->id(__('ID'));
            $transaction->agency()->agency_name('Agency');
            $transaction->account_id(__('Account ID'));
            $transaction->amount(__('Amount'));
            $transaction->payment_type(__('Payment mode'));
            $transaction->status(__('Status'))->using(['0' => 'Failed', '1' => 'Success']);
            $transaction->epx_trns_no(__('EPS Transaction ID'));
            $transaction->receipt_no(__('Receipt No'));
            $transaction->modified(__('Created at'));

            $transaction->actions(function ($actions) {
                $actions->add(new GetTransactionFromEpic)->disableEdit()->disableDelete();
            });

            $transaction->filter(function($filter){

                // Remove the default id filter
                $filter->disableIdFilter();
            
                // Add a column filter
                $filter->between('modified', 'Date Range')->date();
                $filter->like('epx_trns_no', 'EPS Transaction ID');
                $filter->like('receipt_no', 'Receipt No');
                $filter->equal('status', 'Status')->radio(
                    [
                        '' => 'All',
                        1 => 'Success',
                        0 => 'Attempt Payment',
                        2 => 'Failed',
                        3 => 'Pending'
                    ]
                );
                $filter->equal('agency_id', __('Agency'))->select(Agency::all()->pluck('agency_name','id'));
            
            });

            $transaction->disableCreateButton();
        });

        $show->feedback('Feedback', function ($feedback) {

            $feedback->setResource('/manage/admin/feedback');
        
            $feedback->agency()->agency_name('Agency');
            $feedback->title('Title');
            $feedback->message('Message');
            $feedback->status('Status');
            $feedback->created_at();
            $feedback->updated_at();
        });

        return $show;
    }

    protected function form()
    {
        $form = new Form(new User());

        $form->disableEditingCheck();
        $form->disableCreatingCheck();
        $form->disableViewCheck();

        $form->column(1/2, function ($form) {

            $form->text('id', __('User ID'))->attribute('readonly');
            $form->text('name', __('Name'));
            $form->email('email', __('Email'))->updateRules(['required', "email:rfc,dns"]);
            $form->text('username', __('IC'))->creationRules(['required', "unique:users"])
                ->updateRules(['required', "unique:users,username,{{id}}"]);
            $form->text('remember_token', __('Remember token'))->attribute('readonly');
            $form->text('api_token', __('Api token'))->attribute('readonly');
            $form->text('device_token', __('Device token'))->attribute('readonly');
            $form->text('profile.phone_no', __('Phone No.'))->updateRules(['required']);

            $form->divider('Personal Information');
            $form->text('profile.address','Address Line 1');
            $form->text('profile.address2','Address Line 2');
            $form->text('profile.city','City');
            $form->number('profile.postcode','Postcode');
            $form->select('profile.state','State')->options([
                'Pahang' => 'Pahang',
                'Melaka' => 'Melaka',
                'Johor' => 'Johor',
                'Negeri Sembilan' => 'Negeri Sembilan',
                'Selangor' => 'Selangor',
                'Kuala Lumpur' => 'Kuala Lumpur',
                'Putrajaya' => 'Putrajaya',
                'Perak' => 'Perak',
                'Kedah' => 'Kedah',
                'Pulau Pinang' => 'Pulau Pinang',
                'Perlis' => 'Perlis',
                'Kelantan' => 'Kelantan',
                'Terengganu' => 'Terengganu',
                'Kelantan' => 'Kelantan',
                'Sabah' => 'Sabah',
                'Sarawak' => 'Sarawak',
                'Labuan' => 'Labuan'
            ]);
        });

        $form->column(1/2, function ($form) {
        
            $form->divider('Company Information');
            $form->text('profile.company_name','Company Name');
            $form->text('profile.roc_no','Company ROC Number');
            $form->text('profile.company_address1','Company Address Line 1');
            $form->text('profile.company_address2','Company Address Line 2');
            $form->text('profile.company_city','Company City');
            $form->number('profile.company_postcode','Company Postcode');
            $form->select('profile.company_state','Company State')->options([
                'Pahang' => 'Pahang',
                'Melaka' => 'Melaka',
                'Johor' => 'Johor',
                'Negeri Sembilan' => 'Negeri Sembilan',
                'Selangor' => 'Selangor',
                'Kuala Lumpur' => 'Kuala Lumpur',
                'Putrajaya' => 'Putrajaya',
                'Perak' => 'Perak',
                'Kedah' => 'Kedah',
                'Pulau Pinang' => 'Pulau Pinang',
                'Perlis' => 'Perlis',
                'Kelantan' => 'Kelantan',
                'Terengganu' => 'Terengganu',
                'Kelantan' => 'Kelantan',
                'Sabah' => 'Sabah',
                'Sarawak' => 'Sarawak',
                'Labuan' => 'Labuan'
            ]);

            $form->text('profile.user_id')->value($form->id);
            $form->text('profile.full_name')->value($form->name);
            $form->text('profile.id_type')->value('MyKad');
        });

        return $form;
    }
}
