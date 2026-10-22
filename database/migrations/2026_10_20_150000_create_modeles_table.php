<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('modeles', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique();            // id d'origine (MODELES)
            $t->string('name');
            $t->string('car_slug')->nullable();     // page modèle Laravel (/modeles/{slug})
            $t->text('description_fr')->nullable();
            $t->text('description_en')->nullable();
            $t->json('characteristics')->nullable(); // [{fr, en}]
            $t->string('top_image')->nullable();
            $t->string('top_video')->nullable();
            $t->string('bottom_left_image')->nullable();
            $t->string('bottom_right_image')->nullable();
            $t->unsignedInteger('sort')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('modeles'); }
};
