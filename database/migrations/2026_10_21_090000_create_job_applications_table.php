<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
/* Candidatures de /emplois (JobApplicationForm.tsx). CV stocké en privé (storage/app/private/candidatures). */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $t) {
            $t->id();
            $t->string('full_name', 120); $t->string('email', 160); $t->string('phone', 40)->nullable();
            $t->string('position', 200); $t->text('cv_summary'); $t->text('education')->nullable();
            $t->text('skills'); $t->string('languages', 300)->nullable(); $t->text('extras')->nullable();
            $t->string('cv_path')->nullable(); $t->string('cv_name')->nullable(); $t->unsignedInteger('cv_size')->nullable();
            $t->string('locale', 5)->nullable(); $t->string('ip', 45)->nullable();
            $t->timestamp('handled_at')->nullable(); $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('job_applications'); }
};
