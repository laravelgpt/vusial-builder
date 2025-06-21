<?php

namespace LaravelBuilder\VisualBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Component extends Model
{
    protected $fillable = [
        'name',
        'type',
        'category_id',
        'properties',
        'template',
        'styles',
        'scripts',
        'is_active',
        'is_custom',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'properties' => 'array',
        'styles' => 'array',
        'scripts' => 'array',
        'is_active' => 'boolean',
        'is_custom' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'updated_by');
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    public function layouts(): HasMany
    {
        return $this->hasMany(Layout::class);
    }

    public function getProperty(string $key, $default = null)
    {
        return data_get($this->properties, $key, $default);
    }

    public function setProperty(string $key, $value): void
    {
        $properties = $this->properties ?? [];
        data_set($properties, $key, $value);
        $this->properties = $properties;
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
    }

    public function toArray(): array
    {
        $array = parent::toArray();
        $array['category'] = $this->category;
        $array['creator'] = $this->creator;
        $array['updater'] = $this->updater;
        return $array;
    }
} 