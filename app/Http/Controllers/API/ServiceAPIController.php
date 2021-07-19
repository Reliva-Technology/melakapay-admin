<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class ServiceAPIController extends Controller
{
    public function service(Request $request)
    {
        $agency_id = $request->get('q');
        return Service::where('agency_id', $agency_id)->get(['id', DB::raw('category as text')]);
    }
}
