<?php

namespace LaravelBuilder\VisualBuilder\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use LaravelBuilder\VisualBuilder\Services\ApiBuilder;
use LaravelBuilder\VisualBuilder\Services\AuthBuilder;
use LaravelBuilder\VisualBuilder\Services\ComponentBuilder;
use LaravelBuilder\VisualBuilder\Services\AIService;

class BuilderController extends Controller
{
    protected $apiBuilder;
    protected $authBuilder;
    protected $componentBuilder;
    protected $aiService;

    public function __construct(
        ApiBuilder $apiBuilder,
        AuthBuilder $authBuilder,
        ComponentBuilder $componentBuilder,
        AIService $aiService
    ) {
        $this->apiBuilder = $apiBuilder;
        $this->authBuilder = $authBuilder;
        $this->componentBuilder = $componentBuilder;
        $this->aiService = $aiService;
    }

    public function dashboard()
    {
        return view('visual-builder::interface', [
            'builder' => $this->componentBuilder,
        ]);
    }

    public function index()
    {
        $pages = $this->componentBuilder->getPages();
        return view('visual-builder::pages.index', compact('pages'));
    }

    public function create()
    {
        return view('visual-builder::pages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'layout' => 'required|string|max:255',
            'components' => 'array',
        ]);

        $page = $this->componentBuilder->buildPage($validated);

        return redirect()
            ->route('builder.pages.show', $page['id'])
            ->with('success', 'Page created successfully.');
    }

    public function show($id)
    {
        $page = $this->componentBuilder->getPage($id);
        return view('visual-builder::pages.show', compact('page'));
    }

    public function edit($id)
    {
        $page = $this->componentBuilder->getPage($id);
        return view('visual-builder::pages.edit', compact('page'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'layout' => 'required|string|max:255',
            'components' => 'array',
        ]);

        $page = $this->componentBuilder->updatePage($id, $validated);

        return redirect()
            ->route('builder.pages.show', $page['id'])
            ->with('success', 'Page updated successfully.');
    }

    public function destroy($id)
    {
        $this->componentBuilder->deletePage($id);

        return redirect()
            ->route('builder.pages.index')
            ->with('success', 'Page deleted successfully.');
    }

    public function aiSuggest(Request $request)
    {
        $validated = $request->validate([
            'prompt' => 'required|string',
        ]);

        $suggestions = $this->aiService->generateSuggestions($validated['prompt']);

        return response()->json([
            'suggestions' => $suggestions,
        ]);
    }

    public function generateCrud(Request $request)
    {
        $validated = $request->validate([
            'model' => 'required|string|max:255',
            'fields' => 'array',
        ]);

        $crud = $this->aiService->predictCrudStructure($validated['model']);

        return response()->json([
            'crud' => $crud,
        ]);
    }

    public function generateValidation(Request $request)
    {
        $validated = $request->validate([
            'model' => 'required|string|max:255',
            'fields' => 'array',
        ]);

        $rules = $this->aiService->generateValidationRules(
            $validated['model'],
            $validated['fields'] ?? []
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
        ]);

        $seeder = $this->aiService->generateSeedData(
            $validated['model'],
            $validated['count'] ?? 10
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
        ]);

        $optimized = $this->aiService->optimizeCode(
            $validated['code'],
            $validated['context'] ?? ''
        );

        return response()->json([
            'optimized' => $optimized,
        ]);
    }

    public function settings()
    {
        $settings = config('visual-builder');
        return view('visual-builder::settings.index', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'frontend.framework' => 'required|in:livewire,vue',
            'frontend.theme' => 'required|in:light,dark,custom',
            'ai.enabled' => 'boolean',
            'ai.provider' => 'required_if:ai.enabled,true|string',
            'ai.model' => 'required_if:ai.enabled,true|string',
        ]);

        // Update settings in config file
        $this->updateConfigFile($validated);

        return redirect()
            ->route('builder.settings.index')
            ->with('success', 'Settings updated successfully.');
    }

    public function updateTheme(Request $request)
    {
        $validated = $request->validate([
            'theme' => 'required|in:light,dark,custom',
            'custom_colors' => 'array|nullable',
        ]);

        // Update theme settings
        $this->updateThemeSettings($validated);

        return redirect()
            ->route('builder.settings.index')
            ->with('success', 'Theme updated successfully.');
    }

    public function exportSettings()
    {
        $settings = config('visual-builder');
        $filename = 'visual-builder-settings-' . date('Y-m-d') . '.json';

        return response()->json($settings)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Content-Type', 'application/json');
    }

    public function importSettings(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|file|mimes:json',
        ]);

        $settings = json_decode(file_get_contents($validated['settings']), true);

        // Update settings in config file
        $this->updateConfigFile($settings);

        return redirect()
            ->route('builder.settings.index')
            ->with('success', 'Settings imported successfully.');
    }

    protected function updateConfigFile(array $settings)
    {
        $configPath = config_path('visual-builder.php');
        $content = "<?php\n\nreturn " . var_export($settings, true) . ";\n";
        file_put_contents($configPath, $content);
    }

    protected function updateThemeSettings(array $settings)
    {
        $config = config('visual-builder');
        $config['frontend']['theme'] = $settings['theme'];
        
        if ($settings['theme'] === 'custom' && isset($settings['custom_colors'])) {
            $config['frontend']['custom_colors'] = $settings['custom_colors'];
        }

        $this->updateConfigFile($config);
    }
} 