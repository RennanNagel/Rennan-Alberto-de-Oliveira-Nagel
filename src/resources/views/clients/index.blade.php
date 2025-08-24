@extends('layouts.app')

@section('content')

<style>
    .clients-wrap {
        max-width: 1280px;
        margin: 0 auto;
        padding-bottom: 64px;
        padding-top: 16px;
    }

    .page-title {
        font-size: 32px;
        line-height: 1.2;
        font-weight: 700;
        margin: 8px 0 16px;
    }

    .clients-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin: 8px 0 12px;
    }

    .clients-toolbar .toolbar-left {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn {
        display: inline-block;
        padding: 8px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #fff;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-primary {
        background: #2563eb;
        border-color: #2563eb;
        color: #fff;
    }

    .clients-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        table-layout: auto;
    }

    .clients-table th,
    .clients-table td {
        padding: 10px 12px;
        vertical-align: middle;
        border-right: 1px solid #e5e7eb;
        white-space: nowrap;
    }

    .clients-table th:last-child,
    .clients-table td:last-child {
        border-right: none;
    }

    .clients-table thead th {
        background: #fafafa;
        border-bottom: 2px solid #e5e7eb;
        font-weight: 600;
        text-align: center;
    }

    .clients-table tbody tr:nth-child(even) {
        background: #fafafa;
    }

    .clients-table tbody tr:hover {
        background: #f0f7ff;
    }

    .clients-table td:nth-child(1),
    .clients-table td:nth-child(2),
    .clients-table td:nth-child(6),
    .clients-table td:nth-child(7) {
        text-align: center;
    }

    .clients-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin: 12px 0 0;
    }

    .clients-footer .footer-left {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .clients-footer .footer-right {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .clients-footer nav[role="navigation"] {
        margin: 0;
    }

    .clients-footer nav[role="navigation"]>div>div:first-child {
        display: none !important;
    }
</style>


@if(session('ok')) <div style="color:green">{{ session('ok') }}</div> @endif
@if(session('status'))<div style="color:green">{{ session('status') }}</div> @endif
@if ($errors->any())
<div style="color:crimson">
    <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="clients-wrap">

    <h1 class="page-title">Clientes</h1>
    <div class="clients-toolbar">
        <form method="GET" action="{{ route('clients.index') }}" class="toolbar-left">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar..." />
            <select name="sort">
                <option value="id" @selected(request('sort')==='id' )>ID</option>
                <option value="name" @selected(request('sort')==='name' )>Nome</option>
                <option value="email" @selected(request('sort')==='email' )>Email</option>
                <option value="phone" @selected(request('sort')==='phone' )>Telefone</option>
                <option value="is_active" @selected(request('sort')==='is_active' )>Ativos/Inativos</option>
            </select>

            <select name="dir">
                <option value="desc" @selected(request('dir','desc')==='desc' )>Descrescente</option>
                <option value="asc" @selected(request('dir')==='asc' )>Crescente</option>
            </select>

            <select name="per_page">
                @foreach([10,20,50,100] as $n)
                <option value="{{ $n }}" @selected((int)request('per_page',20)===$n)>{{ $n }}</option>
                @endforeach
            </select>

            <button class="btn">Filtrar</button>
        </form>

        <a href="{{ route('clients.create') }}" class="btn btn-primary">+ Novo usuário</a>
    </div>
    <table class="clients-table">
        <thead>
            <tr>
                <th><input id="checkAll" type="checkbox"></th>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Telefone</th>
                <th>Ativo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clients as $c)
            <tr>

                <td><input class="rowcheck" type="checkbox" value="{{ $c->id }}"></td>
                <td>{{ $c->id }}</td>
                <td><a href="{{ route('clients.show',$c) }}">{{ $c->name }}</a></td>
                <td>{{ $c->email }}</td>
                <td>{{ $c->phone }}</td>
                <td>{{ $c->is_active ? 'Sim' : 'Não' }}</td>
                <td style="white-space:nowrap">
                    <a href="{{ route('clients.edit',$c) }}">Editar</a>
                    |
                    <form method="post" action="{{ route('clients.toggle',$c) }}" style="display:inline">
                        @csrf @method('PATCH')
                        <button type="submit">{{ $c->is_active ? 'Desativar' : 'Ativar' }}</button>
                    </form>
                    |
                    <form method="post" action="{{ route('clients.destroy',$c) }}" style="display:inline" onsubmit="return confirm('Deletar este cliente?')">
                        @csrf @method('DELETE')
                        <button type="submit">Excluir</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">Nenhum registro.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="clients-footer">
        <div class="footer-left">
            <form id="bulkDeleteForm" method="POST" action="{{ route('clients.destroyMany') }}">
                @csrf
                @method('DELETE')
                <div id="bulkIds"></div> <!-- os <input type="hidden" name="ids[]"> serão injetados aqui -->
                <button id="bulkDeleteBtn" class="btn" type="submit" disabled
                    onclick="return confirm('Excluir selecionados?')">
                    Excluir selecionados
                </button>
            </form>

            <small>
                Mostrando do {{ $clients->firstItem() }} até {{ $clients->lastItem() }}
                de {{ $clients->total() }} resultados
            </small>
        </div>

        <div class="footer-right">
            {{ $clients->onEachSide(1)->links() }}
        </div>
    </div>

</div>

<script>
    (function() {
        const checkAll = document.getElementById('checkAll');
        const rowChecks = Array.from(document.querySelectorAll('.rowcheck'));
        const bulkForm = document.getElementById('bulkDeleteForm');
        const bulkIds = document.getElementById('bulkIds');
        const bulkBtn = document.getElementById('bulkDeleteBtn');

        if (!bulkForm || !bulkIds || !bulkBtn) return;

        function refreshBulkState() {
            const any = rowChecks.some(c => c.checked);
            bulkBtn.disabled = !any;
        }

        checkAll?.addEventListener('change', () => {
            rowChecks.forEach(c => c.checked = checkAll.checked);
            refreshBulkState();
        });

        rowChecks.forEach(c => c.addEventListener('change', refreshBulkState));

        bulkForm.addEventListener('submit', function(e) {
            bulkIds.innerHTML = '';
            const checked = rowChecks.filter(c => c.checked);

            if (!checked.length) {
                e.preventDefault();
                return;
            }

            checked.forEach(c => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'ids[]';
                inp.value = c.value;
                bulkIds.appendChild(inp);
            });
        });

        refreshBulkState();
    })();
</script>

@endsection