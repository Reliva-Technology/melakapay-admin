<?php

namespace App\Admin\Controllers;

use App\Models\CarianPersendirian;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Carbon\Carbon as Carbon;
use CodeDredd\Soap\Facades\Soap;
use App\Admin\Actions\CarianPersendirian\SearchCarianPersendirian;

class CarianPersendirianController extends AdminController
{
    protected $title = 'Carian Persendirian';

    protected function grid()
    {
        $grid = new Grid(new CarianPersendirian());
        $grid->model()->orderBy('id_portal_transaksi', 'desc');

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
            $filter->like('tarikh', __('Tarikh'));
        
        });

        $grid->actions(function ($actions) {
            $actions->disableDelete()->disableEdit();
        });

        $grid->disableCreateButton();

        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new SearchCarianPersendirian());
        });

        return $grid;
    }

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
            return '<a href="'.url('/admin/carian-persendirian/print-carian-persendirian').'/'.$id_portal_transaksi.'" class="btn btn-sm btn-success" title="Print Carian Persendirian" target="_blank">Print Carian Persendirian</a>';
        });

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableDelete();
            });

        return $show;
    }

    public function print($id)
    {
        $url = 'http://etanah.melaka.gov.my/etanahwsa/CarianPersendirianService?wsdl';
        $client = Soap::baseWsdl($url)->muatTurunDokumen(['idPortalTrans' => $id]);
        $result = json_decode($client->body(),true);

        if($result){
            // update cache bil_paparan count by +1
            CarianPersendirian::where('id_portal_transaksi', $id)->increment('bil_paparan');

            $data = $result['return']['bytes'];
            header('Content-Type: application/pdf');
            header('Content-disposition: attachment;filename='.$id.'.pdf');
            echo $data;
        } else {
            return 'Failed to retrieve Carian Persendirian from eTanah using this transaction ID';
        }
    }
}
