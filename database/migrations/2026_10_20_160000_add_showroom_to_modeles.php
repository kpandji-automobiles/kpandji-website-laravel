<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Showroom virtuel (/showroom) — port de components/kp/VirtualShowroom.tsx */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('modeles', function (Blueprint $t) {
            $t->boolean('in_showroom')->default(false);
            $t->string('badge_fr')->nullable();
            $t->string('badge_en')->nullable();
            $t->string('tagline_fr')->nullable();
            $t->string('tagline_en')->nullable();
            $t->text('sr_description_fr')->nullable();
            $t->text('sr_description_en')->nullable();
            $t->string('brochure')->nullable();
            $t->json('highlights')->nullable(); // [{fr,en}]
            $t->json('gallery')->nullable();    // [{src,alt,tag:ext|int}]
        });
    }
    public function down(): void
    {
        Schema::table('modeles', fn (Blueprint $t) => $t->dropColumn(['in_showroom', 'badge_fr', 'badge_en', 'tagline_fr', 'tagline_en', 'sr_description_fr', 'sr_description_en', 'brochure', 'highlights', 'gallery']));
    }
};
