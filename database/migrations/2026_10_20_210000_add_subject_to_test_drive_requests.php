<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
/* /contact (ContactForm.tsx) réutilise test_drive_requests (type « contact ») : ajoute le sujet. Lignes existantes conservées. */
return new class extends Migration {
    public function up(): void { Schema::table('test_drive_requests', fn (Blueprint $t) => $t->string('subject', 200)->nullable()->after('phone')); }
    public function down(): void { Schema::table('test_drive_requests', fn (Blueprint $t) => $t->dropColumn('subject')); }
};
