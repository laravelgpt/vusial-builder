<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PolicyBuilder extends BaseBuilder
{
    protected function getDefaultName(): string
    {
        return 'policy';
    }

    protected function getConfigPath(): string
    {
        return base_path('config/policy-builder.php');
    }

    protected function getStubsPath(): string
    {
        return base_path('stubs/policy-builder');
    }

    public function build(array $config): array
    {
        $this->output = [];

        // Create policy
        $this->buildPolicy($config);

        // Create test
        $this->buildTest($config);

        return $this->output;
    }

    protected function buildPolicy(array $config): void
    {
        $policyName = $this->getPolicyName($config['name']);
        $policyPath = app_path("Policies/{$policyName}.php");
        $policyStub = $this->getStub('policy');
        $policyContent = $this->replaceStub($policyStub, [
            'namespace' => $this->getNamespace($policyPath),
            'class' => $policyName,
            'model' => $config['name'],
            'modelNamespace' => $this->getNamespace(app_path("Models/{$config['name']}.php")),
            'methods' => $this->getPolicyMethods($config),
        ]);

        $this->createFile($policyPath, $policyContent);
    }

    protected function buildTest(array $config): void
    {
        $testName = $this->getTestName($config['name']);
        $testPath = base_path("tests/Feature/Policies/{$testName}.php");
        $testStub = $this->getStub('test');
        $testContent = $this->replaceStub($testStub, [
            'namespace' => $this->getNamespace($testPath),
            'class' => $testName,
            'policy' => $this->getPolicyName($config['name']),
            'policyNamespace' => $this->getNamespace(app_path("Policies/{$this->getPolicyName($config['name'])}.php")),
            'model' => $config['name'],
            'modelNamespace' => $this->getNamespace(app_path("Models/{$config['name']}.php")),
            'methods' => $this->getTestMethods($config),
        ]);

        $this->createFile($testPath, $testContent);
    }

    protected function getPolicyMethods(array $config): string
    {
        $methods = $config['methods'] ?? ['viewAny', 'view', 'create', 'update', 'delete'];
        $result = [];

        foreach ($methods as $method) {
            $result[] = $this->getMethodContent($method, $config);
        }

        return implode("\n\n    ", $result);
    }

    protected function getMethodContent(string $method, array $config): string
    {
        $methodMap = [
            'viewAny' => "public function viewAny(\$user)\n    {\n        return true;\n    }",
            'view' => "public function view(\$user, \${$config['name']})\n    {\n        return true;\n    }",
            'create' => "public function create(\$user)\n    {\n        return true;\n    }",
            'update' => "public function update(\$user, \${$config['name']})\n    {\n        return true;\n    }",
            'delete' => "public function delete(\$user, \${$config['name']})\n    {\n        return true;\n    }",
            'restore' => "public function restore(\$user, \${$config['name']})\n    {\n        return true;\n    }",
            'forceDelete' => "public function forceDelete(\$user, \${$config['name']})\n    {\n        return true;\n    }",
        ];

        return $methodMap[$method] ?? '';
    }

    protected function getTestMethods(array $config): string
    {
        $methods = $config['methods'] ?? ['viewAny', 'view', 'create', 'update', 'delete'];
        $result = [];

        foreach ($methods as $method) {
            $result[] = $this->getTestMethodContent($method, $config);
        }

        return implode("\n\n    ", $result);
    }

    protected function getTestMethodContent(string $method, array $config): string
    {
        $methodMap = [
            'viewAny' => "public function test_user_can_view_any()\n    {\n        \$user = User::factory()->create();\n\n        \$this->assertTrue(\$user->can('viewAny', {$config['name']}::class));\n    }",
            'view' => "public function test_user_can_view()\n    {\n        \$user = User::factory()->create();\n        \${$config['name']} = {$config['name']}::factory()->create();\n\n        \$this->assertTrue(\$user->can('view', \${$config['name']}));\n    }",
            'create' => "public function test_user_can_create()\n    {\n        \$user = User::factory()->create();\n\n        \$this->assertTrue(\$user->can('create', {$config['name']}::class));\n    }",
            'update' => "public function test_user_can_update()\n    {\n        \$user = User::factory()->create();\n        \${$config['name']} = {$config['name']}::factory()->create();\n\n        \$this->assertTrue(\$user->can('update', \${$config['name']}));\n    }",
            'delete' => "public function test_user_can_delete()\n    {\n        \$user = User::factory()->create();\n        \${$config['name']} = {$config['name']}::factory()->create();\n\n        \$this->assertTrue(\$user->can('delete', \${$config['name']}));\n    }",
            'restore' => "public function test_user_can_restore()\n    {\n        \$user = User::factory()->create();\n        \${$config['name']} = {$config['name']}::factory()->create();\n\n        \$this->assertTrue(\$user->can('restore', \${$config['name']}));\n    }",
            'forceDelete' => "public function test_user_can_force_delete()\n    {\n        \$user = User::factory()->create();\n        \${$config['name']} = {$config['name']}::factory()->create();\n\n        \$this->assertTrue(\$user->can('forceDelete', \${$config['name']}));\n    }",
        ];

        return $methodMap[$method] ?? '';
    }

    protected function getPolicyName(string $model): string
    {
        return $model . 'Policy';
    }

    protected function getTestName(string $model): string
    {
        return $model . 'PolicyTest';
    }
} 