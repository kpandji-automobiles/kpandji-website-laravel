<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
/** Messages écrits par un manager à des utilisateurs : une conversation par destinataire (réponses dans inbox_replies, source « dm »). */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();
            $t->string('subject'); $t->text('body'); $t->string('batch', 40)->nullable()->index();
            $t->timestamp('read_at')->nullable(); $t->string('mail_status', 20)->nullable();
            $t->timestamps(); $t->index(['recipient_id', 'read_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('conversations'); }
};
