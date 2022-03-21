<?php

namespace App\Admin\Actions\CarianPersendirian;

use Encore\Admin\Actions\Action;
use Illuminate\Http\Request;
use CodeDredd\Soap\Facades\Soap;
use App\Models\CarianPersendirian;

class SearchCarianPersendirian extends Action
{
    public $name = 'Search by ID Hakmilik';

    protected $selector = '.search';

    public function handle(Request $request)
    {
        $user_id = $request->get('user_id');
        // load from etanah
        $url = 'http://etanah.melaka.gov.my/etanahwsa/CarianPersendirianService?wsdl';
        $soap = Soap::baseWsdl($url)->senaraiDokumen(['idPengguna' => $user_id]);                
        $result = json_decode($soap->body(),true);

        if(isset($result['return'])){
            foreach ($result['return'] as $value) {
                // Creating timestamp from given date
                $timestamp = strtotime($value['tarikh']);
                $tarikh = date("Y-m-d H:i:s", $timestamp);
                CarianPersendirian::updateOrCreate([
                    "id_portal_transaksi" => $value['idPortalTransaksi'],
                    "id_hakmilik" => $value['idHakmilik'],
                    "bil_paparan" => $value['bilPaparan'],
                    "tarikh" => $tarikh,
                    "user_id" => $user_id
                ]);
            };

            return $this->response()->success('Successfully add new transaction for this user.')->refresh();

        } else {
            return $this->response()->error('No such transaction records exist in eTanah for this user.')->refresh();
        }
    }

    public function form()
    {
        $this->text('user_id', 'Enter User ID');
    }

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-default search">Search Carian Persendirian by User ID</a>
        HTML;
    }
}