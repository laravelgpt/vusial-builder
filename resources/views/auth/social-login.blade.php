@extends('visual-builder::layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Login with Social Account</h4>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row">
                        @foreach($api->auth_providers as $provider => $config)
                            <div class="col-md-6 mb-3">
                                <x-social-login-button
                                    :provider="$provider"
                                    :name="$oauthProviders[$provider]['name']"
                                    :icon="$oauthProviders[$provider]['icon']"
                                    :color="$oauthProviders[$provider]['color']"
                                />
                            </div>
                        @endforeach
                    </div>

                    <div class="text-center mt-3">
                        <p>Or</p>
                        <a href="{{ route('login') }}" class="btn btn-link">
                            Login with Email
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 