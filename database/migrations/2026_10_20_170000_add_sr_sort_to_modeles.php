<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Ordre des onglets du showroom, indépendant de l'ordre de la section « Modèles » de l'accueil. */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('modeles', fn (Blueprint $t) => $t->unsignedInteger('sr_sort')->default(0));
        DB::table('modeles')->update(['sr_sort' => DB::raw('sort')]);
    }
    public function down(): void { Schema::table('modeles', fn (Blueprint $t) => $t->dropColumn('sr_sort')); }
};
