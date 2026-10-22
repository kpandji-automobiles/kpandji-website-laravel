<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
/** Validation des comptes invités (pending -> active | refused) + rôle choisi à l'invitation. */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', fn (Blueprint $t) => $t->string('account_status', 20)->default('active'));
        Schema::table('client_invitations', fn (Blueprint $t) => $t->string('role', 20)->default('client'));
    }
    public function down(): void
    {
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn('account_status'));
        Schema::table('client_invitations', fn (Blueprint $t) => $t->dropColumn('role'));
    }
};
