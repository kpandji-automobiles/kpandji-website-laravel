<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
/** Espaces Manager & Client : rôles, invitations, investissements, écritures de revenus, boîte de réception unifiée. */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->string('role', 20)->default('user')->index();
            $t->string('phone', 40)->nullable();
        });
        DB::table('users')->where('is_admin', 1)->update(['role' => 'admin']);
        Schema::create('client_invitations', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('email'); $t->string('phone', 40)->nullable();
            $t->string('token_hash', 64)->index();
            $t->foreignId('invited_by')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('expires_at'); $t->timestamp('accepted_at')->nullable(); $t->timestamp('revoked_at')->nullable();
            $t->timestamp('last_sent_at')->nullable(); $t->unsignedInteger('send_count')->default(1); $t->string('mail_status', 20)->nullable();
            $t->timestamps();
        });
        Schema::create('investments', function (Blueprint $t) {
            $t->id(); $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $t->string('label'); $t->string('program', 30)->nullable(); $t->unsignedBigInteger('amount')->default(0);
            $t->date('start_date')->nullable(); $t->string('status', 20)->default('actif'); $t->text('notes')->nullable();
            $t->timestamps();
        });
        Schema::create('revenue_entries', function (Blueprint $t) {
            $t->id(); $t->foreignId('investment_id')->constrained('investments')->cascadeOnDelete();
            $t->date('entry_date'); $t->unsignedBigInteger('amount'); $t->string('type', 20); $t->string('description')->nullable();
            $t->timestamps(); $t->index(['investment_id', 'entry_date']);
        });
        Schema::create('client_requests', function (Blueprint $t) {
            $t->id(); $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('investment_id')->nullable()->constrained('investments')->nullOnDelete();
            $t->string('subject'); $t->text('message'); $t->timestamp('handled_at')->nullable(); $t->timestamps();
        });
        Schema::create('inbox_statuses', function (Blueprint $t) {
            $t->id(); $t->string('source', 20); $t->unsignedBigInteger('source_id'); $t->string('status', 20)->default('nouveau');
            $t->timestamps(); $t->unique(['source', 'source_id']);
        });
        Schema::create('inbox_replies', function (Blueprint $t) {
            $t->id(); $t->string('source', 20); $t->unsignedBigInteger('source_id');
            $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('to_email')->nullable(); $t->text('body'); $t->string('mail_status', 20)->nullable();
            $t->timestamps(); $t->index(['source', 'source_id']);
        });
    }
    public function down(): void
    {
        foreach (['inbox_replies', 'inbox_statuses', 'client_requests', 'revenue_entries', 'investments', 'client_invitations'] as $t) Schema::dropIfExists($t);
        Schema::table('users', function (Blueprint $t) { $t->dropIndex(['role']); $t->dropColumn(['role', 'phone']); });
    }
};
