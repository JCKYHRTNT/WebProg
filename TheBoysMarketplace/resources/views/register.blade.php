@extends('layouts.master')

@section('title', 'Register – The Boys')

@section('content')

<style>
    .auth-wrapper {
        display: flex;
        background: white;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        max-width: 900px;
        margin: 2rem auto;
    }

    .auth-left {
        flex: 1;
        background: #f4f4f5;
        padding: 3rem 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .auth-right {
        flex: 1;
        background: var(--tb-blue);
        padding: 3rem 2rem;
        color: white;
        position: relative;
    }

    .auth-title {
        font-size: 1.6rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: #111827;
    }

    .auth-card-logo {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 0.5rem;
        margin-bottom: 0.8rem;
        border: 3px solid var(--tb-yellow);
    }

    .auth-input {
        width: 100%;
        border-radius: 0.75rem;
        border: none;
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
        margin-bottom: 0.9rem;
    }

    .auth-input:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(250,204,21,0.4);
    }

    .auth-button {
        width: 100%;
        padding: 0.7rem;
        border-radius: 0.75rem;
        border: none;
        background: var(--tb-yellow);
        font-weight: 600;
        color: #111827;
        margin-top: 0.5rem;
        transition: 0.2s ease;
    }

    .auth-button:hover {
        background: #fbbf24;
    }

    .auth-footer {
        margin-top: 1rem;
        font-size: 0.85rem;
        color: #e5e7eb;
    }

    .auth-image {
        width: 90%;
        max-width: 360px;
        opacity: 0.95;
    }

    .auth-label {
        font-size: 0.85rem;
        font-weight: 500;
        margin-bottom: 0.25rem;
        color: #e5e7eb;
    }
</style>

<div class="auth-wrapper">

    {{-- LEFT SIDE --}}
    <div class="auth-left">
        <div>
            <h2 class="auth-title">Create a new account</h2>

            <img src="{{ asset('images/login_side.png') }}"
                 alt="Register Illustration"
                 class="auth-image">
        </div>
    </div>

    {{-- RIGHT SIDE --}}
    <div class="auth-right">

        <div class="text-center mb-4">
            <img src="{{ asset('images/the_boys_logo.jpg') }}" class="auth-card-logo">
            <h3 style="font-size:1.3rem;font-weight:600;margin:0;">THE BOYS</h3>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2">
                <ul class="mb-0" style="padding-left:1.2rem;">
                    @foreach ($errors->all() as $error)
                        <li style="font-size:0.8rem;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.submit') }}">
            @csrf

            <label class="auth-label">Name</label>
            <input type="text" name="name" class="auth-input" placeholder="Your Name" required>

            <label class="auth-label">Email</label>
            <input type="email" name="email" class="auth-input" placeholder="Your Email" required>

            <label class="auth-label">Password</label>
            <input type="password" name="password" class="auth-input" placeholder="Your Password" required>

            <label class="auth-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="auth-input" placeholder="Confirm Password" required>

            <button type="submit" class="auth-button">Sign Up</button>
        </form>

        <div class="auth-footer text-center">
            Already have an account?
            <a href="{{ route('login') }}" style="color:var(--tb-yellow);text-decoration:none;">Login</a>
        </div>

    </div>

</div>

@endsection