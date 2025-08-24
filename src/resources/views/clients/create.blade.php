@extends('layouts.app')

@section('content')
<style>
    .form-wrap {
        max-width: 760px;
        margin: 0 auto 72px;
        padding-top: 64px;
    }

    .card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
    }

    .card-header {
        padding: 18px 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    .card-header h1 {
        margin: 0;
        font-size: 22px;
        font-weight: 700;
        color: #111827;
    }

    .card-body {
        padding: 20px;
    }

    .grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px 16px;
    }

    .full {
        grid-column: 1 / -1;
    }

    label {
        display: block;
        font-size: 12px;
        color: #6b7280;
        margin: 0 0 6px 2px;
    }

    input,
    select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #fff;
    }

    input:focus,
    select:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
    }

    .help {
        font-size: 12px;
        color: #6b7280;
        margin-top: 4px
    }

    .error {
        font-size: 12px;
        color: #b91c1c;
        margin-top: 6px
    }

    .actions {
        display: flex;
        gap: 10px;
        justify-content: flex-start;
        margin-top: 18px
    }

    .btn {
        display: inline-block;
        padding: 10px 14px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #fff;
        cursor: pointer;
        text-decoration: none
    }

    .btn-primary {
        background: #2563eb;
        border-color: #2563eb;
        color: #fff
    }
</style>

<div class="form-wrap">
    <div class="card">
        <div class="card-header">
            <h1>Novo Cliente</h1>
        </div>

        <div class="card-body">
            @if ($errors->any())
            <div class="error full" style="margin-bottom:8px">
                <ul style="margin:0 0 0 18px;padding:0;">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('clients.store') }}">
                @csrf

                <div class="grid">
                    <div class="full">
                        <label for="name">Nome</label>
                        <input id="name" name="name" value="{{ old('name') }}" required>
                    </div>

                    <div class="full">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                    </div>

                    <div>
                        <label for="phone">Telefone</label>
                        <input id="phone" name="phone" value="{{ old('phone') }}">
                    </div>

                    <div>
                        <label for="is_active">Status</label>
                        <select id="is_active" name="is_active">
                            <option value="1" {{ old('is_active','1')=='1' ? 'selected' : '' }}>Ativo</option>
                            <option value="0" {{ old('is_active')==='0' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>

                    <div class="full">
                        <label for="password">Senha</label>
                        <input id="password" type="password" name="password" required>
                        <div class="help">Mínimo de 8 caracteres.</div>
                    </div>

                    <div class="full">
                        {!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::display() !!}
                        @error('g-recaptcha-response') <div class="error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="actions">
                    <button class="btn btn-primary" type="submit">Salvar</button>
                    <a class="btn" href="{{ route('clients.index') }}">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

{!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::renderJs() !!}
@endsection