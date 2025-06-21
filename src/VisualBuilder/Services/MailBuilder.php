<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MailBuilder extends BaseBuilder
{
    protected function getDefaultName(): string
    {
        return 'mail';
    }

    protected function getConfigPath(): string
    {
        return base_path('config/mail-builder.php');
    }

    protected function getStubsPath(): string
    {
        return base_path('stubs/mail-builder');
    }

    public function build(array $config): array
    {
        $this->output = [];

        // Create mail class
        $this->buildMail($config);

        // Create mail view
        $this->buildView($config);

        return $this->output;
    }

    protected function buildMail(array $config): void
    {
        $mailName = $this->getMailName($config['name']);
        $mailPath = app_path("Mail/{$mailName}.php");
        $mailStub = $this->getStub('mail');
        $mailContent = $this->replaceStub($mailStub, [
            'namespace' => $this->getNamespace($mailPath),
            'class' => $mailName,
            'model' => $config['name'],
            'modelNamespace' => $this->getNamespace(app_path("Models/{$config['name']}.php")),
            'properties' => $this->getProperties($config),
            'build' => $this->getBuild($config),
            'envelope' => $this->getEnvelope($config),
            'content' => $this->getContent($config),
            'attachments' => $this->getAttachments($config),
            'from' => $this->getFrom($config),
            'replyTo' => $this->getReplyTo($config),
            'cc' => $this->getCc($config),
            'bcc' => $this->getBcc($config),
            'subject' => $this->getSubject($config),
            'markdown' => $this->getMarkdown($config),
            'view' => $this->getView($config),
            'textView' => $this->getTextView($config),
            'htmlView' => $this->getHtmlView($config),
            'rawView' => $this->getRawView($config),
            'with' => $this->getWith($config),
            'withSwiftMessage' => $this->getWithSwiftMessage($config),
            'queue' => $config['queue'] ?? 'default',
            'connection' => $config['connection'] ?? 'sync',
            'tries' => $config['tries'] ?? 3,
            'timeout' => $config['timeout'] ?? 60,
            'retryAfter' => $config['retry_after'] ?? 60,
            'maxExceptions' => $config['max_exceptions'] ?? 3,
        ]);

        $this->createFile($mailPath, $mailContent);
    }

    protected function buildView(array $config): void
    {
        $viewName = $this->getViewName($config['name']);
        $viewPath = resource_path("views/emails/{$viewName}.blade.php");
        $viewStub = $this->getStub('view');
        $viewContent = $this->replaceStub($viewStub, [
            'mail' => $this->getMailName($config['name']),
            'model' => $config['name'],
            'modelVariable' => Str::camel($config['name']),
            'title' => $config['title'] ?? Str::title($config['name']),
            'content' => $this->getContent($config),
            'action' => $this->getAction($config),
        ]);

        $this->createFile($viewPath, $viewContent);
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

    protected function getBuild(array $config): string
    {
        $build = $config['build'] ?? '';
        return $build;
    }

    protected function getEnvelope(array $config): string
    {
        $envelope = $config['envelope'] ?? [];
        $result = [];

        if (isset($envelope['from'])) {
            $result[] = "'from' => '{$envelope['from']}'";
        }

        if (isset($envelope['subject'])) {
            $result[] = "'subject' => '{$envelope['subject']}'";
        }

        if (isset($envelope['replyTo'])) {
            $result[] = "'replyTo' => '{$envelope['replyTo']}'";
        }

        if (isset($envelope['cc'])) {
            $result[] = "'cc' => " . json_encode($envelope['cc']);
        }

        if (isset($envelope['bcc'])) {
            $result[] = "'bcc' => " . json_encode($envelope['bcc']);
        }

        return implode(",\n            ", $result);
    }

    protected function getContent(array $config): string
    {
        $content = $config['content'] ?? '';
        return $content;
    }

    protected function getAttachments(array $config): string
    {
        $attachments = $config['attachments'] ?? [];
        $result = [];

        foreach ($attachments as $attachment) {
            $result[] = $this->formatAttachment($attachment);
        }

        return implode(",\n            ", $result);
    }

    protected function formatAttachment(array $attachment): string
    {
        $result = [];
        foreach ($attachment as $key => $value) {
            if (is_string($value)) {
                $result[] = "'{$key}' => '{$value}'";
            } else {
                $result[] = "'{$key}' => " . json_encode($value);
            }
        }

        return '[' . implode(', ', $result) . ']';
    }

    protected function getFrom(array $config): string
    {
        $from = $config['from'] ?? '';
        return $from;
    }

    protected function getReplyTo(array $config): string
    {
        $replyTo = $config['reply_to'] ?? '';
        return $replyTo;
    }

    protected function getCc(array $config): string
    {
        $cc = $config['cc'] ?? [];
        $result = [];

        foreach ($cc as $email) {
            $result[] = "'{$email}'";
        }

        return implode(', ', $result);
    }

    protected function getBcc(array $config): string
    {
        $bcc = $config['bcc'] ?? [];
        $result = [];

        foreach ($bcc as $email) {
            $result[] = "'{$email}'";
        }

        return implode(', ', $result);
    }

    protected function getSubject(array $config): string
    {
        $subject = $config['subject'] ?? '';
        return $subject;
    }

    protected function getMarkdown(array $config): string
    {
        $markdown = $config['markdown'] ?? '';
        return $markdown;
    }

    protected function getView(array $config): string
    {
        $view = $config['view'] ?? '';
        return $view;
    }

    protected function getTextView(array $config): string
    {
        $textView = $config['text_view'] ?? '';
        return $textView;
    }

    protected function getHtmlView(array $config): string
    {
        $htmlView = $config['html_view'] ?? '';
        return $htmlView;
    }

    protected function getRawView(array $config): string
    {
        $rawView = $config['raw_view'] ?? '';
        return $rawView;
    }

    protected function getWith(array $config): string
    {
        $with = $config['with'] ?? [];
        $result = [];

        foreach ($with as $key => $value) {
            if (is_string($value)) {
                $result[] = "'{$key}' => '{$value}'";
            } else {
                $result[] = "'{$key}' => " . json_encode($value);
            }
        }

        return implode(",\n            ", $result);
    }

    protected function getWithSwiftMessage(array $config): string
    {
        $withSwiftMessage = $config['with_swift_message'] ?? '';
        return $withSwiftMessage;
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

    protected function getMailName(string $model): string
    {
        return $model . 'Mail';
    }

    protected function getViewName(string $model): string
    {
        return Str::kebab($model) . '-mail';
    }

    protected function getRouteName(string $model): string
    {
        return Str::kebab(Str::plural($model));
    }
} 