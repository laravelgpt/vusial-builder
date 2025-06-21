@extends('visual-builder::layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Preview</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('visual-builder.api.form.store', $api) }}" method="POST" class="form-preview">
                @csrf
                
                @foreach($api->form_config['fields'] as $field)
                    <div class="form-group">
                        <label for="{{ $field['name'] }}">{{ $field['label'] }}</label>
                        
                        @switch($field['type'])
                            @case('text')
                            @case('email')
                            @case('password')
                                <input type="{{ $field['type'] }}" 
                                       class="form-control @error($field['name']) is-invalid @enderror" 
                                       id="{{ $field['name'] }}" 
                                       name="{{ $field['name'] }}"
                                       value="{{ old($field['name']) }}"
                                       @if($field['required'] ?? false) required @endif
                                       @if($field['placeholder'] ?? false) placeholder="{{ $field['placeholder'] }}" @endif>
                                @break
                                
                            @case('select')
                                <select class="form-control @error($field['name']) is-invalid @enderror" 
                                        id="{{ $field['name'] }}" 
                                        name="{{ $field['name'] }}"
                                        @if($field['required'] ?? false) required @endif>
                                    <option value="">Select {{ $field['label'] }}</option>
                                    @foreach($field['options'] as $option)
                                        <option value="{{ $option['value'] }}" 
                                                {{ old($field['name']) == $option['value'] ? 'selected' : '' }}>
                                            {{ $option['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @break
                                
                            @case('checkbox')
                                <div class="form-check">
                                    <input type="checkbox" 
                                           class="form-check-input @error($field['name']) is-invalid @enderror" 
                                           id="{{ $field['name'] }}" 
                                           name="{{ $field['name'] }}"
                                           value="1"
                                           {{ old($field['name']) ? 'checked' : '' }}
                                           @if($field['required'] ?? false) required @endif>
                                    <label class="form-check-label" for="{{ $field['name'] }}">
                                        {{ $field['label'] }}
                                    </label>
                                </div>
                                @break
                                
                            @case('radio')
                                @foreach($field['options'] as $option)
                                    <div class="form-check">
                                        <input type="radio" 
                                               class="form-check-input @error($field['name']) is-invalid @enderror" 
                                               id="{{ $field['name'] }}_{{ $option['value'] }}" 
                                               name="{{ $field['name'] }}"
                                               value="{{ $option['value'] }}"
                                               {{ old($field['name']) == $option['value'] ? 'checked' : '' }}
                                               @if($field['required'] ?? false) required @endif>
                                        <label class="form-check-label" for="{{ $field['name'] }}_{{ $option['value'] }}">
                                            {{ $option['label'] }}
                                        </label>
                                    </div>
                                @endforeach
                                @break
                                
                            @case('textarea')
                                <textarea class="form-control @error($field['name']) is-invalid @enderror" 
                                          id="{{ $field['name'] }}" 
                                          name="{{ $field['name'] }}"
                                          rows="{{ $field['rows'] ?? 3 }}"
                                          @if($field['required'] ?? false) required @endif
                                          @if($field['placeholder'] ?? false) placeholder="{{ $field['placeholder'] }}" @endif>{{ old($field['name']) }}</textarea>
                                @break
                                
                            @case('file')
                                <input type="file" 
                                       class="form-control-file @error($field['name']) is-invalid @enderror" 
                                       id="{{ $field['name'] }}" 
                                       name="{{ $field['name'] }}"
                                       @if($field['required'] ?? false) required @endif
                                       @if($field['accept'] ?? false) accept="{{ $field['accept'] }}" @endif>
                                @break
                                
                            @case('date')
                            @case('time')
                            @case('datetime-local')
                                <input type="{{ $field['type'] }}" 
                                       class="form-control @error($field['name']) is-invalid @enderror" 
                                       id="{{ $field['name'] }}" 
                                       name="{{ $field['name'] }}"
                                       value="{{ old($field['name']) }}"
                                       @if($field['required'] ?? false) required @endif>
                                @break
                                
                            @case('number')
                                <input type="number" 
                                       class="form-control @error($field['name']) is-invalid @enderror" 
                                       id="{{ $field['name'] }}" 
                                       name="{{ $field['name'] }}"
                                       value="{{ old($field['name']) }}"
                                       @if($field['min'] ?? false) min="{{ $field['min'] }}" @endif
                                       @if($field['max'] ?? false) max="{{ $field['max'] }}" @endif
                                       @if($field['step'] ?? false) step="{{ $field['step'] }}" @endif
                                       @if($field['required'] ?? false) required @endif>
                                @break
                                
                            @case('color')
                                <input type="color" 
                                       class="form-control @error($field['name']) is-invalid @enderror" 
                                       id="{{ $field['name'] }}" 
                                       name="{{ $field['name'] }}"
                                       value="{{ old($field['name'], $field['default'] ?? '#000000') }}"
                                       @if($field['required'] ?? false) required @endif>
                                @break
                                
                            @case('range')
                                <input type="range" 
                                       class="form-control-range @error($field['name']) is-invalid @enderror" 
                                       id="{{ $field['name'] }}" 
                                       name="{{ $field['name'] }}"
                                       value="{{ old($field['name'], $field['default'] ?? 0) }}"
                                       @if($field['min'] ?? false) min="{{ $field['min'] }}" @endif
                                       @if($field['max'] ?? false) max="{{ $field['max'] }}" @endif
                                       @if($field['step'] ?? false) step="{{ $field['step'] }}" @endif
                                       @if($field['required'] ?? false) required @endif>
                                <div class="range-value">{{ old($field['name'], $field['default'] ?? 0) }}</div>
                                @break
                        @endswitch
                        
                        @error($field['name'])
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        @if($field['help'] ?? false)
                            <small class="form-text text-muted">{{ $field['help'] }}</small>
                        @endif
                    </div>
                @endforeach
                
                <div class="form-actions">
                    @foreach($api->form_config['actions'] ?? [] as $action)
                        <button type="{{ $action['type'] ?? 'submit' }}" 
                                class="btn btn-{{ $action['style'] ?? 'primary' }}"
                                @if($action['name'] ?? false) name="{{ $action['name'] }}" @endif
                                @if($action['value'] ?? false) value="{{ $action['value'] }}" @endif>
                            {{ $action['label'] }}
                        </button>
                    @endforeach
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle range input value display
    document.querySelectorAll('input[type="range"]').forEach(function(input) {
        const valueDisplay = input.nextElementSibling;
        input.addEventListener('input', function() {
            valueDisplay.textContent = this.value;
        });
    });
});
</script>
@endpush
@endsection 