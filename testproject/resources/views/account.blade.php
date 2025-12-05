@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-6">⚙️ Pengaturan Akun</h1>

        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold mb-4">Informasi Profil</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Nama --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
                    <p id="name" class="mt-1 p-2 border border-gray-300 rounded-md bg-gray-50">
                        {{ $user->name }}
                    </p>
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label>
                    <p id="email" class="mt-1 p-2 border border-gray-300 rounded-md bg-gray-50">
                        {{ $user->email }}
                    </p>
                </div>
            </div>

            <div class="mt-8 pt-4 border-t border-gray-200">
                <h2 class="text-2xl font-semibold mb-4">Keamanan</h2>
                <a href="{{ route('password.request') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Ubah Kata Sandi
                </a>
            </div>
        </div>
    @else
        {{-- Pesan jika ada kesalahan dan user tidak terautentikasi --}}
        <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            Anda harus login untuk melihat halaman ini.
        </div>
    @endif
</div>
@endsection