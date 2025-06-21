<?php

namespace LaravelBuilder\VisualBuilder\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use LaravelBuilder\VisualBuilder\Services\ApiBuilder;
use LaravelBuilder\VisualBuilder\Models\Api;
use LaravelBuilder\VisualBuilder\Services\ApiGeneratorService;

class ApiBuilderController extends Controller
{
    protected $apiBuilder;
    protected $apiGenerator;

    public function __construct(ApiBuilder $apiBuilder, ApiGeneratorService $apiGenerator)
    {
        $this->apiBuilder = $apiBuilder;
        $this->apiGenerator = $apiGenerator;
    }

    public function index()
    {
        $apis = Api::with(['versions', 'creator', 'updater'])->get();
        $types = Api::getAvailableTypes();
        
        return view('visual-builder::api.index', compact('apis', 'types'));
    }

    public function create()
    {
        $types = Api::getAvailableTypes();
        $authTypes = Api::getAvailableAuthTypes();
        $oauthProviders = Api::getAvailableOAuthProviders();
        $builderTypes = Api::getAvailableBuilderTypes();
        $formFieldTypes = Api::getAvailableFormFieldTypes();
        $tableFeatures = Api::getAvailableTableFeatures();
        $uiComponents = Api::getAvailableUIComponents();
        
        return view('visual-builder::api.create', compact(
            'types',
            'authTypes',
            'oauthProviders',
            'builderTypes',
            'formFieldTypes',
            'tableFeatures',
            'uiComponents'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys(Api::getAvailableTypes())),
            'builder_type' => 'required|string|in:' . implode(',', array_keys(Api::getAvailableBuilderTypes())),
            'fields' => 'required|array',
            'relationships' => 'nullable|array',
            'validation_rules' => 'nullable|array',
            'auth_type' => 'required|string|in:' . implode(',', array_keys(Api::getAvailableAuthTypes())),
            'auth_providers' => 'nullable|array',
            'auth_config' => 'nullable|array',
            'rate_limit' => 'nullable|array',
            'middleware' => 'nullable|array',
            'form_config' => 'nullable|array',
            'table_config' => 'nullable|array',
            'ui_config' => 'nullable|array',
            'theme_config' => 'nullable|array',
            'is_public' => 'boolean',
        ]);

        $api = Api::create($validated + [
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        // Generate API based on type and builder
        $this->apiGenerator->generate($api);

        return redirect()->route('visual-builder.api.show', $api)
            ->with('success', 'API created successfully.');
    }

    public function show(Api $api)
    {
        $api->load(['versions', 'creator', 'updater']);
        return view('visual-builder::api.show', compact('api'));
    }

    public function edit(Api $api)
    {
        $types = Api::getAvailableTypes();
        $authTypes = Api::getAvailableAuthTypes();
        $oauthProviders = Api::getAvailableOAuthProviders();
        $builderTypes = Api::getAvailableBuilderTypes();
        $formFieldTypes = Api::getAvailableFormFieldTypes();
        $tableFeatures = Api::getAvailableTableFeatures();
        $uiComponents = Api::getAvailableUIComponents();
        
        return view('visual-builder::api.edit', compact(
            'api',
            'types',
            'authTypes',
            'oauthProviders',
            'builderTypes',
            'formFieldTypes',
            'tableFeatures',
            'uiComponents'
        ));
    }

    public function update(Request $request, Api $api)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys(Api::getAvailableTypes())),
            'builder_type' => 'required|string|in:' . implode(',', array_keys(Api::getAvailableBuilderTypes())),
            'fields' => 'required|array',
            'relationships' => 'nullable|array',
            'validation_rules' => 'nullable|array',
            'auth_type' => 'required|string|in:' . implode(',', array_keys(Api::getAvailableAuthTypes())),
            'auth_providers' => 'nullable|array',
            'auth_config' => 'nullable|array',
            'rate_limit' => 'nullable|array',
            'middleware' => 'nullable|array',
            'form_config' => 'nullable|array',
            'table_config' => 'nullable|array',
            'ui_config' => 'nullable|array',
            'theme_config' => 'nullable|array',
            'is_public' => 'boolean',
        ]);

        $api->update($validated + [
            'updated_by' => auth()->id(),
        ]);

        // Regenerate API if type, builder, or auth changed
        if ($api->wasChanged(['type', 'builder_type', 'auth_type', 'auth_providers'])) {
            $this->apiGenerator->regenerate($api);
        }

        return redirect()->route('visual-builder.api.show', $api)
            ->with('success', 'API updated successfully.');
    }

    public function destroy(Api $api)
    {
        $api->delete();
        return redirect()->route('visual-builder.api.index')
            ->with('success', 'API deleted successfully.');
    }

    public function generate(Api $api)
    {
        $this->apiGenerator->generate($api);
        return redirect()->route('visual-builder.api.show', $api)
            ->with('success', 'API generated successfully.');
    }

    public function documentation(Api $api)
    {
        $documentation = $api->generateDocumentation();
        return view('visual-builder::api.documentation', compact('api', 'documentation'));
    }

    public function export(Api $api, string $format = 'postman')
    {
        return $this->apiGenerator->export($api, $format);
    }

    public function test(Api $api)
    {
        $endpoints = $api->endpoints;
        return view('visual-builder::api.test', compact('api', 'endpoints'));
    }

    public function validate(Request $request, $id)
    {
        $validated = $request->validate([
            'endpoint' => 'required|string|max:255',
            'method' => 'required|in:GET,POST,PUT,PATCH,DELETE',
            'data' => 'array',
        ]);

        $api = $this->apiBuilder->getApi($id);
        $validationResult = $this->apiBuilder->validateEndpoint(
            $api,
            $validated['endpoint'],
            $validated['method'],
            $validated['data'] ?? []
        );

        return response()->json($validationResult);
    }

    public function optimize($id)
    {
        $api = $this->apiBuilder->getApi($id);
        $optimizationResult = $this->apiBuilder->optimizeApi($api);

        return response()->json($optimizationResult);
    }

    public function getAiSuggestions(Request $request, Api $api)
    {
        $suggestions = $this->apiBuilder->getAiSuggestions($api);
        return response()->json($suggestions);
    }

    public function applyAiSuggestion(Request $request, Api $api)
    {
        $validated = $request->validate([
            'suggestion_type' => 'required|string',
            'suggestion_data' => 'required|array',
        ]);

        $result = $this->apiBuilder->applyAiSuggestion(
            $api,
            $validated['suggestion_type'],
            $validated['suggestion_data']
        );

        return response()->json($result);
    }

    public function configureRateLimit(Request $request, Api $api)
    {
        $validated = $request->validate([
            'rate_limit' => 'required|array',
            'rate_limit.max_attempts' => 'required|integer|min:1',
            'rate_limit.decay_minutes' => 'required|integer|min:1',
        ]);

        $api->update([
            'rate_limit' => $validated['rate_limit'],
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('visual-builder.api.show', $api)
            ->with('success', 'Rate limit configured successfully.');
    }

    public function configureMiddleware(Request $request, Api $api)
    {
        $validated = $request->validate([
            'middleware' => 'required|array',
            'middleware.*' => 'required|string|exists:middleware,name',
        ]);

        $api->update([
            'middleware' => $validated['middleware'],
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('visual-builder.api.show', $api)
            ->with('success', 'Middleware configured successfully.');
    }

    public function configureSocialAuth(Request $request, Api $api)
    {
        $validated = $request->validate([
            'provider' => 'required|string|in:' . implode(',', array_keys(Api::getAvailableOAuthProviders())),
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
            'redirect_uri' => 'required|url',
            'scopes' => 'nullable|array',
        ]);

        $providers = $api->auth_providers ?? [];
        $providers[$validated['provider']] = [
            'client_id' => $validated['client_id'],
            'client_secret' => $validated['client_secret'],
            'redirect_uri' => $validated['redirect_uri'],
            'scopes' => $validated['scopes'] ?? [],
        ];

        $api->update([
            'auth_providers' => $providers,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('visual-builder.api.show', $api)
            ->with('success', 'Social authentication configured successfully.');
    }

    public function removeSocialAuth(Request $request, Api $api)
    {
        $validated = $request->validate([
            'provider' => 'required|string|in:' . implode(',', array_keys(Api::getAvailableOAuthProviders())),
        ]);

        $providers = $api->auth_providers ?? [];
        unset($providers[$validated['provider']]);

        $api->update([
            'auth_providers' => $providers,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('visual-builder.api.show', $api)
            ->with('success', 'Social authentication removed successfully.');
    }

    public function testSocialAuth(Request $request, Api $api)
    {
        $validated = $request->validate([
            'provider' => 'required|string|in:' . implode(',', array_keys(Api::getAvailableOAuthProviders())),
        ]);

        $provider = $validated['provider'];
        $config = $api->auth_providers[$provider] ?? null;

        if (!$config) {
            return response()->json([
                'error' => 'Provider not configured',
            ], 400);
        }

        try {
            $result = $this->apiBuilder->testSocialAuth($api, $provider);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function configureFormBuilder(Request $request, Api $api)
    {
        $validated = $request->validate([
            'form_config' => 'required|array',
            'form_config.fields' => 'required|array',
            'form_config.validation_rules' => 'nullable|array',
            'form_config.layout' => 'nullable|array',
            'form_config.actions' => 'nullable|array',
            'form_config.permissions' => 'nullable|array',
        ]);

        $api->update([
            'form_config' => $validated['form_config'],
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('visual-builder.api.show', $api)
            ->with('success', 'Form builder configured successfully.');
    }

    public function configureTableBuilder(Request $request, Api $api)
    {
        $validated = $request->validate([
            'table_config' => 'required|array',
            'table_config.columns' => 'required|array',
            'table_config.filters' => 'nullable|array',
            'table_config.actions' => 'nullable|array',
            'table_config.export_config' => 'nullable|array',
            'table_config.permissions' => 'nullable|array',
        ]);

        $api->update([
            'table_config' => $validated['table_config'],
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('visual-builder.api.show', $api)
            ->with('success', 'Table builder configured successfully.');
    }

    public function configureUiBuilder(Request $request, Api $api)
    {
        $validated = $request->validate([
            'ui_config' => 'required|array',
            'ui_config.components' => 'required|array',
            'ui_config.sections' => 'nullable|array',
            'ui_config.layout' => 'nullable|array',
            'ui_config.styles' => 'nullable|array',
        ]);

        $api->update([
            'ui_config' => $validated['ui_config'],
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('visual-builder.api.show', $api)
            ->with('success', 'UI builder configured successfully.');
    }

    public function configureThemeBuilder(Request $request, Api $api)
    {
        $validated = $request->validate([
            'theme_config' => 'required|array',
            'theme_config.colors' => 'required|array',
            'theme_config.typography' => 'nullable|array',
            'theme_config.spacing' => 'nullable|array',
            'theme_config.breakpoints' => 'nullable|array',
            'theme_config.animations' => 'nullable|array',
            'theme_config.components' => 'nullable|array',
        ]);

        $api->update([
            'theme_config' => $validated['theme_config'],
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('visual-builder.api.show', $api)
            ->with('success', 'Theme builder configured successfully.');
    }

    public function previewFormBuilder(Api $api)
    {
        return view('visual-builder::api.form-preview', compact('api'));
    }

    public function previewTableBuilder(Api $api)
    {
        return view('visual-builder::api.table-preview', compact('api'));
    }

    public function previewUiBuilder(Api $api)
    {
        return view('visual-builder::api.ui-preview', compact('api'));
    }

    public function previewThemeBuilder(Api $api)
    {
        return view('visual-builder::api.theme-preview', compact('api'));
    }

    public function exportFormBuilder(Api $api)
    {
        return $this->apiGenerator->export($api, 'form');
    }

    public function exportTableBuilder(Api $api)
    {
        return $this->apiGenerator->export($api, 'table');
    }

    public function exportUiBuilder(Api $api)
    {
        return $this->apiGenerator->export($api, 'ui');
    }

    public function exportThemeBuilder(Api $api)
    {
        return $this->apiGenerator->export($api, 'theme');
    }
} 