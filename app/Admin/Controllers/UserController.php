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
use Carbon\Carbon as Carbon;
use Encore\Admin\Widgets\Box;
use DB;
use App\Admin\Actions\Transaction\GetTransactionFromEpic;

class UserController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'User';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User());

        $grid->column('id', __('ID'));
        $grid->column('name', __('Name'));
        $grid->column('email', __('E-mail'));
        $grid->column('username', __('IC'));
        $grid->updated_at()->display(function ($updated) {
            return Carbon::parse($updated)->diffForHumans();
        });

        $grid->filter(function($filter){

            // Remove the default id filter
            $filter->disableIdFilter();
        
            // Add a column filter
            $filter->like('name', __('Name'));
            $filter->like('email', __('E-mail Address'));
            $filter->like('username', __('IC/Passport No.'));
        
        });

        $today = Carbon::today();

        $grid->header(function ($query) {
            $created = $query->select(DB::raw('count(username) as count, MONTH(created_at) as created_at'))
                ->groupBy('created_at')
                ->get()
                ->pluck('count', 'created_at')
                ->toArray();
            $doughnut = view('admin.charts.user', compact('created'));
            #return new Box('Registration', $doughnut);
        });

        $grid->actions(function ($actions) {
            $actions->add(new ResetPassword);
            $actions->add(new SetUsernameAsPassword);
        });

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

        $show->profile('Profile information', function ($profile) {

            $profile->setResource('/profiles');
        
            $profile->id_type('ID Type');
            $profile->id_no('IC/Passport No.');
            $profile->address('Address Line 1');
            $profile->address2('Address Line 2');
            $profile->postcode();
            $profile->city();
            $profile->state();
            $profile->phone_no('Phone Number');
            $profile->divider();
            $profile->company_name('Company Name');
            $profile->roc_no('Company ROC Number');
            $profile->company_address1('Company Address Line 1');
            $profile->company_address2('Company Address Line 2');
            $profile->company_city('Company City');
            $profile->company_postcode('Company Postcode');
            $profile->company_state('Company State');
        });

        $show->transaction('Transaction', function ($transaction) {

            $transaction->setResource('/transactions');
        
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
                $actions->add(new GetTransactionFromEpic);
            });
        });

        $show->feedback('Feedback', function ($feedback) {

            $feedback->setResource('/feedback');
        
            $feedback->agency()->agency_name('Agency');
            $feedback->title('Title');
            $feedback->message('Message');
            $feedback->status('Status');
            $feedback->created_at();
            $feedback->updated_at();
        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User());

        $form->hidden('id', __('User ID'));
        $form->text('name', __('Name'));
        $form->email('email', __('Email'))->updateRules(['required', "email:rfc,dns"]);
        $form->text('username', __('IC'))->creationRules(['required', "unique:users"])
            ->updateRules(['required', "unique:users,username,{{id}}"]);
        $form->text('remember_token', __('Remember token'))->attribute('readonly');
        $form->text('api_token', __('Api token'))->attribute('readonly');
        $form->text('device_token', __('Device token'))->attribute('readonly');

        return $form;
    }
}
