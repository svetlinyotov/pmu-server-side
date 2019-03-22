<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Location;

class LocationsController extends Controller
{
    public function all() {
        return Location::all();
    }
}