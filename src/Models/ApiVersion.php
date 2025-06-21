<?php

namespace LaravelBuilder\VisualBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiVersion extends Model
{
    protected $fillable = [
        'api_id',
        'version',
        'changes',
        'documentation',
        'is_active',
        'is_deprecated',
        'deprecated_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'changes' => 'array',
        'documentation' => 'array',
        'is_active' => 'boolean',
        'is_deprecated' => 'boolean',
        'deprecated_at' => 'datetime',
    ];

    public function api(): BelongsTo
    {
        return $this->belongsTo(Api::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'updated_by');
    }

    public function getChange(string $type, $default = null)
    {
        return collect($this->changes)
            ->firstWhere('type', $type) ?? $default;
    }

    public function addChange(string $type, array $details): void
    {
        $changes = $this->changes ?? [];
        $changes[] = array_merge([
            'type' => $type,
            'timestamp' => now()->toIso8601String(),
        ], $details);
        $this->changes = $changes;
        $this->save();
    }

    public function removeChange(string $type): void
    {
        $changes = collect($this->changes)
            ->filter(fn ($change) => $change['type'] !== $type)
            ->values()
            ->all();
        $this->changes = $changes;
        $this->save();
    }

    public function deprecate(): void
    {
        $this->is_deprecated = true;
        $this->deprecated_at = now();
        $this->save();
    }

    public function undeprecate(): void
    {
        $this->is_deprecated = false;
        $this->deprecated_at = null;
        $this->save();
    }

    public function generateDocumentation(): array
    {
        $documentation = [
            'version' => $this->version,
            'changes' => $this->changes,
            'api' => $this->api->toArray(),
        ];

        $this->documentation = $documentation;
        $this->save();

        return $documentation;
    }

    public function toArray(): array
    {
        $array = parent::toArray();
        $array['api'] = $this->api;
        $array['creator'] = $this->creator;
        $array['updater'] = $this->updater;
        return $array;
    }
} 