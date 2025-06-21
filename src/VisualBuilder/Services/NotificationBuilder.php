<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class NotificationBuilder extends BaseBuilder
{
    protected function getDefaultName(): string
    {
        return 'notification';
    }

    protected function getConfigPath(): string
    {
        return base_path('config/notification-builder.php');
    }

    protected function getStubsPath(): string
    {
        return base_path('stubs/notification-builder');
    }

    public function build(array $config): array
    {
        $this->output = [];

        // Create notification class
        $this->buildNotification($config);

        // Create notification view
        $this->buildView($config);

        return $this->output;
    }

    protected function buildNotification(array $config): void
    {
        $notificationName = $this->getNotificationName($config['name']);
        $notificationPath = app_path("Notifications/{$notificationName}.php");
        $notificationStub = $this->getStub('notification');
        $notificationContent = $this->replaceStub($notificationStub, [
            'namespace' => $this->getNamespace($notificationPath),
            'class' => $notificationName,
            'model' => $config['name'],
            'modelNamespace' => $this->getNamespace(app_path("Models/{$config['name']}.php")),
            'channels' => $this->getChannels($config),
            'via' => $this->getVia($config),
            'toMail' => $this->getToMail($config),
            'toDatabase' => $this->getToDatabase($config),
            'toBroadcast' => $this->getToBroadcast($config),
            'toNexmo' => $this->getToNexmo($config),
            'toSlack' => $this->getToSlack($config),
            'toArray' => $this->getToArray($config),
        ]);

        $this->createFile($notificationPath, $notificationContent);
    }

    protected function buildView(array $config): void
    {
        $viewName = $this->getViewName($config['name']);
        $viewPath = resource_path("views/notifications/{$viewName}.blade.php");
        $viewStub = $this->getStub('view');
        $viewContent = $this->replaceStub($viewStub, [
            'notification' => $this->getNotificationName($config['name']),
            'model' => $config['name'],
            'modelVariable' => Str::camel($config['name']),
            'title' => $config['title'] ?? Str::title($config['name']),
            'content' => $this->getContent($config),
            'action' => $this->getAction($config),
        ]);

        $this->createFile($viewPath, $viewContent);
    }

    protected function getChannels(array $config): string
    {
        $channels = $config['channels'] ?? ['mail', 'database'];
        return implode("', '", $channels);
    }

    protected function getVia(array $config): string
    {
        $channels = $config['channels'] ?? ['mail', 'database'];
        return implode("', '", $channels);
    }

    protected function getToMail(array $config): string
    {
        $mail = $config['mail'] ?? [];
        $result = [];

        if (isset($mail['subject'])) {
            $result[] = "'subject' => '{$mail['subject']}'";
        }

        if (isset($mail['greeting'])) {
            $result[] = "'greeting' => '{$mail['greeting']}'";
        }

        if (isset($mail['line'])) {
            $result[] = "'line' => '{$mail['line']}'";
        }

        if (isset($mail['action'])) {
            $result[] = "'action' => '{$mail['action']}'";
            $result[] = "'actionUrl' => '{$mail['action_url']}'";
        }

        return implode(",\n            ", $result);
    }

    protected function getToDatabase(array $config): string
    {
        $database = $config['database'] ?? [];
        $result = [];

        if (isset($database['type'])) {
            $result[] = "'type' => '{$database['type']}'";
        }

        if (isset($database['message'])) {
            $result[] = "'message' => '{$database['message']}'";
        }

        if (isset($database['data'])) {
            $result[] = "'data' => " . json_encode($database['data']);
        }

        return implode(",\n            ", $result);
    }

    protected function getToBroadcast(array $config): string
    {
        $broadcast = $config['broadcast'] ?? [];
        $result = [];

        if (isset($broadcast['type'])) {
            $result[] = "'type' => '{$broadcast['type']}'";
        }

        if (isset($broadcast['message'])) {
            $result[] = "'message' => '{$broadcast['message']}'";
        }

        if (isset($broadcast['data'])) {
            $result[] = "'data' => " . json_encode($broadcast['data']);
        }

        return implode(",\n            ", $result);
    }

    protected function getToNexmo(array $config): string
    {
        $nexmo = $config['nexmo'] ?? [];
        $result = [];

        if (isset($nexmo['content'])) {
            $result[] = "'content' => '{$nexmo['content']}'";
        }

        return implode(",\n            ", $result);
    }

    protected function getToSlack(array $config): string
    {
        $slack = $config['slack'] ?? [];
        $result = [];

        if (isset($slack['content'])) {
            $result[] = "'content' => '{$slack['content']}'";
        }

        if (isset($slack['attachment'])) {
            $result[] = "'attachment' => " . json_encode($slack['attachment']);
        }

        return implode(",\n            ", $result);
    }

    protected function getToArray(array $config): string
    {
        $array = $config['array'] ?? [];
        $result = [];

        foreach ($array as $key => $value) {
            if (is_string($value)) {
                $result[] = "'{$key}' => '{$value}'";
            } else {
                $result[] = "'{$key}' => " . json_encode($value);
            }
        }

        return implode(",\n            ", $result);
    }

    protected function getContent(array $config): string
    {
        $content = $config['content'] ?? '';
        return $content;
    }

    protected function getAction(array $config): string
    {
        $action = $config['action'] ?? [];
        if (empty($action)) {
            return '';
        }

        return <<<BLADE
<div class="action">
    <a href="{{ \$actionUrl }}" class="btn btn-primary">
        {{ \$actionText }}
    </a>
</div>
BLADE;
    }

    protected function getNotificationName(string $model): string
    {
        return $model . 'Notification';
    }

    protected function getViewName(string $model): string
    {
        return Str::kebab($model) . '-notification';
    }

    protected function getRouteName(string $model): string
    {
        return Str::kebab(Str::plural($model));
    }
} 