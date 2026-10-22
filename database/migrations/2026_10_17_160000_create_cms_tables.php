<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('contents')) {
            Schema::create('contents', function (Blueprint $t) {
                $t->id();
                $t->string('key')->unique();
                $t->string('group')->index();
                $t->string('type', 20)->default('text');
                $t->longText('value')->nullable();
                $t->string('label')->nullable();
                $t->unsignedInteger('sort')->default(0);
                $t->timestamps();
            });
        }
        if (! Schema::hasTable('pages')) {
            Schema::create('pages', function (Blueprint $t) {
                $t->id();
                $t->string('slug')->unique();
                $t->string('title');
                $t->string('kicker')->nullable();
                $t->text('intro')->nullable();
                $t->longText('body')->nullable();
                $t->string('hero_image')->nullable();
                $t->string('seo_title')->nullable();
                $t->string('seo_description', 500)->nullable();
                $t->boolean('published')->default(true);
                $t->boolean('in_nav')->default(false);
                $t->unsignedInteger('nav_order')->default(0);
                $t->boolean('is_system')->default(false);
                $t->timestamps();
            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('pages');
        Schema::dropIfExists('contents');
    }
};
