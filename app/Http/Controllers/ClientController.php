<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    function findByMobilePhone(Request $request){
        $request->validate([
            'phone' => ['required', 'regex:/^\+380\d{9}$/'],
        ]);


        Client::where('phone', $request->get('phone'))->firstOrFail();

        return response()->json(['status' => 'ok']);

    }
    function store(Request $request){
        $request->validate([
            'name' => 'required',
            'phone' => ['required', 'regex:/^\+380\d{9}$/'],
            'note' =>'string'
        ]);

        Client::create($request->all());
    }

    public function index()
    {
        $clients = Client::all();
        return view('clients.index', compact('clients'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $client->update($request->only('name', 'phone'));

        return redirect()->route('clients.index')->with('success', 'Клиент обновлён.');
    }
    public function search(Request $request)
    {
        $query = $request->input('query');

        $clients = \App\Models\Client::where('phone', 'like', '%' . $query . '%')
            ->limit(5)
            ->get(['id', 'name', 'phone']);

        return response()->json($clients);
    }

}
