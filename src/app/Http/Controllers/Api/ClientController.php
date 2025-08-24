<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $allowed = ['id', 'name', 'email', 'phone', 'is_active', 'created_at'];
        $sort = in_array($request->sort, $allowed) ? $request->sort : 'created_at';
        $dir  = $request->dir === 'asc' ? 'asc' : 'desc';
        $per  = in_array((int)$request->per_page, [10, 20, 50, 100]) ? (int)$request->per_page : 20;
        $q    = trim((string)$request->q);

        $clients = Client::query()
            ->when($q, fn($qq) => $qq->where(
                fn($w) => $w
                    ->where('name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%")
                    ->orWhere('phone', 'like', "%$q%")
            ))
            ->orderBy($sort, $dir)
            ->paginate($per)
            ->withQueryString();

        return ClientResource::collection($clients);
    }

    public function store(StoreClientRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $client = Client::create($data);
        return (new ClientResource($client))->response()->setStatusCode(201);
    }

    public function show(Client $client)
    {
        return new ClientResource($client);
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        $data = $request->validated();
        if (!empty($data['password'])) $data['password'] = Hash::make($data['password']);
        else unset($data['password']);
        $client->update($data);
        return new ClientResource($client);
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return response()->noContent();
    }
}
