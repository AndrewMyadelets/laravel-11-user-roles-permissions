<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleName;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Resources\Admin\RoleResource;
use App\Http\Resources\Admin\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the users.
     *
     * @param Request $request
     * @return Response
     * @throws AuthorizationException
     */
    public function index(Request $request): Response
    {
        $this->authorize('users', User::class);

        $filters = $request->all(['search', 'sort', 'order']);

        $query = User::query()
            ->when($filters['search'], function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->orderBy($filters['sort'] ?? 'id', $filters['order'] ?? 'asc')
            ->with('roles')
            ->paginate(10);

        $users = UserResource::collection($query);

        return Inertia::render('Admin/Users/Index', [
            'title' => 'Users',
            'users' => $users,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new user.
     *
     * @return Response
     * @throws AuthorizationException
     */
    public function create(): Response
    {
        $this->authorize('users.create', User::class);

        $roles = RoleResource::collection(Role::all());

        return Inertia::render('Admin/Users/Create', [
            'title' => 'Create User',
            'roles' => $roles,
        ]);
    }

    /**
     * Stores a newly created user in the database.
     *
     * @param StoreUserRequest $request
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('users.create', User::class);

        $validatedData = $request->validated();

        $roles = filled($validatedData['roles']) ?
            $validatedData['roles'] :
            Role::where('name', RoleName::CUSTOMER->value)->first();

        $user = User::create($validatedData);
        $user->roles()->sync($roles);

        return redirect()->route('admin.users.index', $request->query())->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     *
     * @param User $user
     * @return Response
     * @throws AuthorizationException
     */
    public function show(User $user): Response
    {
        $this->authorize('users.view', User::class);

        $user = UserResource::make($user->load(['roles']));

        return Inertia::render('Admin/Users/Show', [
            'title' => 'User information',
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param User $user
     * @return Response
     * @throws AuthorizationException
     */
    public function edit(User $user): Response
    {
        $this->authorize('users.update', User::class);

        $roles = RoleResource::collection(Role::all());

        return Inertia::render('Admin/Users/Edit', [
            'title' => 'Edit User',
            'user' => UserResource::make($user->load('roles')),
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified user in the database.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('users.update', User::class);

        $validatedData = $request->validated();

        $dataToUpdate = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
        ];

        if (filled($validatedData['password'])) {
            $dataToUpdate['password'] = Hash::make($validatedData['password']);
        }

        $user->update($dataToUpdate);

        $roles = (filled($validatedData['roles']))
            ? $validatedData['roles']
            : Role::where('name', RoleName::CUSTOMER->value)->first();
        $user->roles()->sync($roles);

        return redirect()->route('admin.users.index', $request->query())->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from the database.
     *
     * @param User $user
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('users.delete', User::class);

        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    /**
     * Remove the specified users from the database.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function massDestroy(Request $request): RedirectResponse
    {
        $this->authorize('users.massDelete', User::class);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:users,id',
        ]);

        User::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.users.index', $request->query())->with('success', 'Users deleted successfully.');
    }
}
