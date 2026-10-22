<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('sav_requests', function (Blueprint $t) {
            $t->id(); $t->string('name', 120); $t->string('contact', 160); $t->string('model', 80); $t->text('issue'); $t->string('location', 160);
            $t->string('locale', 5)->nullable(); $t->timestamp('handled_at')->nullable(); $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('sav_requests'); }
};
