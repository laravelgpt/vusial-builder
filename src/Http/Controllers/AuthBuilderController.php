<?php

namespace LaravelBuilder\VisualBuilder\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use LaravelBuilder\VisualBuilder\Services\AuthBuilder;

class AuthBuilderController extends Controller
{
    protected $authBuilder;

    public function __construct(AuthBuilder $authBuilder)
    {
        $this->authBuilder = $authBuilder;
    }

    public function index()
    {
        $authConfig = $this->authBuilder->getAuthConfig();
        return view('visual-builder::auth.index', compact('authConfig'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:web,api,both',
            'features' => 'required|array',
            'features.*' => 'required|in:login,register,password-reset,email-verification,2fa,social-auth',
            'guards' => 'required|array',
            'guards.*' => 'required|in:web,api',
            'providers' => 'required|array',
            'providers.*' => 'required|in:users,admins,staff',
            'roles' => 'array',
            'roles.*.name' => 'required|string|max:255',
            'roles.*.permissions' => 'array',
            'roles.*.permissions.*' => 'required|string|max:255',
        ]);

        $auth = $this->authBuilder->buildAuth($validated);

        return redirect()
            ->route('builder.auth.show', $auth['id'])
            ->with('success', 'Authentication system generated successfully.');
    }

    public function show($id)
    {
        $auth = $this->authBuilder->getAuth($id);
        return view('visual-builder::auth.show', compact('auth'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'type' => 'required|in:web,api,both',
            'features' => 'required|array',
            'features.*' => 'required|in:login,register,password-reset,email-verification,2fa,social-auth',
            'guards' => 'required|array',
            'guards.*' => 'required|in:web,api',
            'providers' => 'required|array',
            'providers.*' => 'required|in:users,admins,staff',
            'roles' => 'array',
            'roles.*.name' => 'required|string|max:255',
            'roles.*.permissions' => 'array',
            'roles.*.permissions.*' => 'required|string|max:255',
        ]);

        $auth = $this->authBuilder->updateAuth($id, $validated);

        return redirect()
            ->route('builder.auth.show', $auth['id'])
            ->with('success', 'Authentication system updated successfully.');
    }

    public function destroy($id)
    {
        $this->authBuilder->deleteAuth($id);

        return redirect()
            ->route('builder.auth.index')
            ->with('success', 'Authentication system deleted successfully.');
    }

    public function roles()
    {
        $roles = $this->authBuilder->getRoles();
        return view('visual-builder::auth.roles', compact('roles'));
    }

    public function createRole(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'array',
            'permissions.*' => 'required|string|max:255',
        ]);

        $role = $this->authBuilder->createRole($validated);

        return redirect()
            ->route('builder.auth.roles')
            ->with('success', 'Role created successfully.');
    }

    public function updateRole(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'array',
            'permissions.*' => 'required|string|max:255',
        ]);

        $role = $this->authBuilder->updateRole($id, $validated);

        return redirect()
            ->route('builder.auth.roles')
            ->with('success', 'Role updated successfully.');
    }

    public function deleteRole($id)
    {
        $this->authBuilder->deleteRole($id);

        return redirect()
            ->route('builder.auth.roles')
            ->with('success', 'Role deleted successfully.');
    }

    public function permissions()
    {
        $permissions = $this->authBuilder->getPermissions();
        return view('visual-builder::auth.permissions', compact('permissions'));
    }

    public function createPermission(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $permission = $this->authBuilder->createPermission($validated);

        return redirect()
            ->route('builder.auth.permissions')
            ->with('success', 'Permission created successfully.');
    }

    public function updatePermission(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $permission = $this->authBuilder->updatePermission($id, $validated);

        return redirect()
            ->route('builder.auth.permissions')
            ->with('success', 'Permission updated successfully.');
    }

    public function deletePermission($id)
    {
        $this->authBuilder->deletePermission($id);

        return redirect()
            ->route('builder.auth.permissions')
            ->with('success', 'Permission deleted successfully.');
    }

    public function socialAuth()
    {
        $providers = $this->authBuilder->getSocialProviders();
        return view('visual-builder::auth.social', compact('providers'));
    }

    public function configureSocialAuth(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|string|max:255',
            'client_id' => 'required|string|max:255',
            'client_secret' => 'required|string|max:255',
            'redirect' => 'required|url|max:255',
        ]);

        $provider = $this->authBuilder->configureSocialProvider($validated);

        return redirect()
            ->route('builder.auth.social')
            ->with('success', 'Social authentication provider configured successfully.');
    }

    public function twoFactor()
    {
        $config = $this->authBuilder->getTwoFactorConfig();
        return view('visual-builder::auth.2fa', compact('config'));
    }

    public function configureTwoFactor(Request $request)
    {
        $validated = $request->validate([
            'enabled' => 'required|boolean',
            'provider' => 'required_if:enabled,true|string|max:255',
            'channels' => 'required_if:enabled,true|array',
            'channels.*' => 'required|in:sms,email,authenticator',
        ]);

        $config = $this->authBuilder->configureTwoFactor($validated);

        return redirect()
            ->route('builder.auth.2fa')
            ->with('success', 'Two-factor authentication configured successfully.');
    }

    public function export()
    {
        $config = $this->authBuilder->exportConfig();
        $filename = 'auth-config-' . date('Y-m-d') . '.json';

        return response()->json($config)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Content-Type', 'application/json');
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'config' => 'required|file|mimes:json',
        ]);

        $config = json_decode(file_get_contents($validated['config']), true);
        $this->authBuilder->importConfig($config);

        return redirect()
            ->route('builder.auth.index')
            ->with('success', 'Authentication configuration imported successfully.');
    }
} 