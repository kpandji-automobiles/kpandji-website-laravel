<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
/** Espace client v2 : lien compte <-> demandes Sira/Privilège, lecture des réponses, messages système (activation…). */
return new class extends Migration {
    public function up(): void
    {
        foreach (['sira_requests', 'privilege_requests'] as $t)
            Schema::table($t, fn (Blueprint $b) => $b->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete());
        Schema::table('inbox_replies', fn (Blueprint $b) => $b->timestamp('read_at')->nullable());
        Schema::create('client_notices', function (Blueprint $b) {
            $b->id(); $b->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $b->string('subject'); $b->text('body'); $b->timestamp('read_at')->nullable(); $b->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('client_notices');
        Schema::table('inbox_replies', fn (Blueprint $b) => $b->dropColumn('read_at'));
        foreach (['sira_requests', 'privilege_requests'] as $t) Schema::table($t, fn (Blueprint $b) => $b->dropConstrainedForeignId('user_id'));
    }
};
