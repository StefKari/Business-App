<?php

namespace App\Modules\UserManagement\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Modules\UserManagement\Requests\StoreUserRequest;
use App\Modules\UserManagement\Requests\UpdateUserRequest;
use App\Modules\UserManagement\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of users
     */
    public function index(): View
    {
        $users = $this->userService
            ->getUsersWithRoles();

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create(): View
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = $this->userService->createUser($request->validated());

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user
     */
    public function show(User $user): View
    {
        $user->load(['role', 'creator', 'activityLogs']);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user): View
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->userService->updateUser($user->id, $request->validated());

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->userService->delete($user->id);

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Activate user
     */
    public function activate(User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $this->userService->activateUser($user->id);

        return back()->with('success', 'User activated successfully.');
    }

    /**
     * Deactivate user
     */
    public function deactivate(User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $this->userService->deactivateUser($user->id);

        return back()->with('success', 'User deactivated successfully.');
    }
}
