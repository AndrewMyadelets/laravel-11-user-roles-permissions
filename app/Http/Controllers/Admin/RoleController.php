<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\RoleResource;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the roles.
     *
     * @param Request $request
     * @return Response
     * @throws AuthorizationException
     */
    public function index(Request $request): Response
    {
        $this->authorize('roles', Role::class);

        $filters = $request->all(['search', 'sort', 'order']);

        $query = Role::query()
            ->when($filters['search'], function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                });
            })
            ->orderBy($filters['sort'] ?? 'id', $filters['order'] ?? 'asc')
            ->with('permissions')
            ->paginate(10);

        $roles = RoleResource::collection($query);

        return Inertia::render('Admin/Roles/Index', [
            'title' => 'Roles',
            'roles' => $roles,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new role.
     *
     * @return Response
     * @throws AuthorizationException
     */
    public function create(): Response
    {
        $this->authorize('roles.create', Role::class);

        $permissions = Permission::all(['id', 'name', 'display_name', 'parent_id']);

        return Inertia::render('Admin/Roles/Create', [
            'title' => 'Create Role',
            'permissions' => $permissions,
        ]);
    }

    /**
     * Stores a newly created role in the database.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('roles.create', Role::class);

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'permissions' => ['array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role = Role::create($validatedData);
        $role->permissions()->sync($validatedData['permissions']);

        return redirect()->route('admin.roles.index', $request->query())->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified role.
     *
     * @param Role $role
     * @return Response
     * @throws AuthorizationException
     */
    public function show(Role $role): Response
    {
        $this->authorize('roles.view', Role::class);

        return Inertia::render('Admin/Roles/Show', [
            'title' => 'Role information',
            'role' => RoleResource::make($role->load('permissions')),
        ]);
    }

    /**
     * Show the form for editing the specified role.
     *
     * @param Role $role
     * @return Response
     * @throws AuthorizationException
     */
    public function edit(Role $role): Response
    {
        $this->authorize('roles.update', Role::class);

        $permissions = Permission::all(['id', 'name', 'display_name', 'parent_id']);

        return Inertia::render('Admin/Roles/Edit', [
            'title' => 'Edit Role',
            'role' => RoleResource::make($role->load('permissions')),
            'permissions' => $permissions,
        ]);
    }

    /**
     * Update the specified role in the database.
     *
     * @param Request $request
     * @param Role $role
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        $this->authorize('roles.update', Role::class);

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'permissions' => ['array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role->update($validatedData);

        $role->permissions()->sync($validatedData['permissions']);

        return redirect()->route('admin.roles.index', $request->query())->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role from the database.
     *
     * @param Role $role
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('roles.delete', Role::class);

        $role->delete();

        return redirect()->back()->with('success', 'Role deleted successfully.');
    }

    /**
     * Remove the specified roles from the database.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function massDestroy(Request $request): RedirectResponse
    {
        $this->authorize('roles.massDelete', Role::class);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:roles,id',
        ]);

        Role::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.roles.index', $request->query())->with('success', 'Roles deleted successfully.');
    }
}
