@extends('visual-builder::layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Configure Social Authentication</h4>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('visual-builder.api.social-auth.configure', $api) }}">
                        @csrf
                        
                        <div class="form-group">
                            <label for="provider">Provider</label>
                            <select name="provider" id="provider" class="form-control @error('provider') is-invalid @enderror" required>
                                <option value="">Select Provider</option>
                                @foreach($oauthProviders as $key => $provider)
                                    <option value="{{ $key }}" {{ old('provider') == $key ? 'selected' : '' }}>
                                        {{ $provider['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('provider')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="client_id">Client ID</label>
                            <input type="text" name="client_id" id="client_id" class="form-control @error('client_id') is-invalid @enderror" value="{{ old('client_id') }}" required>
                            @error('client_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="client_secret">Client Secret</label>
                            <input type="password" name="client_secret" id="client_secret" class="form-control @error('client_secret') is-invalid @enderror" value="{{ old('client_secret') }}" required>
                            @error('client_secret')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="redirect_uri">Redirect URI</label>
                            <input type="url" name="redirect_uri" id="redirect_uri" class="form-control @error('redirect_uri') is-invalid @enderror" value="{{ old('redirect_uri') }}" required>
                            @error('redirect_uri')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="scopes">Scopes</label>
                            <select name="scopes[]" id="scopes" class="form-control @error('scopes') is-invalid @enderror" multiple>
                                @foreach($oauthProviders[old('provider', '')]['scopes'] ?? [] as $scope)
                                    <option value="{{ $scope['id'] }}" {{ in_array($scope['id'], old('scopes', [])) ? 'selected' : '' }}>
                                        {{ $scope['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('scopes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Configure Provider</button>
                        </div>
                    </form>

                    @if($api->auth_providers)
                        <hr>
                        <h5>Configured Providers</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Provider</th>
                                        <th>Client ID</th>
                                        <th>Redirect URI</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($api->auth_providers as $provider => $config)
                                        <tr>
                                            <td>{{ $oauthProviders[$provider]['name'] }}</td>
                                            <td>{{ Str::limit($config['client_id'], 20) }}</td>
                                            <td>{{ Str::limit($config['redirect_uri'], 30) }}</td>
                                            <td>
                                                <form method="POST" action="{{ route('visual-builder.api.social-auth.remove', $api) }}" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="provider" value="{{ $provider }}">
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this provider?')">
                                                        Remove
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-info btn-sm" onclick="testProvider('{{ $provider }}')">
                                                    Test
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function testProvider(provider) {
    fetch(`{{ route('visual-builder.api.social-auth.test', $api) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ provider })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert('Error: ' + data.error);
        } else {
            alert('Success: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error testing provider: ' + error.message);
    });
}

document.getElementById('provider').addEventListener('change', function() {
    const provider = this.value;
    const scopesSelect = document.getElementById('scopes');
    scopesSelect.innerHTML = '';
    
    if (provider && window.oauthProviders[provider]) {
        window.oauthProviders[provider].scopes.forEach(scope => {
            const option = document.createElement('option');
            option.value = scope.id;
            option.textContent = scope.name;
            scopesSelect.appendChild(option);
        });
    }
});
</script>

<script>
window.oauthProviders = @json($oauthProviders);
</script>
@endpush
@endsection 