@extends('visual-builder::layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Table Preview</h3>
            <div class="card-tools">
                @if($api->table_config['features']['search'] ?? false)
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="card-body">
            @if($api->table_config['features']['filters'] ?? false)
                <div class="table-filters mb-3">
                    <form action="{{ route('visual-builder.api.table.filter', $api) }}" method="GET" class="form-inline">
                        @foreach($api->table_config['filters'] as $filter)
                            <div class="form-group mx-2">
                                <label for="filter_{{ $filter['name'] }}" class="mr-2">{{ $filter['label'] }}</label>
                                @switch($filter['type'])
                                    @case('select')
                                        <select class="form-control form-control-sm" 
                                                id="filter_{{ $filter['name'] }}" 
                                                name="filters[{{ $filter['name'] }}]">
                                            <option value="">All</option>
                                            @foreach($filter['options'] as $option)
                                                <option value="{{ $option['value'] }}"
                                                        {{ request('filters.'.$filter['name']) == $option['value'] ? 'selected' : '' }}>
                                                    {{ $option['label'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @break
                                        
                                    @case('date')
                                        <input type="date" 
                                               class="form-control form-control-sm" 
                                               id="filter_{{ $filter['name'] }}" 
                                               name="filters[{{ $filter['name'] }}]"
                                               value="{{ request('filters.'.$filter['name']) }}">
                                        @break
                                        
                                    @case('number')
                                        <input type="number" 
                                               class="form-control form-control-sm" 
                                               id="filter_{{ $filter['name'] }}" 
                                               name="filters[{{ $filter['name'] }}]"
                                               value="{{ request('filters.'.$filter['name']) }}"
                                               @if($filter['min'] ?? false) min="{{ $filter['min'] }}" @endif
                                               @if($filter['max'] ?? false) max="{{ $filter['max'] }}" @endif>
                                        @break
                                        
                                    @default
                                        <input type="text" 
                                               class="form-control form-control-sm" 
                                               id="filter_{{ $filter['name'] }}" 
                                               name="filters[{{ $filter['name'] }}]"
                                               value="{{ request('filters.'.$filter['name']) }}"
                                               placeholder="{{ $filter['placeholder'] ?? '' }}">
                                @endswitch
                            </div>
                        @endforeach
                        <button type="submit" class="btn btn-primary btn-sm">Apply Filters</button>
                        <a href="{{ route('visual-builder.api.table.preview', $api) }}" class="btn btn-secondary btn-sm ml-2">Reset</a>
                    </form>
                </div>
            @endif
            
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            @if($api->table_config['features']['row_selection'] ?? false)
                                <th>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="select-all">
                                        <label class="custom-control-label" for="select-all"></label>
                                    </div>
                                </th>
                            @endif
                            
                            @foreach($api->table_config['columns'] as $column)
                                <th>
                                    {{ $column['label'] }}
                                    @if(($api->table_config['features']['sort'] ?? false) && ($column['sortable'] ?? true))
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => $column['name'], 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" 
                                           class="sort-link">
                                            <i class="fas fa-sort{{ request('sort') == $column['name'] ? '-'.request('direction') : '' }}"></i>
                                        </a>
                                    @endif
                                </th>
                            @endforeach
                            
                            @if($api->table_config['features']['actions'] ?? false)
                                <th>Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $item)
                            <tr>
                                @if($api->table_config['features']['row_selection'] ?? false)
                                    <td>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" 
                                                   class="custom-control-input" 
                                                   id="select-{{ $item->id }}"
                                                   name="selected[]"
                                                   value="{{ $item->id }}">
                                            <label class="custom-control-label" for="select-{{ $item->id }}"></label>
                                        </div>
                                    </td>
                                @endif
                                
                                @foreach($api->table_config['columns'] as $column)
                                    <td>
                                        @if($api->table_config['features']['inline_edit'] ?? false)
                                            <div class="editable" 
                                                 data-field="{{ $column['name'] }}"
                                                 data-id="{{ $item->id }}"
                                                 data-type="{{ $column['type'] ?? 'text' }}">
                                                {{ $item->{$column['name']} }}
                                            </div>
                                        @else
                                            {{ $item->{$column['name']} }}
                                        @endif
                                    </td>
                                @endforeach
                                
                                @if($api->table_config['features']['actions'] ?? false)
                                    <td>
                                        <div class="btn-group">
                                            @foreach($api->table_config['actions'] as $action)
                                                <a href="{{ route($action['route'], $item->id) }}" 
                                                   class="btn btn-sm btn-{{ $action['style'] ?? 'primary' }}"
                                                   @if($action['confirm'] ?? false)
                                                   onclick="return confirm('{{ $action['confirm_message'] ?? 'Are you sure?' }}')"
                                                   @endif>
                                                    @if($action['icon'] ?? false)
                                                        <i class="{{ $action['icon'] }}"></i>
                                                    @endif
                                                    {{ $action['label'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($api->table_config['features']['pagination'] ?? false)
                <div class="mt-3">
                    {{ $data->links() }}
                </div>
            @endif
            
            @if($api->table_config['features']['bulk_actions'] ?? false)
                <div class="bulk-actions mt-3">
                    <form action="{{ route('visual-builder.api.table.bulk-action', $api) }}" method="POST" class="form-inline">
                        @csrf
                        <select name="action" class="form-control form-control-sm mr-2">
                            <option value="">Bulk Actions</option>
                            @foreach($api->table_config['bulk_actions'] as $action)
                                <option value="{{ $action['name'] }}">{{ $action['label'] }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                    </form>
                </div>
            @endif
            
            @if($api->table_config['features']['export'] ?? false)
                <div class="export-actions mt-3">
                    <div class="btn-group">
                        @foreach($api->table_config['export_config']['formats'] as $format)
                            <a href="{{ route('visual-builder.api.table.export', [$api, 'format' => $format]) }}" 
                               class="btn btn-sm btn-secondary">
                                Export as {{ strtoupper($format) }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle select all checkbox
    const selectAll = document.getElementById('select-all');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('input[name="selected[]"]').forEach(function(checkbox) {
                checkbox.checked = selectAll.checked;
            });
        });
    }
    
    // Handle inline editing
    document.querySelectorAll('.editable').forEach(function(element) {
        element.addEventListener('click', function() {
            const field = this.dataset.field;
            const id = this.dataset.id;
            const type = this.dataset.type;
            const value = this.textContent.trim();
            
            let input;
            switch (type) {
                case 'select':
                    input = document.createElement('select');
                    // Add options based on column configuration
                    break;
                    
                case 'textarea':
                    input = document.createElement('textarea');
                    input.rows = 3;
                    break;
                    
                default:
                    input = document.createElement('input');
                    input.type = type;
            }
            
            input.value = value;
            input.className = 'form-control form-control-sm';
            
            this.textContent = '';
            this.appendChild(input);
            input.focus();
            
            input.addEventListener('blur', function() {
                const newValue = this.value;
                element.textContent = newValue;
                
                // Send update to server
                fetch(`/api/table/${id}/update`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        field: field,
                        value: newValue
                    })
                });
            });
        });
    });
});
</script>
@endpush
@endsection 