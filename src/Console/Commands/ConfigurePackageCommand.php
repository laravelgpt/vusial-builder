<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ConfigurePackageCommand extends Command
{
    protected $signature = 'package:configure {--preset= : Use a predefined configuration preset} {--validate : Validate configuration before saving}';
    protected $description = 'Configure the package settings';

    protected $presets = [
        'default' => [
            'theme' => 'light',
            'primary_color' => '#3B82F6',
            'secondary_color' => '#6B7280',
            'success_color' => '#10B981',
            'danger_color' => '#EF4444',
            'warning_color' => '#F59E0B',
            'info_color' => '#3B82F6',
        ],
        'dark' => [
            'theme' => 'dark',
            'primary_color' => '#60A5FA',
            'secondary_color' => '#9CA3AF',
            'success_color' => '#34D399',
            'danger_color' => '#F87171',
            'warning_color' => '#FBBF24',
            'info_color' => '#60A5FA',
        ],
        'minimal' => [
            'theme' => 'light',
            'primary_color' => '#000000',
            'secondary_color' => '#4B5563',
            'success_color' => '#059669',
            'danger_color' => '#DC2626',
            'warning_color' => '#D97706',
            'info_color' => '#2563EB',
        ],
    ];

    public function handle()
    {
        $this->info('Configuring package settings...');

        // Check for preset
        if ($preset = $this->option('preset')) {
            if (!isset($this->presets[$preset])) {
                $this->error("Preset '{$preset}' not found. Available presets: " . implode(', ', array_keys($this->presets)));
                return;
            }

            if ($this->confirm("Would you like to use the '{$preset}' preset?")) {
                $this->applyPreset($preset);
                return;
            }
        }

        // Configure theme
        $theme = $this->choice(
            'Select theme',
            ['light', 'dark'],
            config('package.styles.theme', 'light')
        );

        // Configure colors with validation
        $colors = $this->configureColors();

        // Configure components
        $this->info('Configuring components...');
        $components = $this->configureComponents();

        // Validate configuration if requested
        if ($this->option('validate')) {
            if (!$this->validateConfiguration($theme, $colors, $components)) {
                return;
            }
        }

        // Update configuration file
        $this->updateConfigurationFile([
            'styles' => array_merge(['theme' => $theme], $colors),
            'components' => $components,
        ]);

        $this->info('Package configured successfully!');
    }

    protected function configureColors(): array
    {
        $colors = [];
        $colorFields = [
            'primary_color' => ['label' => 'Primary color', 'default' => '#3B82F6'],
            'secondary_color' => ['label' => 'Secondary color', 'default' => '#6B7280'],
            'success_color' => ['label' => 'Success color', 'default' => '#10B981'],
            'danger_color' => ['label' => 'Danger color', 'default' => '#EF4444'],
            'warning_color' => ['label' => 'Warning color', 'default' => '#F59E0B'],
            'info_color' => ['label' => 'Info color', 'default' => '#3B82F6'],
        ];

        foreach ($colorFields as $field => $config) {
            do {
                $color = $this->ask(
                    "Enter {$config['label']} (hex)",
                    config("package.styles.{$field}", $config['default'])
                );

                if (!$this->isValidHexColor($color)) {
                    $this->error('Invalid hex color format. Please use format #RRGGBB or #RRGGBBAA');
                }
            } while (!$this->isValidHexColor($color));

            $colors[$field] = $color;
        }

        return $colors;
    }

    protected function configureComponents(): array
    {
        $components = [];
        $componentConfig = config('package.components');

        foreach ($componentConfig as $name => $settings) {
            if ($this->confirm("Would you like to configure the {$name} component?")) {
                $components[$name] = $this->configureComponent($name, $settings);
            } else {
                $components[$name] = $settings;
            }
        }

        return $components;
    }

    protected function validateConfiguration(string $theme, array $colors, array $components): bool
    {
        $validator = Validator::make([
            'theme' => $theme,
            'colors' => $colors,
            'components' => $components,
        ], [
            'theme' => 'required|in:light,dark',
            'colors.*' => 'required|regex:/^#[0-9A-Fa-f]{6,8}$/',
            'components.*.enabled' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return false;
        }

        return true;
    }

    protected function isValidHexColor(string $color): bool
    {
        return preg_match('/^#[0-9A-Fa-f]{6,8}$/', $color) === 1;
    }

    protected function applyPreset(string $preset): void
    {
        $this->info("Applying '{$preset}' preset...");

        $this->updateConfigurationFile([
            'styles' => $this->presets[$preset],
        ]);

        $this->info("Preset '{$preset}' applied successfully!");
    }

    protected function configureComponent(string $name, array $settings)
    {
        $config = [];

        foreach ($settings as $key => $value) {
            if (is_bool($value)) {
                $config[$key] = $this->confirm("Enable {$key} for {$name}?", $value);
            } elseif (is_numeric($value)) {
                $config[$key] = $this->ask("Enter {$key} for {$name}", $value);
            } else {
                $config[$key] = $this->ask("Enter {$key} for {$name}", $value);
            }
        }

        $this->updateConfigurationFile([
            'components' => [
                $name => $config,
            ],
        ]);
    }

    protected function updateConfigurationFile(array $config)
    {
        $configFile = config_path('package.php');
        $configContent = File::get($configFile);

        foreach ($config as $key => $value) {
            $configContent = $this->updateConfigValue($configContent, $key, $value);
        }

        File::put($configFile, $configContent);
    }

    protected function updateConfigValue(string $content, string $key, $value)
    {
        if (is_array($value)) {
            foreach ($value as $subKey => $subValue) {
                $content = $this->updateConfigValue($content, "{$key}.{$subKey}", $subValue);
            }
            return $content;
        }

        $pattern = "/'{$key}'\s*=>\s*.*,/";
        $replacement = "'{$key}' => " . $this->formatValue($value) . ",";

        if (preg_match($pattern, $content)) {
            return preg_replace($pattern, $replacement, $content);
        }

        return $content;
    }

    protected function formatValue($value)
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_string($value)) {
            return "'{$value}'";
        }

        return $value;
    }
} 