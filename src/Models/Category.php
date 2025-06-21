<?php

namespace LaravelBuilder\VisualBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
        'icon',
        'parent_id',
        'order',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function components(): HasMany
    {
        return $this->hasMany(Component::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'updated_by');
    }

    public function getFullPath(): string
    {
        $path = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }

    public function getAllChildren(): array
    {
        $children = $this->children->all();

        foreach ($this->children as $child) {
            $children = array_merge($children, $child->getAllChildren());
        }

        return $children;
    }

    public function getAllComponents(): array
    {
        $components = $this->components->all();

        foreach ($this->children as $child) {
            $components = array_merge($components, $child->getAllComponents());
        }

        return $components;
    }

    public function toArray(): array
    {
        $array = parent::toArray();
        $array['parent'] = $this->parent;
        $array['children'] = $this->children;
        $array['creator'] = $this->creator;
        $array['updater'] = $this->updater;
        $array['full_path'] = $this->getFullPath();
        return $array;
    }
} 