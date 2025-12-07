<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    /**
     * User account page: /u/{username}/account
     */
    public function userAccount(Request $request, string $username)
    {
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $user = User::findOrFail(session('user_id'));
        $expectedSlug = Str::slug($user->name);

        if ($username !== $expectedSlug) {
            return redirect()->route('account', ['username' => $expectedSlug]);
        }

        return view('account', [
            'user'          => $user,
            'isAdminPage'   => false,
        ]);
    }

    /**
     * Admin account page: /a/{username}/account
     */
    public function adminAccount(Request $request, string $username)
    {
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $user = User::findOrFail(session('user_id'));
        $expectedSlug = Str::slug($user->name);

        if ($username !== $expectedSlug) {
            return redirect()->route('account.admin', ['username' => $expectedSlug]);
        }

        return view('account', [
            'user'          => $user,
            'isAdminPage'   => true,
        ]);
    }

    /**
     * Update account (name + email, password confirmation).
     */
    public function update(Request $request, string $username)
    {
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $user = User::findOrFail(session('user_id'));
        $expectedSlug = Str::slug($user->name);

        if ($username !== $expectedSlug) {
            $baseRoute = $request->routeIs('account.admin.update') ? 'account.admin' : 'account';

            return redirect()->route($baseRoute, ['username' => $expectedSlug]);
        }

        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password_confirmation' => ['required', 'string'],
        ]);

        if (!Hash::check($data['password_confirmation'], $user->password)) {
            return back()->with('error', 'Incorrect password.');
        }

        $user->name  = $data['name'];
        $user->email = $data['email'];
        $user->save();

        // Update session name
        session(['name' => $user->name]);

        $newSlug   = Str::slug($user->name);
        $baseRoute = $request->routeIs('account.admin.update') ? 'account.admin' : 'account';

        return redirect()
            ->route($baseRoute, ['username' => $newSlug])
            ->with('success', 'Account updated.');
    }

    /**
     * Delete account (password confirmation).
     */
    public function destroy(Request $request, string $username)
    {
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $user = User::findOrFail(session('user_id'));
        $expectedSlug = Str::slug($user->name);

        $data = $request->validate([
            'password_confirmation' => ['required', 'string'],
        ]);

        if (!Hash::check($data['password_confirmation'], $user->password)) {
            return back()->with('error', 'Incorrect password.');
        }

        // Delete user + logout
        $user->delete();
        session()->flush();

        return redirect('/')->with('success', 'Account deleted.');
    }
}