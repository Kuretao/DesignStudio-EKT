<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('partners')
            ->whereNotNull('name')
            ->where(function ($query): void {
                $query->whereNull('name_ru')
                    ->orWhereColumn('name_ru', '!=', 'name');
            })
            ->update([
                'name_ru' => DB::raw('name'),
                'updated_at' => now(),
            ]);

        DB::table('partners')
            ->whereNotNull('note')
            ->where(function ($query): void {
                $query->whereNull('note_ru')
                    ->orWhereColumn('note_ru', '!=', 'note');
            })
            ->update([
                'note_ru' => DB::raw('note'),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Do not roll content back: editors may change these fields after deploy.
    }
};
