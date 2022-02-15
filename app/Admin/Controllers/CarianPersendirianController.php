<?php

namespace App\Admin\Controllers;

use App\Models\CarianPersendirian;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Carbon\Carbon as Carbon;
use CodeDredd\Soap\Facades\Soap;

class CarianPersendirianController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'CarianPersendirian';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CarianPersendirian());

        $grid->column('id', __('ID'));
        $grid->column('bil_paparan', __('Bil. Paparan'));
        $grid->column('id_hakmilik', __('ID Hakmilik'));
        $grid->column('id_portal_transaksi', __('ID Portal'));
        $grid->column('tarikh', __('Tarikh'));
        $grid->column('user_id', __('User ID'));

        $grid->filter(function($filter){

            // Remove the default id filter
            $filter->disableIdFilter();
        
            // Add a column filter
            $filter->like('id_hakmilik', __('ID Hakmilik'));
            $filter->like('id_portal_transaksi', __('ID Portal'));
        
        });

        $grid->actions(function ($actions) {
            $actions->disableDelete()->disableEdit();
        });

        $grid->disableCreateButton();

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
        $show = new Show(CarianPersendirian::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('bil_paparan', __('Bil paparan'));
        $show->field('id_hakmilik', __('ID hakmilik'));
        $show->field('id_portal_transaksi', __('ID portal'));
        $show->field('tarikh', __('Tarikh'));
        $show->field('user_id', __('User ID'));
        $show->id_portal_transaksi(__('Action'))->unescape()->as(function ($id_portal_transaksi) {
            return '<a href="'.url('/carian-persendirian/print-carian-persendirian').'/'.$id_portal_transaksi.'" class="btn btn-sm btn-success" title="Print Carian Persendirian" target="_blank">Print Carian Persendirian</a>';
        });

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableDelete();
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
        $form = new Form(new CarianPersendirian());

        $form->number('bil_paparan', __('Bil paparan'));
        $form->text('id_hakmilik', __('ID hakmilik'));
        $form->text('id_portal_transaksi', __('Id portal'));
        $form->text('tarikh', __('Tarikh'));
        $form->number('user_id', __('User ID'));

        return $form;
    }

    public function print($id)
    {
        $url = 'http://etanah.melaka.gov.my/etanahwsa/CarianPersendirianService?wsdl';
        $client = Soap::baseWsdl($url)->muatTurunDokumen(['idPortalTrans' => $id]);
        $result = json_decode($client->body(),true);
        $data = $result['return']['bytes'];
        header('Content-Type: application/pdf');
        header('Content-disposition: attachment;filename='.$id.'.pdf');
        echo $data;
    }
}
