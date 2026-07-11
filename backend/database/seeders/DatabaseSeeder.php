<?php

namespace Database\Seeders;

use App\Models\Award;
use App\Models\Partner;
use App\Models\Faq;
use App\Models\MenuItem;
use App\Models\NewsArticle;
use App\Models\Page;
use App\Models\PageBlock;
use App\Models\Project;
use App\Models\Promo;
use App\Models\Review;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\UiText;
use App\Models\Vacancy;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Support\DefaultUiTexts;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\Models\MoonshineUserRole;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $role = MoonshineUserRole::query()->firstOrCreate(
            ['id' => MoonshineUserRole::DEFAULT_ROLE_ID],
            ['name' => 'Admin'],
        );

        MoonshineUser::query()->updateOrCreate([
            'email' => env('ADMIN_EMAIL', 'admin@example.com'),
        ], [
            'moonshine_user_role_id' => $role->id,
            'name' => env('ADMIN_NAME', 'Admin'),
            'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
        ]);

        SiteSetting::query()->updateOrCreate(['id' => 1], [
            'site_name' => '3D Smart Design Studio',
            'phone' => '+7 (987) 942-12-42',
            'phone_href' => 'tel:+79879421242',
            'emails' => "3dsmartdesign@bk.ru - обсуждение проекта\ninfo@3dsmartdesign.ru - реклама, сотрудничество",
            'schedule' => 'Пн-Пт 9:00-20:00, Сб-Вс 10:00-19:00',
            'address' => 'Работаем в Самаре и удаленно с другими городами и странами',
            'map_src' => 'https://www.openstreetmap.org/export/embed.html?bbox=49.92%2C53.12%2C50.30%2C53.30&layer=mapnik&marker=53.1959%2C50.1002',
            'telegram_url' => 'https://t.me/+79879421242',
            'max_url' => 'https://max.ru/',
            'vk_url' => 'https://vk.com/3dsmartdesign',
            'animations_enabled' => true,
            'smooth_scroll_enabled' => true,
            'page_reveal_enabled' => true,
            'linkedin_url' => 'https://www.linkedin.com/in/3dsmartdesignstudio',
            'behance_url' => 'https://www.behance.net/3dsmartdesign',
            'pinterest_url' => 'https://ru.pinterest.com/3D_SMART_DESIGN_STUDIO/',
            'seo_title' => '3D Smart Design Studio',
            'seo_description' => 'Студия концептуального дизайна. Интерьеры, архитектура, ландшафт.',
        ]);

        collect(DefaultUiTexts::rows())->each(fn (array $row, int $index) => UiText::query()->updateOrCreate(
            ['key' => $row['key']],
            [
                ...$row,
                'position' => $index + 1,
            ],
        ));

        collect([
            ['/o-nas', 'О нас', 'About Us'],
            ['/portfolio', 'Портфолио', 'Portfolio'],
            ['/services', 'Услуги', 'Services'],
            ['/kontakty', 'Контакты', 'Contacts'],
            ['/akcii-i-skidki', 'Акции', 'Promotions'],
            ['/novosti', 'Новости', 'News'],
            ['/blog', 'Блог', 'Blog'],
            ['/otzyvy-o-nas', 'Отзывы', 'Reviews'],
            ['/karera', 'Карьера', 'Careers'],
            ['/partneram', 'Партнерам', 'Partners'],
        ])->each(fn (array $item, int $index) => MenuItem::query()->updateOrCreate(
            ['menu_area' => MenuItem::AREA_MAIN, 'href' => $item[0]],
            [
                'label' => $item[1],
                'label_ru' => $item[1],
                'label_en' => $item[2],
                'parent_id' => null,
                'position' => $index + 1,
                'is_active' => true,
            ],
        ));

        collect([
            [
                'label' => 'Архитектурное проектирование',
                'label_en' => 'Architectural Design',
                'href' => '/arhitekturnoe-proektirovanie',
                'description' => 'Концепция, фасады, объемы здания и рабочая документация для строителей.',
                'description_en' => 'Concept, facades, building massing, and working documentation for contractors.',
                'items' => [
                    ['label' => 'Эскизный проект', 'label_en' => 'Concept Design', 'href' => '/eskiznyj-proekt'],
                    ['label' => 'Рабочая документация', 'label_en' => 'Working Documentation', 'href' => '/rabochaya-dokumentaciya'],
                ],
            ],
            [
                'label' => 'Дизайн интерьера',
                'label_en' => 'Interior Design',
                'href' => '/dizajn-interyera',
                'description' => 'Жилые и коммерческие интерьеры, комплектация и авторский надзор.',
                'description_en' => 'Residential and commercial interiors, procurement, and author supervision.',
                'items' => [
                    ['label' => 'Дизайн интерьера частных пространств', 'label_en' => 'Private Interior Design', 'href' => '/dizajn-interyera-chastnyh-prostranstv'],
                    ['label' => 'Дизайн интерьера коммерческого пространства', 'label_en' => 'Commercial Interior Design', 'href' => '/dizajn-interera-kommercheskogo-prostranstva'],
                    ['label' => 'Комплектация объекта', 'label_en' => 'Project Procurement', 'href' => '/komplektaciya-ob-ekta'],
                    ['label' => 'Авторский надзор', 'label_en' => 'Author Supervision', 'href' => '/avtorskij-nadzor'],
                ],
            ],
            [
                'label' => '3D-визуализация',
                'label_en' => '3D Visualization',
                'href' => '/3d-vizualizaciya',
                'description' => 'Рендеры, архитектурная и интерьерная визуализация, интерактивные 360°-туры.',
                'description_en' => 'Renders, architectural and interior visualization, and interactive 360° tours.',
                'items' => [
                    ['label' => 'Архитектурная 3D-визуализация ЖК и девелопмента', 'label_en' => 'Architectural 3D Visualization for Residential Developments', 'href' => '/arhitekturnaya-3d-vizualizaciya'],
                    ['label' => 'Архитектурная 3D-визуализация коттеджей', 'label_en' => 'Architectural 3D Visualization for Cottages', 'href' => '/arhitekturnaya-3d-vizualizaciya-kottedzhej'],
                    ['label' => 'Интерьерная 3D-визуализация', 'label_en' => 'Interior 3D Visualization', 'href' => '/interernaya-3d-vizualizaciya'],
                    ['label' => 'Виртуальные 3D-туры 360°', 'label_en' => 'Virtual 3D Tours 360°', 'href' => '/virtualnyj-3d-tur-360'],
                ],
            ],
            [
                'label' => 'Ландшафтный дизайн',
                'label_en' => 'Landscape Design',
                'href' => '/landshaftnyj-dizajn',
                'description' => 'Генплан, инженерные решения, озеленение и авторский надзор реализации.',
                'description_en' => 'Master plan, engineering solutions, planting, and implementation supervision.',
                'items' => [
                    ['label' => 'Ландшафтное проектирование и генплан', 'label_en' => 'Landscape Planning and Master Plan', 'href' => '/landshaftnoe-proektirovanie-i-genplan'],
                    ['label' => 'Проектирование инженерных систем', 'label_en' => 'Engineering Systems Design', 'href' => '/proektirovanie-inzhenernyh-sistem'],
                    ['label' => 'Озеленение и дендроплан', 'label_en' => 'Planting Plan and Dendrology', 'href' => '/ozelenenie-i-dendroplan'],
                    ['label' => 'Авторский надзор и реализация', 'label_en' => 'Author Supervision and Implementation', 'href' => '/avtorskij-nadzor-i-realizaciya'],
                ],
            ],
        ])->each(function (array $group, int $index): void {
            $groupItem = MenuItem::query()->updateOrCreate(
                ['menu_area' => MenuItem::AREA_SERVICES, 'href' => $group['href']],
                [
                    'label' => $group['label'],
                    'label_ru' => $group['label'],
                    'label_en' => $group['label_en'],
                    'parent_id' => null,
                    'description' => $group['description'],
                    'description_ru' => $group['description'],
                    'description_en' => $group['description_en'],
                    'position' => ($index + 1) * 10,
                    'is_active' => true,
                ],
            );

            collect($group['items'])->each(static function (array $item, int $childIndex) use ($groupItem): void {
                MenuItem::query()->updateOrCreate(
                    ['menu_area' => MenuItem::AREA_SERVICES, 'href' => $item['href']],
                    [
                        'label' => $item['label'],
                        'label_ru' => $item['label'],
                        'label_en' => $item['label_en'],
                        'parent_id' => $groupItem->id,
                        'description' => null,
                        'description_ru' => null,
                        'description_en' => null,
                        'position' => $childIndex + 1,
                        'is_active' => true,
                    ],
                );
            });
        });

        $projects = collect([
            ['kp-pavlovy-ozera', 'КП "Павловы Озера"', 'KP "Pavlovy Ozera"', 'Интерьеры', 'Interiors', 'Москва', 'Moscow', '2026', 'Авторский интерьер загородного дома: мягкая неоклассика, продуманная кухня-гостиная, приватные спальни и спокойная палитра натуральных материалов.', 'Author-designed country house interior: soft neoclassic, thoughtful kitchen-living room, private bedrooms, and a calm palette of natural materials.', '/images/cms/country-house-interior.webp'],
            ['river-park', 'ЖК "River Park"', 'RC "River Park"', 'Интерьеры', 'Interiors', 'Самара', 'Samara', '2025', 'Квартира с открытой кухней-гостиной, скрытым хранением, световыми сценариями и визуализацией до начала ремонтных работ.', 'Apartment with open kitchen-living room, hidden storage, lighting scenarios, and visualization before renovation work begins.', '/images/cms/river-park-interior.webp'],
            ['landshaftnyj-projekt', 'Ландшафтный проект', 'Landscape Project', 'Ландшафт', 'Landscape', 'Висловка', 'Vislovka', '2025', 'Благоустройство участка с маршрутами, вечерней подсветкой, зонами отдыха и растениями, подобранными под климат и уход.', 'Landscaping with pathways, evening lighting, recreation areas, and plants selected for the local climate and maintenance needs.', '/images/cms/landscape-garden.webp'],
            ['ofisnoe-prostranstvo', 'Офисное пространство', 'Office Space', 'Интерьеры', 'Interiors', 'Белгородская область', 'Belgorod Region', '2026', 'Коммерческий интерьер с понятной навигацией, рабочими зонами, переговорными и визуальным языком бренда.', 'Commercial interior with clear navigation, work zones, meeting rooms, and a visual brand language.', '/images/cms/office-space.webp'],
            ['villa-austria', 'Villa Austria', 'Villa Austria', 'Архитектура', 'Architecture', 'Вена', 'Vienna', '2024', 'Архитектурная визуализация виллы: фасады, посадка на участок, вечерний свет, окружение и презентационные ракурсы.', 'Architectural visualization of a villa: facades, site placement, evening lighting, surroundings, and presentation views.', '/images/cms/villa-exterior.webp'],
            ['kp-greenwood-2', 'КП GreenWood-2', 'KP GreenWood-2', 'Архитектура', 'Architecture', 'Москва', 'Moscow', '2025', 'Концепция частного дома с выразительной геометрией, панорамным остеклением и подготовкой материалов для согласования.', 'Concept for a private house with expressive geometry, panoramic glazing, and presentation materials for approval.', '/images/cms/greenwood-house.webp'],
        ]);

        $projects->each(fn (array $project, int $index) => Project::query()->updateOrCreate(
            ['slug' => $project[0]],
            [
                'title' => $project[1],
                'title_en' => $project[2],
                'category' => $project[3],
                'category_en' => $project[4],
                'location' => $project[5],
                'location_en' => $project[6],
                'year' => $project[7],
                'description' => $project[8],
                'description_en' => $project[9],
                'image' => $project[10],
                'before_image' => $index < 2 ? '/images/cms/warm-interior-before.webp' : null,
                'after_image' => $index < 2 ? '/images/cms/warm-interior-after.webp' : null,
                'position' => $index + 1,
                'is_featured' => $index < 3,
                'is_published' => true,
            ],
        ));

        collect([
            ['dizajn-interyera', 'Дизайн интерьера', 'Interior Design', 'Квартиры, дома, офисы', 'Apartments, houses, offices', 'Проект под ключ для квартир, коттеджей и коммерческих помещений: планировка, 3D-визуализация, чертежи, спецификации и сопровождение реализации.', 'Full-service design for apartments, cottages, and commercial spaces: layout, 3D visualization, drawings, specifications, and implementation support.', $projects[1][10], 'от 650 ₽/м²', 'from 650 RUB/m²', '3-12 недель', '3-12 weeks'],
            ['dizajn-interera-kommercheskogo-prostranstva', 'Дизайн коммерческого пространства', 'Commercial Interior Design', 'Офисы, шоурумы, рестораны', 'Offices, showrooms, restaurants', 'Создаем интерьеры, которые работают на поток, бренд и продажи: от сценариев движения посетителей до мебели, света и технической документации.', 'We create interiors that work for foot traffic, brand, and sales — from visitor flow scenarios to furniture, lighting, and technical documentation.', $projects[3][10], 'от 1 500 ₽/м²', 'from 1,500 RUB/m²', '4-10 недель', '4-10 weeks'],
            ['komplektaciya-ob-ekta', 'Комплектация объекта', 'Project Procurement', 'Материалы, мебель, поставки', 'Materials, furniture, supplies', 'Подбираем материалы, мебель, освещение и оборудование, готовим ведомости, контролируем бюджет и помогаем проекту не рассыпаться на этапе закупок.', 'We select materials, furniture, lighting, and equipment, prepare schedules, control the budget, and keep the project on track during the procurement phase.', $projects[0][10], 'индивидуально', 'custom pricing', 'на протяжении ремонта', 'throughout renovation'],
            ['3d-vizualizaciya', '3D визуализация', '3D Visualization', 'Фотореалистичные рендеры', 'Photorealistic renders', 'Создаем реалистичные изображения для согласований, презентаций, девелопмента и маркетинга.', 'We create realistic images for approvals, presentations, development, and marketing.', $projects[0][10], 'от 10 000 ₽', 'from 10,000 RUB', 'от 5 дней', 'from 5 days'],
            ['arhitekturnaya-3d-vizualizaciya', 'Архитектурная 3D визуализация', 'Architectural 3D Visualization', 'Коттеджи и жилые комплексы', 'Cottages and residential complexes', 'Преобразуем чертежи в продающий образ: фасады, посадка на участок, дворы, благоустройство, дневные и вечерние сцены.', 'We turn drawings into compelling visuals: facades, site placement, courtyards, landscaping, day and night scenes.', $projects[5][10], 'от 40 000 ₽', 'from 40,000 RUB', '5-7 дней', '5-7 days'],
            ['landshaftnyj-dizajn', 'Ландшафтный дизайн', 'Landscape Design', 'Благоустройство участков', 'Site landscaping', 'Проектируем участки под ключ: зонирование, дорожки, озеленение, дренажные решения, освещение, террасы и сценарии отдыха.', 'Full landscape design: zoning, pathways, planting, drainage, lighting, terraces, and recreation scenarios.', $projects[2][10], 'от 1 500 ₽/м²', 'from 1,500 RUB/m²', '3-8 недель', '3-8 weeks'],
        ])->each(fn (array $service, int $index) => Service::query()->updateOrCreate(
            ['slug' => $service[0]],
            [
                'title' => $service[1],
                'title_en' => $service[2],
                'eyebrow' => $service[3],
                'eyebrow_en' => $service[4],
                'text' => $service[5],
                'text_en' => $service[6],
                'image' => $service[7],
                'price' => $service[8],
                'price_en' => $service[9],
                'timeline' => $service[10],
                'timeline_en' => $service[11],
                'deliverables' => "обмерный план и ТЗ\n3D-визуализация\nрабочие чертежи\nподбор материалов",
                'deliverables_en' => "measurement plan and brief\n3D visualization\nworking drawings\nmaterial selection",
                'benefits' => "понятный бюджет\nпоэтапная оплата\nконтроль реализации\nпомощь с подрядчиками",
                'benefits_en' => "clear budget\nphased payments\nimplementation control\ncontractor coordination",
                'process' => "бриф\nпланировка\nвизуальная концепция\nчертежи\nсопровождение",
                'process_en' => "brief\nlayout\nvisual concept\ndrawings\nimplementation support",
                'position' => $index + 1,
                'is_published' => true,
            ],
        ));

        $homePage = Page::query()->updateOrCreate(
            ['slug' => 'home'],
            ['title' => 'Главная', 'title_en' => 'Home', 'template' => 'content', 'is_published' => true],
        );

        PageBlock::query()->updateOrCreate(
            ['page_id' => $homePage->id, 'position' => 1],
            [
                'type' => 'hero',
                'eyebrow' => 'Студия дизайна интерьера и архитектуры в Самаре',
                'eyebrow_en' => 'Interior design and architecture studio in Samara',
                'title' => 'Дизайн с умом.',
                'title_en' => 'Design with logic.',
                'subtitle' => 'Создаем интерьеры, архитектуру, 3D-визуализацию и ландшафтные проекты: от концепции до рабочей документации, комплектации и сопровождения.',
                'subtitle_en' => 'We create interiors, architecture, 3D visualization, and landscape projects — from concept to working documentation, procurement, and support.',
                'link_label' => 'Обсудить проект',
                'link_label_en' => 'Discuss a project',
                'link_href' => '/kontakty',
                'is_active' => true,
            ],
        );

        PageBlock::query()->updateOrCreate(
            ['page_id' => $homePage->id, 'position' => 2],
            [
                'type' => 'text',
                'eyebrow' => 'Философия проекта',
                'eyebrow_en' => 'Project philosophy',
                'text' => 'Мы проектируем не стены, а сценарии жизни: утренний свет, маршрут взгляда, тишину материалов и точную документацию для реализации.',
                'text_en' => 'We design life scenarios, not walls: morning light, sightlines, material silence, and precise documentation for implementation.',
                'is_active' => true,
            ],
        );

        collect([
            ['o-nas', 'О нас', 'About Us', 'about'],
            ['partneram', 'Партнерам', 'For Partners', 'content'],
            ['blog', 'Блог', 'Blog', 'blog'],
            ['user/agreement', 'Пользовательское соглашение', 'User Agreement', 'legal'],
            ['politika-konfidencialnosti', 'Политика конфиденциальности', 'Privacy Policy', 'legal'],
        ])->each(function (array $data): void {
            $page = Page::query()->updateOrCreate(
                ['slug' => $data[0]],
                ['title' => $data[1], 'title_en' => $data[2], 'template' => $data[3], 'is_published' => true],
            );

            PageBlock::query()->updateOrCreate(
                ['page_id' => $page->id, 'position' => 1],
                [
                    'type' => 'hero',
                    'eyebrow' => '3D Smart Design',
                    'title' => $data[1],
                    'title_en' => $data[2],
                    'subtitle' => 'Редактируемый блок страницы из MoonShine.',
                    'subtitle_en' => 'Editable page block from MoonShine.',
                    'text' => 'Этот текст можно заменить в админ-панели. Дизайн остается на Next, а содержание приходит из Laravel API.',
                    'text_en' => 'This text can be replaced in the admin panel. Design stays on Next.js while content comes from the Laravel API.',
                    'is_active' => true,
                ],
            );
        });

        collect([
            ['s-novym-2026-godom-i-rozhdestvom', 'С Новым 2026 годом и Рождеством', 'Happy New Year and Christmas 2026', '15 декабря 2025', 'December 15, 2025', '2025-12-15', 'Студия', 'Studio', 'Поздравляем клиентов и партнеров, подводим итоги сезона и готовим новые проектные направления.', 'We congratulate our clients and partners, review the season, and prepare new project directions.', 'Мы поздравляем клиентов и партнеров с наступающим 2026 годом и Рождеством. Подводим итоги года, делимся планами на новый сезон.', 'We wish our clients and partners a happy 2026 and Merry Christmas. We review the year and share plans for the new season.'],
            ['odin-klik-vsya-kvartira-virtualnyj-tur-360', 'Один клик - вся квартира: виртуальный тур 360°', 'One click — the whole apartment: 360° virtual tour', '20 августа 2025', 'August 20, 2025', '2025-08-20', 'Услуги', 'Services', 'Как интерактивный тур помогает продавать, согласовывать планировки и показывать объект удаленно.', 'How an interactive tour helps sell properties, coordinate layouts, and showcase objects remotely.', 'Интерактивный 3D-тур дает покупателям полное ощущение пространства, помогает продавцам закрыть сделки быстрее и снижает количество личных просмотров.', 'An interactive 3D tour gives buyers a full sense of space, helps sellers close deals faster, and reduces the number of in-person viewings.'],
            ['top-5-oshibok-pri-remonte-v-samare', 'Топ-5 ошибок при ремонте в Самаре', 'Top 5 renovation mistakes in Samara', '15 августа 2025', 'August 15, 2025', '2025-08-15', 'Советы', 'Tips', 'Неполная смета, экономия на материалах, ремонт без проекта и другие ошибки, которые дорого исправлять.', 'Incomplete budgets, saving on materials, renovating without a design — and other expensive mistakes.', 'Частые ошибки при ремонте: неполная смета, экономия на материалах, ремонт без дизайн-проекта, неверная последовательность работ и игнорирование инженерных систем.', 'Common renovation mistakes: incomplete budgets, cheap materials, skipping the design phase, wrong work sequence, and ignoring engineering systems.'],
        ])->each(fn (array $article, int $index) => NewsArticle::query()->updateOrCreate(
            ['slug' => $article[0]],
            [
                'title' => $article[1],
                'title_en' => $article[2],
                'date' => $article[3],
                'date_en' => $article[4],
                'date_iso' => $article[5],
                'category' => $article[6],
                'category_en' => $article[7],
                'preview' => $article[8],
                'preview_en' => $article[9],
                'image' => '/images/cms/construction-plan.webp',
                'reading_time' => '3 мин',
                'reading_time_en' => '3 min',
                'body' => $article[10],
                'body_en' => $article[11],
                'position' => $index + 1,
                'is_published' => true,
            ],
        ));

        Promo::query()->updateOrCreate(['slug' => 'interior-start'], [
            'badge' => 'Пакет',
            'badge_en' => 'Package',
            'title' => 'Старт дизайн-проекта',
            'title_en' => 'Design project starter',
            'highlight' => 'планировка + мудборд',
            'highlight_en' => 'layout + moodboard',
            'valid_until' => 'до 31.12.2026',
            'valid_until_en' => 'until December 31, 2026',
            'description' => 'Быстрый вход в проект для квартир и домов.',
            'description_en' => 'A fast entry into a design project for apartments and houses.',
            'conditions' => "обмер и техническое задание\nдо 3 планировочных решений\nколлажи по ключевым зонам",
            'conditions_en' => "measurement and brief\nup to 3 layout options\ncollages for key zones",
            'image' => $projects[1][10],
            'position' => 1,
            'is_active' => true,
        ]);

        Review::query()->updateOrCreate(['name' => 'Анна и Дмитрий'], [
            'name' => 'Анна и Дмитрий',
            'name_en' => 'Anna and Dmitry',
            'date' => '2025',
            'date_en' => '2025',
            'service' => 'Дизайн интерьера',
            'service_en' => 'Interior Design',
            'title' => 'Проект получился точным и спокойным',
            'title_en' => 'The project turned out precise and calm',
            'text' => 'Команда помогла собрать понятную планировку, материалы и визуализации до начала ремонта.',
            'text_en' => 'The team helped us put together a clear layout, materials, and visualizations before renovation started.',
            'admin_reply' => 'Спасибо за доверие. Такие проекты особенно ценны, когда интерьер становится удобным каждый день.',
            'admin_reply_en' => 'Thank you for your trust. Projects like these are especially valuable when the interior becomes comfortable every day.',
            'image' => $projects[1][10],
            'position' => 1,
            'is_published' => true,
        ]);

        collect([
            ['Диплом администрации области', 'Regional Administration Diploma', 'Администрация Самарской области', 'Samara Regional Administration', '2026', 'Официальное подтверждение профессионального уровня студии и вклада в развитие визуальной культуры среды.', 'Official recognition of the studio\'s professional level and contribution to visual culture development.'],
            ['Профессиональный сертификат', 'Professional Certificate', 'Профильная аттестация', 'Professional Certification', '2026', 'Подтверждение профессиональных компетенций в проектировании, визуализации и сопровождении объектов.', 'Confirmation of professional competencies in design, visualization, and project support.'],
            ['Благодарственное письмо', 'Letter of Appreciation', 'Партнеры и заказчики', 'Partners and Clients', '2026', 'Отметка партнерского подхода, ответственности в коммуникации и качества работы с проектами.', 'Recognition of a collaborative approach, responsible communication, and project quality.'],
        ])->each(fn (array $award, int $index) => Award::query()->updateOrCreate(
            ['title' => $award[0]],
            [
                'title' => $award[0],
                'title_en' => $award[1],
                'issuer' => $award[2],
                'issuer_en' => $award[3],
                'year' => $award[4],
                'description' => $award[5],
                'description_en' => $award[6],
                'position' => $index + 1,
                'is_active' => true,
            ],
        ));

        collect([
            ['Capital Group', 'Capital Group', 'Крупная российская девелоперская компания в Москве и МО', 'Major Russian developer in Moscow and the Moscow Region', '/images/partners/capital-group.webp'],
            ['ГК Новый ДОН', 'Novy DON Group', 'Девелопер в Самарской области', 'Developer in the Samara Region', '/images/partners/new-don.webp'],
            ['Донстрой', 'Donstroy', 'Ведущий девелопер Москвы', 'Leading Moscow developer', '/images/partners/donstroy.svg'],
            ['Новое время', 'Novoye Vremya', 'Девелоперские проекты', 'Development projects', '/images/partners/new-time.svg'],
            ['Группа компаний ФСК', 'FSK Group', 'Девелоперские проекты и строительство', 'Development and construction projects', '/images/partners/fsk.svg'],
            ['СЗ Парковый', 'SZ Parkovy', 'Специализированный застройщик', 'Specialized developer', '/images/partners/parkovy.webp'],
            ['ЖК Весна', 'RC Vesna', 'Жилой комплекс', 'Residential complex', '/images/partners/vesna.webp'],
            ['Строительная компания «Град»', 'Grad Construction Company', 'Строительная компания', 'Construction company', '/images/partners/grad.svg'],
        ])->each(fn (array $partner, int $index) => Partner::query()->updateOrCreate(
            ['name' => $partner[0]],
            [
                'name' => $partner[0],
                'name_en' => $partner[1],
                'note' => $partner[2],
                'note_en' => $partner[3],
                'logo' => $partner[4],
                'position' => $index + 1,
                'is_active' => true,
            ],
        ));

        collect([
            [
                'title' => 'Дизайнер интерьера',
                'title_en' => 'Interior Designer',
                'employment' => 'Гибрид / удаленно',
                'employment_en' => 'Hybrid / remote',
                'location' => 'Самара или другой город',
                'location_en' => 'Samara or another city',
                'salary' => 'от 90 000 ₽ + проектный бонус',
                'salary_en' => 'from 90,000 RUB + project bonus',
                'description' => 'Для жилых проектов: планировки, концепции, 3D-постановка задач и уверенная коммуникация с клиентом.',
                'description_en' => 'For residential projects: layouts, concepts, 3D task setting, and confident client communication.',
                'requirements' => "портфолио реализованных или детально проработанных интерьеров\nпонимание эргономики, материалов и узлов\nспокойная, точная коммуникация с клиентами",
                'requirements_en' => "portfolio of completed or deeply developed interiors\nunderstanding of ergonomics, materials, and construction details\ncalm, precise client communication",
                'responsibilities' => "вести проект от брифа до рабочей документации\nсобирать мудборды и концептуальные решения\nготовить ТЗ для визуализаторов и смежников",
                'responsibilities_en' => "manage a project from brief to working documentation\nassemble moodboards and concept solutions\nprepare tasks for visualizers and adjacent specialists",
            ],
            [
                'title' => '3D-визуализатор интерьеров',
                'title_en' => 'Interior 3D Visualizer',
                'employment' => 'Удаленно',
                'employment_en' => 'Remote',
                'location' => 'Любой город',
                'location_en' => 'Any city',
                'salary' => 'сдельно, от 3 500 ₽ за ракурс',
                'salary_en' => 'piecework, from 3,500 RUB per view',
                'description' => 'Нужен человек с аккуратным светом, материалами и вниманием к атмосфере, а не только к геометрии.',
                'description_en' => 'We need someone with careful lighting, materials, and attention to atmosphere, not just geometry.',
                'requirements' => "3ds Max + Corona/V-Ray или сопоставимый стек\nчистые сцены и понятная организация файлов\nвкус к натуральным материалам, свету и композиции",
                'requirements_en' => "3ds Max + Corona/V-Ray or a comparable stack\nclean scenes and clear file organization\na feel for natural materials, light, and composition",
                'responsibilities' => "создавать фотореалистичные интерьерные ракурсы\nработать по ТЗ дизайнера и референсам\nготовить быстрые превью и финальные рендеры",
                'responsibilities_en' => "create photorealistic interior views\nwork from designer briefs and references\nprepare quick previews and final renders",
            ],
            [
                'title' => 'Архитектор-проектировщик',
                'title_en' => 'Architectural Designer',
                'employment' => 'Проектная занятость',
                'employment_en' => 'Project-based',
                'location' => 'Самара / удаленно',
                'location_en' => 'Samara / remote',
                'salary' => 'обсуждается по проекту',
                'salary_en' => 'discussed per project',
                'description' => 'Для коттеджей и малых коммерческих объектов: фасады, планировочная логика, рабочие решения.',
                'description_en' => 'For cottages and small commercial properties: facades, planning logic, and working solutions.',
                'requirements' => "уверенная работа с Archicad/Revit или AutoCAD\nпонимание конструктивной и инженерной логики\nпортфолио частных домов или коммерческих объектов",
                'requirements_en' => "confident work in Archicad/Revit or AutoCAD\nunderstanding of structural and engineering logic\nportfolio of private homes or commercial properties",
                'responsibilities' => "разрабатывать планировочные и фасадные концепции\nготовить комплект чертежей для согласования\nкоординироваться с визуализаторами и дизайнерами",
                'responsibilities_en' => "develop planning and facade concepts\nprepare drawing sets for approval\ncoordinate with visualizers and designers",
            ],
            [
                'title' => 'Менеджер дизайн-проектов',
                'title_en' => 'Design Project Manager',
                'employment' => 'Гибрид',
                'employment_en' => 'Hybrid',
                'location' => 'Самара',
                'location_en' => 'Samara',
                'salary' => 'от 80 000 ₽',
                'salary_en' => 'from 80,000 RUB',
                'description' => 'Человек, который держит сроки, документы и коммуникацию так, чтобы дизайнеры могли спокойно проектировать.',
                'description_en' => 'A person who keeps timelines, documents, and communication steady so designers can focus on design.',
                'requirements' => "опыт в дизайне, ремонте, архитектуре или смежной сфере\nструктурность и бережная настойчивость\nумение переводить хаос в понятный список действий",
                'requirements_en' => "experience in design, renovation, architecture, or a related field\nstructure and tactful persistence\nability to turn chaos into a clear action list",
                'responsibilities' => "вести календарь проекта и контроль этапов\nсобирать вводные от клиента и команды\nготовить статусы, счета и простую проектную документацию",
                'responsibilities_en' => "manage project schedules and stage control\ncollect inputs from clients and the team\nprepare statuses, invoices, and simple project documentation",
            ],
            [
                'title' => 'Специалист по комплектации',
                'title_en' => 'Procurement Specialist',
                'employment' => 'Гибрид / частичная занятость',
                'employment_en' => 'Hybrid / part-time',
                'location' => 'Самара',
                'location_en' => 'Samara',
                'salary' => 'фикс + бонус',
                'salary_en' => 'fixed pay + bonus',
                'description' => 'Для подбора материалов, мебели, света и поставщиков без случайных решений и сорванных сроков.',
                'description_en' => 'For selecting materials, furniture, lighting, and suppliers without random decisions or missed deadlines.',
                'requirements' => "знание рынка отделочных материалов и мебели\nвнимание к артикулам, срокам и ценам\nумение предлагать аналоги без потери визуальной идеи",
                'requirements_en' => "knowledge of the finishing materials and furniture market\nattention to SKUs, timelines, and prices\nability to suggest alternatives without losing the visual idea",
                'responsibilities' => "подбирать позиции под концепцию и бюджет\nвести таблицы, аналоги и статусы заказов\nкоммуницировать с салонами, поставщиками и подрядчиками",
                'responsibilities_en' => "select items for the concept and budget\nmaintain tables, alternatives, and order statuses\ncommunicate with showrooms, suppliers, and contractors",
            ],
        ])->each(fn (array $vacancy, int $index) => Vacancy::query()->updateOrCreate(
            ['title' => $vacancy['title']],
            [
                'title' => $vacancy['title'],
                'title_ru' => $vacancy['title'],
                'title_en' => $vacancy['title_en'],
                'employment' => $vacancy['employment'],
                'employment_ru' => $vacancy['employment'],
                'employment_en' => $vacancy['employment_en'],
                'location' => $vacancy['location'],
                'location_ru' => $vacancy['location'],
                'location_en' => $vacancy['location_en'],
                'salary' => $vacancy['salary'],
                'salary_ru' => $vacancy['salary'],
                'salary_en' => $vacancy['salary_en'],
                'description' => $vacancy['description'],
                'description_ru' => $vacancy['description'],
                'description_en' => $vacancy['description_en'],
                'requirements' => $vacancy['requirements'],
                'requirements_ru' => $vacancy['requirements'],
                'requirements_en' => $vacancy['requirements_en'],
                'responsibilities' => $vacancy['responsibilities'],
                'responsibilities_ru' => $vacancy['responsibilities'],
                'responsibilities_en' => $vacancy['responsibilities_en'],
                'position' => $index + 1,
                'is_active' => true,
            ],
        ));

        collect([
            [
                'Сколько длится разработка проекта?',
                'How long does project development take?',
                'Эскизная концепция занимает 2-4 недели, полный дизайн-проект обычно 8-14 недель.',
                'A concept design takes 2-4 weeks; a full design project usually takes 8-14 weeks.',
            ],
            [
                'Можно заказать только визуализацию?',
                'Can I order visualization only?',
                'Да. Для точного результата понадобятся планы, чертежи, референсы и список нужных ракурсов.',
                'Yes. For an accurate result, we need plans, drawings, references, and a list of required views.',
            ],
            [
                'Работаете удаленно?',
                'Do you work remotely?',
                'Да. Мы ведем проекты в Самаре и удаленно: используем видео-брифы, облачные доски и подробные чертежи.',
                'Yes. We handle projects in Samara and remotely using video briefs, cloud boards, and detailed working drawings.',
            ],
        ])->each(fn (array $faq, int $index) => Faq::query()->updateOrCreate(
            ['question' => $faq[0]],
            [
                'question' => $faq[0],
                'question_ru' => $faq[0],
                'question_en' => $faq[1],
                'answer' => $faq[2],
                'answer_ru' => $faq[2],
                'answer_en' => $faq[3],
                'position' => $index + 1,
                'is_published' => true,
            ],
        ));
    }
}
