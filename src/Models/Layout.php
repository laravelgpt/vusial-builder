<?php

namespace LaravelBuilder\VisualBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Layout extends Model
{
    protected $fillable = [
        'name',
        'description',
        'template',
        'regions',
        'styles',
        'scripts',
        'is_active',
        'is_system',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'regions' => 'array',
        'styles' => 'array',
        'scripts' => 'array',
        'is_active' => 'boolean',
        'is_system' => 'boolean',
    ];

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'updated_by');
    }

    public function getRegion(string $name, $default = null)
    {
        return collect($this->regions)->firstWhere('name', $name) ?? $default;
    }

    public function addRegion(string $name, string $description): void
    {
        $regions = $this->regions ?? [];
        $regions[] = [
            'name' => $name,
            'description' => $description,
        ];
        $this->regions = $regions;
        $this->save();
    }

    public function removeRegion(string $name): void
    {
        $regions = collect($this->regions)
            ->filter(fn ($region) => $region['name'] !== $name)
            ->values()
            ->all();
        $this->regions = $regions;
        $this->save();
    }

    public function getStyle(string $key, $default = null)
    {
        return data_get($this->styles, $key, $default);
    }

    public function setStyle(string $key, $value): void
    {
        $styles = $this->styles ?? [];
        data_set($styles, $key, $value);
        $this->styles = $styles;
        $this->save();
    }

    public function getScript(string $key, $default = null)
    {
        return data_get($this->scripts, $key, $default);
    }

    public function setScript(string $key, $value): void
    {
        $scripts = $this->scripts ?? [];
        data_set($scripts, $key, $value);
        $this->scripts = $scripts;
        $this->save();
    }

    public function render(): string
    {
        $template = $this->template;
        $regions = $this->regions;

        foreach ($regions as $region) {
            $placeholder = "{{ region:{$region['name']} }}";
            $content = $this->getRegionContent($region['name']);
            $template = str_replace($placeholder, $content, $template);
        }

        return $template;
    }

    protected function getRegionContent(string $regionName): string
    {
        $page = $this->pages->first();
        if (!$page) {
            return '';
        }

        $components = $page->components()
            ->wherePivot('region', $regionName)
            ->orderBy('pivot_order')
            ->get();

        return $components->map(function ($component) {
            return $component->render();
        })->join('');
    }

    public function toArray(): array
    {
        $array = parent::toArray();
        $array['pages'] = $this->pages;
        $array['creator'] = $this->creator;
        $array['updater'] = $this->updater;
        return $array;
    }
} 