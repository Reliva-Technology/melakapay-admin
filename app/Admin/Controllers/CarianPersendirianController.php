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
    protected $title = 'Carian Persendirian';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CarianPersendirian());
        $grid->model()->orderBy('id', 'desc');

        $grid->column('id', __('ID'));
        $grid->column('bil_paparan', __('Bil. Paparan'));
        $grid->column('id_hakmilik', __('ID Hakmilik'));
        $grid->column('id_portal_transaksi', __('ID Portal'));
        $grid->column('tarikh', __('Tarikh'));
        $grid->column('user_id', __('User ID'));
        $grid->column('user.name', __('User'));

        $grid->filter(function($filter){

            // Remove the default id filter
            $filter->disableIdFilter();
        
            // Add a column filter
            $filter->like('id_hakmilik', __('ID Hakmilik'));
            $filter->like('id_portal_transaksi', __('ID Portal'));
            $filter->like('user_id', __('User ID'));
        
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

        $form->number('id', __('ID'));

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

    public function carian($user_id)
    {
        // load from etanah
        $url = 'http://etanah.melaka.gov.my/etanahwsa/CarianPersendirianService?wsdl';
        $soap = Soap::baseWsdl($url)->senaraiDokumen(['idPengguna' => $user_id]);                
        $result = json_decode($soap->body(),true);

        if(isset($result['return'])){
            foreach ($result['return'] as $value) {
                CarianPersendirian::updateOrCreate([
                    "id_portal_transaksi" => $value['idPortalTransaksi'],
                    "id_hakmilik" => $value['idHakmilik'],
                    "bil_paparan" => $value['bilPaparan'],
                    "tarikh" => $value['tarikh'],
                    "user_id" => $user_id
                ]);
            };

        } else {
            return back()->withErrors('Error', 'No such transaction records exist in eTanah for this user.');
        }
    }
}
