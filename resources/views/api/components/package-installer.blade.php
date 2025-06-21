<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="p-6 border-b">
        <h3 class="text-2xl font-semibold">Package Installation</h3>
        <p class="text-gray-500 mt-2">Select your preferred framework to view installation instructions</p>
    </div>

    <!-- Framework Selection -->
    <div class="p-6 border-b">
        <div class="grid grid-cols-4 gap-4">
            <button onclick="selectFramework('blade')" 
                    class="framework-btn p-4 rounded-lg border-2 text-center transition-all hover:shadow-md"
                    data-framework="blade">
                <div class="flex flex-col items-center space-y-2">
                    <svg class="w-8 h-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605" />
                    </svg>
                    <span class="font-medium">Blade</span>
                </div>
            </button>

            <button onclick="selectFramework('livewire')" 
                    class="framework-btn p-4 rounded-lg border-2 text-center transition-all hover:shadow-md"
                    data-framework="livewire">
                <div class="flex flex-col items-center space-y-2">
                    <svg class="w-8 h-8 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                    </svg>
                    <span class="font-medium">Livewire</span>
                </div>
            </button>

            <button onclick="selectFramework('vue')" 
                    class="framework-btn p-4 rounded-lg border-2 text-center transition-all hover:shadow-md"
                    data-framework="vue">
                <div class="flex flex-col items-center space-y-2">
                    <svg class="w-8 h-8 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                    </svg>
                    <span class="font-medium">Vue.js</span>
                </div>
            </button>

            <button onclick="selectFramework('react')" 
                    class="framework-btn p-4 rounded-lg border-2 text-center transition-all hover:shadow-md"
                    data-framework="react">
                <div class="flex flex-col items-center space-y-2">
                    <svg class="w-8 h-8 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                    </svg>
                    <span class="font-medium">React</span>
                </div>
            </button>
        </div>
    </div>

    <!-- Installation Instructions -->
    <div class="p-6">
        <!-- Blade Instructions -->
        <div id="blade-instructions" class="installation-content">
            <div class="prose max-w-none">
                <h4 class="text-lg font-semibold mb-4">Blade Installation</h4>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <pre class="text-sm"><code>composer require your-package-name

// Publish the package assets
php artisan vendor:publish --provider="YourPackage\ServiceProvider"

// Add the following to your .env file
PACKAGE_KEY=your-value</code></pre>
                </div>
                <div class="mt-4">
                    <h5 class="font-medium mb-2">Usage Example:</h5>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <pre class="text-sm"><code>@include('your-package::components.example')</code></pre>
                    </div>
                </div>
            </div>
        </div>

        <!-- Livewire Instructions -->
        <div id="livewire-instructions" class="installation-content hidden">
            <div class="prose max-w-none">
                <h4 class="text-lg font-semibold mb-4">Livewire Installation</h4>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <pre class="text-sm"><code>composer require your-package-name

// Publish the package assets
php artisan vendor:publish --provider="YourPackage\ServiceProvider"

// Add Livewire scripts to your layout
@livewireStyles
@livewireScripts</code></pre>
                </div>
                <div class="mt-4">
                    <h5 class="font-medium mb-2">Usage Example:</h5>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <pre class="text-sm"><code>&lt;livewire:your-component /&gt;</code></pre>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vue.js Instructions -->
        <div id="vue-instructions" class="installation-content hidden">
            <div class="prose max-w-none">
                <h4 class="text-lg font-semibold mb-4">Vue.js Installation</h4>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <pre class="text-sm"><code>npm install your-package-name

// Add to your main.js or app.js
import YourPackage from 'your-package-name'
Vue.use(YourPackage)</code></pre>
                </div>
                <div class="mt-4">
                    <h5 class="font-medium mb-2">Usage Example:</h5>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <pre class="text-sm"><code>&lt;your-component
  :prop1="value1"
  :prop2="value2"
/&gt;</code></pre>
                    </div>
                </div>
            </div>
        </div>

        <!-- React Instructions -->
        <div id="react-instructions" class="installation-content hidden">
            <div class="prose max-w-none">
                <h4 class="text-lg font-semibold mb-4">React Installation</h4>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <pre class="text-sm"><code>npm install your-package-name

// Import in your component
import { YourComponent } from 'your-package-name'</code></pre>
                </div>
                <div class="mt-4">
                    <h5 class="font-medium mb-2">Usage Example:</h5>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <pre class="text-sm"><code>&lt;YourComponent
  prop1={value1}
  prop2={value2}
/&gt;</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function selectFramework(framework) {
    // Update button styles
    document.querySelectorAll('.framework-btn').forEach(btn => {
        btn.classList.remove('border-primary', 'bg-primary/5');
        btn.classList.add('border-gray-200');
    });
    
    const selectedBtn = document.querySelector(`[data-framework="${framework}"]`);
    selectedBtn.classList.remove('border-gray-200');
    selectedBtn.classList.add('border-primary', 'bg-primary/5');
    
    // Show selected instructions
    document.querySelectorAll('.installation-content').forEach(content => {
        content.classList.add('hidden');
    });
    document.getElementById(`${framework}-instructions`).classList.remove('hidden');
}

// Initialize with Blade selected
document.addEventListener('DOMContentLoaded', () => {
    selectFramework('blade');
});
</script>
@endpush

@push('styles')
<style>
.framework-btn {
    transition: all 0.2s ease-in-out;
}

.framework-btn:hover {
    transform: translateY(-2px);
}

.framework-btn.selected {
    border-color: var(--primary);
    background-color: var(--primary-5);
}

.installation-content {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endpush 