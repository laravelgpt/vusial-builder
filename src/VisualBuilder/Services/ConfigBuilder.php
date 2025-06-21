<?php

namespace LaravelBuilder\VisualBuilder\Services;

use LaravelBuilder\VisualBuilder\Support\LaravelVersion;

class ConfigBuilder extends BaseBuilder
{
    /**
     * Build the configuration files.
     */
    public function build(string $model): array
    {
        $output = [];

        // Build base configuration
        $output['config'] = $this->buildConfig($model);

        // Build version-specific configurations
        if (LaravelVersion::isLaravel12()) {
            $output['env_config'] = $this->buildEnvironmentConfig($model);
            $output['middleware_config'] = $this->buildMiddlewareConfig($model);
        }

        // Build feature configurations
        if (LaravelVersion::hasPennant()) {
            $output['pennant_config'] = $this->buildPennantConfig($model);
        }

        if (LaravelVersion::hasReverb()) {
            $output['reverb_config'] = $this->buildReverbConfig($model);
        }

        if (LaravelVersion::hasVolt()) {
            $output['volt_config'] = $this->buildVoltConfig($model);
        }

        return $output;
    }

    /**
     * Build the base configuration file.
     */
    protected function buildConfig(string $model): array
    {
        $configPath = $this->getConfigPath('visual-builder.php');
        $stubPath = $this->getStubPath('config-builder/config.stub');

        return [
            'path' => $configPath,
            'content' => $this->buildFromStub($stubPath, [
                'model' => $model,
                'php_version' => LaravelVersion::getMinimumPhpVersion(),
                'has_pennant' => LaravelVersion::hasPennant(),
                'has_reverb' => LaravelVersion::hasReverb(),
                'has_volt' => LaravelVersion::hasVolt(),
                'has_prompts' => LaravelVersion::hasPrompts(),
            ]),
        ];
    }

    /**
     * Build environment-specific configuration.
     */
    protected function buildEnvironmentConfig(string $model): array
    {
        $environments = ['local', 'staging', 'production'];
        $output = [];

        foreach ($environments as $env) {
            $configPath = $this->getConfigPath("visual-builder.{$env}.php");
            $stubPath = $this->getStubPath('config-builder/env_config.stub');

            $output[$env] = [
                'path' => $configPath,
                'content' => $this->buildFromStub($stubPath, [
                    'model' => $model,
                    'environment' => $env,
                ]),
            ];
        }

        return $output;
    }

    /**
     * Build middleware configuration.
     */
    protected function buildMiddlewareConfig(string $model): array
    {
        $configPath = $this->getConfigPath('middleware.php');
        $stubPath = $this->getStubPath('config-builder/middleware_config.stub');

        return [
            'path' => $configPath,
            'content' => $this->buildFromStub($stubPath, [
                'model' => $model,
            ]),
        ];
    }

    /**
     * Build Pennant feature configuration.
     */
    protected function buildPennantConfig(string $model): array
    {
        $configPath = $this->getConfigPath('pennant.php');
        $stubPath = $this->getStubPath('config-builder/pennant_config.stub');

        return [
            'path' => $configPath,
            'content' => $this->buildFromStub($stubPath, [
                'model' => $model,
            ]),
        ];
    }

    /**
     * Build Reverb configuration.
     */
    protected function buildReverbConfig(string $model): array
    {
        $configPath = $this->getConfigPath('reverb.php');
        $stubPath = $this->getStubPath('config-builder/reverb_config.stub');

        return [
            'path' => $configPath,
            'content' => $this->buildFromStub($stubPath, [
                'model' => $model,
            ]),
        ];
    }

    /**
     * Build Volt configuration.
     */
    protected function buildVoltConfig(string $model): array
    {
        $configPath = $this->getConfigPath('volt.php');
        $stubPath = $this->getStubPath('config-builder/volt_config.stub');

        return [
            'path' => $configPath,
            'content' => $this->buildFromStub($stubPath, [
                'model' => $model,
            ]),
        ];
    }

    /**
     * Get the configuration path.
     */
    protected function getConfigPath(string $filename): string
    {
        return config_path($filename);
    }

    /**
     * Get the stub path.
     */
    protected function getStubPath(string $path): string
    {
        return __DIR__ . '/../../stubs/' . $path;
    }
} 