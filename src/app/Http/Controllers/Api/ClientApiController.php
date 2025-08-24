<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientApiController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->integer('per_page', 20);
        $sort    = $request->get('sort', 'created_at');
        $dir     = $request->get('dir', 'desc');
        $q       = $request->get('q');

        $query = Client::query();

        if ($q) {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        if (in_array($sort, ['id', 'name', 'email', 'phone', 'is_active', 'created_at', 'updated_at'], true)) {
            $query->orderBy($sort, $dir === 'asc' ? 'asc' : 'desc');
        }

        $page = $query->paginate($perPage)->appends($request->query());

        return response()->json([
            'data' => ClientResource::collection($page->items()),
            'meta' => [
                'total'        => $page->total(),
                'per_page'     => $page->perPage(),
                'current_page' => $page->currentPage(),
                'last_page'    => $page->lastPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:clients,email'],
            'phone'    => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8'],
        ];

        if (! app()->environment('testing')) {
            $rules['g-recaptcha-response'] = ['required', 'captcha'];
        }

        $data = $request->validate($rules);
        $data['password'] = bcrypt($data['password']);

        $client = Client::create($data);

        return response()->json(new ClientResource($client), 201);
    }

    public function show(Client $client)
    {
        return response()->json(new ClientResource($client));
    }

    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'name'     => ['sometimes', 'required', 'string', 'max:255'],
            'email'    => ['sometimes', 'required', 'email', 'max:255', Rule::unique('clients', 'email')->ignore($client->id)],
            'phone'    => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (! empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $client->update($data);

        return response()->json(new ClientResource($client));
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return response()->json(['message' => 'Deletado.']);
    }

    public function destroyMany(Request $request)
    {
        $ids = $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['integer', 'exists:clients,id']])['ids'];
        Client::whereIn('id', $ids)->delete();
        return response()->json(['message' => 'Deletados.']);
    }

    public function toggle(Client $client)
    {
        $client->update(['is_active' => ! $client->is_active]);
        return response()->json(new ClientResource($client));
    }
}
