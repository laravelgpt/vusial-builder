@extends('visual-builder::layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="bg-white rounded-lg shadow-lg">
        <div class="p-6 border-b">
            <div class="flex justify-between items-center">
                <h3 class="text-2xl font-semibold">UI Preview</h3>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <label for="format" class="text-sm font-medium text-gray-700">Format:</label>
                        <select id="format" class="rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" onchange="switchFormat(this.value)">
                            <option value="blade">Blade</option>
                            <option value="livewire">Livewire</option>
                            <option value="vue">Vue.js</option>
                            <option value="react">React</option>
                        </select>
                    </div>
                    <button onclick="copyCode()" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" />
                        </svg>
                        Copy Code
                    </button>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div id="preview-container">
                @foreach($api->ui_config['sections'] as $section)
                    <div class="mb-8">
                        <h4 class="text-xl font-medium mb-2">{{ $section['title'] }}</h4>
                        <p class="text-gray-500 mb-4">{{ $section['description'] }}</p>
                        
                        <div class="space-y-6">
                            @foreach($section['components'] as $component)
                                <div class="p-4 border rounded-lg">
                                    <!-- Blade Format -->
                                    <div class="format-content blade-format">
                                        @include('api.components.blade.' . $component['type'], ['component' => $component])
                                    </div>

                                    <!-- Livewire Format -->
                                    <div class="format-content livewire-format hidden">
                                        @include('api.components.livewire.' . $component['type'], ['component' => $component])
                                    </div>

                                    <!-- Vue.js Format -->
                                    <div class="format-content vue-format hidden">
                                        @include('api.components.vue.' . $component['type'], ['component' => $component])
                                    </div>

                                    <!-- React Format -->
                                    <div class="format-content react-format hidden">
                                        @include('api.components.react.' . $component['type'], ['component' => $component])
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function switchFormat(format) {
    // Hide all format contents
    document.querySelectorAll('.format-content').forEach(el => {
        el.classList.add('hidden');
    });
    
    // Show selected format
    document.querySelectorAll(`.${format}-format`).forEach(el => {
        el.classList.remove('hidden');
    });
}

function copyCode() {
    const format = document.getElementById('format').value;
    const code = document.querySelector(`.${format}-format`).innerHTML;
    
    // Create a temporary textarea element
    const textarea = document.createElement('textarea');
    textarea.value = code;
    document.body.appendChild(textarea);
    
    // Select and copy the text
    textarea.select();
    document.execCommand('copy');
    
    // Remove the temporary element
    document.body.removeChild(textarea);
    
    // Show success message
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg';
    toast.textContent = 'Code copied to clipboard!';
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(tooltip => {
        new Tooltip(tooltip, {
            placement: tooltip.dataset.placement || 'top',
            trigger: tooltip.dataset.trigger || 'hover'
        });
    });
});
</script>
@endpush

@push('styles')
<style>
/* Base styles */
:root {
    --background: 0 0% 100%;
    --foreground: 222.2 84% 4.9%;
    --card: 0 0% 100%;
    --card-foreground: 222.2 84% 4.9%;
    --popover: 0 0% 100%;
    --popover-foreground: 222.2 84% 4.9%;
    --primary: 222.2 47.4% 11.2%;
    --primary-foreground: 210 40% 98%;
    --secondary: 210 40% 96.1%;
    --secondary-foreground: 222.2 47.4% 11.2%;
    --muted: 210 40% 96.1%;
    --muted-foreground: 215.4 16.3% 46.9%;
    --accent: 210 40% 96.1%;
    --accent-foreground: 222.2 47.4% 11.2%;
    --destructive: 0 84.2% 60.2%;
    --destructive-foreground: 210 40% 98%;
    --border: 214.3 31.8% 91.4%;
    --input: 214.3 31.8% 91.4%;
    --ring: 222.2 84% 4.9%;
    --radius: 0.5rem;
}

/* Dark mode styles */
@media (prefers-color-scheme: dark) {
    :root {
        --background: 222.2 84% 4.9%;
        --foreground: 210 40% 98%;
        --card: 222.2 84% 4.9%;
        --card-foreground: 210 40% 98%;
        --popover: 222.2 84% 4.9%;
        --popover-foreground: 210 40% 98%;
        --primary: 210 40% 98%;
        --primary-foreground: 222.2 47.4% 11.2%;
        --secondary: 217.2 32.6% 17.5%;
        --secondary-foreground: 210 40% 98%;
        --muted: 217.2 32.6% 17.5%;
        --muted-foreground: 215 20.2% 65.1%;
        --accent: 217.2 32.6% 17.5%;
        --accent-foreground: 210 40% 98%;
        --destructive: 0 62.8% 30.6%;
        --destructive-foreground: 210 40% 98%;
        --border: 217.2 32.6% 17.5%;
        --input: 217.2 32.6% 17.5%;
        --ring: 212.7 26.8% 83.9%;
    }
}

/* Animations */
@keyframes slideDown {
    from { height: 0; }
    to { height: var(--radix-accordion-content-height); }
}

@keyframes slideUp {
    from { height: var(--radix-accordion-content-height); }
    to { height: 0; }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
}

/* Component styles */
.accordion-content[data-state="open"] {
    animation: slideDown 300ms cubic-bezier(0.87, 0, 0.13, 1);
}

.accordion-content[data-state="closed"] {
    animation: slideUp 300ms cubic-bezier(0.87, 0, 0.13, 1);
}

.toast[data-state="open"] {
    animation: fadeIn 300ms cubic-bezier(0.87, 0, 0.13, 1);
}

.toast[data-state="closed"] {
    animation: fadeOut 300ms cubic-bezier(0.87, 0, 0.13, 1);
}

/* Format-specific styles */
.format-content {
    transition: opacity 0.3s ease-in-out;
}

.format-content.hidden {
    display: none;
}

/* Code preview styles */
.code-preview {
    background-color: #1e1e1e;
    color: #d4d4d4;
    padding: 1rem;
    border-radius: 0.5rem;
    font-family: 'Fira Code', monospace;
    font-size: 0.875rem;
    line-height: 1.5;
    overflow-x: auto;
}

.code-preview .keyword {
    color: #569cd6;
}

.code-preview .string {
    color: #ce9178;
}

.code-preview .comment {
    color: #6a9955;
}

.code-preview .tag {
    color: #569cd6;
}

.code-preview .attribute {
    color: #9cdcfe;
}
</style>
@endpush
@endsection 