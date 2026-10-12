<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $users = User::withCount('orders')
            ->when($search !== '', fn ($q) => $q->where(function ($w) use ($search) {
                $w->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            }))
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.form', ['user' => new User(['role' => 'employee'])]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => ['required', Rule::in(User::ROLES)],
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create($data);

        return redirect()->route('users.show', $user)->with('success', 'User account created.');
    }

    public function show(User $user)
    {
        $recentOrders = $user->orders()->with('customer')->latest()->take(5)->get();

        return view('users.show', compact('user', 'recentOrders'));
    }

    public function edit(User $user)
    {
        return view('users.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // A disabled form field is not submitted, so keep the admin's own role
        if ($user->id === auth()->id()) {
            $request->merge(['role' => $user->role]);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => ['required', Rule::in(User::ROLES)],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Admins cannot remove their own admin role by accident
        if ($user->id === auth()->id()) {
            $data['role'] = $user->role;
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.show', $user)->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete(); // orders they handled stay, as unclaimed

        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
}
