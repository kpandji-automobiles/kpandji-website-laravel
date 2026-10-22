<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        if (!Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', fn (Blueprint $t) => $t->boolean('is_admin')->default(false));
        }
        if (!Schema::hasColumn('cars', 'is_active')) {
            Schema::table('cars', function (Blueprint $t) {
                $t->boolean('is_active')->default(true);
                $t->unsignedInteger('sort_order')->default(0);
            });
            DB::table('cars')->update(['sort_order' => DB::raw('position')]);
        }
        if (!Schema::hasColumn('test_drive_requests', 'handled_at')) {
            Schema::table('test_drive_requests', fn (Blueprint $t) => $t->timestamp('handled_at')->nullable());
        }
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $t) {
                $t->string('key')->primary();
                $t->text('value')->nullable();
                $t->timestamps();
            });
        }
    }
    public function down(): void {
        Schema::dropIfExists('settings');
        Schema::table('test_drive_requests', fn (Blueprint $t) => $t->dropColumn('handled_at'));
        Schema::table('cars', fn (Blueprint $t) => $t->dropColumn(['is_active', 'sort_order']));
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn('is_admin'));
    }
};
