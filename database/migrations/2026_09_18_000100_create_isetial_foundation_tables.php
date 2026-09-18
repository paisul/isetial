<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('masjids', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->string('photo')->nullable();
            $table->text('description')->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('member_number')->unique();
            $table->foreignId('home_masjid_id')->constrained('masjids')->restrictOnDelete();
            $table->date('joined_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });
        Schema::create('role_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('masjid_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'role_id', 'masjid_id']);
        });
        Schema::create('organization_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('masjid_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->date('starts_at');
            $table->date('ends_at')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('masjid_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->enum('context_type', ['isetial', 'dkm']);
            $table->foreignId('masjid_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();
        });
        Schema::create('position_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('position_id')->constrained()->cascadeOnDelete();
            $table->foreignId('division_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('masjid_id')->nullable()->constrained()->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();
            $table->index(['masjid_id', 'is_active']);
        });
        Schema::create('guidelines', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->boolean('published')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('masjid_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->longText('description')->nullable();
            $table->dateTime('event_at');
            $table->string('location')->nullable();
            $table->string('photo')->nullable();
            $table->enum('status', ['draft', 'scheduled', 'completed', 'cancelled'])->default('draft');
            $table->boolean('published')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['masjid_id', 'slug']);
            $table->index(['masjid_id', 'published', 'event_at']);
        });
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('masjid_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->boolean('published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assigned_to')->constrained('people')->cascadeOnDelete();
            $table->foreignId('masjid_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->date('due_at')->nullable();
            $table->enum('status', ['todo', 'doing', 'done'])->default('todo');
            $table->timestamps();
        });
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('masjid_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();
            $table->unique(['masjid_id', 'key']);
        });
    }

    public function down(): void
    {
        foreach (['settings', 'tasks', 'announcements', 'activities', 'guidelines', 'position_assignments', 'positions', 'divisions', 'organization_periods', 'role_assignments', 'roles', 'memberships', 'masjids'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
