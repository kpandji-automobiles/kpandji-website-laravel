<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('cars', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();
            $t->string('name');
            $t->string('category')->nullable();
            $t->text('description')->nullable();
            $t->json('highlights')->nullable();
            $t->json('specs')->nullable();
            $t->unsignedBigInteger('price')->nullable();       // Prix catalogue FCFA (à renseigner)
            $t->unsignedBigInteger('promo_price')->nullable(); // Prix promo FCFA (optionnel, prioritaire sur le %)
            $t->unsignedBigInteger('campaign_price')->nullable(); // TTX CAMPAGNE (fichier TARIF CAMPAGNE V)
            $t->unsignedBigInteger('maintenance_cost')->nullable(); // Coût entretien
            $t->string('warranty')->nullable();
            $t->json('versions')->nullable();
            $t->string('image')->nullable();
            $t->string('hero_image')->nullable();
            $t->string('brochure')->nullable();
            $t->unsignedInteger('position')->default(0);
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('cars'); }
};
