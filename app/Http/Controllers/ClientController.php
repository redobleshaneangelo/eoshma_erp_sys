<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class ClientController extends Controller
{
    // Display the list of clients
    public function index()
    {
        $query = Client::query();

        $clients = $query
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($client) {
                $client->hashed_id = Hashids::encode($client->id);
                return $client;
            });

        return response()->json([
            'ok' => true,
            'clients' => $clients->map(function($client) {
                return [
                    'id'             => $client->hashed_id,
                    'last_name'      => $client->last_name,
                    'first_name'     => $client->first_name,
                    'middle_initial' => $client->middle_initial,
                    'contact_person' => $client->contact_person,
                    'position'       => $client->position,
                    'phone'          => $client->phone,
                    'email'          => $client->email,
                    'status'         => $client->status,
                ];
            })
        ]);
    }

    /**
     * Display the specified client.
     * Laravel automatically finds the client by ID provided in the URL.
     */
    public function show(Client $client)
    {
        $client->image_url = $client->image_url 
            ? asset('storage/' . $client->image_url)
            : null;

        return response()->json($client);
    }

    // Store a new client
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'last_name'      => 'required|string|max:255',
            'first_name'     => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:5',
            'contact_person' => 'required|string|max:255',
            'position'       => 'required|string|max:255',
            'phone'          => 'required|string|min:7|max:15',
            'email'          => 'required|email|unique:clients,email',
            'office_address' => 'required|string|max:500',
            'notes'          => 'nullable|string',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('clients', 'public');
            $validatedData['image_url'] = $path;
        }

        $client = Client::create($validatedData);

        return response()->json([
            'message' => 'Client created successfully!',
            'client'  => $client
        ], 201);
    }
}