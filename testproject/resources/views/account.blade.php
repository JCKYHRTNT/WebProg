@extends('layouts.master')

@section('title', 'Account – The Boys')

@php
    use Illuminate\Support\Str;

    /** @var \App\Models\User $user */
    $isAdmin      = session('role') === 'admin';
    $slug         = Str::slug($user->name);
    $profileImage = $user->profpic
        ? asset($user->profpic)
        : asset('images/default_avatar.png'); // create this or change path
@endphp

@section('content')

<style>
    .tb-btn-account {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        border-radius: 999px;
        padding: 0.6rem 0.9rem;
        font-size: 0.9rem;
        font-weight: 500;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: filter 0.15s ease, background-color 0.15s ease;
    }

    /* Edit = blue */
    .tb-btn-account-edit {
        background: var(--tb-blue);
        color: #ffffff;
    }
    .tb-btn-account-edit:hover {
        filter: brightness(1.12);
    }

    /* Delete = red */
    .tb-btn-account-delete {
        background: #dc2626;
        color: #ffffff;
    }
    .tb-btn-account-delete:hover {
        background: #ef4444;
    }

    /* Sign out = darker red */
    .tb-btn-account-logout {
        background: #b91c1c;
        color: #ffffff;
    }
    .tb-btn-account-logout:hover {
        background: #991b1b;
    }

    /* User/Admin switch = different blue/purple */
    .tb-btn-account-role {
        background: #1d4ed8; /* or any distinct color */
        color: #ffffff;
    }
    .tb-btn-account-role:hover {
        filter: brightness(1.12);
    }
</style>

<div class="tb-card p-4 p-md-5 mb-3">

    {{-- Header --}}
    <h1 style="font-size:1.8rem;font-weight:700;margin-bottom:1rem;">Account</h1>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success py-2">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger py-2">
            {{ session('error') }}
        </div>
    @endif

    {{-- Profile row --}}
    <div class="d-flex flex-wrap align-items-center" style="gap:1.5rem;">

        {{-- Left: profile image --}}
        <div style="flex:0 0 96px;">
            <div style="
                width:96px;
                height:96px;
                border-radius:999px;
                overflow:hidden;
                border:2px solid #e5e7eb;
                background:#f3f4f6;
            ">
                <img src="{{ $profileImage }}"
                     alt="Profile picture"
                     class="w-100 h-100"
                     style="object-fit:cover;">
            </div>
        </div>

        {{-- Right: name + email --}}
        <div class="flex-grow-1">
            <h2 style="font-size:1.3rem;font-weight:600;margin-bottom:0.25rem;">
                {{ $user->name }}
            </h2>
            <p style="margin:0;font-size:0.95rem;color:var(--tb-gray-text);">
                {{ $user->email }}
            </p>
        </div>
    </div>

    {{-- Divider --}}
    <hr class="mt-4 mb-3">

    {{-- EDIT ACCOUNT --}}
    <div class="mb-3">
        <div style="max-width:200px;">
            <button type="button"
                    class="tb-btn-account tb-btn-account-edit"
                    id="btnToggleEdit">
                Edit Account
            </button>
        </div>

        @php
            $updateRoute = $isAdminPage
                ? route('account.admin.update', ['username' => $slug])
                : route('account.update', ['username' => $slug]);
        @endphp

        <div id="editFormWrapper" class="mt-3 d-none" style="max-width:420px;">
            <form method="POST" action="{{ $updateRoute }}">
                @csrf

                <div class="mb-2">
                    <label for="name" style="font-size:0.85rem;font-weight:500;">Name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control"
                        value="{{ old('name', $user->name) }}"
                        required
                    >
                </div>

                <div class="mb-2">
                    <label for="email" style="font-size:0.85rem;font-weight:500;">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        value="{{ old('email', $user->email) }}"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" style="font-size:0.85rem;font-weight:500;">
                        Confirm Password
                    </label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="form-control"
                        required
                    >
                </div>

                <div style="max-width:200px;">
                    <button type="submit"
                            class="tb-btn-account tb-btn-account-edit">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- DELETE ACCOUNT --}}
    <div class="mb-3">
        <div style="max-width:200px;">
            <button type="button"
                    class="tb-btn-account tb-btn-account-delete"
                    id="btnToggleDelete">
                Delete Account
            </button>
        </div>

        @php
            $deleteRoute = $isAdminPage
                ? route('account.admin.delete', ['username' => $slug])
                : route('account.delete', ['username' => $slug]);
        @endphp

        <div id="deleteFormWrapper" class="mt-3 d-none" style="max-width:420px;">
            <form method="POST"
                  action="{{ $deleteRoute }}"
                  onsubmit="return confirm('Are you sure you want to delete your account?');">
                @csrf

                <div class="mb-2">
                    <label for="delete_password_confirmation" style="font-size:0.85rem;font-weight:500;">
                        Confirm Password
                    </label>
                    <input
                        type="password"
                        id="delete_password_confirmation"
                        name="password_confirmation"
                        class="form-control"
                        required
                    >
                </div>

                <button type="submit" class="tb-btn-danger">
                    Confirm Delete
                </button>
            </form>
        </div>
    </div>

    {{-- Sign out --}}
    <div class="mt-4" style="max-width:200px;">
        <a href="{{ route('logout') }}"
        class="tb-btn-account tb-btn-account-logout">
            Sign Out
        </a>
    </div>

    {{-- Admin / User switch button --}}
    @if($isAdmin)
        <div class="mt-4" style="max-width:200px;">
            @if($isAdminPage)
                <a href="{{ route('home.user', ['username' => $slug]) }}"
                class="tb-btn-account tb-btn-account-role">
                    User
                </a>
            @else
                <a href="{{ route('admin.user', ['username' => $slug]) }}"
                class="tb-btn-account tb-btn-account-role">
                    Admin
                </a>
            @endif
        </div>
    @endif

</div>

{{-- Simple JS toggles --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editBtn   = document.getElementById('btnToggleEdit');
        const editWrap  = document.getElementById('editFormWrapper');
        const delBtn    = document.getElementById('btnToggleDelete');
        const delWrap   = document.getElementById('deleteFormWrapper');

        if (editBtn && editWrap) {
            editBtn.addEventListener('click', function () {
                editWrap.classList.toggle('d-none');
            });
        }

        if (delBtn && delWrap) {
            delBtn.addEventListener('click', function () {
                delWrap.classList.toggle('d-none');
            });
        }
    });
</script>

@endsection