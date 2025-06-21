<?php

namespace LaravelBuilder\VisualBuilder\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use LaravelBuilder\VisualBuilder\Services\ComponentBuilder;

class ComponentBuilderController extends Controller
{
    protected $componentBuilder;

    public function __construct(ComponentBuilder $componentBuilder)
    {
        $this->componentBuilder = $componentBuilder;
    }

    public function index()
    {
        $components = $this->componentBuilder->getComponents();
        return view('visual-builder::components.index', compact('components'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'properties' => 'required|array',
            'properties.*.name' => 'required|string|max:255',
            'properties.*.type' => 'required|string|max:255',
            'properties.*.default' => 'nullable',
            'properties.*.validation' => 'array',
            'template' => 'required|string',
            'styles' => 'array',
            'scripts' => 'array',
        ]);

        $component = $this->componentBuilder->createComponent($validated);

        return redirect()
            ->route('builder.components.show', $component['id'])
            ->with('success', 'Component created successfully.');
    }

    public function show($id)
    {
        $component = $this->componentBuilder->getComponent($id);
        return view('visual-builder::components.show', compact('component'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'properties' => 'required|array',
            'properties.*.name' => 'required|string|max:255',
            'properties.*.type' => 'required|string|max:255',
            'properties.*.default' => 'nullable',
            'properties.*.validation' => 'array',
            'template' => 'required|string',
            'styles' => 'array',
            'scripts' => 'array',
        ]);

        $component = $this->componentBuilder->updateComponent($id, $validated);

        return redirect()
            ->route('builder.components.show', $component['id'])
            ->with('success', 'Component updated successfully.');
    }

    public function destroy($id)
    {
        $this->componentBuilder->deleteComponent($id);

        return redirect()
            ->route('builder.components.index')
            ->with('success', 'Component deleted successfully.');
    }

    public function export($id)
    {
        $component = $this->componentBuilder->getComponent($id);
        $export = $this->componentBuilder->exportComponent($component);

        return response()->json($export)
            ->header('Content-Disposition', 'attachment; filename="' . $component['name'] . '.json"')
            ->header('Content-Type', 'application/json');
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'component' => 'required|file|mimes:json',
        ]);

        $data = json_decode(file_get_contents($validated['component']), true);
        $component = $this->componentBuilder->importComponent($data);

        return redirect()
            ->route('builder.components.show', $component['id'])
            ->with('success', 'Component imported successfully.');
    }

    public function categories()
    {
        $categories = $this->componentBuilder->getCategories();
        return view('visual-builder::components.categories', compact('categories'));
    }

    public function createCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'required|string|max:255',
        ]);

        $category = $this->componentBuilder->createCategory($validated);

        return redirect()
            ->route('builder.components.categories')
            ->with('success', 'Category created successfully.');
    }

    public function updateCategory(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'required|string|max:255',
        ]);

        $category = $this->componentBuilder->updateCategory($id, $validated);

        return redirect()
            ->route('builder.components.categories')
            ->with('success', 'Category updated successfully.');
    }

    public function deleteCategory($id)
    {
        $this->componentBuilder->deleteCategory($id);

        return redirect()
            ->route('builder.components.categories')
            ->with('success', 'Category deleted successfully.');
    }

    public function layouts()
    {
        $layouts = $this->componentBuilder->getLayouts();
        return view('visual-builder::components.layouts', compact('layouts'));
    }

    public function createLayout(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'template' => 'required|string',
            'regions' => 'required|array',
            'regions.*.name' => 'required|string|max:255',
            'regions.*.description' => 'required|string',
        ]);

        $layout = $this->componentBuilder->createLayout($validated);

        return redirect()
            ->route('builder.components.layouts')
            ->with('success', 'Layout created successfully.');
    }

    public function updateLayout(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'template' => 'required|string',
            'regions' => 'required|array',
            'regions.*.name' => 'required|string|max:255',
            'regions.*.description' => 'required|string',
        ]);

        $layout = $this->componentBuilder->updateLayout($id, $validated);

        return redirect()
            ->route('builder.components.layouts')
            ->with('success', 'Layout updated successfully.');
    }

    public function deleteLayout($id)
    {
        $this->componentBuilder->deleteLayout($id);

        return redirect()
            ->route('builder.components.layouts')
            ->with('success', 'Layout deleted successfully.');
    }

    public function preview($id)
    {
        $component = $this->componentBuilder->getComponent($id);
        $preview = $this->componentBuilder->previewComponent($component);

        return view('visual-builder::components.preview', compact('component', 'preview'));
    }

    public function validate(Request $request, $id)
    {
        $validated = $request->validate([
            'properties' => 'required|array',
        ]);

        $component = $this->componentBuilder->getComponent($id);
        $validationResult = $this->componentBuilder->validateComponent($component, $validated['properties']);

        return response()->json($validationResult);
    }

    public function optimize($id)
    {
        $component = $this->componentBuilder->getComponent($id);
        $optimizationResult = $this->componentBuilder->optimizeComponent($component);

        return response()->json($optimizationResult);
    }
} 