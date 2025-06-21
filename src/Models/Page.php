<?php

namespace LaravelBuilder\VisualBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Page extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'layout_id',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_active',
        'is_published',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'content' => 'array',
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function layout(): BelongsTo
    {
        return $this->belongsTo(Layout::class);
    }

    public function components(): BelongsToMany
    {
        return $this->belongsToMany(Component::class)
            ->withPivot(['order', 'properties', 'styles', 'scripts'])
            ->withTimestamps();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'updated_by');
    }

    public function getComponentProperty(int $componentId, string $key, $default = null)
    {
        $component = $this->components->firstWhere('id', $componentId);
        if (!$component) {
            return $default;
        }

        return data_get($component->pivot->properties, $key, $default);
    }

    public function setComponentProperty(int $componentId, string $key, $value): void
    {
        $component = $this->components->firstWhere('id', $componentId);
        if (!$component) {
            return;
        }

        $properties = $component->pivot->properties ?? [];
        data_set($properties, $key, $value);
        $component->pivot->properties = $properties;
        $component->pivot->save();
    }

    public function getComponentStyle(int $componentId, string $key, $default = null)
    {
        $component = $this->components->firstWhere('id', $componentId);
        if (!$component) {
            return $default;
        }

        return data_get($component->pivot->styles, $key, $default);
    }

    public function setComponentStyle(int $componentId, string $key, $value): void
    {
        $component = $this->components->firstWhere('id', $componentId);
        if (!$component) {
            return;
        }

        $styles = $component->pivot->styles ?? [];
        data_set($styles, $key, $value);
        $component->pivot->styles = $styles;
        $component->pivot->save();
    }

    public function getComponentScript(int $componentId, string $key, $default = null)
    {
        $component = $this->components->firstWhere('id', $componentId);
        if (!$component) {
            return $default;
        }

        return data_get($component->pivot->scripts, $key, $default);
    }

    public function setComponentScript(int $componentId, string $key, $value): void
    {
        $component = $this->components->firstWhere('id', $componentId);
        if (!$component) {
            return;
        }

        $scripts = $component->pivot->scripts ?? [];
        data_set($scripts, $key, $value);
        $component->pivot->scripts = $scripts;
        $component->pivot->save();
    }

    public function getOrderedComponents(): array
    {
        return $this->components()
            ->orderBy('pivot_order')
            ->get()
            ->all();
    }

    public function publish(): void
    {
        $this->is_published = true;
        $this->published_at = now();
        $this->save();
    }

    public function unpublish(): void
    {
        $this->is_published = false;
        $this->published_at = null;
        $this->save();
    }

    public function toArray(): array
    {
        $array = parent::toArray();
        $array['layout'] = $this->layout;
        $array['components'] = $this->components;
        $array['creator'] = $this->creator;
        $array['updater'] = $this->updater;
        return $array;
    }
} 