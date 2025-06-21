<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel Visual Builder') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <!-- Navigation -->
        <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('builder.dashboard') }}">
                                <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <x-nav-link :href="route('builder.dashboard')" :active="request()->routeIs('builder.dashboard')">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                            <x-nav-link :href="route('builder.pages')" :active="request()->routeIs('builder.pages')">
                                {{ __('Pages') }}
                            </x-nav-link>
                            <x-nav-link :href="route('builder.api')" :active="request()->routeIs('builder.api')">
                                {{ __('API Builder') }}
                            </x-nav-link>
                            <x-nav-link :href="route('builder.auth')" :active="request()->routeIs('builder.auth')">
                                {{ __('Auth') }}
                            </x-nav-link>
                        </div>
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                    <div>{{ Auth::user()->name }}</div>

                                    <div class="ml-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('Profile') }}
                                </x-dropdown-link>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <!-- Builder Interface -->
                            <div class="builder-interface">
                                <!-- Component Palette -->
                                <div class="component-palette">
                                    <h3 class="text-lg font-semibold mb-4">Components</h3>
                                    <div class="grid grid-cols-2 gap-4">
                                        @foreach($builder->getAvailableComponents() as $category => $components)
                                            <div class="component-category">
                                                <h4 class="font-medium mb-2">{{ ucfirst($category) }}</h4>
                                                <div class="space-y-2">
                                                    @foreach($components as $component)
                                                        <div class="component-item" draggable="true" data-component="{{ $component }}">
                                                            {{ ucfirst($component) }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Canvas -->
                                <div class="builder-canvas" id="builderCanvas">
                                    <div class="canvas-dropzone">
                                        <p class="text-gray-500">Drag and drop components here</p>
                                    </div>
                                </div>

                                <!-- Properties Panel -->
                                <div class="properties-panel">
                                    <h3 class="text-lg font-semibold mb-4">Properties</h3>
                                    <div id="componentProperties">
                                        <p class="text-gray-500">Select a component to edit its properties</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Builder Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize the builder
            const builder = new VisualBuilder({
                canvas: document.getElementById('builderCanvas'),
                propertiesPanel: document.getElementById('componentProperties'),
            });

            // Make components draggable
            document.querySelectorAll('.component-item').forEach(item => {
                item.addEventListener('dragstart', handleDragStart);
                item.addEventListener('dragend', handleDragEnd);
            });

            // Make canvas a drop target
            const canvas = document.getElementById('builderCanvas');
            canvas.addEventListener('dragover', handleDragOver);
            canvas.addEventListener('drop', handleDrop);

            // Handle drag and drop events
            function handleDragStart(e) {
                e.dataTransfer.setData('text/plain', e.target.dataset.component);
                e.target.classList.add('dragging');
            }

            function handleDragEnd(e) {
                e.target.classList.remove('dragging');
            }

            function handleDragOver(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'copy';
            }

            function handleDrop(e) {
                e.preventDefault();
                const componentType = e.dataTransfer.getData('text/plain');
                const rect = canvas.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                builder.addComponent(componentType, { x, y });
            }
        });
    </script>

    <!-- Styles -->
    <style>
        .builder-interface {
            display: grid;
            grid-template-columns: 250px 1fr 300px;
            gap: 1rem;
            height: calc(100vh - 200px);
        }

        .component-palette {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 0.5rem;
            overflow-y: auto;
        }

        .component-item {
            background: white;
            padding: 0.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.25rem;
            cursor: move;
            user-select: none;
        }

        .component-item:hover {
            background: #f1f5f9;
        }

        .component-item.dragging {
            opacity: 0.5;
        }

        .builder-canvas {
            background: white;
            border: 2px dashed #e2e8f0;
            border-radius: 0.5rem;
            position: relative;
            overflow: auto;
        }

        .canvas-dropzone {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        .properties-panel {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 0.5rem;
            overflow-y: auto;
        }

        /* Dark mode styles */
        .dark .component-palette,
        .dark .properties-panel {
            background: #1e293b;
        }

        .dark .component-item {
            background: #334155;
            border-color: #475569;
            color: #e2e8f0;
        }

        .dark .component-item:hover {
            background: #475569;
        }

        .dark .builder-canvas {
            background: #334155;
            border-color: #475569;
        }
    </style>
</body>
</html> 