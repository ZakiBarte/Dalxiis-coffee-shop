<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class TeamController extends Controller
{
    // Admin manages managers and employees. A manager manages employees only.
    private function allowedRoles(): array
    {
        return auth()->user()->isAdmin() ? ['manager', 'employee'] : ['employee'];
    }

    private function authorizeMember(User $member): void
    {
        abort_unless(in_array($member->role, $this->allowedRoles()), 403, 'You cannot manage this account.');
    }

    private function rules(?User $member = null): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email' . ($member ? ',' . $member->id : ''),
            'role' => ['required', Rule::in($this->allowedRoles())],
            'password' => [$member ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'phone' => 'nullable|string|max:20',
            'job_title' => 'nullable|string|max:100',
            'hired_on' => 'nullable|date',
            'is_active' => 'required|boolean',
        ];
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $members = User::with('staffProfile')
            ->withCount('orders')
            ->whereIn('role', $this->allowedRoles())
            ->when($search !== '', fn ($q) => $q->where(function ($w) use ($search) {
                $w->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%")
                  ->orWhereHas('staffProfile', fn ($p) => $p->where('job_title', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%"));
            }))
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('team.index', compact('members'));
    }

    public function create()
    {
        return view('team.form', [
            'member' => new User(['role' => 'employee']),
            'roles' => $this->allowedRoles(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        $member = User::create(Arr::only($data, ['name', 'email', 'role', 'password']));
        $member->staffProfile()->create(Arr::only($data, ['phone', 'job_title', 'hired_on', 'is_active']));

        return redirect()->route('team.show', $member)->with('success', ucfirst($member->role) . ' account created.');
    }

    public function show(User $member)
    {
        $this->authorizeMember($member);
        $member->load('staffProfile');
        $recentOrders = $member->orders()->with('customer')->latest()->take(5)->get();

        return view('team.show', compact('member', 'recentOrders'));
    }

    public function edit(User $member)
    {
        $this->authorizeMember($member);
        $member->load('staffProfile');

        return view('team.form', ['member' => $member, 'roles' => $this->allowedRoles()]);
    }

    public function update(Request $request, User $member)
    {
        $this->authorizeMember($member);

        $data = $request->validate($this->rules($member));

        $fields = Arr::only($data, ['name', 'email', 'role']);
        if (! empty($data['password'])) {
            $fields['password'] = $data['password'];
        }

        $member->update($fields);
        $member->staffProfile()->updateOrCreate([], Arr::only($data, ['phone', 'job_title', 'hired_on', 'is_active']));

        return redirect()->route('team.show', $member)->with('success', 'Staff member updated successfully.');
    }
}
