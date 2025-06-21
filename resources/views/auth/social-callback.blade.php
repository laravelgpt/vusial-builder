@extends('visual-builder::layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Processing Social Login</h4>
                </div>

                <div class="card-body text-center">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p>Please wait while we process your login...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get the authorization code from the URL
    const urlParams = new URLSearchParams(window.location.search);
    const code = urlParams.get('code');
    const state = urlParams.get('state');
    const error = urlParams.get('error');

    if (error) {
        window.location.href = '{{ route("social.login", ["provider" => $provider]) }}?error=' + encodeURIComponent(error);
        return;
    }

    if (!code || !state) {
        window.location.href = '{{ route("social.login", ["provider" => $provider]) }}?error=Invalid callback parameters';
        return;
    }

    // Send the code to the server
    fetch('{{ route("social.callback", ["provider" => $provider]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            code,
            state
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            window.location.href = '{{ route("social.login", ["provider" => $provider]) }}?error=' + encodeURIComponent(data.error);
        } else {
            window.location.href = data.redirect || '{{ route("home") }}';
        }
    })
    .catch(error => {
        window.location.href = '{{ route("social.login", ["provider" => $provider]) }}?error=' + encodeURIComponent('An error occurred during authentication');
    });
});
</script>
@endpush
@endsection 