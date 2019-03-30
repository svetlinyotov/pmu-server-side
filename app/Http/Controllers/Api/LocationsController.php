<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Location;

class LocationsController extends Controller
{
    public function all() {
        return Location::all();
    }

    public function show($id) {
        $info = Location::where("id", $id)->first();
        return view("locations.show",["info" => $info]);
    }
}