<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('test_drive_requests', function (Blueprint $t) {
            $t->id();
            $t->foreignId('car_id')->nullable()->constrained()->nullOnDelete();
            $t->string('type')->default('essai'); // essai | information
            $t->string('name');
            $t->string('email')->nullable();
            $t->string('phone');
            $t->date('preferred_date')->nullable();
            $t->text('message')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('test_drive_requests'); }
};
