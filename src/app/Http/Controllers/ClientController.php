<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use Illuminate\Validation\Rule;
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

        return view('clients.index', compact('clients', 'q', 'sort', 'dir', 'per'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(StoreClientRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        Client::create($data);
        return redirect()->route('clients.index')->with('ok', 'Cliente criado.');
    }

    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, \App\Models\Client $client)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', Rule::unique('clients', 'email')->ignore($client->id)],
            'phone'     => ['nullable', 'string', 'max:50'],
            'is_active' => ['required', 'boolean'],
            'password'  => ['nullable', 'string', 'min:8'],
        ]);

        // senha opcional
        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $client->update($data);

        return redirect()
            ->route('clients.index')
            ->with('ok', 'Cliente atualizado!');
    }

    public function toggle(Client $client)
    {
        $client->update(['is_active' => ! $client->is_active]);
        return redirect()->route('clients.index')->with('ok', 'Status atualizado.');
    }

    // App/Http/Controllers/ClientController.php

    public function destroyMany(Request $request)
    {
        $ids = (array) $request->input('ids', []);

        if (! empty($ids)) {
            Client::whereIn('id', $ids)->delete();
        }

        return redirect()
            ->route('clients.index')  // <— garante /clients nos testes e no browser
            ->with('ok', 'Clientes excluídos com sucesso.');
    }



    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.index')->with('ok', 'Cliente deletado.');
    }
}
