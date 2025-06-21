<?php

namespace LaravelBuilder\VisualBuilder\Services;

use OpenAI\Client;
use Illuminate\Support\Facades\Config;

class AIService
{
    protected $client;
    protected $model;

    public function __construct()
    {
        $this->client = new Client([
            'api_key' => Config::get('visual-builder.ai.api_key'),
        ]);
        $this->model = Config::get('visual-builder.ai.model', 'gpt-4');
    }

    public function generateSuggestions(string $prompt)
    {
        $response = $this->client->chat()->create([
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an expert Laravel developer assistant. Provide detailed, accurate suggestions for Laravel development.',
                ],
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
        ]);

        return $response->choices[0]->message->content;
    }

    public function predictCrudStructure(string $modelName)
    {
        $prompt = "Generate a complete CRUD structure for a Laravel model named {$modelName}. Include:";
        $prompt .= "\n1. Model properties and relationships";
        $prompt .= "\n2. Migration structure";
        $prompt .= "\n3. Controller methods";
        $prompt .= "\n4. Validation rules";
        $prompt .= "\n5. Resource/Transformer structure";

        return $this->generateSuggestions($prompt);
    }

    public function suggestRelationships(string $modelName, array $context = [])
    {
        $prompt = "Suggest appropriate relationships for a Laravel model named {$modelName}.";
        if (!empty($context)) {
            $prompt .= "\nContext: " . json_encode($context);
        }

        return $this->generateSuggestions($prompt);
    }

    public function generateValidationRules(string $modelName, array $fields = [])
    {
        $prompt = "Generate Laravel validation rules for a model named {$modelName}.";
        if (!empty($fields)) {
            $prompt .= "\nFields: " . json_encode($fields);
        }

        return $this->generateSuggestions($prompt);
    }

    public function generateSeedData(string $modelName, int $count = 10)
    {
        $prompt = "Generate realistic seed data for {$count} records of a Laravel model named {$modelName}.";
        $prompt .= "\nInclude all necessary fields and relationships.";

        return $this->generateSuggestions($prompt);
    }

    public function suggestUILayout(string $componentType, array $requirements = [])
    {
        $prompt = "Suggest an optimal UI layout for a {$componentType} component.";
        if (!empty($requirements)) {
            $prompt .= "\nRequirements: " . json_encode($requirements);
        }

        return $this->generateSuggestions($prompt);
    }

    public function generateTestCases(string $componentName, string $componentType)
    {
        $prompt = "Generate comprehensive test cases for a {$componentType} component named {$componentName}.";
        $prompt .= "\nInclude unit tests, feature tests, and browser tests where appropriate.";

        return $this->generateSuggestions($prompt);
    }

    public function optimizeCode(string $code, string $context = '')
    {
        $prompt = "Optimize the following Laravel code for performance, security, and best practices:";
        $prompt .= "\n\n{$code}";
        if ($context) {
            $prompt .= "\n\nContext: {$context}";
        }

        return $this->generateSuggestions($prompt);
    }

    public function generateApiDocumentation(array $endpoints)
    {
        $prompt = "Generate OpenAPI/Swagger documentation for the following API endpoints:";
        $prompt .= "\n" . json_encode($endpoints, JSON_PRETTY_PRINT);

        return $this->generateSuggestions($prompt);
    }

    public function suggestSecurityMeasures(string $componentType, array $context = [])
    {
        $prompt = "Suggest security measures and best practices for a {$componentType} component.";
        if (!empty($context)) {
            $prompt .= "\nContext: " . json_encode($context);
        }

        return $this->generateSuggestions($prompt);
    }

    public function analyzePerformance(string $code)
    {
        $prompt = "Analyze the following Laravel code for performance issues and suggest optimizations:";
        $prompt .= "\n\n{$code}";

        return $this->generateSuggestions($prompt);
    }

    public function generateMigration(string $tableName, array $fields)
    {
        $prompt = "Generate a Laravel migration for a table named {$tableName} with the following fields:";
        $prompt .= "\n" . json_encode($fields, JSON_PRETTY_PRINT);

        return $this->generateSuggestions($prompt);
    }

    public function suggestIndexes(string $tableName, array $fields, array $queries = [])
    {
        $prompt = "Suggest appropriate database indexes for a table named {$tableName} with the following fields:";
        $prompt .= "\n" . json_encode($fields, JSON_PRETTY_PRINT);
        if (!empty($queries)) {
            $prompt .= "\nCommon queries: " . json_encode($queries);
        }

        return $this->generateSuggestions($prompt);
    }
} 