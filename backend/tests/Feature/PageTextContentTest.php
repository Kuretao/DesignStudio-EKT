<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\PageBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageTextContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_text_page_payload_contains_editor_body(): void
    {
        Page::query()->create([
            'slug' => 'dostavka-i-oplata',
            'title' => 'Доставка и оплата',
            'template' => 'text',
            'body' => '<h2>Доставка</h2><p>Текст из редактора.</p>',
            'is_published' => true,
        ]);

        $this->getJson('/api/v1/pages/dostavka-i-oplata')
            ->assertOk()
            ->assertJsonPath('template', 'text')
            ->assertJsonPath('body', '<h2>Доставка</h2><p>Текст из редактора.</p>');
    }

    public function test_page_block_payload_contains_button_fields(): void
    {
        $page = Page::query()->create([
            'slug' => 'pervyj-ekran',
            'title' => 'Первый экран',
            'template' => 'content',
            'is_published' => true,
        ]);

        PageBlock::query()->create([
            'page_id' => $page->id,
            'type' => 'hero',
            'image' => "page-blocks/hero-1.webp\nhttps://example.com/hero-2.webp",
            'title' => 'Заголовок блока',
            'subtitle' => 'Описание блока',
            'link_label' => 'Открыть',
            'link_href' => '/kontakty',
            'position' => 1,
            'is_active' => true,
        ]);

        $this->getJson('/api/v1/pages/pervyj-ekran')
            ->assertOk()
            ->assertJsonPath('blocks.0.image', '/storage/page-blocks/hero-1.webp')
            ->assertJsonPath('blocks.0.images.1', 'https://example.com/hero-2.webp')
            ->assertJsonPath('blocks.0.title', 'Заголовок блока')
            ->assertJsonPath('blocks.0.subtitle', 'Описание блока')
            ->assertJsonPath('blocks.0.linkLabel', 'Открыть')
            ->assertJsonPath('blocks.0.linkHref', '/kontakty');
    }

    public function test_page_payload_contains_ru_and_en_versions_for_page_and_blocks(): void
    {
        $page = Page::query()->create([
            'slug' => 'o-nas',
            'title_ru' => 'О нас',
            'title_en' => 'About Us',
            'template' => 'about',
            'body_ru' => '<p>Русский текст страницы.</p>',
            'body_en' => '<p>English page text.</p>',
            'seo_title_ru' => 'О нас | 3D Smart Design',
            'seo_title_en' => 'About Us | 3D Smart Design',
            'seo_description_ru' => 'Русское SEO-описание.',
            'seo_description_en' => 'English SEO description.',
            'is_published' => true,
        ]);

        PageBlock::query()->create([
            'page_id' => $page->id,
            'type' => 'hero',
            'eyebrow_ru' => 'Студия интерьера',
            'eyebrow_en' => 'Interior studio',
            'title_ru' => 'О нас',
            'title_en' => 'About Us',
            'subtitle_ru' => 'Русский подзаголовок.',
            'subtitle_en' => 'English subtitle.',
            'text_ru' => 'Русский текст блока.',
            'text_en' => 'English block text.',
            'link_label_ru' => 'Связаться',
            'link_label_en' => 'Contact us',
            'link_href' => '/kontakty',
            'position' => 1,
            'is_active' => true,
        ]);

        $this->getJson('/api/v1/pages/o-nas')
            ->assertOk()
            ->assertJsonPath('titleRu', 'О нас')
            ->assertJsonPath('titleEn', 'About Us')
            ->assertJsonPath('bodyRu', '<p>Русский текст страницы.</p>')
            ->assertJsonPath('bodyEn', '<p>English page text.</p>')
            ->assertJsonPath('seoTitleRu', 'О нас | 3D Smart Design')
            ->assertJsonPath('seoTitleEn', 'About Us | 3D Smart Design')
            ->assertJsonPath('seoDescriptionRu', 'Русское SEO-описание.')
            ->assertJsonPath('seoDescriptionEn', 'English SEO description.')
            ->assertJsonPath('blocks.0.eyebrowRu', 'Студия интерьера')
            ->assertJsonPath('blocks.0.eyebrowEn', 'Interior studio')
            ->assertJsonPath('blocks.0.titleRu', 'О нас')
            ->assertJsonPath('blocks.0.titleEn', 'About Us')
            ->assertJsonPath('blocks.0.subtitleRu', 'Русский подзаголовок.')
            ->assertJsonPath('blocks.0.subtitleEn', 'English subtitle.')
            ->assertJsonPath('blocks.0.textRu', 'Русский текст блока.')
            ->assertJsonPath('blocks.0.textEn', 'English block text.')
            ->assertJsonPath('blocks.0.linkLabelRu', 'Связаться')
            ->assertJsonPath('blocks.0.linkLabelEn', 'Contact us');
    }

    public function test_legal_pages_are_backfilled_with_editable_content(): void
    {
        foreach (['politika-konfidencialnosti', 'user/agreement'] as $slug) {
            $page = Page::query()->where('slug', $slug)->firstOrFail();

            $this->assertSame('legal', $page->template);
            $this->assertNotEmpty($page->fieldRu('body'));

            $this->getJson('/api/v1/pages/'.$slug)
                ->assertOk()
                ->assertJsonPath('slug', $slug)
                ->assertJsonPath('template', 'legal')
                ->assertJsonPath('bodyRu', $page->fieldRu('body'));
        }
    }
}
