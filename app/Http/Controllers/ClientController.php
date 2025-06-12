<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    function findByMobilePhone(Request $request){
        $request->validate([
            'phone' => 'required|string',
        ]);
        Client::where('phone', $request->get('phone'))->firstOrFail();

        return response()->json(['status' => 'ok']);

    }
    function store(Request $request){
        $request->validate([
            'name' => 'required',
            'phone' => 'required|string',
            'note' =>'string'
        ]);

        Client::create($request->all());
    }
}
