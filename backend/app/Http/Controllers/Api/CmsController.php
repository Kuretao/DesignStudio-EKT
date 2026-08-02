<?php

namespace App\Http\Controllers\Api;

use App\Mail\LeadCreated;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Controllers\Controller;
use App\Models\Award;
use App\Models\Faq;
use App\Models\Lead;
use App\Models\MenuItem;
use App\Models\NewsArticle;
use App\Models\Page;
use App\Models\Partner;
use App\Models\Project;
use App\Models\Promo;
use App\Models\Review;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\UiText;
use App\Models\Vacancy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class CmsController extends Controller
{
    public function bootstrap(): JsonResponse
    {
        $settings = SiteSetting::query()->first();

        return response()->json([
            'settings' => $this->settings($settings),
            'uiTexts' => $this->uiTexts(),
            'menuItems' => $this->menuItems(),
            'serviceNavigationGroups' => $this->serviceNavigationGroups(),
        ]);
    }

    public function all(): JsonResponse
    {
        return response()->json([
            'settings' => $this->settings(SiteSetting::query()->first()),
            'uiTexts' => $this->uiTexts(),
            'menuItems' => $this->menuItems(),
            'serviceNavigationGroups' => $this->serviceNavigationGroups(),
            'pages' => Page::query()->where('is_published', true)->with(['blocks' => fn ($query) => $query->where('is_active', true)])->get()->map(fn (Page $page) => $this->pagePayload($page))->values(),
            'projects' => Project::query()->where('is_published', true)->orderBy('position')->get()->map(fn (Project $project) => $this->projectPayload($project))->values(),
            'services' => Service::query()->where('is_published', true)->orderBy('position')->get()->map(fn (Service $service) => $this->servicePayload($service))->values(),
            'news' => NewsArticle::query()->where('is_published', true)->orderBy('position')->get()->map(fn (NewsArticle $article) => $this->newsPayload($article))->values(),
            'promos' => Promo::query()->where('is_active', true)->orderBy('position')->get()->map(fn (Promo $promo) => $this->promoPayload($promo))->values(),
            'awards' => Award::query()->where('is_active', true)->orderBy('position')->get()->map(fn (Award $award) => $this->awardPayload($award))->values(),
            'partners' => Partner::query()->where('is_active', true)->orderBy('position')->get()->map(fn (Partner $partner) => $this->partnerPayload($partner))->values(),
            'reviews' => Review::query()->where('is_published', true)->orderBy('position')->get()->map(fn (Review $review) => $this->reviewPayload($review))->values(),
            'faqs' => Faq::query()->where('is_published', true)->orderBy('position')->get()->map(fn (Faq $faq) => $this->faqPayload($faq))->values(),
            'vacancies' => Vacancy::query()->where('is_active', true)->orderBy('position')->get()->map(fn (Vacancy $vacancy) => $this->vacancyPayload($vacancy))->values(),
        ]);
    }

    public function page(string $slug): JsonResponse
    {
        $page = Page::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->with(['blocks' => fn ($query) => $query->where('is_active', true)])
            ->firstOrFail();

        return response()->json($this->pagePayload($page));
    }

    public function projects(): JsonResponse
    {
        return response()->json(Project::query()->where('is_published', true)->orderBy('position')->get()->map(fn (Project $project) => $this->projectPayload($project))->values());
    }

    public function project(string $slug): JsonResponse
    {
        return response()->json($this->projectPayload(Project::query()->where('slug', $slug)->where('is_published', true)->firstOrFail()));
    }

    public function services(): JsonResponse
    {
        return response()->json(Service::query()->where('is_published', true)->orderBy('position')->get()->map(fn (Service $service) => $this->servicePayload($service))->values());
    }

    public function service(string $slug): JsonResponse
    {
        return response()->json($this->servicePayload(Service::query()->where('slug', $slug)->where('is_published', true)->firstOrFail()));
    }

    public function news(): JsonResponse
    {
        return response()->json(NewsArticle::query()->where('is_published', true)->orderBy('position')->get()->map(fn (NewsArticle $article) => $this->newsPayload($article))->values());
    }

    public function newsArticle(string $slug): JsonResponse
    {
        return response()->json($this->newsPayload(NewsArticle::query()->where('slug', $slug)->where('is_published', true)->firstOrFail()));
    }

    public function promos(): JsonResponse
    {
        return response()->json(Promo::query()->where('is_active', true)->orderBy('position')->get()->map(fn (Promo $promo) => $this->promoPayload($promo))->values());
    }

    public function awards(): JsonResponse
    {
        return response()->json(Award::query()->where('is_active', true)->orderBy('position')->get()->map(fn (Award $award) => $this->awardPayload($award))->values());
    }

    public function partners(): JsonResponse
    {
        return response()->json(Partner::query()->where('is_active', true)->orderBy('position')->get()->map(fn (Partner $partner) => $this->partnerPayload($partner))->values());
    }

    public function reviews(): JsonResponse
    {
        return response()->json(Review::query()->where('is_published', true)->orderBy('position')->get()->map(fn (Review $review) => $this->reviewPayload($review))->values());
    }

    public function faqs(): JsonResponse
    {
        return response()->json(Faq::query()->where('is_published', true)->orderBy('position')->get()->map(fn (Faq $faq) => $this->faqPayload($faq))->values());
    }

    public function storeLead(StoreLeadRequest $request): JsonResponse
    {
        $lead = Lead::query()->create([
            'source' => $request->string('source')->value() ?: 'site',
            'channel' => $request->input('channel'),
            'name' => $request->input('name'),
            'contact' => $request->input('contact'),
            'service' => $request->input('service'),
            'message' => $request->input('message'),
            'payload' => $request->input('payload'),
        ]);

        $this->sendLeadNotification($lead);

        return response()->json(['id' => $lead->id, 'status' => 'ok'], 201);
    }

    private function sendLeadNotification(Lead $lead): void
    {
        $recipient = SiteSetting::query()->value('lead_notification_email')
            ?: config('mail.leads.to');

        if (blank($recipient)) {
            return;
        }

        try {
            Mail::to($recipient)->send(new LeadCreated($lead));
        } catch (Throwable $exception) {
            Log::error('Lead notification email failed.', [
                'lead_id' => $lead->id,
                'recipient' => $recipient,
                'exception' => $exception,
            ]);
        }
    }

    private function settings(?SiteSetting $settings): array
    {
        return [
            'siteName' => $settings?->site_name ?? '3D Smart Design Studio',
            'logo' => $this->storageAsset($settings?->site_logo),
            'logoSmall' => $this->storageAsset($settings?->site_logo_small),
            'favicon' => $this->storageAsset($settings?->favicon),
            'appleTouchIcon' => $this->storageAsset($settings?->apple_touch_icon),
            'socialPreviewImage' => $this->storageAsset($settings?->social_preview_image),
            'updatedAt' => $settings?->updated_at?->timestamp,
            'phone' => $settings?->phone,
            'phoneHref' => $settings?->phone_href,
            'emails' => $this->lines($settings?->emails),
            'leadNotificationEmail' => $settings?->lead_notification_email,
            'schedule' => $settings?->schedule,
            'address' => $settings?->address,
            'mapSrc' => $settings?->map_src,
            'seoTitle' => $settings?->seo_title,
            'seoDescription' => $settings?->seo_description,
            'compareEyebrow' => $settings?->compare_eyebrow,
            'compareTitle' => $settings?->compare_title,
            'compareText' => $settings?->compare_text,
            'messengers' => [
                'telegram' => $settings?->telegram_url,
                'max' => $settings?->max_url,
                'vk' => $settings?->vk_url,
                'phoneHref' => $settings?->phone_href,
            ],
            'animations' => [
                'enabled' => $settings?->animations_enabled ?? true,
                'smoothScroll' => $settings?->smooth_scroll_enabled ?? true,
                'pageReveal' => $settings?->page_reveal_enabled ?? true,
            ],
            'socials' => [
                'vk' => $settings?->vk_url,
                'linkedin' => $settings?->linkedin_url,
                'behance' => $settings?->behance_url,
                'pinterest' => $settings?->pinterest_url,
            ],
        ];
    }

    private function uiTexts(): array
    {
        return UiText::query()
            ->where('is_active', true)
            ->orderBy('position')
            ->get()
            ->mapWithKeys(fn (UiText $text): array => [
                $text->key => [
                    'ru' => $text->valueRu(),
                    'en' => $text->valueEn(),
                    'group' => $text->group,
                    'label' => $text->label,
                ],
            ])
            ->all();
    }

    private function menuItems(): array
    {
        return MenuItem::query()
            ->where('menu_area', MenuItem::AREA_MAIN)
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('position')
            ->get()
            ->map(fn (MenuItem $item): array => [
                ...$this->menuLabelPayload($item),
                'href' => $item->siteHref(),
            ])
            ->filter(static fn (array $item): bool => filled($item['href']))
            ->values()
            ->all();
    }

    private function serviceNavigationGroups(): array
    {
        return MenuItem::query()
            ->with(['children' => fn ($query) => $query
                ->where('menu_area', MenuItem::AREA_SERVICES)
                ->where('is_active', true)
                ->orderBy('position')])
            ->where('menu_area', MenuItem::AREA_SERVICES)
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('position')
            ->get()
            ->map(function (MenuItem $group): ?array {
                $href = $group->siteHref();

                if ($href === null) {
                    return null;
                }

                $items = $group->children
                    ->map(function (MenuItem $item): ?array {
                        $href = $item->siteHref();

                        if ($href === null) {
                            return null;
                        }

                        return [
                            ...$this->menuLabelPayload($item),
                            'href' => $href,
                        ];
                    })
                    ->filter()
                    ->values()
                    ->all();

                return [
                    'id' => 'service-nav-' . $group->id,
                    'title' => $group->labelRu(),
                    'titleRu' => $group->labelRu(),
                    'titleEn' => $group->labelEn(),
                    'href' => $href,
                    'description' => $group->descriptionRu() ?? '',
                    'descriptionRu' => $group->descriptionRu(),
                    'descriptionEn' => $group->descriptionEn(),
                    'items' => $items,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function menuLabelPayload(MenuItem $item): array
    {
        return [
            'label' => $item->labelRu(),
            'labelRu' => $item->labelRu(),
            'labelEn' => $item->labelEn(),
        ];
    }

    private function pagePayload(Page $page): array
    {
        return [
            'id' => $page->slug,
            'slug' => $page->slug,
            'title' => $page->fieldRu('title'),
            'titleRu' => $page->fieldRu('title'),
            'titleEn' => $page->fieldEn('title'),
            'template' => $page->template,
            'body' => $page->fieldRu('body'),
            'bodyRu' => $page->fieldRu('body'),
            'bodyEn' => $page->fieldEn('body'),
            'seoTitle' => $page->fieldRu('seo_title'),
            'seoTitleRu' => $page->fieldRu('seo_title'),
            'seoTitleEn' => $page->fieldEn('seo_title'),
            'seoDescription' => $page->fieldRu('seo_description'),
            'seoDescriptionRu' => $page->fieldRu('seo_description'),
            'seoDescriptionEn' => $page->fieldEn('seo_description'),
            'blocks' => $page->blocks->map(function ($block): array {
                $images = $this->imageList($block->image);

                return [
                    'type' => $block->type,
                    'visualVariant' => $block->visual_variant,
                    'mediaPosition' => $block->media_position,
                    'motionPreset' => $block->motion_preset,
                    'cardState' => $block->card_state,
                    'eyebrow' => $block->fieldRu('eyebrow'),
                    'eyebrowRu' => $block->fieldRu('eyebrow'),
                    'eyebrowEn' => $block->fieldEn('eyebrow'),
                    'title' => $block->fieldRu('title'),
                    'titleRu' => $block->fieldRu('title'),
                    'titleEn' => $block->fieldEn('title'),
                    'subtitle' => $block->fieldRu('subtitle'),
                    'subtitleRu' => $block->fieldRu('subtitle'),
                    'subtitleEn' => $block->fieldEn('subtitle'),
                    'text' => $block->fieldRu('text'),
                    'textRu' => $block->fieldRu('text'),
                    'textEn' => $block->fieldEn('text'),
                    'image' => $images[0] ?? null,
                    'images' => $images,
                    'imageAlt' => $block->fieldRu('image_alt'),
                    'imageAltRu' => $block->fieldRu('image_alt'),
                    'imageAltEn' => $block->fieldEn('image_alt'),
                    'linkLabel' => $block->fieldRu('link_label'),
                    'linkLabelRu' => $block->fieldRu('link_label'),
                    'linkLabelEn' => $block->fieldEn('link_label'),
                    'linkHref' => $block->link_href,
                    'settings' => $block->settings ?? [],
                ];
            })->values(),
        ];
    }

    private function faqPayload(Faq $faq): array
    {
        return [
            'q' => $faq->questionRu(),
            'a' => $faq->answerRu(),
            'qRu' => $faq->questionRu(),
            'qEn' => $faq->questionEn(),
            'aRu' => $faq->answerRu(),
            'aEn' => $faq->answerEn(),
        ];
    }

    private function vacancyPayload(Vacancy $vacancy): array
    {
        return [
            'id' => $vacancy->id,
            'title' => $vacancy->fieldRu('title'),
            'titleRu' => $vacancy->fieldRu('title'),
            'titleEn' => $vacancy->fieldEn('title'),
            'employment' => $vacancy->fieldRu('employment'),
            'employmentRu' => $vacancy->fieldRu('employment'),
            'employmentEn' => $vacancy->fieldEn('employment'),
            'location' => $vacancy->fieldRu('location'),
            'locationRu' => $vacancy->fieldRu('location'),
            'locationEn' => $vacancy->fieldEn('location'),
            'salary' => $vacancy->fieldRu('salary'),
            'salaryRu' => $vacancy->fieldRu('salary'),
            'salaryEn' => $vacancy->fieldEn('salary'),
            'description' => $vacancy->fieldRu('description'),
            'descriptionRu' => $vacancy->fieldRu('description'),
            'descriptionEn' => $vacancy->fieldEn('description'),
            'requirements' => $this->lines($vacancy->fieldRu('requirements')),
            'requirementsRu' => $this->lines($vacancy->fieldRu('requirements')),
            'requirementsEn' => $this->lines($vacancy->fieldEn('requirements')),
            'responsibilities' => $this->lines($vacancy->fieldRu('responsibilities')),
            'responsibilitiesRu' => $this->lines($vacancy->fieldRu('responsibilities')),
            'responsibilitiesEn' => $this->lines($vacancy->fieldEn('responsibilities')),
        ];
    }

    private function projectPayload(Project $project): array
    {
        return [
            'id' => $project->id,
            'slug' => $project->slug,
            'title' => $project->fieldRu('title'),
            'titleRu' => $project->fieldRu('title'),
            'titleEn' => $project->fieldEn('title'),
            'category' => $project->fieldRu('category'),
            'categoryRu' => $project->fieldRu('category'),
            'categoryEn' => $project->fieldEn('category'),
            'location' => $project->fieldRu('location'),
            'locationRu' => $project->fieldRu('location'),
            'locationEn' => $project->fieldEn('location'),
            'year' => $project->year,
            'description' => $project->fieldRu('description'),
            'descriptionRu' => $project->fieldRu('description'),
            'descriptionEn' => $project->fieldEn('description'),
            'image' => $project->effective_image,
            'beforeImage' => $project->effective_before_image,
            'afterImage' => $project->effective_after_image,
            'galleryEyebrow' => $project->fieldRu('gallery_eyebrow'),
            'galleryEyebrowRu' => $project->fieldRu('gallery_eyebrow'),
            'galleryEyebrowEn' => $project->fieldEn('gallery_eyebrow'),
            'galleryTitle' => $project->fieldRu('gallery_title'),
            'galleryTitleRu' => $project->fieldRu('gallery_title'),
            'galleryTitleEn' => $project->fieldEn('gallery_title'),
            'galleryText' => $project->fieldRu('gallery_text'),
            'galleryTextRu' => $project->fieldRu('gallery_text'),
            'galleryTextEn' => $project->fieldEn('gallery_text'),
            'galleryImages' => $this->imageList($project->gallery_images),
            'galleryLabels' => $this->lines($project->fieldRu('gallery_labels')),
            'galleryLabelsRu' => $this->lines($project->fieldRu('gallery_labels')),
            'galleryLabelsEn' => $this->lines($project->fieldEn('gallery_labels')),
            'isFeatured' => $project->is_featured,
            'featuredLabel' => $project->fieldRu('featured_label'),
            'featuredLabelRu' => $project->fieldRu('featured_label'),
            'featuredLabelEn' => $project->fieldEn('featured_label'),
            'featuredTitle' => $project->fieldRu('featured_title'),
            'featuredTitleRu' => $project->fieldRu('featured_title'),
            'featuredTitleEn' => $project->fieldEn('featured_title'),
            'featuredDescription' => $project->fieldRu('featured_description'),
            'featuredDescriptionRu' => $project->fieldRu('featured_description'),
            'featuredDescriptionEn' => $project->fieldEn('featured_description'),
            'featuredImage' => $project->effective_featured_image,
        ];
    }

    private function servicePayload(Service $service): array
    {
        $images = $this->mediaSlides($service->effective_image, $service->hero_images);

        return [
            'id' => $service->slug,
            'slug' => $service->slug,
            'title' => $service->fieldRu('title'),
            'titleRu' => $service->fieldRu('title'),
            'titleEn' => $service->fieldEn('title'),
            'eyebrow' => $service->fieldRu('eyebrow'),
            'eyebrowRu' => $service->fieldRu('eyebrow'),
            'eyebrowEn' => $service->fieldEn('eyebrow'),
            'text' => $service->fieldRu('text'),
            'textRu' => $service->fieldRu('text'),
            'textEn' => $service->fieldEn('text'),
            'image' => $images[0] ?? $service->effective_image,
            'images' => $images,
            'heroImages' => $images,
            'pdfUrl' => $service->effective_pdf,
            'pdfTitle' => $service->fieldRu('pdf_title'),
            'pdfTitleRu' => $service->fieldRu('pdf_title'),
            'pdfTitleEn' => $service->fieldEn('pdf_title'),
            'price' => $service->fieldRu('price'),
            'priceRu' => $service->fieldRu('price'),
            'priceEn' => $service->fieldEn('price'),
            'timeline' => $service->fieldRu('timeline'),
            'timelineRu' => $service->fieldRu('timeline'),
            'timelineEn' => $service->fieldEn('timeline'),
            'isHomeItem' => $service->is_home_item,
            'deliverables' => $this->lines($service->fieldRu('deliverables')),
            'deliverablesRu' => $this->lines($service->fieldRu('deliverables')),
            'deliverablesEn' => $this->lines($service->fieldEn('deliverables')),
            'deliverableImages' => $this->lines($service->deliverable_images),
            'benefits' => $this->lines($service->fieldRu('benefits')),
            'benefitsRu' => $this->lines($service->fieldRu('benefits')),
            'benefitsEn' => $this->lines($service->fieldEn('benefits')),
            'process' => $this->lines($service->fieldRu('process')),
            'processRu' => $this->lines($service->fieldRu('process')),
            'processEn' => $this->lines($service->fieldEn('process')),
        ];
    }

    private function newsPayload(NewsArticle $article): array
    {
        $images = $this->mediaSlides($article->effective_image, $article->hero_images);

        return [
            'slug' => $article->slug,
            'title' => $article->fieldRu('title'),
            'titleRu' => $article->fieldRu('title'),
            'titleEn' => $article->fieldEn('title'),
            'date' => $article->fieldRu('date'),
            'dateRu' => $article->fieldRu('date'),
            'dateEn' => $article->fieldEn('date'),
            'dateIso' => $article->date_iso?->toDateString(),
            'category' => $article->fieldRu('category'),
            'categoryRu' => $article->fieldRu('category'),
            'categoryEn' => $article->fieldEn('category'),
            'preview' => $article->fieldRu('preview'),
            'previewRu' => $article->fieldRu('preview'),
            'previewEn' => $article->fieldEn('preview'),
            'image' => $images[0] ?? $article->effective_image,
            'images' => $images,
            'heroImages' => $images,
            'readingTime' => $article->fieldRu('reading_time'),
            'readingTimeRu' => $article->fieldRu('reading_time'),
            'readingTimeEn' => $article->fieldEn('reading_time'),
            'body' => collect(preg_split('/\R{2,}/', (string) $article->fieldRu('body')) ?: [])
                ->filter()
                ->map(fn (string $text) => ['type' => 'paragraph', 'text' => trim($text)])
                ->values(),
            'bodyRu' => collect(preg_split('/\R{2,}/', (string) $article->fieldRu('body')) ?: [])
                ->filter()
                ->map(fn (string $text) => ['type' => 'paragraph', 'text' => trim($text)])
                ->values(),
            'bodyEn' => collect(preg_split('/\R{2,}/', (string) $article->fieldEn('body')) ?: [])
                ->filter()
                ->map(fn (string $text) => ['type' => 'paragraph', 'text' => trim($text)])
                ->values(),
        ];
    }

    private function promoPayload(Promo $promo): array
    {
        return [
            'id' => $promo->slug,
            'slug' => $promo->slug,
            'badge' => $promo->fieldRu('badge'),
            'badgeRu' => $promo->fieldRu('badge'),
            'badgeEn' => $promo->fieldEn('badge'),
            'title' => $promo->fieldRu('title'),
            'titleRu' => $promo->fieldRu('title'),
            'titleEn' => $promo->fieldEn('title'),
            'highlight' => $promo->fieldRu('highlight'),
            'highlightRu' => $promo->fieldRu('highlight'),
            'highlightEn' => $promo->fieldEn('highlight'),
            'validUntil' => $promo->fieldRu('valid_until'),
            'validUntilRu' => $promo->fieldRu('valid_until'),
            'validUntilEn' => $promo->fieldEn('valid_until'),
            'description' => $promo->fieldRu('description'),
            'descriptionRu' => $promo->fieldRu('description'),
            'descriptionEn' => $promo->fieldEn('description'),
            'conditions' => $this->lines($promo->fieldRu('conditions')),
            'conditionsRu' => $this->lines($promo->fieldRu('conditions')),
            'conditionsEn' => $this->lines($promo->fieldEn('conditions')),
            'image' => $promo->effective_image,
        ];
    }

    private function awardPayload(Award $award): array
    {
        return [
            'title' => $award->fieldRu('title'),
            'titleRu' => $award->fieldRu('title'),
            'titleEn' => $award->fieldEn('title'),
            'issuer' => $award->fieldRu('issuer'),
            'issuerRu' => $award->fieldRu('issuer'),
            'issuerEn' => $award->fieldEn('issuer'),
            'year' => $award->year,
            'description' => $award->fieldRu('description'),
            'descriptionRu' => $award->fieldRu('description'),
            'descriptionEn' => $award->fieldEn('description'),
            'image' => $award->effective_image,
        ];
    }

    private function partnerPayload(Partner $partner): array
    {
        return [
            'name' => $partner->fieldRu('name'),
            'nameRu' => $partner->fieldRu('name'),
            'nameEn' => $partner->fieldEn('name'),
            'note' => $partner->fieldRu('note'),
            'noteRu' => $partner->fieldRu('note'),
            'noteEn' => $partner->fieldEn('note'),
            'logo' => $partner->effective_logo,
        ];
    }

    private function reviewPayload(Review $review): array
    {
        return [
            'name' => $review->fieldRu('name'),
            'nameRu' => $review->fieldRu('name'),
            'nameEn' => $review->fieldEn('name'),
            'date' => $review->fieldRu('date'),
            'dateRu' => $review->fieldRu('date'),
            'dateEn' => $review->fieldEn('date'),
            'service' => $review->fieldRu('service'),
            'serviceRu' => $review->fieldRu('service'),
            'serviceEn' => $review->fieldEn('service'),
            'title' => $review->fieldRu('title'),
            'titleRu' => $review->fieldRu('title'),
            'titleEn' => $review->fieldEn('title'),
            'text' => $review->fieldRu('text'),
            'textRu' => $review->fieldRu('text'),
            'textEn' => $review->fieldEn('text'),
            'adminReply' => $review->fieldRu('admin_reply'),
            'adminReplyRu' => $review->fieldRu('admin_reply'),
            'adminReplyEn' => $review->fieldEn('admin_reply'),
            'image' => $review->image,
        ];
    }

    private function lines(?string $value): array
    {
        return collect(preg_split('/\R/u', (string) $value) ?: [])
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    private function imageList(?string $value): array
    {
        return collect(preg_split('/\R/u', (string) $value) ?: [])
            ->map(fn (string $line) => $this->assetUrl(trim($line)))
            ->filter()
            ->values()
            ->all();
    }

    private function mediaSlides(?string $primary, ?string $extra): array
    {
        return collect([
            $this->assetUrl($primary),
            ...$this->imageList($extra),
        ])
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function storageAsset(?string $path): ?string
    {
        return $this->assetUrl($path);
    }

    private function assetUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $path = trim($path);

        if (
            preg_match('/^(https?:)?\/\//i', $path) === 1
            || str_starts_with($path, 'data:')
            || str_starts_with($path, '/')
        ) {
            return $path;
        }

        return '/storage/' . ltrim($path, '/');
    }
}
