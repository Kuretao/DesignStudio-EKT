<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $pageId = $this->ensurePage(
            'partneram',
            'Партнерам',
            'For Partners',
            'Страница для партнеров, подрядчиков, поставщиков и девелоперов.',
            'A page for partners, contractors, suppliers, and developers.',
            $now,
        );

        $this->ensureBlock($pageId, 'hero', 10, [
            'eyebrow' => '3D Smart Design',
            'eyebrow_ru' => '3D Smart Design',
            'eyebrow_en' => '3D Smart Design',
            'title' => 'Партнерам',
            'title_ru' => 'Партнерам',
            'title_en' => 'For Partners',
            'subtitle' => 'Открыты к сотрудничеству с поставщиками, подрядчиками, шоурумами, брендами и девелоперами.',
            'subtitle_ru' => 'Открыты к сотрудничеству с поставщиками, подрядчиками, шоурумами, брендами и девелоперами.',
            'subtitle_en' => 'We are open to cooperation with suppliers, contractors, showrooms, brands, and developers.',
            'image' => '/images/cms/country-house-interior.webp',
            'motion_preset' => 'preview',
        ], $now);

        $this->ensureBlock($pageId, 'list', 20, [
            'eyebrow' => 'Форматы',
            'eyebrow_ru' => 'Форматы',
            'eyebrow_en' => 'Formats',
            'title' => 'Где можем быть полезны друг другу',
            'title_ru' => 'Где можем быть полезны друг другу',
            'title_en' => 'Where we can be useful to each other',
            'subtitle' => 'Каждый пункт редактируется отдельной строкой в поле дополнительного текста.',
            'subtitle_ru' => 'Каждый пункт редактируется отдельной строкой в поле дополнительного текста.',
            'subtitle_en' => 'Each item is edited as a separate line in the additional text field.',
            'text' => "Материалы, мебель, свет и декор для комплектации проектов\nПодрядные команды для ремонта, строительства и монтажа\nДевелоперские и коммерческие проекты для визуализации\nКоллаборации с шоурумами, брендами и профильными специалистами",
            'text_ru' => "Материалы, мебель, свет и декор для комплектации проектов\nПодрядные команды для ремонта, строительства и монтажа\nДевелоперские и коммерческие проекты для визуализации\nКоллаборации с шоурумами, брендами и профильными специалистами",
            'text_en' => "Materials, furniture, lighting, and decor for project procurement\nContractor teams for renovation, construction, and installation\nDevelopment and commercial projects for visualization\nCollaborations with showrooms, brands, and industry specialists",
            'card_state' => 'normal',
        ], $now);

        $this->ensureBlock($pageId, 'form', 30, [
            'eyebrow' => 'Обратная связь',
            'eyebrow_ru' => 'Обратная связь',
            'eyebrow_en' => 'Contact',
            'title' => 'Предложить сотрудничество',
            'title_ru' => 'Предложить сотрудничество',
            'title_en' => 'Suggest a partnership',
            'subtitle' => 'Коротко опишите предложение, формат работы и оставьте контакт. Заявка попадет в раздел “Заявки” в CMS.',
            'subtitle_ru' => 'Коротко опишите предложение, формат работы и оставьте контакт. Заявка попадет в раздел “Заявки” в CMS.',
            'subtitle_en' => 'Briefly describe your offer, work format, and leave a contact. The request will appear in the CMS leads section.',
            'link_label' => 'Отправить',
            'link_label_ru' => 'Отправить',
            'link_label_en' => 'Send',
            'settings' => json_encode([
                'namePlaceholder' => 'Ваше имя',
                'companyPlaceholder' => 'Компания или направление',
                'contactPlaceholder' => 'Телефон, e-mail или Telegram',
                'messagePlaceholder' => 'Коротко о предложении',
                'sentLabel' => 'Заявка подготовлена',
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'card_state' => 'featured',
        ], $now);
    }

    public function down(): void
    {
        $pageId = DB::table('pages')->where('slug', 'partneram')->value('id');

        if (! $pageId) {
            return;
        }

        DB::table('page_blocks')
            ->where('page_id', $pageId)
            ->whereIn('type', ['list', 'form'])
            ->whereIn('position', [20, 30])
            ->delete();
    }

    private function ensurePage(string $slug, string $titleRu, string $titleEn, string $textRu, string $textEn, mixed $now): int
    {
        $page = DB::table('pages')->where('slug', $slug)->first();

        if ($page) {
            DB::table('pages')->where('id', $page->id)->update([
                'template' => 'content',
                'title_ru' => DB::raw('COALESCE(title_ru, ' . DB::getPdo()->quote($titleRu) . ')'),
                'title_en' => DB::raw('COALESCE(title_en, ' . DB::getPdo()->quote($titleEn) . ')'),
                'seo_description_ru' => DB::raw('COALESCE(seo_description_ru, ' . DB::getPdo()->quote($textRu) . ')'),
                'seo_description_en' => DB::raw('COALESCE(seo_description_en, ' . DB::getPdo()->quote($textEn) . ')'),
                'updated_at' => $now,
            ]);

            return (int) $page->id;
        }

        return (int) DB::table('pages')->insertGetId([
            'slug' => $slug,
            'title' => $titleRu,
            'title_ru' => $titleRu,
            'title_en' => $titleEn,
            'template' => 'content',
            'seo_description_ru' => $textRu,
            'seo_description_en' => $textEn,
            'is_published' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function ensureBlock(int $pageId, string $type, int $position, array $values, mixed $now): void
    {
        $exists = DB::table('page_blocks')
            ->where('page_id', $pageId)
            ->where('type', $type)
            ->when($type !== 'hero', fn ($query) => $query->where('position', $position))
            ->exists();

        if ($exists) {
            return;
        }

        DB::table('page_blocks')->insert([
            'page_id' => $pageId,
            'type' => $type,
            'position' => $position,
            'is_active' => true,
            'visual_variant' => $values['visual_variant'] ?? 'default',
            'media_position' => $values['media_position'] ?? null,
            'motion_preset' => $values['motion_preset'] ?? 'none',
            'card_state' => $values['card_state'] ?? 'normal',
            'settings' => $values['settings'] ?? null,
            'created_at' => $now,
            'updated_at' => $now,
            ...$values,
        ]);
    }
};
