<?php

namespace App\Admin\Actions\CarianPersendirian;

use Encore\Admin\Actions\Action;
use Illuminate\Http\Request;

class SearchCarianPersendirian extends Action
{
    public $name = 'Search by ID Hakmilik';

    protected $selector = '.search';

    public function handle(Request $request)
    {
        // $request ...

        return $this->response()->success('Success message...')->refresh();
    }

    public function form()
    {
        $this->text('id_hakmilik', 'Enter ID Hakmilik');
    }

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-default search">Search Carian Persendirian</a>
HTML;
    }
}