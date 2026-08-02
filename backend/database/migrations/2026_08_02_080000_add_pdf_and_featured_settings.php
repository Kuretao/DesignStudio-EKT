<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            if (! Schema::hasColumn('site_settings', 'lead_notification_email')) {
                $table->string('lead_notification_email')->nullable()->after('emails');
            }
        });

        Schema::table('services', function (Blueprint $table): void {
            if (! Schema::hasColumn('services', 'pdf_file')) {
                $table->string('pdf_file')->nullable()->after('hero_images');
            }

            if (! Schema::hasColumn('services', 'pdf_title_ru')) {
                $table->string('pdf_title_ru')->nullable()->after('pdf_file');
                $table->string('pdf_title_en')->nullable()->after('pdf_title_ru');
            }
        });

        Schema::table('projects', function (Blueprint $table): void {
            if (! Schema::hasColumn('projects', 'featured_label_ru')) {
                $table->string('featured_label_ru')->nullable()->after('is_featured');
                $table->string('featured_label_en')->nullable()->after('featured_label_ru');
                $table->string('featured_title_ru')->nullable()->after('featured_label_en');
                $table->string('featured_title_en')->nullable()->after('featured_title_ru');
                $table->text('featured_description_ru')->nullable()->after('featured_title_en');
                $table->text('featured_description_en')->nullable()->after('featured_description_ru');
                $table->text('featured_image')->nullable()->after('featured_description_en');
                $table->string('featured_image_file')->nullable()->after('featured_image');
            }
        });

        DB::table('site_settings')
            ->whereNull('lead_notification_email')
            ->orWhere('lead_notification_email', '')
            ->update([
                'lead_notification_email' => env('LEAD_NOTIFICATION_TO') ?: 'info@3dsmartdesign.ru',
            ]);
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            $table->dropColumn([
                'featured_label_ru',
                'featured_label_en',
                'featured_title_ru',
                'featured_title_en',
                'featured_description_ru',
                'featured_description_en',
                'featured_image',
                'featured_image_file',
            ]);
        });

        Schema::table('services', function (Blueprint $table): void {
            $table->dropColumn(['pdf_file', 'pdf_title_ru', 'pdf_title_en']);
        });

        Schema::table('site_settings', function (Blueprint $table): void {
            $table->dropColumn('lead_notification_email');
        });
    }
};
