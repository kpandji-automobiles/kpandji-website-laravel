<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('privilege_requests', function (Blueprint $t) {
            $t->id(); $t->string('source', 20)->default('contact'); $t->string('name', 120); $t->string('country', 120); $t->string('city', 120);
            $t->string('phone', 40); $t->string('email', 160); $t->string('locale', 5)->nullable();
            $t->timestamp('handled_at')->nullable(); $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('privilege_requests'); }
};
