<?php

namespace App\Support;

final class DefaultUiTexts
{
    public static function rows(): array
    {
        $rows = [
            self::row('header.menu', 'header', 'Кнопка открытия меню', 'Меню', 'Menu'),
            self::row('header.close', 'header', 'Кнопка закрытия меню', 'Закрыть', 'Close'),
            self::row('header.contact', 'header', 'Кнопка связи в шапке', 'Написать нам', 'Contact us'),
            self::row('header.mainMenuAria', 'header', 'ARIA-метка основного меню', 'Основное меню', 'Main menu'),
            self::row('header.backToMenu', 'header', 'Назад из услуг в меню', '← Меню', '← Menu'),
            self::row('header.backToServices', 'header', 'Назад в список услуг', '← Услуги', '← Services'),
            self::row('header.servicesRoot', 'header', 'Корневой пункт услуг', 'Услуги', 'Services'),
            self::row('header.servicesDescription', 'header', 'Описание структуры услуг в меню', 'Категории сгруппированы по составу работ. Внутри каждого направления - посадочные страницы услуг.', 'Categories are grouped by scope. Each direction contains service landing pages.'),
            self::row('header.languageRu', 'header', 'Переключатель русского языка', 'РУ', 'RU'),
            self::row('header.languageEn', 'header', 'Переключатель английского языка', 'ENG', 'ENG'),

            self::row('menu.about', 'navigation', 'Fallback: О нас', 'О нас', 'About us'),
            self::row('menu.portfolio', 'navigation', 'Fallback: Портфолио', 'Портфолио', 'Portfolio'),
            self::row('menu.blog', 'navigation', 'Fallback: Блог', 'Блог', 'Blog'),
            self::row('menu.contacts', 'navigation', 'Fallback: Контакты', 'Контакты', 'Contacts'),

            self::row('breadcrumbs.home', 'layout', 'Хлебные крошки: главная', 'Главная', 'Home'),
            self::row('breadcrumbs.aria', 'layout', 'ARIA-метка хлебных крошек', 'Хлебные крошки', 'Breadcrumbs'),
            self::row('fixed.vk', 'layout', 'Плавающая кнопка VK', 'VK', 'VK'),
            self::row('fixed.telegram', 'layout', 'Плавающая кнопка Telegram', 'Telegram', 'Telegram'),
            self::row('fixed.max', 'layout', 'Плавающая кнопка MAX', 'MAX', 'MAX'),

            self::row('footer.brandText', 'footer', 'Описание студии в подвале', 'Студия дизайна интерьеров, архитектурного проектирования, 3D-визуализации и ландшафтного дизайна.', 'Interior design, architectural design, 3D visualization, and landscape design studio.'),
            self::row('footer.brandKind', 'footer', 'Подпись под названием бренда', 'Студия', 'Studio'),
            self::row('footer.services', 'footer', 'Заголовок колонки услуг', 'Услуги', 'Services'),
            self::row('footer.sections', 'footer', 'Заголовок колонки разделов', 'Разделы', 'Sections'),
            self::row('footer.contacts', 'footer', 'Заголовок колонки контактов', 'Контакты', 'Contacts'),
            self::row('footer.phone', 'footer', 'Метка телефона', 'Телефон', 'Phone'),
            self::row('footer.email', 'footer', 'Метка почты', 'Почта', 'Email'),
            self::row('footer.schedule', 'footer', 'Метка графика', 'График', 'Hours'),
            self::row('footer.address', 'footer', 'Метка адреса', 'Адрес', 'Location'),
            self::row('footer.weekdays', 'footer', 'График будни', 'Пн–Пт: 9:00 – 20:00', 'Mon-Fri: 9:00 AM - 8:00 PM'),
            self::row('footer.weekend', 'footer', 'График выходные', 'Сб–Вс: 10:00 – 19:00', 'Sat-Sun: 10:00 AM - 7:00 PM'),
            self::row('footer.locationCity', 'footer', 'Город в подвале', 'Самара, Россия', 'Samara, Russia'),
            self::row('footer.remote', 'footer', 'Пояснение про удаленную работу', 'Работаем удалённо по всему миру', 'Remote projects worldwide'),
            self::row('footer.career', 'footer', 'Ссылка карьеры', 'Карьера', 'Careers'),
            self::row('footer.partners', 'footer', 'Ссылка партнерам', 'Партнёрам', 'For Partners'),
            self::row('footer.privacy', 'footer', 'Политика конфиденциальности', 'Политика конфиденциальности', 'Privacy Policy'),
            self::row('footer.agreement', 'footer', 'Пользовательское соглашение', 'Пользовательское соглашение', 'User Agreement'),
            self::row('footer.owner', 'footer', 'Юридическая строка в подвале', 'ИП Шакинене Екатерина Игоревна', 'Individual entrepreneur Ekaterina Shakinene'),

            self::row('home.featureProject01', 'home', 'Подпись первого избранного проекта', 'Избранный проект 01', 'Featured project 01'),
            self::row('home.featureProject02', 'home', 'Подпись второго избранного проекта', 'Избранный проект 02', 'Featured project 02'),
            self::row('home.projectTypeLabel', 'home', 'Подпись типа проекта', 'Тип', 'Type'),
            self::row('home.projectLocationLabel', 'home', 'Подпись локации проекта', 'Локация', 'Location'),
            self::row('home.projectYearLabel', 'home', 'Подпись года проекта', 'Год', 'Year'),

            self::row('about.label', 'about-home', 'Секция о студии: метка', 'О нас', 'About us'),
            self::row('about.title', 'about-home', 'Секция о студии: заголовок', 'Студия, которая собирает проект как систему', 'A studio that builds every project as a system'),
            self::row('about.text', 'about-home', 'Секция о студии: текст', '3D Smart Design Studio соединяет интерьер, архитектуру, визуализацию, ландшафт и комплектацию в один спокойный процесс. Клиент видит не набор картинок, а понятный маршрут от идеи до реализации.', '3D Smart Design Studio connects interior design, architecture, visualization, landscape, and procurement into one calm process. The client sees a clear route from idea to implementation, not just a set of images.'),
            self::row('about.moreButton', 'about-home', 'Кнопка о студии', 'Подробнее о студии', 'More about the studio'),
            self::row('about.contactButton', 'about-home', 'Кнопка обсудить проект', 'Обсудить проект', 'Discuss a project'),
            self::row('about.imageAlt', 'about-home', 'Alt изображения о студии', 'Интерьерный проект 3D Smart Design Studio', 'Interior project by 3D Smart Design Studio'),
            self::row('about.imageEyebrow', 'about-home', 'Подпись изображения о студии', 'Дизайн с логикой', 'Design with logic'),
            self::row('about.imageTitle', 'about-home', 'Заголовок изображения о студии', 'Красивое решение должно выдерживать стройку, бюджет и повседневную жизнь.', 'A beautiful solution must withstand construction, budget, and everyday life.'),
            self::row('about.fact1.value', 'about-home', 'Факт 1: значение', '10 лет', '10 years'),
            self::row('about.fact1.label', 'about-home', 'Факт 1: подпись', 'практики в интерьерах, архитектуре и 3D', 'of practice in interiors, architecture, and 3D'),
            self::row('about.fact2.value', 'about-home', 'Факт 2: значение', '25+', '25+'),
            self::row('about.fact2.label', 'about-home', 'Факт 2: подпись', 'специалистов под разные масштабы задач', 'specialists for projects of different scales'),
            self::row('about.fact3.value', 'about-home', 'Факт 3: значение', '90%', '90%'),
            self::row('about.fact3.label', 'about-home', 'Факт 3: подпись', 'клиентов возвращаются с новыми проектами', 'of clients return with new projects'),
            self::row('about.principle1', 'about-home', 'Принцип 1', 'начинаем с сценария жизни, а не с декора', 'we start with life scenarios, not decoration'),
            self::row('about.principle2', 'about-home', 'Принцип 2', 'проверяем красоту чертежами, светом и бюджетом', 'we test beauty with drawings, light, and budget'),
            self::row('about.principle3', 'about-home', 'Принцип 3', 'ведем проект до реализации, комплектации и замен', 'we guide the project through implementation, procurement, and replacements'),

            self::row('awards.label', 'awards-home', 'Награды: метка', 'Награды и дипломы', 'Awards and diplomas'),
            self::row('awards.title', 'awards-home', 'Награды: заголовок', 'Подтверждения опыта и доверия', 'Proof of experience and trust'),
            self::row('awards.text', 'awards-home', 'Награды: текст', 'Дипломы, сертификаты и благодарности показывают, что студия работает открыто, системно и подтверждает качество не только портфолио.', 'Diplomas, certificates, and letters of appreciation show that the studio works transparently, systematically, and proves quality beyond the portfolio.'),
            self::row('awards.allButton', 'awards-home', 'Кнопка всех наград', 'Все награды', 'All awards'),

            self::row('brandStrip.label', 'partners-home', 'Партнеры: метка', 'Нам доверяют', 'Trusted by'),
            self::row('brandStrip.title', 'partners-home', 'Партнеры: заголовок', 'Девелоперы и застройщики', 'Developers and construction companies'),
            self::row('brandStrip.text', 'partners-home', 'Партнеры: текст', 'Проекты студии выбирают частные клиенты, девелоперы и строительные компании, которым важны понятная визуализация и аккуратная реализация.', 'The studio is chosen by private clients, developers, and construction companies that need clear visualization and careful implementation.'),

            self::row('servicesSummary.label', 'services-home', 'Услуги: метка', 'Услуги и цены', 'Services and pricing'),
            self::row('servicesSummary.title', 'services-home', 'Услуги: заголовок', 'Услуги и цены', 'Services and pricing'),
            self::row('servicesSummary.text', 'services-home', 'Услуги: текст', 'Стоимость фиксируется после брифа и состава работ. Ниже - понятная стартовая структура по основным направлениям студии.', 'The final price is fixed after the brief and scope are clear. Below is a simple starting structure for the studio’s core directions.'),
            self::row('servicePages.label', 'services-home', 'Направления: метка', 'Направления услуг', 'Service directions'),
            self::row('servicePages.title', 'services-home', 'Направления: заголовок', 'Направления 3D Smart Design Studio', '3D Smart Design Studio directions'),
            self::row('servicePages.text', 'services-home', 'Направления: текст', 'Основные направления держат SEO-вес, а посадочные страницы вложены как подпункты: так клиент быстрее ориентируется в услугах.', 'Core directions keep SEO value, while landing pages are nested as subitems so clients can navigate services faster.'),
            self::row('servicePages.categoryLabel', 'services-home', 'Подпись категории услуг', 'Основная категория', 'Main category'),
            self::row('workflow.label', 'services-home', 'Этапы: метка', 'Этапы работы', 'Workflow'),
            self::row('workflow.title', 'services-home', 'Этапы: заголовок', 'Этапы работы без хаоса и лишней коммуникации', 'A workflow without chaos or excess communication'),

            self::row('serviceDetail.hero.calcButton', 'service-detail', 'Услуга: кнопка расчета', 'Рассчитать стоимость проекта', 'Estimate project cost'),
            self::row('serviceDetail.hero.allServicesButton', 'service-detail', 'Услуга: кнопка всех услуг', 'Все услуги', 'All services'),
            self::row('serviceDetail.hero.priceLabel', 'service-detail', 'Услуга: метка цены', 'Стоимость', 'Price'),
            self::row('serviceDetail.hero.timelineLabel', 'service-detail', 'Услуга: метка срока', 'Срок', 'Timeline'),
            self::row('serviceDetail.compare.label', 'service-detail', 'До/После: метка', 'До / После', 'Before / After'),
            self::row('serviceDetail.compare.title', 'service-detail', 'До/После: заголовок', 'Как идея превращается в готовое пространство', 'How an idea becomes a finished space'),
            self::row('serviceDetail.compare.text', 'service-detail', 'До/После: текст', 'Здесь можно поставить реальные пары изображений по услуге: исходное состояние и итоговый результат после проектирования.', 'Use real image pairs for the service here: the initial state and the final result after design.'),
            self::row('serviceDetail.compare.before', 'service-detail', 'До/После: подпись до', 'До', 'Before'),
            self::row('serviceDetail.compare.after', 'service-detail', 'До/После: подпись после', 'После', 'After'),
            self::row('serviceDetail.compare.aria', 'service-detail', 'До/После: ARIA', 'Сравнение до и после', 'Before and after comparison'),
            self::row('serviceDetail.portfolio.label', 'service-detail', 'Портфолио услуги: метка', 'Тематическое портфолио', 'Related portfolio'),
            self::row('serviceDetail.portfolio.title', 'service-detail', 'Портфолио услуги: заголовок', 'Целевые проекты по услуге', 'Projects for this service'),
            self::row('serviceDetail.portfolio.text', 'service-detail', 'Портфолио услуги: текст', 'Карточки ведут на индивидуальные страницы проектов с уникальным URL. После наполнения CMS здесь останутся только релевантные кейсы.', 'Cards open individual project pages with unique URLs. After CMS content is filled, only relevant cases will remain here.'),
            self::row('serviceDetail.documents.label', 'service-detail', 'Документация: метка', 'Документация', 'Documentation'),
            self::row('serviceDetail.documents.title', 'service-detail', 'Документация: заголовок', 'Что входит в состав рабочей документации', 'What is included in the working documentation'),
            self::row('serviceDetail.documents.text', 'service-detail', 'Документация: текст', 'Блок готов под реальные чертежи, ведомости, дендропланы и схемы инженерии. Изображение можно открыть крупно.', 'This block is ready for real drawings, schedules, planting plans, and engineering diagrams. The image can be opened larger.'),
            self::row('serviceDetail.documents.sheetLabel', 'service-detail', 'Документация: лист', 'Лист', 'Sheet'),
            self::row('serviceDetail.documents.zoomHint', 'service-detail', 'Документация: подсказка увеличения', 'Нажмите, чтобы увеличить', 'Click to zoom'),
            self::row('serviceDetail.documents.closeAria', 'service-detail', 'Документация: ARIA закрытия', 'Закрыть просмотр', 'Close preview'),
            self::row('serviceDetail.documents.zoomAlt', 'service-detail', 'Документация: alt увеличенного изображения', 'Увеличенный лист документации', 'Enlarged documentation sheet'),
            self::row('serviceDetail.benefits.label', 'service-detail', 'Преимущества услуги: метка', 'Почему это работает', 'Why it works'),
            self::row('serviceDetail.benefits.title', 'service-detail', 'Преимущества услуги: заголовок', 'Страница собрана из реальной структуры услуги', 'The page is built from the real service structure'),
            self::row('serviceDetail.process.title', 'service-detail', 'Процесс услуги: заголовок', 'Как движется проект', 'How the project moves'),
            self::row('serviceDetail.process.text', 'service-detail', 'Процесс услуги: текст', 'Процесс коротко пересобран из старых страниц: от заявки и исходных данных до финальных файлов, чертежей или сопровождения.', 'The process is summarized from the old pages: from request and source data to final files, drawings, or support.'),
            self::row('serviceDetail.expert.label', 'service-detail', 'Экспертный блок: метка', 'Экспертность', 'Expertise'),
            self::row('serviceDetail.expert.title', 'service-detail', 'Экспертный блок: заголовок', 'Остались вопросы по проекту? Давайте обсудим', 'Have project questions? Let us discuss them'),

            self::row('portfolio.filters.directions', 'portfolio-home', 'Фильтр направлений', 'Directions', 'Directions'),
            self::row('portfolio.filters.square', 'portfolio-home', 'Фильтр площади', 'Square', 'Area'),
            self::row('portfolio.filters.tone', 'portfolio-home', 'Фильтр тона', 'Tone', 'Tone'),
            self::row('portfolio.filters.allProjects', 'portfolio-home', 'Все проекты', 'All projects', 'All projects'),
            self::row('portfolio.filters.allOptions', 'portfolio-home', 'Все опции', 'All options', 'All options'),
            self::row('portfolio.filters.searchPlaceholder', 'portfolio-home', 'Плейсхолдер поиска портфолио', 'Country, City', 'Country, City'),
            self::row('portfolio.preview', 'portfolio-home', 'Подпись карточки портфолио', 'Предпросмотр', 'Preview'),
            self::row('portfolio.selectedProjectLabel', 'portfolio-home', 'Метка выбранного проекта', 'Выбранный проект', 'Selected project'),
            self::row('portfolio.detailsButton', 'portfolio-home', 'Кнопка подробностей проекта', 'Подробная информация', 'Details'),
            self::row('portfolio.viewImage', 'portfolio-home', 'Кнопка просмотра изображения', 'Смотреть', 'View'),
            self::row('portfolio.taskTitle', 'portfolio-home', 'Карточка проекта: задача', 'Задача', 'Task'),
            self::row('portfolio.taskText', 'portfolio-home', 'Карточка проекта: текст задачи', 'Собрать цельный визуальный код объекта: планировка, материалы, свет и настроение.', 'Build a complete visual code for the object: layout, materials, lighting, and mood.'),
            self::row('portfolio.resultTitle', 'portfolio-home', 'Карточка проекта: результат', 'Результат', 'Result'),
            self::row('portfolio.resultText', 'portfolio-home', 'Карточка проекта: текст результата', 'Проект можно презентовать, согласовывать с подрядчиками и использовать как базу реализации.', 'The project can be presented, coordinated with contractors, and used as an implementation base.'),
            self::row('portfolio.formatTitle', 'portfolio-home', 'Карточка проекта: формат', 'Формат', 'Format'),
            self::row('portfolio.formatText', 'portfolio-home', 'Карточка проекта: текст формата', '3D-ракурсы, подбор решений, рабочая логика и визуальная подача для клиента.', '3D views, solution selection, working logic, and visual presentation for the client.'),
            self::row('portfolio.lightboxAria', 'portfolio-home', 'ARIA просмотра проекта', 'Просмотр изображения проекта', 'Project image preview'),
            self::row('portfolio.closeViewAria', 'portfolio-home', 'ARIA закрыть просмотр', 'Закрыть просмотр', 'Close preview'),
            self::row('portfolio.prevImageAria', 'portfolio-home', 'ARIA предыдущее изображение', 'Предыдущее изображение', 'Previous image'),
            self::row('portfolio.nextImageAria', 'portfolio-home', 'ARIA следующее изображение', 'Следующее изображение', 'Next image'),
            self::row('portfolio.compareAria', 'portfolio-home', 'ARIA сравнения до/после', 'Сравнение до и после', 'Before and after comparison'),

            self::row('faq.label', 'faq-home', 'FAQ: метка', 'FAQ', 'FAQ'),
            self::row('faq.title', 'faq-home', 'FAQ: заголовок', 'Частые вопросы', 'Frequently asked questions'),

            self::row('contact.label', 'contact-home', 'Контакты: метка', 'Контакты', 'Contacts'),
            self::row('contact.titleLine1', 'contact-home', 'Контакты: заголовок строка 1', 'Свяжитесь', 'Get in'),
            self::row('contact.titleLine2', 'contact-home', 'Контакты: заголовок строка 2', 'с нами', 'touch'),
            self::row('contact.phoneLabel', 'contact-home', 'Контакты: телефон', 'Телефон', 'Phone'),
            self::row('contact.emailLabel', 'contact-home', 'Контакты: почта', 'Почта', 'Email'),
            self::row('contact.scheduleLabel', 'contact-home', 'Контакты: график', 'График', 'Hours'),
            self::row('contact.locationLabel', 'contact-home', 'Контакты: локация', 'Локация', 'Location'),
            self::row('contact.locationCity', 'contact-home', 'Контакты: город', 'Самара, Россия', 'Samara, Russia'),
            self::row('contact.remoteText', 'contact-home', 'Контакты: удаленно', 'Работаем удалённо по всему миру', 'Remote projects worldwide'),
            self::row('contact.formLabel', 'contact-home', 'Форма: метка', 'Написать нам', 'Write to us'),
            self::row('contact.formTitle', 'contact-home', 'Форма: заголовок', 'Оставить заявку', 'Send a request'),
            self::row('contact.namePlaceholder', 'contact-home', 'Форма: имя', 'Ваше имя', 'Your name'),
            self::row('contact.contactPlaceholder', 'contact-home', 'Форма: контакт', 'Телефон или e-mail', 'Phone or email'),
            self::row('contact.servicePlaceholder', 'contact-home', 'Форма: услуга', 'Выберите услугу', 'Choose a service'),
            self::row('contact.messagePlaceholder', 'contact-home', 'Форма: сообщение', 'Коротко о проекте', 'Briefly about the project'),
            self::row('contact.submitButton', 'contact-home', 'Форма: отправить', 'Отправить заявку', 'Send request'),
            self::row('contact.sentButton', 'contact-home', 'Форма: отправлено', '✓ Заявка отправлена', '✓ Request sent'),
            self::row('contact.mapTitle', 'contact-home', 'Карта: title iframe', 'Карта 3D Smart Design Studio', '3D Smart Design Studio map'),
            self::row('contact.mapEyebrow', 'contact-home', 'Карта: подпись', 'Студия', 'Studio'),
            self::row('contact.mapLocation', 'contact-home', 'Карта: локация', 'Самара · работаем удаленно', 'Samara · remote projects'),

            self::row('modal.closeAria', 'modal', 'Модалка: закрыть', 'Закрыть', 'Close'),
            self::row('modal.eyebrow', 'modal', 'Модалка: подпись', 'Связаться с нами', 'Contact us'),
            self::row('modal.defaultTitle', 'modal', 'Модалка: заголовок', 'Напишите нам', 'Write to us'),
            self::row('modal.promoTitle', 'modal', 'Модалка: акция', 'Записаться на акцию', 'Sign up for the promotion'),
            self::row('modal.messengerLabel', 'modal', 'Модалка: мессенджеры', 'Написать в мессенджере', 'Message us'),
            self::row('modal.callLabel', 'modal', 'Модалка: звонок', 'Звонок', 'Call'),
            self::row('modal.namePlaceholder', 'modal', 'Модалка: имя', 'Ваше имя', 'Your name'),
            self::row('modal.contactPlaceholder', 'modal', 'Модалка: контакт', 'Телефон или e-mail', 'Phone or email'),
            self::row('modal.submitButton', 'modal', 'Модалка: отправить', 'Отправить заявку', 'Send request'),
            self::row('modal.consentStart', 'modal', 'Модалка: согласие начало', 'Я ознакомился(-ась) с', 'I have read the'),
            self::row('modal.agreement', 'modal', 'Модалка: соглашение', 'пользовательским соглашением', 'user agreement'),
            self::row('modal.and', 'modal', 'Модалка: союз', 'и', 'and'),
            self::row('modal.privacy', 'modal', 'Модалка: политика', 'политикой конфиденциальности', 'privacy policy'),
            self::row('modal.responseTime', 'modal', 'Модалка: время ответа', 'Отвечаем в течение рабочего дня · Пн-Пт 9:00-20:00', 'We reply within one business day · Mon-Fri 9:00 AM - 8:00 PM'),
        ];

        return [
            ...$rows,
            ...self::workflowRows(),
            ...self::styleLabRows(),
            ...self::quizRows(),
            ...self::portfolioFilterRows(),
            ...self::contactStatRows(),
            ...self::aboutFullRows(),
            ...self::pageShellRows(),
        ];
    }

    /**
     * Строки полной страницы "О нас" (/o-nas).
     * Группа about-full: hero, статистика, направления, принципы, команда, процесс, CTA.
     */
    private static function aboutFullRows(): array
    {
        $rows = [
            // Hero / банер
            self::row('aboutFull.hero.label', 'about-full', 'О нас: метка над заголовком', 'О студии', 'About the studio'),
            self::row('aboutFull.hero.title', 'about-full', 'О нас: главный заголовок', 'Дизайн с умом', 'Design with logic'),
            self::row('aboutFull.hero.text', 'about-full', 'О нас: описание в банере', '3D Smart Design Studio соединяет эстетику, инженерную логику и понятный процесс. Заказчик видит не просто красивую картинку, а уверенный маршрут от идеи до реализации.', '3D Smart Design Studio combines aesthetics, engineering logic, and a clear process. The client sees not just a beautiful image, but a confident route from idea to implementation.'),
            self::row('aboutFull.hero.cta1', 'about-full', 'О нас: основная кнопка', 'Обсудить проект', 'Discuss a project'),
            self::row('aboutFull.hero.cta2', 'about-full', 'О нас: вторая кнопка', 'Смотреть работы', 'View projects'),
            self::row('aboutFull.hero.imageLabel', 'about-full', 'О нас: подпись на изображении', 'Премиальная подача начинается с точной системы', 'Premium presentation starts with a precise system'),
            self::row('aboutFull.hero.approachLabel', 'about-full', 'О нас: подпись «Что внутри подхода»', 'Что внутри подхода', 'Inside the approach'),

            // Принципы
            self::row('aboutFull.principles.label', 'about-full', 'Принципы: метка', 'Принципы', 'Principles'),
            self::row('aboutFull.principles.title', 'about-full', 'Принципы: заголовок', 'Не декорируем хаос. Собираем систему.', 'We do not decorate chaos. We build a system.'),
            self::row('aboutFull.principles.text', 'about-full', 'Принципы: текст', 'Хороший проект ощущается легко, потому что за ним стоит точная работа: планировочная логика, материалы, свет, документация, бюджет и коммуникация.', 'A good project feels effortless because precise work stands behind it: planning logic, materials, light, documentation, budget, and communication.'),

            // Команда
            self::row('aboutFull.work.imageLabel1', 'about-full', 'Команда: подпись большого изображения', 'Интерьер как рабочий сценарий жизни', 'Interior as a working life scenario'),
            self::row('aboutFull.work.imageLabel2', 'about-full', 'Команда: подпись изображения архитектуры', 'Архитектура и визуализация', 'Architecture and visualization'),
            self::row('aboutFull.work.imageLabel3', 'about-full', 'Команда: подпись изображения ландшафта', 'Ландшафт как продолжение дома', 'Landscape as an extension of the house'),
            self::row('aboutFull.team.label', 'about-full', 'Команда: метка', 'Команда', 'Team'),
            self::row('aboutFull.team.title', 'about-full', 'Команда: заголовок', 'Под задачу собирается нужный состав', 'The right team is assembled for each task'),
            self::row('aboutFull.team.text', 'about-full', 'Команда: текст', 'В проект могут входить дизайнеры, архитекторы, 3D-визуализаторы, комплектаторы и специалисты по рабочей документации. Клиенту не нужно координировать всех отдельно: мы держим проектную логику внутри студии.', 'Designers, architects, 3D visualizers, procurement specialists, and working documentation experts can join a project. The client does not need to coordinate everyone separately: we keep the project logic inside the studio.'),

            // Процесс
            self::row('aboutFull.process.label', 'about-full', 'Процесс: метка', 'Процесс', 'Process'),
            self::row('aboutFull.process.title', 'about-full', 'Процесс: заголовок', 'Как мы работаем', 'How we work'),
            self::row('aboutFull.process.text', 'about-full', 'Процесс: текст', 'Каждый этап дает понятный результат: от первого разговора до комплекта файлов, с которым можно принимать решения и двигаться дальше.', 'Each stage produces a clear result: from the first conversation to a set of files you can use to make decisions and move forward.'),

            // CTA (оранжевый банер)
            self::row('aboutFull.cta.eyebrow', 'about-full', 'CTA: надзаголовок', 'Начнем с разговора', 'Let’s start with a conversation'),
            self::row('aboutFull.cta.title', 'about-full', 'CTA: заголовок', 'Расскажите о проекте, а мы предложим маршрут работы.', 'Tell us about the project and we will suggest a way forward.'),
            self::row('aboutFull.cta.button', 'about-full', 'CTA: кнопка', 'Связаться', 'Get in touch'),
        ];

        // Статистика (4 пункта)
        foreach (self::aboutFullStats() as $id => $stat) {
            $rows[] = self::row("aboutFull.stats.{$id}.value", 'about-full', "Статистика {$id}: значение", $stat['value'][0], $stat['value'][1]);
            $rows[] = self::row("aboutFull.stats.{$id}.label", 'about-full', "Статистика {$id}: подпись", $stat['label'][0], $stat['label'][1]);
        }

        // Направления (6 пунктов в правой панели hero)
        foreach (self::aboutFullDirections() as $id => $direction) {
            $rows[] = self::row("aboutFull.directions.{$id}", 'about-full', "Направление {$id}", $direction[0], $direction[1]);
        }

        // Принципы (4 пункта, заголовок + текст)
        foreach (self::aboutFullPrinciples() as $id => $principle) {
            $rows[] = self::row("aboutFull.principles.item.{$id}.title", 'about-full', "Принцип {$id}: заголовок", $principle['title'][0], $principle['title'][1]);
            $rows[] = self::row("aboutFull.principles.item.{$id}.text", 'about-full', "Принцип {$id}: текст", $principle['text'][0], $principle['text'][1]);
        }

        // Буллеты команды (4 пункта)
        foreach (self::aboutFullTeamBullets() as $id => $bullet) {
            $rows[] = self::row("aboutFull.team.bullet.{$id}", 'about-full', "Команда: пункт {$id}", $bullet[0], $bullet[1]);
        }

        // Этапы процесса (6 шагов)
        foreach (self::aboutFullProcessSteps() as $id => $step) {
            $rows[] = self::row("aboutFull.process.step.{$id}.title", 'about-full', "Процесс {$id}: заголовок", $step['title'][0], $step['title'][1]);
            $rows[] = self::row("aboutFull.process.step.{$id}.text", 'about-full', "Процесс {$id}: текст", $step['text'][0], $step['text'][1]);
        }

        return $rows;
    }

    private static function aboutFullStats(): array
    {
        return [
            '1' => [
                'value' => ['10 лет', '10 years'],
                'label' => ['проектируем интерьеры, архитектуру и визуализации', 'designing interiors, architecture, and visualizations'],
            ],
            '2' => [
                'value' => ['25+', '25+'],
                'label' => ['специалистов подключаются под масштаб задачи', 'specialists join based on the task scale'],
            ],
            '3' => [
                'value' => ['90%', '90%'],
                'label' => ['клиентов возвращаются с новыми проектами', 'of clients return with new projects'],
            ],
            '4' => [
                'value' => ['1500+', '1500+'],
                'label' => ['задач в год: от рендера до полного сопровождения', 'tasks per year: from a render to full support'],
            ],
        ];
    }

    private static function aboutFullDirections(): array
    {
        return [
            '1' => ['Дизайн интерьера', 'Interior design'],
            '2' => ['Коммерческие пространства', 'Commercial spaces'],
            '3' => ['Архитектурное проектирование', 'Architectural design'],
            '4' => ['Архитектурная 3D-визуализация', 'Architectural 3D visualization'],
            '5' => ['Ландшафтный дизайн', 'Landscape design'],
            '6' => ['Комплектация объекта', 'Project procurement'],
        ];
    }

    private static function aboutFullPrinciples(): array
    {
        return [
            '1' => [
                'title' => ['Сначала сценарий', 'Scenario first'],
                'text' => ['Мы начинаем не с красивой картинки, а с того, как человек будет жить, работать, принимать гостей и двигаться по пространству.', 'We start not with a beautiful image, but with how a person will live, work, host guests, and move through the space.'],
            ],
            '2' => [
                'title' => ['Красота держится на чертежах', 'Beauty rests on drawings'],
                'text' => ['Визуальная идея сразу проверяется планировкой, материалами, светом, узлами, бюджетом и возможностью реализации.', 'The visual idea is immediately tested against the layout, materials, lighting, details, budget, and feasibility.'],
            ],
            '3' => [
                'title' => ['Проект не бросается после рендера', 'The project is not dropped after the render'],
                'text' => ['Помогаем с комплектацией, заменами, подрядчиками и авторским сопровождением, чтобы результат не распался на стройке.', 'We help with procurement, substitutions, contractors, and author supervision so the result does not fall apart on site.'],
            ],
            '4' => [
                'title' => ['Удаленно тоже точно', 'Precise even remotely'],
                'text' => ['Работаем с клиентами из разных городов через видео-брифы, 3D-презентации, облачные доски и понятные пакеты документации.', 'We work with clients from different cities through video briefs, 3D presentations, cloud boards, and clear documentation packages.'],
            ],
        ];
    }

    private static function aboutFullTeamBullets(): array
    {
        return [
            '1' => ['единая коммуникация', 'single communication channel'],
            '2' => ['понятные этапы', 'clear stages'],
            '3' => ['фиксированный состав работ', 'fixed scope of work'],
            '4' => ['реалистичная визуальная подача', 'realistic visual presentation'],
        ];
    }

    private static function aboutFullProcessSteps(): array
    {
        return [
            '1' => [
                'title' => ['Бриф', 'Brief'],
                'text' => ['Фиксируем задачу, стиль, бюджет, сроки, состав помещений и ограничения объекта.', 'We capture the task, style, budget, timeline, room layout, and site constraints.'],
            ],
            '2' => [
                'title' => ['Концепция', 'Concept'],
                'text' => ['Собираем планировку, референсы, материалы, свет и первый визуальный язык проекта.', 'We assemble the layout, references, materials, lighting, and the first visual language of the project.'],
            ],
            '3' => [
                'title' => ['3D и согласование', '3D and approval'],
                'text' => ['Показываем будущий результат до закупок и ремонта, спокойно проходим итерации правок.', 'We show the future result before procurement and renovation, calmly working through rounds of edits.'],
            ],
            '4' => [
                'title' => ['Документация', 'Documentation'],
                'text' => ['Готовим чертежи, ведомости, спецификации и техническую базу для подрядчиков.', 'We prepare drawings, schedules, specifications, and the technical base for contractors.'],
            ],
            '5' => [
                'title' => ['Комплектация', 'Procurement'],
                'text' => ['Подбираем материалы, мебель, свет, аналоги и поставщиков под согласованный бюджет.', 'We select materials, furniture, lighting, alternatives, and suppliers to fit the agreed budget.'],
            ],
            '6' => [
                'title' => ['Сопровождение', 'Support'],
                'text' => ['Отвечаем на вопросы реализации и помогаем сохранить авторскую идею в реальном объекте.', 'We answer implementation questions and help preserve the author’s idea in the real object.'],
            ],
        ];
    }

    private static function workflowRows(): array
    {
        return collect([
            ['brief', 'Бриф', 'Brief'],
            ['concept', 'Концепция', 'Concept'],
            ['visualization', 'Визуализация', 'Visualization'],
            ['drawings', 'Чертежи', 'Drawings'],
            ['procurement', 'Комплектация', 'Procurement'],
            ['support', 'Сопровождение', 'Support'],
        ])->map(fn (array $item, int $index): array => self::row(
            "workflow.steps.{$item[0]}",
            'services-home',
            'Этап ' . ($index + 1),
            $item[1],
            $item[2],
        ))->all();
    }

    private static function portfolioFilterRows(): array
    {
        return collect([
            ['square.compact', 'до 100 м²', 'up to 100 m²'],
            ['square.medium', '100-250 м²', '100-250 m²'],
            ['square.large', '250+ м²', '250+ m²'],
            ['tone.warm', 'Тёплый', 'Warm'],
            ['tone.neutral', 'Нейтральный', 'Neutral'],
            ['tone.dark', 'Тёмный', 'Dark'],
        ])->map(fn (array $item): array => self::row(
            "portfolio.filters.{$item[0]}",
            'portfolio-home',
            'Опция фильтра портфолио',
            $item[1],
            $item[2],
        ))->all();
    }

    private static function contactStatRows(): array
    {
        return collect([
            ['1.value', '1', '1'],
            ['1.suffix', ' час', ' hour'],
            ['1.label', 'среднее время ответа', 'average response time'],
            ['2.value', '7', '7'],
            ['2.suffix', ' стран', ' countries'],
            ['2.label', 'география проектов', 'project geography'],
            ['3.value', '10', '10'],
            ['3.suffix', ' лет', ' years'],
            ['3.label', 'на рынке дизайна', 'in the design market'],
        ])->map(fn (array $item): array => self::row(
            "contact.stats.{$item[0]}",
            'contact-home',
            'Статистика контактов',
            $item[1],
            $item[2],
        ))->all();
    }

    private static function styleLabRows(): array
    {
        $rows = [
            self::row('styleLab.eyebrow', 'style-lab', 'Подбор стиля: метка', 'Подбор стиля', 'Style lab'),
            self::row('styleLab.title', 'style-lab', 'Подбор стиля: заголовок', 'Соберите настроение будущего интерьера', 'Build the mood of your future interior'),
            self::row('styleLab.text', 'style-lab', 'Подбор стиля: текст', 'Быстрый интерактивный эскиз показывает, как меняется ощущение проекта от стиля, материала и светового сценария.', 'A quick interactive sketch shows how the project mood changes with style, material, and lighting scenario.'),
            self::row('styleLab.briefLabel', 'style-lab', 'Бриф: метка', 'Бриф в один взгляд', 'Brief at a glance'),
            self::row('styleLab.styleLabel', 'style-lab', 'Метка стиля', 'Стиль', 'Style'),
            self::row('styleLab.materialLabel', 'style-lab', 'Метка материала', 'Материал', 'Material'),
            self::row('styleLab.lightLabel', 'style-lab', 'Метка света', 'Свет', 'Light'),
            self::row('styleLab.saveButton', 'style-lab', 'Кнопка сохранить', 'Сохранить подборку', 'Save selection'),
            self::row('styleLab.calculateButton', 'style-lab', 'Кнопка расчета', 'Рассчитать проект', 'Estimate project'),
            self::row('styleLab.savedMessage', 'style-lab', 'Сообщение сохранено', 'Подборка сохранена', 'Selection saved'),
            self::row('styleLab.colorAriaPrefix', 'style-lab', 'ARIA цвет', 'Цвет', 'Color'),
            self::row('styleLab.accentMaterialAria', 'style-lab', 'ARIA акцент', 'Акцентный материал', 'Accent material'),
        ];

        $styles = [
            ['minimal', 'Современный', 'Modern', 'Спокойный интерьер с чистой геометрией и воздухом', 'A calm interior with clean geometry and air', 'минимализм, контемпорари, скрытое хранение', 'minimalism, contemporary, hidden storage'],
            ['classic', 'Неоклассика', 'Neoclassical', 'Мягкая элегантность без перегруза и музейности', 'Soft elegance without overload or museum stiffness', 'симметрия, уют, благородные материалы', 'symmetry, comfort, noble materials'],
            ['loft', 'Лофт', 'Loft', 'Фактурное пространство с сильным характером', 'A textured space with strong character', 'камень, металл, открытая планировка', 'stone, metal, open layout'],
            ['eco', 'Эко', 'Eco', 'Светлый сценарий с природной палитрой и тактильностью', 'A bright scenario with natural palette and tactility', 'дерево, лен, зелень, естественный свет', 'wood, linen, greenery, natural light'],
        ];

        foreach ($styles as [$id, $labelRu, $labelEn, $headlineRu, $headlineEn, $moodRu, $moodEn]) {
            $rows[] = self::row("styleLab.styles.{$id}.label", 'style-lab', "Стиль {$id}: название", $labelRu, $labelEn);
            $rows[] = self::row("styleLab.styles.{$id}.headline", 'style-lab', "Стиль {$id}: заголовок", $headlineRu, $headlineEn);
            $rows[] = self::row("styleLab.styles.{$id}.mood", 'style-lab', "Стиль {$id}: настроение", $moodRu, $moodEn);
        }

        $materials = [
            ['wood', 'Теплое дерево', 'Warm wood', 'лиственница / дуб', 'larch / oak'],
            ['stone', 'Крупный камень', 'Large-format stone', 'травертин / кварц', 'travertine / quartz'],
            ['textile', 'Мягкий текстиль', 'Soft textile', 'лен / букле', 'linen / boucle'],
            ['metal', 'Темный металл', 'Dark metal', 'графит / бронза', 'graphite / bronze'],
        ];

        foreach ($materials as [$id, $labelRu, $labelEn, $textureRu, $textureEn]) {
            $rows[] = self::row("styleLab.materials.{$id}.label", 'style-lab', "Материал {$id}: название", $labelRu, $labelEn);
            $rows[] = self::row("styleLab.materials.{$id}.texture", 'style-lab', "Материал {$id}: текстура", $textureRu, $textureEn);
        }

        $lights = [
            ['morning', 'Утро', 'Morning', 'мягкий дневной свет', 'soft daylight'],
            ['evening', 'Вечер', 'Evening', 'теплые акцентные группы', 'warm accent groups'],
            ['gallery', 'Галерея', 'Gallery', 'контраст и точечные акценты', 'contrast and spot accents'],
        ];

        foreach ($lights as [$id, $labelRu, $labelEn, $noteRu, $noteEn]) {
            $rows[] = self::row("styleLab.lights.{$id}.label", 'style-lab', "Свет {$id}: название", $labelRu, $labelEn);
            $rows[] = self::row("styleLab.lights.{$id}.note", 'style-lab', "Свет {$id}: заметка", $noteRu, $noteEn);
        }

        return $rows;
    }

    private static function quizRows(): array
    {
        $rows = [
            self::row('quiz.sectionLabel', 'quiz', 'Квиз: метка секции', 'Project quiz', 'Project quiz'),
            self::row('quiz.titlePrefix', 'quiz', 'Квиз: начало заголовка', 'Рассчитайте стоимость и сроки', 'Estimate the cost and timeline'),
            self::row('quiz.titleDefaultProject', 'quiz', 'Квиз: проект по умолчанию', ' вашего проекта', ' of your project'),
            self::row('quiz.titleSuffix', 'quiz', 'Квиз: конец заголовка', 'за 1 минуту', 'in 1 minute'),
            self::row('quiz.intro', 'quiz', 'Квиз: вводный текст', 'Ответьте на 5 вопросов, и мы подготовим персональное предложение. Бонусом отправим PDF-чек-лист «Подготовка к ремонту: с чего начать».', 'Answer 5 questions and we will prepare a personal proposal. As a bonus, we will send a PDF checklist: “Preparing for renovation: where to start”.'),
            self::row('quiz.estimateLabel', 'quiz', 'Квиз: блок оценки', 'Предварительный ориентир', 'Preliminary estimate'),
            self::row('quiz.priceLabel', 'quiz', 'Квиз: стоимость', 'Стоимость', 'Cost'),
            self::row('quiz.timelineLabel', 'quiz', 'Квиз: сроки', 'Сроки', 'Timeline'),
            self::row('quiz.finalStep', 'quiz', 'Квиз: финал', 'Финал', 'Final'),
            self::row('quiz.questionProgress', 'quiz', 'Квиз: прогресс вопроса', 'Вопрос {current} из {total}', 'Question {current} of {total}'),
            self::row('quiz.sidebarText', 'quiz', 'Квиз: пояснение сбоку', 'Ответы помогают быстро понять масштаб, сроки и формат проекта. После отправки мы свяжемся с вами в выбранном канале.', 'Answers help us quickly understand the scale, timing, and format. After submission, we will contact you in the selected channel.'),
            self::row('quiz.backButton', 'quiz', 'Квиз: назад', 'Назад', 'Back'),
            self::row('quiz.toEstimateButton', 'quiz', 'Квиз: к расчету', 'К расчету', 'To estimate'),
            self::row('quiz.finalEyebrow', 'quiz', 'Квиз: финальная подпись', 'Спасибо! Мы уже начали расчет вашего проекта.', 'Thank you! We have already started estimating your project.'),
            self::row('quiz.finalTitle', 'quiz', 'Квиз: финальный заголовок', 'Укажите, куда прислать расчет и ваш бонус', 'Tell us where to send the estimate and your bonus'),
            self::row('quiz.finalText', 'quiz', 'Квиз: финальный текст', 'Оставьте контакт, а мы подготовим персональное предложение и PDF-чек-лист по старту ремонта.', 'Leave your contact details and we will prepare a personal proposal and a PDF checklist for starting renovation.'),
            self::row('quiz.namePlaceholder', 'quiz', 'Квиз: имя', 'Ваше имя', 'Your name'),
            self::row('quiz.contactPlaceholder', 'quiz', 'Квиз: контакт', 'Телефон, e-mail или @username', 'Phone, email, or @username'),
            self::row('quiz.contactError', 'quiz', 'Квиз: ошибка контакта', 'Добавьте контакт и подтвердите согласие, чтобы мы знали, куда отправить расчет.', 'Add a contact and confirm consent so we know where to send the estimate.'),
            self::row('quiz.maxButton', 'quiz', 'Квиз: MAX', 'Получить расчет в MAX', 'Get estimate in MAX'),
            self::row('quiz.telegramButton', 'quiz', 'Квиз: Telegram', 'Получить расчет в Telegram', 'Get estimate in Telegram'),
            self::row('quiz.consentStart', 'quiz', 'Квиз: согласие начало', 'Я ознакомился(-ась) с', 'I have read the'),
            self::row('quiz.agreement', 'quiz', 'Квиз: соглашение', 'пользовательским соглашением', 'user agreement'),
            self::row('quiz.and', 'quiz', 'Квиз: союз', 'и', 'and'),
            self::row('quiz.privacy', 'quiz', 'Квиз: политика', 'политикой конфиденциальности', 'privacy policy'),
            self::row('quiz.submitted', 'quiz', 'Квиз: заявка сохранена', 'Заявка сохранена. Мы свяжемся с вами в выбранном канале: {channel}.', 'Request saved. We will contact you in the selected channel: {channel}.'),
            self::row('quiz.restartButton', 'quiz', 'Квиз: пройти заново', 'Пройти заново', 'Start again'),
            self::row('quiz.defaultTimeline', 'quiz', 'Квиз: срок по умолчанию', '3-12 недель', '3-12 weeks'),
            self::row('quiz.imageAlt', 'quiz', 'Квиз: alt изображения', 'Интерьер с мягким светом и натуральными материалами', 'Interior with soft light and natural materials'),
        ];

        $questionSets = self::quizQuestionSets();

        foreach ($questionSets as $kind => $questions) {
            foreach ($questions as $questionId => $question) {
                $rows[] = self::row("quiz.{$kind}.{$questionId}.title", 'quiz', "Квиз {$kind}: вопрос {$questionId}", $question['title'][0], $question['title'][1]);
                $rows[] = self::row("quiz.{$kind}.{$questionId}.goal", 'quiz', "Квиз {$kind}: цель {$questionId}", $question['goal'][0], $question['goal'][1]);

                foreach ($question['options'] as $index => $option) {
                    $optionNumber = $index + 1;
                    $rows[] = self::row("quiz.{$kind}.{$questionId}.option{$optionNumber}.label", 'quiz', "Квиз {$kind}: {$questionId} опция {$optionNumber}", $option['label'][0], $option['label'][1]);

                    if (isset($option['note'])) {
                        $rows[] = self::row("quiz.{$kind}.{$questionId}.option{$optionNumber}.note", 'quiz', "Квиз {$kind}: {$questionId} заметка {$optionNumber}", $option['note'][0], $option['note'][1]);
                    }

                    if (isset($option['timeline'])) {
                        $rows[] = self::row("quiz.{$kind}.{$questionId}.option{$optionNumber}.timeline", 'quiz', "Квиз {$kind}: {$questionId} срок {$optionNumber}", $option['timeline'][0], $option['timeline'][1]);
                    }
                }
            }
        }

        return $rows;
    }

    private static function quizQuestionSets(): array
    {
        $start = [
            'title' => ['Когда планируете приступать к реализации?', 'When do you plan to start implementation?'],
            'goal' => ['Оценим приоритет и ближайшие свободные окна команды.', 'We will assess priority and the nearest available team slots.'],
            'options' => [
                ['label' => ['Срочно', 'Urgently'], 'note' => ['Нужно было вчера', 'Needed yesterday']],
                ['label' => ['В ближайший месяц', 'Within the next month']],
                ['label' => ['В течение 3-6 месяцев', 'Within 3-6 months']],
                ['label' => ['Пока просто прицениваюсь', 'Just checking prices for now']],
            ],
        ];

        return [
            'interior' => [
                'object' => [
                    'title' => ['Тип объекта', 'Object type'],
                    'goal' => ['Поможет понять масштаб и специфику проекта.', 'This helps us understand the project scale and specifics.'],
                    'options' => [
                        ['label' => ['Квартира в новостройке', 'New-build apartment'], 'note' => ['Чистый старт и гибкая планировка', 'A clean start and flexible layout']],
                        ['label' => ['Квартира во вторичном жилье', 'Existing apartment'], 'note' => ['Учитываем демонтаж и существующие узлы', 'We account for demolition and existing utilities']],
                        ['label' => ['Частный дом / коттедж', 'Private house / cottage'], 'note' => ['Интерьер, архитектура и участок в одной логике', 'Interior, architecture, and plot in one logic']],
                        ['label' => ['Коммерческое помещение', 'Commercial space'], 'note' => ['Офис, кафе, шоурум или другое пространство', 'Office, cafe, showroom, or another space']],
                        ['label' => ['Только отдельная зона', 'A single zone only'], 'note' => ['Кухня, гостиная, спальня или кабинет', 'Kitchen, living room, bedroom, or office']],
                    ],
                ],
                'area' => [
                    'title' => ['Площадь объекта', 'Object area'],
                    'goal' => ['Нужна для первичного расчета стоимости.', 'Needed for an initial cost estimate.'],
                    'options' => [
                        ['label' => ['До 50 м²', 'Up to 50 m²']],
                        ['label' => ['50-100 м²', '50-100 m²']],
                        ['label' => ['100-200 м²', '100-200 m²']],
                        ['label' => ['Более 200 м²', 'More than 200 m²']],
                    ],
                ],
                'scope' => [
                    'title' => ['Какой объем услуг вам необходим?', 'What scope of services do you need?'],
                    'goal' => ['Поймем, нужен полный цикл или отдельный этап.', 'We will understand whether you need a full cycle or a separate stage.'],
                    'options' => [
                        ['label' => ['Полный дизайн-проект', 'Full design project'], 'note' => ['Чертежи, 3D-визуализация и комплектация', 'Drawings, 3D visualization, and procurement'], 'timeline' => ['8-14 недель', '8-14 weeks']],
                        ['label' => ['Только 3D-визуализация', '3D visualization only'], 'note' => ['Когда планировка и ТЗ уже готовы', 'When layout and brief are ready'], 'timeline' => ['от 5 рабочих дней', 'from 5 business days']],
                        ['label' => ['Архитектурное проектирование', 'Architectural design'], 'note' => ['Для строительства дома с нуля', 'For building a house from scratch'], 'timeline' => ['10-18 недель', '10-18 weeks']],
                        ['label' => ['Удаленное проектирование', 'Remote design'], 'note' => ['Если объект находится в другом городе', 'If the object is in another city'], 'timeline' => ['6-12 недель', '6-12 weeks']],
                    ],
                ],
                'style' => [
                    'title' => ['В каком стиле вы видите будущий интерьер?', 'What style do you imagine for the future interior?'],
                    'goal' => ['Так дизайнер быстрее поймет визуальное направление.', 'This helps the designer understand the visual direction faster.'],
                    'options' => [
                        ['label' => ['Современный', 'Modern'], 'note' => ['Минимализм, контемпорари', 'Minimalism, contemporary']],
                        ['label' => ['Неоклассика', 'Neoclassical'], 'note' => ['Элегантность и мягкий уют', 'Elegance and soft comfort']],
                        ['label' => ['Лофт / индустриальный', 'Loft / industrial'], 'note' => ['Фактура, металл, выразительная геометрия', 'Texture, metal, expressive geometry']],
                        ['label' => ['Скандинавский / эко', 'Scandinavian / eco'], 'note' => ['Свет, воздух, натуральные материалы', 'Light, air, natural materials']],
                        ['label' => ['Пока не определился', 'Not sure yet'], 'note' => ['Нужна консультация дизайнера', 'Need a designer consultation']],
                    ],
                ],
                'start' => $start,
            ],
            'architecture' => [
                'object' => [
                    'title' => ['Что нужно спроектировать?', 'What needs to be designed?'],
                    'goal' => ['Так мы поймем масштаб архитектурной задачи.', 'This helps us understand the scale of the architectural task.'],
                    'options' => [
                        ['label' => ['Частный дом / коттедж', 'Private house / cottage'], 'note' => ['Новый объект с нуля', 'A new object from scratch']],
                        ['label' => ['Реконструкция дома', 'House reconstruction'], 'note' => ['Работаем с существующим объемом', 'Working with an existing volume']],
                        ['label' => ['Коммерческое здание', 'Commercial building'], 'note' => ['Офис, павильон, общественное пространство', 'Office, pavilion, public space']],
                        ['label' => ['Только фасады', 'Facades only'], 'note' => ['Нужно обновить внешний образ', 'Need to refresh the exterior image']],
                    ],
                ],
                'area' => [
                    'title' => ['Ориентировочная площадь здания', 'Approximate building area'],
                    'goal' => ['Нужна для первичной оценки состава работ.', 'Needed for an initial scope assessment.'],
                    'options' => [
                        ['label' => ['До 150 м²', 'Up to 150 m²']],
                        ['label' => ['150-250 м²', '150-250 m²']],
                        ['label' => ['250-450 м²', '250-450 m²']],
                        ['label' => ['Более 450 м²', 'More than 450 m²']],
                    ],
                ],
                'scope' => [
                    'title' => ['Какой этап нужен?', 'Which stage do you need?'],
                    'goal' => ['От этапа зависит глубина чертежей и сроки.', 'The stage defines drawing depth and timing.'],
                    'options' => [
                        ['label' => ['Эскизный проект', 'Concept design'], 'note' => ['Концепция, планы, фасады и образ здания', 'Concept, plans, facades, and building image'], 'timeline' => ['3-6 недель', '3-6 weeks']],
                        ['label' => ['Рабочая документация', 'Working documentation'], 'note' => ['Комплект чертежей для строителей', 'Drawing set for builders'], 'timeline' => ['6-12 недель', '6-12 weeks']],
                        ['label' => ['Архитектура под ключ', 'Turnkey architecture'], 'note' => ['От концепции до рабочей базы', 'From concept to working base'], 'timeline' => ['10-18 недель', '10-18 weeks']],
                        ['label' => ['Архитектурная консультация', 'Architectural consultation'], 'note' => ['Разбор идеи, участка или планировки', 'Review of idea, plot, or layout'], 'timeline' => ['1-3 дня', '1-3 days']],
                    ],
                ],
                'style' => [
                    'title' => ['Какой образ ближе?', 'Which image feels closer?'],
                    'goal' => ['Поможет быстрее собрать фасады и материалы.', 'This helps assemble facades and materials faster.'],
                    'options' => [
                        ['label' => ['Современная архитектура', 'Modern architecture'], 'note' => ['Лаконичные объемы, стекло, камень', 'Laconic volumes, glass, stone']],
                        ['label' => ['Неоклассика', 'Neoclassical'], 'note' => ['Симметрия, мягкая статусность', 'Symmetry, soft status']],
                        ['label' => ['Шале / натуральные материалы', 'Chalet / natural materials'], 'note' => ['Дерево, камень, теплый силуэт', 'Wood, stone, warm silhouette']],
                        ['label' => ['Нужна авторская концепция', 'Need an author concept'], 'note' => ['Пока нет четкого направления', 'No clear direction yet']],
                    ],
                ],
                'start' => $start,
            ],
            'visualization' => [
                'object' => [
                    'title' => ['Что нужно визуализировать?', 'What needs visualization?'],
                    'goal' => ['Подберем подходящий пайплайн и состав ракурсов.', 'We will choose the right pipeline and view set.'],
                    'options' => [
                        ['label' => ['Интерьер', 'Interior'], 'note' => ['Квартира, дом, офис или коммерческая зона', 'Apartment, house, office, or commercial zone']],
                        ['label' => ['Коттедж / фасады', 'Cottage / facades'], 'note' => ['Экстерьер и посадка на участок', 'Exterior and placement on the plot']],
                        ['label' => ['ЖК / девелопмент', 'Residential development'], 'note' => ['Маркетинговые ракурсы для продаж', 'Marketing views for sales']],
                        ['label' => ['360-тур', '360 tour'], 'note' => ['Интерактивная прогулка по объекту', 'Interactive walkthrough of the object']],
                    ],
                ],
                'area' => [
                    'title' => ['Сколько ракурсов или зон нужно?', 'How many views or zones do you need?'],
                    'goal' => ['Так расчет будет ближе к реальному объему.', 'This makes the estimate closer to real scope.'],
                    'options' => [
                        ['label' => ['1-2 ракурса', '1-2 views']],
                        ['label' => ['3-5 ракурсов', '3-5 views']],
                        ['label' => ['6-10 ракурсов', '6-10 views']],
                        ['label' => ['Большая серия', 'Large series']],
                    ],
                ],
                'scope' => [
                    'title' => ['Что уже есть на старте?', 'What do you already have at the start?'],
                    'goal' => ['От исходников зависит скорость и стоимость.', 'Source materials affect speed and cost.'],
                    'options' => [
                        ['label' => ['Есть чертежи и ТЗ', 'Drawings and brief are ready'], 'note' => ['Можно быстро запускать модель', 'We can start the model quickly'], 'timeline' => ['5-7 дней', '5-7 days']],
                        ['label' => ['Есть 3D-модель', '3D model is ready'], 'note' => ['Нужны материалы, свет и финализация', 'Need materials, light, and finalization'], 'timeline' => ['3-5 дней', '3-5 days']],
                        ['label' => ['Есть только идея', 'Only an idea exists'], 'note' => ['Поможем собрать ТЗ и образ', 'We will help assemble the brief and image'], 'timeline' => ['7-14 дней', '7-14 days']],
                        ['label' => ['Нужны материалы для продаж', 'Need sales materials'], 'note' => ['Ракурсы, кадрирование и подача', 'Views, framing, and presentation'], 'timeline' => ['10-18 дней', '10-18 days']],
                    ],
                ],
                'style' => [
                    'title' => ['Какой уровень подачи нужен?', 'What presentation level do you need?'],
                    'goal' => ['Это влияет на детализацию сцены и постобработку.', 'This affects scene detail and post-processing.'],
                    'options' => [
                        ['label' => ['Фотореализм', 'Photorealism'], 'note' => ['Максимально натуральный свет и материалы', 'Maximum natural light and materials']],
                        ['label' => ['Премиальная презентация', 'Premium presentation'], 'note' => ['Кадры для сайта, буклета или рекламы', 'Images for website, booklet, or advertising']],
                        ['label' => ['Быстрое согласование', 'Fast approval'], 'note' => ['Понятная подача без лишнего декора', 'Clear presentation without extra decoration']],
                        ['label' => ['Вечерний сценарий', 'Evening scenario'], 'note' => ['Подсветка, атмосфера, окружение', 'Lighting, atmosphere, surroundings']],
                    ],
                ],
                'start' => $start,
            ],
            'landscape' => [
                'object' => [
                    'title' => ['Какой участок проектируем?', 'What plot are we designing?'],
                    'goal' => ['Поможет учесть сценарии отдыха и инженерные ограничения.', 'This helps account for leisure scenarios and engineering limits.'],
                    'options' => [
                        ['label' => ['Участок при коттедже', 'Plot by a cottage'], 'note' => ['Сад как продолжение дома', 'Garden as an extension of the house']],
                        ['label' => ['Дачный участок', 'Country plot'], 'note' => ['Практичный отдых и сезонность', 'Practical rest and seasonality']],
                        ['label' => ['КП / общественная территория', 'Cottage village / public area'], 'note' => ['Маршруты, въездные группы, благоустройство', 'Routes, entrance groups, landscaping']],
                        ['label' => ['Только отдельная зона', 'A single zone only'], 'note' => ['Патио, въезд, терраса или зона отдыха', 'Patio, entrance, terrace, or lounge area']],
                    ],
                ],
                'area' => [
                    'title' => ['Площадь участка', 'Plot area'],
                    'goal' => ['Нужна для первичного расчета генплана и визуализации.', 'Needed for initial master plan and visualization estimate.'],
                    'options' => [
                        ['label' => ['До 6 соток', 'Up to 600 m²']],
                        ['label' => ['6-12 соток', '600-1,200 m²']],
                        ['label' => ['12-25 соток', '1,200-2,500 m²']],
                        ['label' => ['Более 25 соток', 'More than 2,500 m²']],
                    ],
                ],
                'scope' => [
                    'title' => ['Что важно включить?', 'What is important to include?'],
                    'goal' => ['Соберем состав проекта без лишних этапов.', 'We will assemble the scope without unnecessary stages.'],
                    'options' => [
                        ['label' => ['Генплан и зонирование', 'Master plan and zoning'], 'note' => ['Маршруты, площадки, логика участка', 'Routes, areas, plot logic'], 'timeline' => ['3-5 недель', '3-5 weeks']],
                        ['label' => ['Озеленение и дендроплан', 'Planting and dendrology plan'], 'note' => ['Растения, сезонность, уход', 'Plants, seasonality, care'], 'timeline' => ['2-4 недели', '2-4 weeks']],
                        ['label' => ['Инженерия участка', 'Plot engineering'], 'note' => ['Дренаж, автополив, свет', 'Drainage, irrigation, lighting'], 'timeline' => ['3-6 недель', '3-6 weeks']],
                        ['label' => ['Ландшафт под ключ', 'Turnkey landscape'], 'note' => ['Генплан, растения, инженерия и сопровождение', 'Master plan, plants, engineering, and support'], 'timeline' => ['6-10 недель', '6-10 weeks']],
                    ],
                ],
                'style' => [
                    'title' => ['Какой характер сада ближе?', 'Which garden character feels closer?'],
                    'goal' => ['Так проще выбрать растения, материалы и ритм.', 'This makes it easier to choose plants, materials, and rhythm.'],
                    'options' => [
                        ['label' => ['Современный минимализм', 'Modern minimalism'], 'note' => ['Четкая геометрия и спокойные посадки', 'Clear geometry and calm planting']],
                        ['label' => ['Английский сад', 'English garden'], 'note' => ['Живописность, мягкие линии, цветение', 'Picturesque feel, soft lines, blooming']],
                        ['label' => ['Скандинавский / эко', 'Scandinavian / eco'], 'note' => ['Натуральность и простой уход', 'Natural look and easy care']],
                        ['label' => ['Пока не понимаю', 'Not sure yet'], 'note' => ['Нужна консультация и подбор образа', 'Need consultation and image selection']],
                    ],
                ],
                'start' => $start,
            ],
        ];
    }

    private static function pageShellRows(): array
    {
        $rows = [
            self::row('slider.dotAria', 'slider', 'Слайдер: ARIA точки', 'Слайд {number}', 'Slide {number}'),
            self::row('slider.prevAria', 'slider', 'Слайдер: предыдущий слайд', 'Предыдущий слайд', 'Previous slide'),
            self::row('slider.nextAria', 'slider', 'Слайдер: следующий слайд', 'Следующий слайд', 'Next slide'),

            self::row('news.hero.label', 'news-page', 'Новости hero: метка', 'Журнал студии', 'Studio journal'),
            self::row('news.hero.title', 'news-page', 'Новости hero: заголовок', 'Блог и новости', 'Blog and news'),
            self::row('news.hero.text', 'news-page', 'Новости hero: текст', 'Пишем о проектах, решениях и деталях, которые делают интерьер дороже на вид, спокойнее в реализации и точнее для жизни.', 'We write about projects, decisions, and details that make interiors feel richer, calmer to implement, and more precise for everyday life.'),
            self::row('news.hero.primaryButton', 'news-page', 'Новости hero: кнопка', 'Читать главное', 'Read featured'),
            self::row('news.hero.countTemplate', 'news-page', 'Новости hero: количество материалов', '{count} материала', '{count} articles'),
            self::row('news.readingTimeSuffix', 'news-page', 'Новости: время чтения', '{time} чтения', '{time} read'),
            self::row('news.openArticle', 'news-page', 'Новости: открыть статью', 'Открыть статью', 'Open article'),
            self::row('news.list.label', 'news-page', 'Новости список: метка', 'Новости студии', 'Studio news'),
            self::row('news.list.title', 'news-page', 'Новости список: заголовок', 'Все материалы', 'All articles'),
            self::row('news.list.text', 'news-page', 'Новости список: текст', 'Обновления студии, новые услуги, советы по проектированию и тренды рынка.', 'Studio updates, new services, design advice, and market trends.'),
            self::row('news.readArticle', 'news-page', 'Новости: читать статью', 'Читать статью', 'Read article'),
            self::row('news.readShort', 'news-page', 'Новости: короткая кнопка', 'Читать', 'Read'),
            self::row('newsArticle.backButton', 'news-page', 'Статья: назад', '← Все новости', '← All news'),
            self::row('newsArticle.author.label', 'news-page', 'Статья: автор метка', 'Об авторе', 'About the author'),
            self::row('newsArticle.author.text', 'news-page', 'Статья: автор текст', 'Команда 3D Smart Design Studio — студия концептуального дизайна. Интерьеры, архитектура, ландшафт и 3D-визуализация.', '3D Smart Design Studio is a conceptual design studio for interiors, architecture, landscape, and 3D visualization.'),
            self::row('newsArticle.cta.label', 'news-page', 'Статья: CTA метка', 'Обсудить проект', 'Discuss a project'),
            self::row('newsArticle.cta.text', 'news-page', 'Статья: CTA текст', 'Есть идея или вопрос? Свяжитесь с нами — ответим в течение рабочего дня.', 'Have an idea or a question? Contact us and we will reply within one business day.'),
            self::row('newsArticle.cta.button', 'news-page', 'Статья: CTA кнопка', 'Написать нам', 'Write to us'),
            self::row('newsArticle.related.label', 'news-page', 'Статья: читать также', 'Читать также', 'Read also'),
            self::row('newsArticle.related.title', 'news-page', 'Статья: другие новости', 'Другие новости', 'Other news'),

            self::row('promos.hero.fallbackAlt', 'promos-page', 'Акции hero: alt запасного слайда', 'Ландшафтный проект', 'Landscape project'),
            self::row('promos.hero.label', 'promos-page', 'Акции hero: метка', 'Специальные условия', 'Special offers'),
            self::row('promos.hero.title', 'promos-page', 'Акции hero: заголовок', 'Акции и скидки', 'Promotions and discounts'),
            self::row('promos.hero.text', 'promos-page', 'Акции hero: текст', 'Пакетные условия для комплексных проектов. Стоимость фиксируется после брифа — ниже стартовая структура.', 'Package terms for complex projects. The cost is fixed after the brief; the starting structure is below.'),
            self::row('promos.hero.validUntil', 'promos-page', 'Акции hero: срок действия', 'Акции действуют до 31 января 2026', 'Promotions are valid until January 31, 2026'),
            self::row('promos.validUntilPrefix', 'promos-page', 'Акции: префикс срока карточки', 'до {date}', 'until {date}'),
            self::row('promos.card.button', 'promos-page', 'Акции: кнопка карточки', 'Записаться на акцию', 'Claim promotion'),
            self::row('promos.cta.label', 'promos-page', 'Акции CTA: метка', 'Нужна консультация?', 'Need a consultation?'),
            self::row('promos.cta.title', 'promos-page', 'Акции CTA: заголовок', 'Расскажем подробнее о каждой акции', 'We will explain every promotion'),
            self::row('promos.cta.text', 'promos-page', 'Акции CTA: текст', 'Свяжитесь с нами удобным способом — ответим в течение рабочего дня и подберём оптимальный пакет.', 'Contact us in a convenient way; we will reply within one business day and select the best package.'),
            self::row('promos.cta.button', 'promos-page', 'Акции CTA: кнопка', 'Связаться с нами', 'Contact us'),

            self::row('awardsPage.hero.alt1', 'awards-page', 'Награды hero: alt 1', 'Проект 3D Smart Design Studio', '3D Smart Design Studio project'),
            self::row('awardsPage.hero.alt2', 'awards-page', 'Награды hero: alt 2', 'Интерьерный проект', 'Interior project'),
            self::row('awardsPage.hero.alt3', 'awards-page', 'Награды hero: alt 3', 'Архитектурная визуализация', 'Architectural visualization'),
            self::row('awardsPage.hero.label', 'awards-page', 'Награды hero: метка', 'Награды и дипломы', 'Awards and diplomas'),
            self::row('awardsPage.hero.title', 'awards-page', 'Награды hero: заголовок', 'Документы, которые подтверждают доверие', 'Documents that prove trust'),
            self::row('awardsPage.hero.text', 'awards-page', 'Награды hero: текст', 'Дипломы, сертификаты, благодарственные письма и профессиональные подтверждения студии собраны в одном разделе.', 'Diplomas, certificates, letters of appreciation, and professional proof are collected in one section.'),

            self::row('reviews.hero.label', 'reviews-page', 'Отзывы hero: метка', 'Client stories', 'Client stories'),
            self::row('reviews.hero.title', 'reviews-page', 'Отзывы hero: заголовок', 'Отзывы о нас', 'Reviews about us'),
            self::row('reviews.hero.text', 'reviews-page', 'Отзывы hero: текст', 'Здесь важны не громкие обещания, а то, как клиенты описывают процесс: сроки, внимание к деталям, спокойную коммуникацию и результат, который хочется показывать.', 'What matters here is not loud promises, but how clients describe the process: timing, attention to detail, calm communication, and a result they want to show.'),
            self::row('reviews.hero.primaryButton', 'reviews-page', 'Отзывы hero: основная кнопка', 'Стать клиентом', 'Become a client'),
            self::row('reviews.hero.secondaryButton', 'reviews-page', 'Отзывы hero: вторая кнопка', 'Смотреть портфолио', 'View portfolio'),
            self::row('reviews.list.label', 'reviews-page', 'Отзывы список: метка', 'Опыт клиентов', 'Client experience'),
            self::row('reviews.list.title', 'reviews-page', 'Отзывы список: заголовок', 'Слова, за которыми виден процесс', 'Words that reveal the process'),
            self::row('reviews.list.text', 'reviews-page', 'Отзывы список: текст', 'Перенесли отзывы с исходной страницы и собрали их в живую презентационную витрину: реальные даты, направления работ, короткие выводы и ответы студии.', 'We collected reviews into a live presentation: real dates, work directions, short takeaways, and studio replies.'),
            self::row('reviews.replies.label', 'reviews-page', 'Отзывы ответы: метка', 'Диалог со студией', 'Dialogue with the studio'),
            self::row('reviews.replies.title', 'reviews-page', 'Отзывы ответы: заголовок', 'Ответы администратора без сухой формальности', 'Studio replies without dry formality'),
            self::row('reviews.replies.text', 'reviews-page', 'Отзывы ответы: текст', 'Для страницы оставили не только клиентские впечатления, но и короткий ответ студии — так блок выглядит живым и поддерживает доверие.', 'The page keeps not only client impressions, but also short studio replies, so the block feels alive and supports trust.'),
            self::row('reviews.replies.itemLabel', 'reviews-page', 'Отзывы ответы: подпись ответа', 'Ответ на отзыв #{number}', 'Reply to review #{number}'),
            self::row('reviews.replies.role', 'reviews-page', 'Отзывы ответы: роль', 'Администратор', 'Administrator'),
            self::row('reviews.form.imageAlt', 'reviews-page', 'Отзывы форма: alt', 'Оставить отзыв', 'Leave a review'),
            self::row('reviews.form.label', 'reviews-page', 'Отзывы форма: метка', 'Оставить отзыв', 'Leave a review'),
            self::row('reviews.form.title', 'reviews-page', 'Отзывы форма: заголовок', 'Расскажите, каким был ваш проект', 'Tell us what your project was like'),
            self::row('reviews.form.namePlaceholder', 'reviews-page', 'Отзывы форма: имя', 'Имя *', 'Name *'),
            self::row('reviews.form.emailPlaceholder', 'reviews-page', 'Отзывы форма: email', 'E-mail *', 'E-mail *'),
            self::row('reviews.form.textPlaceholder', 'reviews-page', 'Отзывы форма: текст', 'Текст *', 'Text *'),
            self::row('reviews.form.notice', 'reviews-page', 'Отзывы форма: примечание', 'Отправленное сообщение появится после проверки администратором сайта.', 'The submitted message will appear after administrator review.'),
            self::row('reviews.form.button', 'reviews-page', 'Отзывы форма: кнопка', 'Отправить отзыв', 'Send review'),
        ];

        return [
            ...$rows,
            ...self::careerPageRows(),
            ...self::portfolioCaseRows(),
        ];
    }

    private static function careerPageRows(): array
    {
        return [
            self::row('career.hero.image', 'career-page', 'Карьера hero: изображение', '/images/cms/career-team.webp', '/images/cms/career-team.webp'),
            self::row('career.hero.imageAlt', 'career-page', 'Карьера hero: alt', 'Команда дизайн-студии за рабочим столом', 'Design studio team at a work table'),
            self::row('career.hero.label', 'career-page', 'Карьера hero: метка', 'Карьера / 3D Smart Design Studio', 'Careers / 3D Smart Design Studio'),
            self::row('career.hero.title', 'career-page', 'Карьера hero: заголовок', 'Карьера в студии', 'Careers at the studio'),
            self::row('career.hero.text', 'career-page', 'Карьера hero: текст', 'Ищем людей, которые умеют делать пространство понятным: в концепции, визуализации, документации, комплектации и коммуникации.', 'We are looking for people who make space understandable in concept, visualization, documentation, procurement, and communication.'),
            self::row('career.hero.primaryButton', 'career-page', 'Карьера hero: кнопка вакансий', 'Смотреть вакансии', 'View vacancies'),
            self::row('career.hero.secondaryButton', 'career-page', 'Карьера hero: кнопка резюме', 'Отправить резюме', 'Send resume'),
            self::row('career.hero.stat1.value', 'career-page', 'Карьера статистика 1: значение', '5', '5'),
            self::row('career.hero.stat1.label', 'career-page', 'Карьера статистика 1: подпись', 'открытых направлений', 'open directions'),
            self::row('career.hero.stat2.value', 'career-page', 'Карьера статистика 2: значение', 'гибрид', 'hybrid'),
            self::row('career.hero.stat2.label', 'career-page', 'Карьера статистика 2: подпись', 'и удаленная работа', 'and remote work'),
            self::row('career.hero.stat3.value', 'career-page', 'Карьера статистика 3: значение', 'проектно', 'project-based'),
            self::row('career.hero.stat3.label', 'career-page', 'Карьера статистика 3: подпись', 'можно начать без full-time', 'can start without full-time'),
            self::row('career.departments.label', 'career-page', 'Карьера направления: метка', 'Кого ждем', 'Who we are looking for'),
            self::row('career.departmentFallback1', 'career-page', 'Карьера направление по умолчанию 1', 'Интерьеры', 'Interiors'),
            self::row('career.departmentFallback2', 'career-page', 'Карьера направление по умолчанию 2', '3D-визуализация', '3D visualization'),
            self::row('career.departmentFallback3', 'career-page', 'Карьера направление по умолчанию 3', 'Комплектация', 'Procurement'),
            self::row('career.departmentFallback4', 'career-page', 'Карьера направление по умолчанию 4', 'Архитектура', 'Architecture'),
            self::row('career.departmentFallback5', 'career-page', 'Карьера направление по умолчанию 5', 'Ландшафт', 'Landscape'),
            self::row('career.vacancies.label', 'career-page', 'Карьера вакансии: метка', 'Открытые позиции', 'Open positions'),
            self::row('career.vacancies.title', 'career-page', 'Карьера вакансии: заголовок', 'Вакансии для тех, кто любит точность и красивый результат', 'Vacancies for people who love precision and beautiful results'),
            self::row('career.vacancies.text', 'career-page', 'Карьера вакансии: текст', 'Мы открыты к сотрудничеству с дизайнерами, визуализаторами, архитекторами и специалистами по комплектации. Отклики помогают собрать сильную проектную команду под новые задачи.', 'We are open to cooperation with designers, visualizers, architects, and procurement specialists. Applications help us assemble strong teams for new tasks.'),
            self::row('career.card.tasksLabel', 'career-page', 'Карьера карточка: задачи', 'Задачи', 'Tasks'),
            self::row('career.card.requirementsLabel', 'career-page', 'Карьера карточка: важно', 'Важно', 'Important'),
            self::row('career.card.applyButton', 'career-page', 'Карьера карточка: кнопка', 'Откликнуться', 'Apply'),
            self::row('career.cta.label', 'career-page', 'Карьера CTA: метка', 'Не нашли роль?', 'Did not find your role?'),
            self::row('career.cta.title', 'career-page', 'Карьера CTA: заголовок', 'Напишите нам, если чувствуете совпадение', 'Write to us if it feels like a match'),
            self::row('career.cta.text', 'career-page', 'Карьера CTA: текст', 'Иногда нужный специалист появляется раньше вакансии. Расскажите, чем можете усилить студию, и приложите портфолио.', 'Sometimes the right specialist appears before the vacancy. Tell us how you can strengthen the studio and attach your portfolio.'),
            self::row('career.cta.button', 'career-page', 'Карьера CTA: кнопка', 'Отправить отклик', 'Send application'),
            self::row('career.modal.label', 'career-page', 'Карьера модалка: метка', 'Отклик на вакансию', 'Vacancy application'),
            self::row('career.modal.closeAria', 'career-page', 'Карьера модалка: закрыть', 'Закрыть', 'Close'),
            self::row('career.modal.namePlaceholder', 'career-page', 'Карьера модалка: имя', 'Имя и фамилия', 'Full name'),
            self::row('career.modal.contactPlaceholder', 'career-page', 'Карьера модалка: контакт', 'Телефон, e-mail или Telegram', 'Phone, e-mail, or Telegram'),
            self::row('career.modal.portfolioPlaceholder', 'career-page', 'Карьера модалка: портфолио', 'Ссылка на портфолио / резюме', 'Portfolio / resume link'),
            self::row('career.modal.messagePlaceholder', 'career-page', 'Карьера модалка: сообщение', 'Коротко о себе и релевантном опыте', 'Briefly about yourself and relevant experience'),
            self::row('career.modal.consentStart', 'career-page', 'Карьера модалка: согласие начало', 'Я согласен(-на) с', 'I agree with the'),
            self::row('career.modal.privacy', 'career-page', 'Карьера модалка: политика', 'политикой конфиденциальности', 'privacy policy'),
            self::row('career.modal.consentEnd', 'career-page', 'Карьера модалка: согласие конец', 'и передачей данных для рассмотрения отклика.', 'and data transfer for application review.'),
            self::row('career.modal.requiredError', 'career-page', 'Карьера модалка: ошибка контакта', 'Заполните имя и контакт, чтобы мы могли ответить.', 'Fill in your name and contact so we can reply.'),
            self::row('career.modal.consentError', 'career-page', 'Карьера модалка: ошибка согласия', 'Нужно согласие на обработку данных.', 'Consent to data processing is required.'),
            self::row('career.modal.sentMessage', 'career-page', 'Карьера модалка: отправлено', 'Отклик сохранен. Мы свяжемся с вами после рассмотрения заявки.', 'Application saved. We will contact you after review.'),
            self::row('career.modal.submitButton', 'career-page', 'Карьера модалка: кнопка', 'Отправить отклик', 'Send application'),
        ];
    }

    private static function portfolioCaseRows(): array
    {
        return [
            self::row('portfolio.hero.kicker1', 'portfolio-home', 'Портфолио hero: слайд 1', 'Featured interior', 'Featured interior'),
            self::row('portfolio.hero.kicker2', 'portfolio-home', 'Портфолио hero: слайд 2', 'City apartment', 'City apartment'),
            self::row('portfolio.hero.kicker3', 'portfolio-home', 'Портфолио hero: слайд 3', 'Landscape story', 'Landscape story'),
            self::row('portfolio.hero.kicker4', 'portfolio-home', 'Портфолио hero: слайд 4', 'Commercial space', 'Commercial space'),
            self::row('portfolio.hero.kickerDefault', 'portfolio-home', 'Портфолио hero: слайд по умолчанию', 'Selected project', 'Selected project'),
            self::row('portfolio.hero.viewButton', 'portfolio-home', 'Портфолио hero: кнопка', 'Смотреть проект', 'View project'),
            self::row('portfolioCase.hero.backButton', 'portfolio-case', 'Кейс: назад', '← Портфолио', '← Portfolio'),
            self::row('portfolioCase.hero.readButton', 'portfolio-case', 'Кейс: читать', 'Читать кейс', 'Read case'),
            self::row('portfolioCase.hero.discussButton', 'portfolio-case', 'Кейс: обсудить', 'Обсудить похожий', 'Discuss similar'),
            self::row('portfolioCase.passport.label', 'portfolio-case', 'Кейс паспорт: метка', 'Паспорт проекта', 'Project passport'),
            self::row('portfolioCase.passport.direction', 'portfolio-case', 'Кейс паспорт: направление', 'Направление', 'Direction'),
            self::row('portfolioCase.passport.location', 'portfolio-case', 'Кейс паспорт: локация', 'Локация', 'Location'),
            self::row('portfolioCase.passport.year', 'portfolio-case', 'Кейс паспорт: год', 'Год', 'Year'),
            self::row('portfolioCase.passport.remote', 'portfolio-case', 'Кейс паспорт: удаленно', 'Удаленно', 'Remote'),
            self::row('portfolioCase.passport.inProgress', 'portfolio-case', 'Кейс паспорт: в работе', 'В работе', 'In progress'),
            self::row('portfolioCase.metrics.type', 'portfolio-case', 'Кейс метрика: тип', 'Тип', 'Type'),
            self::row('portfolioCase.metrics.location', 'portfolio-case', 'Кейс метрика: локация', 'Локация', 'Location'),
            self::row('portfolioCase.metrics.year', 'portfolio-case', 'Кейс метрика: год', 'Год', 'Year'),
            self::row('portfolioCase.metrics.focus', 'portfolio-case', 'Кейс метрика: фокус', 'Фокус', 'Focus'),
            self::row('portfolioCase.metrics.remote', 'portfolio-case', 'Кейс метрика: удаленно', 'Удаленный проект', 'Remote project'),
            self::row('portfolioCase.metrics.inProgress', 'portfolio-case', 'Кейс метрика: в работе', 'В работе', 'In progress'),
            self::row('portfolioCase.nav.story', 'portfolio-case', 'Кейс навигация: история', 'История', 'Story'),
            self::row('portfolioCase.nav.gallery', 'portfolio-case', 'Кейс навигация: галерея', 'Галерея', 'Gallery'),
            self::row('portfolioCase.nav.compare', 'portfolio-case', 'Кейс навигация: до/после', 'До / после', 'Before / after'),
            self::row('portfolioCase.nav.related', 'portfolio-case', 'Кейс навигация: похожие', 'Похожие', 'Related'),
            self::row('portfolioCase.story.label', 'portfolio-case', 'Кейс история: метка', 'История проекта', 'Project story'),
            self::row('portfolioCase.story.title', 'portfolio-case', 'Кейс история: заголовок', 'Что важно увидеть в этом кейсе', 'What matters in this case'),
            self::row('portfolioCase.deliverables.label', 'portfolio-case', 'Кейс состав: метка', 'Состав', 'Deliverables'),
            self::row('portfolioCase.deliverables.title', 'portfolio-case', 'Кейс состав: заголовок', 'Что входит в проектную подачу', 'What the project presentation includes'),
            self::row('portfolioCase.gallery.sectionLabel', 'portfolio-case', 'Кейс галерея: метка', 'Галерея', 'Gallery'),
            self::row('portfolioCase.gallery.title', 'portfolio-case', 'Кейс галерея: заголовок', 'Ракурсы и детали проекта', 'Views and project details'),
            self::row('portfolioCase.gallery.text', 'portfolio-case', 'Кейс галерея: текст', 'Изображения собраны в удобный просмотр: можно открыть крупный кадр и быстро переключиться между визуальными состояниями.', 'Images are collected into a convenient viewer: open a large frame and quickly switch between visual states.'),
            self::row('portfolioCase.gallery.label1', 'portfolio-case', 'Кейс галерея: подпись 1', 'Главный ракурс', 'Main view'),
            self::row('portfolioCase.gallery.label2', 'portfolio-case', 'Кейс галерея: подпись 2', 'Финальная подача', 'Final presentation'),
            self::row('portfolioCase.gallery.label3', 'portfolio-case', 'Кейс галерея: подпись 3', 'Исходная сцена', 'Initial scene'),
            self::row('portfolioCase.gallery.label4', 'portfolio-case', 'Кейс галерея: подпись 4', 'Материалы', 'Materials'),
            self::row('portfolioCase.gallery.label5', 'portfolio-case', 'Кейс галерея: подпись 5', 'Контекст', 'Context'),
            self::row('portfolioCase.lightboxAria', 'portfolio-case', 'Кейс просмотр: ARIA', 'Просмотр изображения проекта', 'Project image preview'),
            self::row('portfolioCase.closeViewAria', 'portfolio-case', 'Кейс просмотр: закрыть', 'Закрыть просмотр', 'Close preview'),
            self::row('portfolioCase.prevImageAria', 'portfolio-case', 'Кейс просмотр: назад', 'Предыдущее изображение', 'Previous image'),
            self::row('portfolioCase.nextImageAria', 'portfolio-case', 'Кейс просмотр: вперед', 'Следующее изображение', 'Next image'),
            self::row('portfolioCase.compare.label', 'portfolio-case', 'Кейс сравнение: метка', 'До / после', 'Before / after'),
            self::row('portfolioCase.compare.title', 'portfolio-case', 'Кейс сравнение: заголовок', 'Сравнение визуального сценария', 'Visual scenario comparison'),
            self::row('portfolioCase.compare.text', 'portfolio-case', 'Кейс сравнение: текст', 'Ползунок помогает быстро увидеть разницу между исходным состоянием и финальной проектной подачей.', 'The slider helps quickly see the difference between the initial state and final project presentation.'),
            self::row('portfolioCase.compare.before', 'portfolio-case', 'Кейс сравнение: alt до', 'до', 'before'),
            self::row('portfolioCase.compare.after', 'portfolio-case', 'Кейс сравнение: alt после', 'после', 'after'),
            self::row('portfolioCase.compare.beforeLabel', 'portfolio-case', 'Кейс сравнение: до', 'До', 'Before'),
            self::row('portfolioCase.compare.afterLabel', 'portfolio-case', 'Кейс сравнение: после', 'После', 'After'),
            self::row('portfolioCase.compareAria', 'portfolio-case', 'Кейс сравнение: ARIA', 'Сравнение до и после', 'Before and after comparison'),
            self::row('portfolioCase.process.label', 'portfolio-case', 'Кейс процесс: метка', 'Процесс', 'Process'),
            self::row('portfolioCase.process.title', 'portfolio-case', 'Кейс процесс: заголовок', 'Как проект становится понятным', 'How the project becomes clear'),
            self::row('portfolioCase.process.text', 'portfolio-case', 'Кейс процесс: текст', 'Каждый этап делает следующую встречу короче: меньше догадок, больше проверенных решений и ясных материалов для согласования.', 'Each stage makes the next meeting shorter: fewer guesses, more verified decisions, and clear approval materials.'),
            self::row('portfolioCase.related.label', 'portfolio-case', 'Кейс похожие: метка', 'Похожие проекты', 'Related projects'),
            self::row('portfolioCase.related.title', 'portfolio-case', 'Кейс похожие: заголовок', 'Можно открыть дальше', 'Explore more'),
            self::row('portfolioCase.related.allButton', 'portfolio-case', 'Кейс похожие: все', 'Все портфолио →', 'All portfolio →'),
            self::row('portfolioCase.cta.label', 'portfolio-case', 'Кейс CTA: метка', 'Следующий шаг', 'Next step'),
            self::row('portfolioCase.cta.title', 'portfolio-case', 'Кейс CTA: заголовок', 'Нужен проект с такой же ясной подачей?', 'Need a project with the same clear presentation?'),
            self::row('portfolioCase.cta.text', 'portfolio-case', 'Кейс CTA: текст', 'Расскажите о задаче, площади, сроках и формате работы. Мы предложим понятный маршрут: от брифа до визуальной или рабочей документации.', 'Tell us about the task, area, timing, and work format. We will suggest a clear route from brief to visual or working documentation.'),
            self::row('portfolioCase.cta.primaryButton', 'portfolio-case', 'Кейс CTA: основная кнопка', 'Обсудить проект', 'Discuss project'),
            self::row('portfolioCase.cta.secondaryButton', 'portfolio-case', 'Кейс CTA: вторая кнопка', 'Услуги студии', 'Studio services'),
        ];
    }

    private static function row(string $key, string $group, string $label, string $ru, ?string $en = null, ?string $description = null): array
    {
        return [
            'key' => $key,
            'group' => $group,
            'label' => $label,
            'value_ru' => $ru,
            'value_en' => $en,
            'description' => $description,
            'is_active' => true,
        ];
    }
}
