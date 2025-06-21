<?php

namespace LaravelBuilder\VisualBuilder\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use LaravelBuilder\VisualBuilder\Services\AIService;

class AIController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function suggest(Request $request)
    {
        $validated = $request->validate([
            'prompt' => 'required|string',
            'context' => 'string|nullable',
            'type' => 'required|in:component,api,auth,layout',
        ]);

        $suggestions = $this->aiService->generateSuggestions(
            $validated['prompt'],
            $validated['context'] ?? '',
            $validated['type']
        );

        return response()->json([
            'suggestions' => $suggestions,
        ]);
    }

    public function predictCrud(Request $request)
    {
        $validated = $request->validate([
            'model' => 'required|string|max:255',
            'description' => 'required|string',
            'fields' => 'array',
        ]);

        $crud = $this->aiService->predictCrudStructure(
            $validated['model'],
            $validated['description'],
            $validated['fields'] ?? []
        );

        return response()->json([
            'crud' => $crud,
        ]);
    }

    public function generateValidation(Request $request)
    {
        $validated = $request->validate([
            'model' => 'required|string|max:255',
            'fields' => 'required|array',
            'fields.*.name' => 'required|string|max:255',
            'fields.*.type' => 'required|string|max:255',
            'fields.*.description' => 'string|nullable',
        ]);

        $rules = $this->aiService->generateValidationRules(
            $validated['model'],
            $validated['fields']
        );

        return response()->json([
            'rules' => $rules,
        ]);
    }

    public function generateSeeder(Request $request)
    {
        $validated = $request->validate([
            'model' => 'required|string|max:255',
            'count' => 'integer|min:1|max:100',
            'fields' => 'required|array',
            'fields.*.name' => 'required|string|max:255',
            'fields.*.type' => 'required|string|max:255',
            'fields.*.description' => 'string|nullable',
        ]);

        $seeder = $this->aiService->generateSeedData(
            $validated['model'],
            $validated['count'] ?? 10,
            $validated['fields']
        );

        return response()->json([
            'seeder' => $seeder,
        ]);
    }

    public function optimizeCode(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'context' => 'string|nullable',
            'type' => 'required|in:component,api,auth,layout',
        ]);

        $optimized = $this->aiService->optimizeCode(
            $validated['code'],
            $validated['context'] ?? '',
            $validated['type']
        );

        return response()->json([
            'optimized' => $optimized,
        ]);
    }

    public function analyzePerformance(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'type' => 'required|in:component,api,auth,layout',
        ]);

        $analysis = $this->aiService->analyzePerformance(
            $validated['code'],
            $validated['type']
        );

        return response()->json([
            'analysis' => $analysis,
        ]);
    }

    public function suggestImprovements(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'type' => 'required|in:component,api,auth,layout',
            'context' => 'string|nullable',
        ]);

        $improvements = $this->aiService->suggestImprovements(
            $validated['code'],
            $validated['type'],
            $validated['context'] ?? ''
        );

        return response()->json([
            'improvements' => $improvements,
        ]);
    }

    public function generateDocumentation(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'type' => 'required|in:component,api,auth,layout',
            'format' => 'required|in:markdown,html,pdf',
        ]);

        $documentation = $this->aiService->generateDocumentation(
            $validated['code'],
            $validated['type'],
            $validated['format']
        );

        return response()->json([
            'documentation' => $documentation,
        ]);
    }

    public function predictDependencies(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'type' => 'required|in:component,api,auth,layout',
        ]);

        $dependencies = $this->aiService->predictDependencies(
            $validated['code'],
            $validated['type']
        );

        return response()->json([
            'dependencies' => $dependencies,
        ]);
    }

    public function suggestSecurity(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'type' => 'required|in:component,api,auth,layout',
        ]);

        $security = $this->aiService->suggestSecurity(
            $validated['code'],
            $validated['type']
        );

        return response()->json([
            'security' => $security,
        ]);
    }

    public function generateTests(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'type' => 'required|in:component,api,auth,layout',
            'framework' => 'required|in:phpunit,pest',
        ]);

        $tests = $this->aiService->generateTests(
            $validated['code'],
            $validated['type'],
            $validated['framework']
        );

        return response()->json([
            'tests' => $tests,
        ]);
    }
} 