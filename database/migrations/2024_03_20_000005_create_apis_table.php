<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apis', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('model');
            $table->string('type')->default('rest'); // REST, CURL, GraphQL, etc.
            $table->string('builder_type')->default('api');
            $table->json('fields');
            $table->json('relationships')->nullable();
            $table->json('validation_rules')->nullable();
            $table->json('endpoints')->nullable();
            $table->json('documentation')->nullable();
            $table->string('auth_type')->default('none');
            $table->json('auth_providers')->nullable();
            $table->json('auth_config')->nullable();
            $table->json('rate_limit')->nullable();
            $table->json('middleware')->nullable();
            $table->json('ai_suggestions')->nullable();
            $table->json('form_config')->nullable();
            $table->json('table_config')->nullable();
            $table->json('ui_config')->nullable();
            $table->json('theme_config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['name', 'model']);
        });

        Schema::create('api_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_id')->constrained()->onDelete('cascade');
            $table->string('version');
            $table->json('changes')->nullable();
            $table->json('documentation')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_deprecated')->default(false);
            $table->timestamp('deprecated_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['api_id', 'version']);
        });

        // Create social authentication tables
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('provider');
            $table->string('provider_id');
            $table->string('token');
            $table->string('refresh_token')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('user_data')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'provider_id']);
        });

        Schema::create('oauth_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_id')->constrained()->onDelete('cascade');
            $table->string('provider');
            $table->string('client_id');
            $table->string('client_secret');
            $table->json('scopes')->nullable();
            $table->json('redirect_uris')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['api_id', 'provider']);
        });

        Schema::create('form_builders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('form');
            $table->json('fields');
            $table->json('validation_rules')->nullable();
            $table->json('layout')->nullable();
            $table->json('actions')->nullable();
            $table->json('permissions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('table_builders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('model');
            $table->json('columns');
            $table->json('filters')->nullable();
            $table->json('actions')->nullable();
            $table->json('export_config')->nullable();
            $table->json('permissions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ui_components', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->json('props')->nullable();
            $table->json('slots')->nullable();
            $table->json('events')->nullable();
            $table->json('styles')->nullable();
            $table->json('variants')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('light');
            $table->json('colors')->nullable();
            $table->json('typography')->nullable();
            $table->json('spacing')->nullable();
            $table->json('breakpoints')->nullable();
            $table->json('animations')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('themes');
        Schema::dropIfExists('ui_components');
        Schema::dropIfExists('table_builders');
        Schema::dropIfExists('form_builders');
        Schema::dropIfExists('oauth_clients');
        Schema::dropIfExists('social_accounts');
        Schema::dropIfExists('api_versions');
        Schema::dropIfExists('apis');
    }
}; 