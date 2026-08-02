<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', static function (Blueprint $table): void {
            if (! Schema::hasColumn('services', 'is_home_item')) {
                $table->boolean('is_home_item')->default(false)->after('is_published');
            }
        });

        DB::table('services')
            ->whereIn('slug', [
                'arhitekturnoe-proektirovanie',
                'arhitekturnaya-3d-vizualizaciya',
                'dizajn-interyera',
                'komplektaciya-ob-ekta',
                'landshaftnyj-dizajn',
                'landshaftnoe-proektirovanie-i-genplan',
                'avtorskij-nadzor',
            ])
            ->update(['is_home_item' => true]);
    }

    public function down(): void
    {
        Schema::table('services', static function (Blueprint $table): void {
            if (Schema::hasColumn('services', 'is_home_item')) {
                $table->dropColumn('is_home_item');
            }
        });
    }
};
