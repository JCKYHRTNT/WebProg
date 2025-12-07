@extends('layouts.master')

@section('title', 'Login – The Boys')

@section('content')

<style>
    .login-wrapper {
        display: flex;
        background: white;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        max-width: 900px;
        margin: 2rem auto;
    }

    .login-left {
        flex: 1;
        background: #f4f4f5;
        padding: 3rem 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-right {
        flex: 1;
        background: var(--tb-blue);
        padding: 3rem 2rem;
        color: white;
        position: relative;
    }

    .login-title {
        font-size: 1.6rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: #111827;
    }

    .login-card-logo {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 0.5rem;
        margin-bottom: 0.8rem;
        border: 3px solid var(--tb-yellow);
    }

    .login-input {
        width: 100%;
        border-radius: 0.75rem;
        border: none;
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .login-input:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(250,204,21,0.4);
    }

    .login-button {
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

    .login-button:hover {
        background: #fbbf24;
    }

    .login-footer {
        margin-top: 1rem;
        font-size: 0.85rem;
        color: #e5e7eb;
    }

    .login-image {
        width: 90%;
        max-width: 360px;
        opacity: 0.95;
    }

    .login-label {
        font-size: 0.85rem;
        font-weight: 500;
        margin-bottom: 0.25rem;
        color: #e5e7eb;
    }
</style>

<div class="login-wrapper">

    {{-- LEFT SIDE --}}
    <div class="login-left">
        <div>
            <h2 class="login-title">Login to your account</h2>

            <img src="{{ asset('images/login_side.png') }}"
                 alt="Login Illustration"
                 class="login-image">
        </div>
    </div>

    {{-- RIGHT SIDE --}}
    <div class="login-right">

        <div class="text-center mb-4">
            <img src="{{ asset('images/the_boys_logo.jpg') }}" class="login-card-logo">
            <h3 style="font-size:1.3rem;font-weight:600;margin:0;">THE BOYS</h3>
        </div>

        @if(session('error'))
            <div class="alert alert-danger py-2">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf

            <label class="login-label">Email</label>
            <input type="email" name="email" class="login-input" placeholder="Your Email" value="{{ old('email') }}" required>

            <label class="login-label">Password</label>
            <input type="password" name="password" class="login-input" placeholder="Your Password" required>

            <button type="submit" class="login-button">Login</button>
        </form>

        <div class="login-footer text-center">
            Don’t have an account? <a href="{{ route('register') }}" style="color:var(--tb-yellow);text-decoration:none;">Sign Up</a>
        </div>

    </div>

</div>

@endsection