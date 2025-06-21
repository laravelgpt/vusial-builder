@extends('visual-builder::layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Theme Preview</h3>
        </div>
        <div class="card-body">
            <div class="theme-preview">
                <!-- Colors -->
                <div class="theme-section mb-4">
                    <h4>Colors</h4>
                    <div class="row">
                        @foreach($api->theme_config['colors'] as $name => $color)
                            <div class="col-md-3 mb-3">
                                <div class="color-preview" style="background-color: {{ $color }}; height: 100px;"></div>
                                <div class="mt-2">
                                    <strong>{{ $name }}</strong>
                                    <code>{{ $color }}</code>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Typography -->
                <div class="theme-section mb-4">
                    <h4>Typography</h4>
                    @foreach($api->theme_config['typography'] as $name => $style)
                        <div class="typography-preview mb-3">
                            <h5>{{ $name }}</h5>
                            <div style="
                                font-family: {{ $style['font_family'] ?? 'inherit' }};
                                font-size: {{ $style['font_size'] ?? 'inherit' }};
                                font-weight: {{ $style['font_weight'] ?? 'inherit' }};
                                line-height: {{ $style['line_height'] ?? 'inherit' }};
                                letter-spacing: {{ $style['letter_spacing'] ?? 'inherit' }};
                                text-transform: {{ $style['text_transform'] ?? 'inherit' }};
                            ">
                                The quick brown fox jumps over the lazy dog
                            </div>
                            <small class="text-muted">
                                Font: {{ $style['font_family'] ?? 'inherit' }} |
                                Size: {{ $style['font_size'] ?? 'inherit' }} |
                                Weight: {{ $style['font_weight'] ?? 'inherit' }} |
                                Line Height: {{ $style['line_height'] ?? 'inherit' }} |
                                Letter Spacing: {{ $style['letter_spacing'] ?? 'inherit' }} |
                                Transform: {{ $style['text_transform'] ?? 'inherit' }}
                            </small>
                        </div>
                    @endforeach
                </div>
                
                <!-- Spacing -->
                <div class="theme-section mb-4">
                    <h4>Spacing</h4>
                    <div class="row">
                        @foreach($api->theme_config['spacing'] as $name => $value)
                            <div class="col-md-3 mb-3">
                                <div class="spacing-preview">
                                    <div style="
                                        width: {{ $value }};
                                        height: {{ $value }};
                                        background-color: #e9ecef;
                                        margin: 0 auto;
                                    "></div>
                                    <div class="mt-2 text-center">
                                        <strong>{{ $name }}</strong>
                                        <code>{{ $value }}</code>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Breakpoints -->
                <div class="theme-section mb-4">
                    <h4>Breakpoints</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Value</th>
                                    <th>Preview</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($api->theme_config['breakpoints'] as $name => $value)
                                    <tr>
                                        <td>{{ $name }}</td>
                                        <td><code>{{ $value }}</code></td>
                                        <td>
                                            <div class="breakpoint-preview" style="
                                                width: {{ $value }};
                                                height: 20px;
                                                background-color: #e9ecef;
                                                margin: 0 auto;
                                            "></div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Animations -->
                <div class="theme-section mb-4">
                    <h4>Animations</h4>
                    <div class="row">
                        @foreach($api->theme_config['animations'] as $name => $animation)
                            <div class="col-md-4 mb-3">
                                <div class="animation-preview">
                                    <div class="animated-element" style="
                                        width: 100px;
                                        height: 100px;
                                        background-color: #007bff;
                                        animation: {{ $name }} {{ $animation['duration'] ?? '1s' }} {{ $animation['timing'] ?? 'ease' }} infinite;
                                    "></div>
                                    <div class="mt-2">
                                        <strong>{{ $name }}</strong>
                                        <code>
                                            Duration: {{ $animation['duration'] ?? '1s' }} |
                                            Timing: {{ $animation['timing'] ?? 'ease' }}
                                        </code>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Components -->
                <div class="theme-section mb-4">
                    <h4>Components</h4>
                    @foreach($api->theme_config['components'] as $name => $component)
                        <div class="component-preview mb-4">
                            <h5>{{ $name }}</h5>
                            <div class="component-example">
                                @switch($component['type'])
                                    @case('button')
                                        <button class="btn" style="
                                            background-color: {{ $component['background_color'] ?? '#007bff' }};
                                            color: {{ $component['text_color'] ?? '#ffffff' }};
                                            padding: {{ $component['padding'] ?? '0.375rem 0.75rem' }};
                                            border-radius: {{ $component['border_radius'] ?? '0.25rem' }};
                                            border: {{ $component['border'] ?? 'none' }};
                                        ">
                                            {{ $component['text'] ?? 'Button' }}
                                        </button>
                                        @break
                                        
                                    @case('card')
                                        <div class="card" style="
                                            background-color: {{ $component['background_color'] ?? '#ffffff' }};
                                            border: {{ $component['border'] ?? '1px solid rgba(0,0,0,.125)' }};
                                            border-radius: {{ $component['border_radius'] ?? '0.25rem' }};
                                        ">
                                            <div class="card-body">
                                                <h5 class="card-title" style="
                                                    color: {{ $component['title_color'] ?? '#212529' }};
                                                    font-size: {{ $component['title_size'] ?? '1.25rem' }};
                                                ">
                                                    {{ $component['title'] ?? 'Card Title' }}
                                                </h5>
                                                <p class="card-text" style="
                                                    color: {{ $component['text_color'] ?? '#212529' }};
                                                    font-size: {{ $component['text_size'] ?? '1rem' }};
                                                ">
                                                    {{ $component['text'] ?? 'Card content goes here.' }}
                                                </p>
                                            </div>
                                        </div>
                                        @break
                                        
                                    @case('input')
                                        <input type="text" class="form-control" style="
                                            background-color: {{ $component['background_color'] ?? '#ffffff' }};
                                            border: {{ $component['border'] ?? '1px solid #ced4da' }};
                                            border-radius: {{ $component['border_radius'] ?? '0.25rem' }};
                                            padding: {{ $component['padding'] ?? '0.375rem 0.75rem' }};
                                            color: {{ $component['text_color'] ?? '#495057' }};
                                        " placeholder="{{ $component['placeholder'] ?? 'Input' }}">
                                        @break
                                        
                                    @case('alert')
                                        <div class="alert" style="
                                            background-color: {{ $component['background_color'] ?? '#cce5ff' }};
                                            border: {{ $component['border'] ?? '1px solid #b8daff' }};
                                            border-radius: {{ $component['border_radius'] ?? '0.25rem' }};
                                            color: {{ $component['text_color'] ?? '#004085' }};
                                            padding: {{ $component['padding'] ?? '0.75rem 1.25rem' }};
                                        ">
                                            {{ $component['text'] ?? 'Alert message' }}
                                        </div>
                                        @break
                                @endswitch
                            </div>
                            <div class="component-styles mt-2">
                                <pre><code>{{ json_encode($component, JSON_PRETTY_PRINT) }}</code></pre>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from { transform: translateX(-100%); }
    to { transform: translateX(0); }
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.theme-section {
    padding: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
}

.color-preview {
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
}

.typography-preview {
    padding: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
}

.spacing-preview {
    padding: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
}

.animation-preview {
    padding: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    text-align: center;
}

.component-preview {
    padding: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
}

.component-example {
    margin-bottom: 1rem;
}

.component-styles {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.25rem;
}

pre {
    margin: 0;
    white-space: pre-wrap;
}

code {
    color: #e83e8c;
    background-color: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
}
</style>
@endpush
@endsection 