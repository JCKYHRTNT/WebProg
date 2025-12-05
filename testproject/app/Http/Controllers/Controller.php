<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class AccountController extends Controller 
{
    public function index()
    {
        // $user kini seharusnya terambil dengan benar
        $user = Auth::user(); 

        // Mengirimkan variabel $user ke view
        return view('account.index', ['user' => $user]);
    }
}