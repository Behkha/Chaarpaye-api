<?php

namespace App\Http\Controllers\v3;

use App\Http\Resources\CityResource;
use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $cities = City::with('province:id,name')
            ->active()
            ->orderBy('cities.name')
            ->get(['id', 'name', 'province_id', 'image']);

        return CityResource::collection($cities);
    }

}
