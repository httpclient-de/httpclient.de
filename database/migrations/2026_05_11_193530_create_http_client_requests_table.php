<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('http_client_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('request_collection_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('method')->default('GET');
            $table->text('url');
            $table->json('headers')->nullable();
            $table->json('query_params')->nullable();
            $table->longText('body')->nullable();
            $table->string('body_type')->default('none'); // none, json, form-data, etc.
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('http_client_requests');
    }
};
