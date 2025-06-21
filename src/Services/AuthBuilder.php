<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use LaravelBuilder\VisualBuilder\Generators\AuthControllerGenerator;
use LaravelBuilder\VisualBuilder\Generators\AuthViewGenerator;
use LaravelBuilder\VisualBuilder\Generators\RolePermissionGenerator;

class AuthBuilder
{
    protected $authControllerGenerator;
    protected $authViewGenerator;
    protected $rolePermissionGenerator;

    public function __construct()
    {
        $this->authControllerGenerator = new AuthControllerGenerator();
        $this->authViewGenerator = new AuthViewGenerator();
        $this->rolePermissionGenerator = new RolePermissionGenerator();
    }

    public function build(array $config)
    {
        $this->validateConfig($config);

        // Generate auth components
        $this->generateAuthControllers($config);
        $this->generateAuthViews($config);
        $this->generateRolePermissions($config);

        return [
            'status' => 'success',
            'message' => 'Authentication system built successfully',
            'components' => $this->getGeneratedComponents($config),
        ];
    }

    protected function validateConfig(array $config)
    {
        if (!isset($config['guards']) || empty($config['guards'])) {
            throw new \InvalidArgumentException('At least one guard must be specified');
        }
    }

    protected function generateAuthControllers(array $config)
    {
        foreach ($config['guards'] as $guard => $guardConfig) {
            $this->authControllerGenerator->generate([
                'guard' => $guard,
                'config' => $guardConfig,
                'features' => $config['features'] ?? [],
            ]);
        }
    }

    protected function generateAuthViews(array $config)
    {
        foreach ($config['guards'] as $guard => $guardConfig) {
            $this->authViewGenerator->generate([
                'guard' => $guard,
                'config' => $guardConfig,
                'features' => $config['features'] ?? [],
                'theme' => $config['theme'] ?? 'default',
            ]);
        }
    }

    protected function generateRolePermissions(array $config)
    {
        if (isset($config['roles']) && !empty($config['roles'])) {
            $this->rolePermissionGenerator->generate([
                'roles' => $config['roles'],
                'permissions' => $config['permissions'] ?? [],
                'assignments' => $config['role_permissions'] ?? [],
            ]);
        }
    }

    public function generateSocialAuth(array $config)
    {
        $providers = $config['providers'] ?? [];
        $this->authControllerGenerator->generateSocialAuth($providers);
        $this->authViewGenerator->generateSocialAuthViews($providers);
    }

    public function generateTwoFactorAuth()
    {
        $this->authControllerGenerator->generateTwoFactorAuth();
        $this->authViewGenerator->generateTwoFactorAuthViews();
    }

    protected function getGeneratedComponents(array $config)
    {
        return [
            'controllers' => $this->authControllerGenerator->getGeneratedFiles(),
            'views' => $this->authViewGenerator->getGeneratedFiles(),
            'roles_permissions' => $this->rolePermissionGenerator->getGeneratedFiles(),
        ];
    }

    public function getAvailableGuards()
    {
        return array_keys(config('auth.guards', []));
    }

    public function getAvailableSocialProviders()
    {
        return [
            'google',
            'facebook',
            'twitter',
            'github',
            'linkedin',
        ];
    }

    public function getDefaultRoles()
    {
        return [
            'super-admin' => [
                'name' => 'Super Admin',
                'permissions' => ['*'],
            ],
            'admin' => [
                'name' => 'Admin',
                'permissions' => [
                    'manage_users',
                    'manage_roles',
                    'manage_permissions',
                ],
            ],
            'user' => [
                'name' => 'User',
                'permissions' => [
                    'view_profile',
                    'edit_profile',
                ],
            ],
        ];
    }

    public function getDefaultPermissions()
    {
        return [
            'manage_users' => 'Manage Users',
            'manage_roles' => 'Manage Roles',
            'manage_permissions' => 'Manage Permissions',
            'view_profile' => 'View Profile',
            'edit_profile' => 'Edit Profile',
        ];
    }
} 