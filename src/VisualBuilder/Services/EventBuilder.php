<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class EventBuilder extends BaseBuilder
{
    protected function getDefaultName(): string
    {
        return 'event';
    }

    protected function getConfigPath(): string
    {
        return base_path('config/event-builder.php');
    }

    protected function getStubsPath(): string
    {
        return base_path('stubs/event-builder');
    }

    public function build(array $config): array
    {
        $this->output = [];

        // Create event class
        $this->buildEvent($config);

        // Create listener class
        $this->buildListener($config);

        return $this->output;
    }

    protected function buildEvent(array $config): void
    {
        $eventName = $this->getEventName($config['name']);
        $eventPath = app_path("Events/{$eventName}.php");
        $eventStub = $this->getStub('event');
        $eventContent = $this->replaceStub($eventStub, [
            'namespace' => $this->getNamespace($eventPath),
            'class' => $eventName,
            'model' => $config['name'],
            'modelNamespace' => $this->getNamespace(app_path("Models/{$config['name']}.php")),
            'properties' => $this->getProperties($config),
            'broadcast' => $this->getBroadcast($config),
            'broadcastAs' => $this->getBroadcastAs($config),
            'broadcastOn' => $this->getBroadcastOn($config),
            'broadcastWith' => $this->getBroadcastWith($config),
            'shouldBroadcast' => $config['should_broadcast'] ?? false,
            'shouldQueue' => $config['should_queue'] ?? false,
            'queue' => $config['queue'] ?? 'default',
            'connection' => $config['connection'] ?? 'sync',
            'tries' => $config['tries'] ?? 3,
            'timeout' => $config['timeout'] ?? 60,
            'retryAfter' => $config['retry_after'] ?? 60,
            'maxExceptions' => $config['max_exceptions'] ?? 3,
        ]);

        $this->createFile($eventPath, $eventContent);
    }

    protected function buildListener(array $config): void
    {
        $listenerName = $this->getListenerName($config['name']);
        $listenerPath = app_path("Listeners/{$listenerName}.php");
        $listenerStub = $this->getStub('listener');
        $listenerContent = $this->replaceStub($listenerStub, [
            'namespace' => $this->getNamespace($listenerPath),
            'class' => $listenerName,
            'event' => $this->getEventName($config['name']),
            'eventNamespace' => $this->getNamespace(app_path("Events/{$this->getEventName($config['name'])}.php")),
            'handle' => $this->getHandle($config),
            'shouldQueue' => $config['should_queue'] ?? false,
            'queue' => $config['queue'] ?? 'default',
            'connection' => $config['connection'] ?? 'sync',
            'tries' => $config['tries'] ?? 3,
            'timeout' => $config['timeout'] ?? 60,
            'retryAfter' => $config['retry_after'] ?? 60,
            'maxExceptions' => $config['max_exceptions'] ?? 3,
        ]);

        $this->createFile($listenerPath, $listenerContent);
    }

    protected function getProperties(array $config): string
    {
        $properties = $config['properties'] ?? [];
        $result = [];

        foreach ($properties as $property) {
            $result[] = $this->formatProperty($property);
        }

        return implode("\n    ", $result);
    }

    protected function formatProperty(array $property): string
    {
        $result = [];
        foreach ($property as $key => $value) {
            if (is_string($value)) {
                $result[] = "'{$key}' => '{$value}'";
            } else {
                $result[] = "'{$key}' => " . json_encode($value);
            }
        }

        return 'public $' . $property['name'] . ';';
    }

    protected function getBroadcast(array $config): string
    {
        $broadcast = $config['broadcast'] ?? [];
        $result = [];

        foreach ($broadcast as $key => $value) {
            if (is_string($value)) {
                $result[] = "'{$key}' => '{$value}'";
            } else {
                $result[] = "'{$key}' => " . json_encode($value);
            }
        }

        return implode(",\n            ", $result);
    }

    protected function getBroadcastAs(array $config): string
    {
        $broadcastAs = $config['broadcast_as'] ?? '';
        return $broadcastAs;
    }

    protected function getBroadcastOn(array $config): string
    {
        $broadcastOn = $config['broadcast_on'] ?? [];
        $result = [];

        foreach ($broadcastOn as $channel) {
            $result[] = "'{$channel}'";
        }

        return implode(",\n            ", $result);
    }

    protected function getBroadcastWith(array $config): string
    {
        $broadcastWith = $config['broadcast_with'] ?? [];
        $result = [];

        foreach ($broadcastWith as $key => $value) {
            if (is_string($value)) {
                $result[] = "'{$key}' => '{$value}'";
            } else {
                $result[] = "'{$key}' => " . json_encode($value);
            }
        }

        return implode(",\n            ", $result);
    }

    protected function getHandle(array $config): string
    {
        $handle = $config['handle'] ?? '';
        return $handle;
    }

    protected function getEventName(string $model): string
    {
        return $model . 'Event';
    }

    protected function getListenerName(string $model): string
    {
        return $model . 'Listener';
    }

    protected function getRouteName(string $model): string
    {
        return Str::kebab(Str::plural($model));
    }
} 