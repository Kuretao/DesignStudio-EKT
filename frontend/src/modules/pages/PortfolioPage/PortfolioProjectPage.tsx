"use client";

import { useEffect, useMemo, useRef, useState } from "react";
import Link from "next/link";
import gsap from "gsap";
import { useCms, useCmsText } from "@/src/cms";
import type { Project, ProjectCategory } from "@/src/types";
import CinematicImage from "@/src/components/common/CinematicImage";
import HeroBackdropSlider from "@/src/components/common/HeroBackdropSlider";
import SectionLabel from "@/src/components/common/SectionLabel";
import VirtualTourDemo from "@/src/components/common/VirtualTourDemo";
import { GlassPanel } from "@/src/ui";

type ProjectCaseCopy = {
  focus: string;
  intro: string;
  chapters: {
    title: string;
    text: string;
  }[];
  deliverables: string[];
  process: string[];
  values: string[];
};

const projectCaseCopy: Record<ProjectCategory, ProjectCaseCopy> = {
  Интерьеры: {
    focus: "Интерьерный кейс",
    intro:
      "В интерьерном проекте важно собрать не только красивую картинку, но и понятную бытовую механику: маршруты, хранение, свет, материалы и настроение, которое не устанет через сезон.",
    chapters: [
      {
        title: "Задача",
        text:
          "Сформировать цельное пространство с логичной планировкой, спокойной палитрой и визуальными акцентами, которые помогают заказчику заранее увидеть будущий интерьер.",
      },
      {
        title: "Решение",
        text:
          "Мы выстраиваем сценарии движения, собираем материалы вокруг главного тона проекта и проверяем ключевые зоны через 3D-ракурсы до старта реализации.",
      },
      {
        title: "Результат",
        text:
          "Кейс превращается в рабочую основу для согласований: понятно, какие решения сохранять, где нужны уточнения и как удержать выбранную атмосферу на объекте.",
      },
    ],
    deliverables: ["Планировочная логика", "3D-ракурсы ключевых зон", "Материальная палитра", "Световые сценарии"],
    process: ["Бриф и исходные данные", "Планировка", "Визуальная концепция", "3D-подача", "Рабочая логика"],
    values: ["Комфорт", "Свет", "Хранение", "Материалы"],
  },
  Архитектура: {
    focus: "Архитектурная подача",
    intro:
      "Архитектурный проект держится на ясной геометрии, посадке на участок и точной визуальной истории, которую можно показывать заказчику, подрядчикам или отделу продаж.",
    chapters: [
      {
        title: "Задача",
        text:
          "Показать объект как законченную среду: фасады, пропорции, окружение, свет и сценарий восприятия с нескольких важных точек.",
      },
      {
        title: "Решение",
        text:
          "Мы собираем объем, проверяем силуэт, настраиваем материалы и освещение так, чтобы архитектура читалась выразительно и при этом оставалась технически понятной.",
      },
      {
        title: "Результат",
        text:
          "Получается презентационный набор, который помогает быстрее согласовывать идею, обсуждать детали и уверенно двигаться к следующей стадии проекта.",
      },
    ],
    deliverables: ["Фасадные ракурсы", "Посадка в окружение", "Материалы экстерьера", "Дневный или вечерний сценарий"],
    process: ["Исходные чертежи", "Объем и пропорции", "Материалы", "Свет и камеры", "Финальная подача"],
    values: ["Геометрия", "Контекст", "Силуэт", "Презентация"],
  },
  Ландшафт: {
    focus: "Ландшафтный сценарий",
    intro:
      "Ландшафтный кейс показывает участок как систему: маршруты, зоны отдыха, растения, свет и материалы должны работать вместе и быть понятными до реализации.",
    chapters: [
      {
        title: "Задача",
        text:
          "Превратить участок в последовательность удобных зон, где движение, отдых, озеленение и вечерняя подсветка поддерживают общий образ.",
      },
      {
        title: "Решение",
        text:
          "Мы связываем функциональные сценарии с визуальным ритмом: дорожки, посадки, покрытия и свет собираются в читаемый план будущего сада.",
      },
      {
        title: "Результат",
        text:
          "Проект дает заказчику ясное представление о будущем участке и помогает заранее согласовать материалы, растения и очередность работ.",
      },
    ],
    deliverables: ["Зонирование участка", "Маршруты и покрытия", "Озеленение", "Вечерняя подсветка"],
    process: ["Анализ участка", "Зонирование", "Подбор материалов", "3D-визуализация", "Схема реализации"],
    values: ["Маршруты", "Растения", "Свет", "Уход"],
  },
};

const fallbackCaseCopy = projectCaseCopy["Интерьеры"];

function uniqueImages(images: Array<string | undefined>) {
  return Array.from(new Set(images.filter(Boolean))) as string[];
}

function projectCaseId(project: Project) {
  if (project.category === "Архитектура") return "architecture";
  if (project.category === "Ландшафт") return "landscape";
  return "interiors";
}

function getProjectCopy(project: Project, text: (key: string, fallback?: string) => string) {
  const copy = projectCaseCopy[project.category] ?? fallbackCaseCopy;
  const id = projectCaseId(project);

  const chapters =
    project.storyChapters && project.storyChapters.length > 0
      ? project.storyChapters.map((chapter) => ({
          title: chapter.title || chapter.title_ru || "Заголовок",
          text: chapter.text || chapter.text_ru || "Текст",
        }))
      : [];

  const deliverables =
    project.deliverables && project.deliverables.length > 0
      ? project.deliverables
      : [];

  return {
    focus: text(`portfolioCase.${id}.focus`, copy.focus),
    intro: project.caseIntro || text(`portfolioCase.${id}.intro`, copy.intro),
    chapters,
    deliverables,
    process: [],
    values: copy.values.map((item, index) => text(`portfolioCase.${id}.values.${index + 1}`, item)),
  };
}

function getRelatedProjects(projects: Project[], project: Project) {
  const sameCategory = projects.filter((item) => item.slug !== project.slug && item.category === project.category);
  const other = projects.filter((item) => item.slug !== project.slug && item.category !== project.category);

  return [...sameCategory, ...other].slice(0, 3);
}

function hasText(value?: string | null) {
  return Boolean(value && value.trim());
}

function hasStoryContent(project: Project) {
  return Boolean(
    project.storyChapters?.some((chapter) =>
      hasText(chapter.title || chapter.titleRu || chapter.title_ru) ||
      hasText(chapter.text || chapter.textRu || chapter.text_ru),
    ),
  );
}

function hasDeliverablesContent(project: Project) {
  return Boolean(project.deliverables?.some((item) => hasText(item)));
}

function MetricStrip({ project, copy }: { project: Project; copy: ProjectCaseCopy }) {
  const text = useCmsText();
  const metrics = [
    [text("portfolioCase.metrics.type", "Тип"), project.category],
    [text("portfolioCase.metrics.location", "Локация"), project.location || text("portfolioCase.metrics.remote", "Удаленный проект")],
    [text("portfolioCase.metrics.year", "Год"), project.year || text("portfolioCase.metrics.inProgress", "В работе")],
    [text("portfolioCase.metrics.focus", "Фокус"), copy.values[0]],
  ];

  return (
    <section className="border-y border-white/10 bg-[#0c0b09]/50 px-5 md:px-10 lg:px-16">
      <div className="mx-auto grid max-w-7xl divide-y divide-white/10 md:grid-cols-4 md:divide-x md:divide-y-0">
        {metrics.map(([label, value]) => (
          <div key={label} className="py-6 md:px-6">
            <p className="text-xs uppercase text-white/40">{label}</p>
            <p className="mt-2 text-2xl font-light text-[#F5F2EC]">{value}</p>
          </div>
        ))}
      </div>
    </section>
  );
}

function ProjectHero({ project, gallery, copy }: { project: Project; gallery: string[]; copy: ProjectCaseCopy }) {
  const text = useCmsText();
  return (
    <section className="relative min-h-[92vh] overflow-hidden px-5 pb-16 pt-28 md:px-10 lg:px-16">
      <HeroBackdropSlider
        slides={gallery.map((image) => ({ image, alt: project.title }))}
        controlsClassName="bottom-5"
      />
      <div className="absolute inset-0 bg-[linear-gradient(90deg,rgba(5,5,5,.96)_0%,rgba(5,5,5,.76)_48%,rgba(5,5,5,.28)_100%)]" />
      <div className="absolute inset-0 bg-[linear-gradient(0deg,#050505_0%,rgba(5,5,5,.38)_34%,transparent_76%)]" />

      <div className="relative z-10 mx-auto grid min-h-[calc(92vh-7rem)] max-w-7xl gap-10 lg:grid-cols-[1fr_360px] lg:items-end">
        <div className="pb-14">
          <Link href="/portfolio" className="mb-8 inline-flex items-center gap-3 text-sm text-[#D69A66] transition hover:text-[#F5F2EC]">
            {text("portfolioCase.hero.backButton", "← Портфолио")}
          </Link>
          <p className="text-xs uppercase text-[#D69A66]">{copy.focus}</p>
          <h1 className="mt-5 max-w-5xl text-[clamp(2.75rem,5.4vw,5.6rem)] font-light leading-[0.96] text-white">
            {project.title}
          </h1>
          <p className="mt-7 max-w-2xl text-lg leading-relaxed text-[#E8E0D8]/85 md:text-xl">
            {project.description}
          </p>
          <div className="mt-9 flex flex-wrap gap-3">
            <a
              href="#case-story"
              className="rounded-full border border-[#D69A66] bg-[#D69A66] px-6 py-4 text-xs uppercase text-[#050505] transition duration-300 hover:-translate-y-0.5 hover:bg-[#E3AD7B]"
            >
              {text("portfolioCase.hero.readButton", "Читать кейс")}
            </a>
            <Link
              href="/kontakty"
              className="rounded-full border border-white/15 bg-black/25 px-6 py-4 text-xs uppercase text-white/75 backdrop-blur transition duration-300 hover:border-[#D69A66]/70 hover:text-white"
            >
              {text("portfolioCase.hero.discussButton", "Обсудить похожий")}
            </Link>
          </div>
        </div>

        <GlassPanel className="mb-14 rounded-[2rem] p-6">
          <p className="text-xs uppercase text-[#D69A66]">{text("portfolioCase.passport.label", "Паспорт проекта")}</p>
          <div className="mt-5 divide-y divide-white/10">
            {[
              [text("portfolioCase.passport.direction", "Направление"), project.category],
              [text("portfolioCase.passport.location", "Локация"), project.location || text("portfolioCase.passport.remote", "Удаленно")],
              [text("portfolioCase.passport.year", "Год"), project.year || text("portfolioCase.passport.inProgress", "В работе")],
            ].map(([label, value]) => (
              <div key={label} className="flex items-center justify-between gap-5 py-4">
                <span className="text-sm text-white/45">{label}</span>
                <span className="text-right text-sm text-[#F5F2EC]">{value}</span>
              </div>
            ))}
          </div>
          <div className="mt-5 rounded-[1.25rem] border border-white/10 bg-white/[0.04] p-5">
            <p className="text-sm leading-relaxed text-[#D6D1CA]">{copy.intro}</p>
          </div>
        </GlassPanel>
      </div>
    </section>
  );
}

function CaseNavigation({
  hasStory = false,
  hasDeliverables = false,
  hasVirtualTour = false,
  hasGallery = false,
  hasCompare = false,
  hasRelated = false,
}: {
  hasStory?: boolean;
  hasDeliverables?: boolean;
  hasVirtualTour?: boolean;
  hasGallery?: boolean;
  hasCompare?: boolean;
  hasRelated?: boolean;
}) {
  const text = useCmsText();
  const items = [
    ...(hasStory ? [[text("portfolioCase.nav.story", "История"), "#case-story"]] : []),
    ...(hasDeliverables ? [[text("portfolioCase.nav.deliverables", "Состав"), "#case-deliverables"]] : []),
    ...(hasVirtualTour ? [[text("portfolioCase.nav.virtualTour", "360° тур"), "#case-virtual-tour"]] : []),
    ...(hasGallery ? [[text("portfolioCase.nav.gallery", "Галерея"), "#case-gallery"]] : []),
    ...(hasCompare ? [[text("portfolioCase.nav.compare", "До / после"), "#case-compare"]] : []),
    ...(hasRelated ? [[text("portfolioCase.nav.related", "Похожие"), "#case-related"]] : []),
  ];

  if (!items.length) return null;

  return (
    <nav className="relative z-20 border-y border-white/10 bg-[#0c0b09] px-5 py-3 md:px-10 lg:px-16">
      <div className="mx-auto flex max-w-7xl gap-2 overflow-x-auto">
        {items.map(([label, href]) => (
          <a
            key={href}
            href={href}
            className="shrink-0 rounded-full border border-white/10 px-4 py-2 text-sm text-white/65 transition hover:border-[#D69A66]/60 hover:text-[#D69A66]"
          >
            {label}
          </a>
        ))}
      </div>
    </nav>
  );
}

function ProjectStory({ project, copy }: { project: Project; copy: ProjectCaseCopy }) {
  const text = useCmsText();
  const hasChapters = copy.chapters.some((chapter) => hasText(chapter.title) || hasText(chapter.text));
  if (!hasText(project.caseIntro) && !hasChapters) return null;

  return (
    <section id="case-story" className="scroll-mt-36 px-5 py-24 md:px-10 lg:px-16">
      <div className="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.8fr_1.2fr]">
        <div className="lg:sticky lg:top-36 lg:self-start">
          <SectionLabel>{text("portfolioCase.story.label", "История проекта")}</SectionLabel>
          <h2 className="max-w-3xl text-4xl font-light leading-tight text-[#F5F2EC] md:text-6xl">
            {text("portfolioCase.story.title", "Что важно увидеть в этом кейсе")}
          </h2>
          <p className="mt-6 max-w-xl text-lg leading-relaxed text-[#D6D1CA]">
            {project.description}
          </p>
        </div>

        <div className="grid gap-4">
          {copy.chapters.map((chapter, index) => (
            <GlassPanel key={chapter.title} className="rounded-[2rem] p-7 md:p-8">
              <div className="mb-8 flex items-start justify-between gap-5">
                <h3 className="text-3xl font-light text-white">{chapter.title}</h3>
                <span className="text-sm text-[#D69A66]">0{index + 1}</span>
              </div>
              <p className="text-lg leading-relaxed text-[#D6D1CA]">{chapter.text}</p>
            </GlassPanel>
          ))}
        </div>
      </div>
    </section>
  );
}

function Deliverables({ copy }: { copy: ProjectCaseCopy }) {
  const text = useCmsText();
  const items = copy.deliverables.filter(hasText);
  if (!items.length) return null;

  return (
    <section id="case-deliverables" className="scroll-mt-36 border-y border-white/10 px-5 py-20 md:px-10 lg:px-16">
      <div className="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-start">
        <div>
          <SectionLabel>{text("portfolioCase.deliverables.label", "Состав")}</SectionLabel>
          <h2 className="text-4xl font-light leading-tight md:text-6xl">{text("portfolioCase.deliverables.title", "Что входит в проектную подачу")}</h2>
        </div>
        <div className="grid gap-4 md:grid-cols-2">
          {items.map((item, index) => (
            <div key={item} className="rounded-[1.5rem] border border-white/10 bg-white/[0.03] p-6">
              <span className="text-sm text-[#D69A66]">0{index + 1}</span>
              <h3 className="mt-8 text-2xl font-light text-white">{item}</h3>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

function ProjectGallery({
  project,
  gallery,
  related,
}: {
  project: Project;
  gallery: string[];
  related: Project[];
}) {
  const cmsText = useCmsText();
  const text = (key: string, fallback: string) => {
    if (key === "portfolioCase.gallery.sectionLabel" && project.galleryEyebrow) {
      return project.galleryEyebrow;
    }

    if (key === "portfolioCase.gallery.title" && project.galleryTitle) {
      return project.galleryTitle;
    }

    if (key === "portfolioCase.gallery.text" && project.galleryText) {
      return project.galleryText;
    }

    return cmsText(key, fallback);
  };
  const [lightboxIndex, setLightboxIndex] = useState<number | null>(null);
  const [galleryOffset, setGalleryOffset] = useState(0);
  const galleryStripRef = useRef<HTMLDivElement | null>(null);
  const defaultLabels = [
    text("portfolioCase.gallery.label1", "Главный ракурс"),
    text("portfolioCase.gallery.label2", "Финальная подача"),
    text("portfolioCase.gallery.label3", "Исходная сцена"),
    text("portfolioCase.gallery.label4", "Материалы"),
    text("portfolioCase.gallery.label5", "Контекст"),
  ];
  const labels = defaultLabels.map((label, index) => project.galleryLabels?.[index] || label);
  const lightboxImage = lightboxIndex === null ? null : gallery[lightboxIndex];
  const currentLightboxIndex = lightboxIndex ?? 0;
  const lowerGallery = gallery.slice(1);
  const labelFor = (index: number) => labels[index] || text("portfolioCase.gallery.defaultCardLabel", "Ракурс проекта");
  const visibleLowerGallery = lowerGallery.length > 0
    ? Array.from({ length: Math.min(3, lowerGallery.length) }, (_, index) => {
        const lowerIndex = (galleryOffset + index) % lowerGallery.length;
        const galleryIndex = lowerIndex + 1;

        return {
          image: gallery[galleryIndex],
          index: galleryIndex,
        };
      })
    : [];

  useEffect(() => {
    setGalleryOffset(0);
    setLightboxIndex(null);
  }, [project.slug]);

  useEffect(() => {
    const cards = galleryStripRef.current?.querySelectorAll(".case-gallery-card");
    if (!cards?.length) return;

    gsap.fromTo(
      cards,
      { autoAlpha: 0, x: 24, scale: 0.985 },
      {
        autoAlpha: 1,
        x: 0,
        scale: 1,
        duration: 0.46,
        stagger: 0.055,
        ease: "power3.out",
        clearProps: "transform,opacity,visibility",
      },
    );
  }, [galleryOffset, project.slug]);

  if (!gallery.length) return null;

  return (
    <section id="case-gallery" className="scroll-mt-36 px-5 py-24 md:px-10 lg:px-16">
      <div className="mx-auto max-w-7xl">
        <div className="mb-12 grid gap-8 md:grid-cols-[1fr_0.8fr] md:items-end">
          <div>
            <SectionLabel>{text("portfolioCase.gallery.sectionLabel", "Галерея")}</SectionLabel>
            <h2 className="text-4xl font-light leading-tight md:text-6xl">{text("portfolioCase.gallery.title", "Ракурсы и детали проекта")}</h2>
          </div>
          <p className="text-lg leading-relaxed text-[#D6D1CA]">
            {text("portfolioCase.gallery.text", "Изображения собраны в удобный просмотр: можно открыть крупный кадр и быстро переключиться между визуальными состояниями.")}
          </p>
        </div>

        <div className="grid gap-5">
          <button
            type="button"
            onClick={() => setLightboxIndex(0)}
            className="group relative min-h-[420px] overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.03] text-left transition duration-300 hover:-translate-y-1 hover:border-[#D69A66]/60 md:min-h-[560px]"
          >
            <CinematicImage frames={[gallery[0], gallery[1], gallery[2]]} alt={project.title} fill hint="view" />
            <div className="absolute inset-0 bg-gradient-to-t from-[#050505]/70 via-transparent to-[#D69A66]/10" />
            <div className="absolute bottom-6 left-6 right-6">
              <p className="text-xs uppercase text-[#D69A66]">01 / {labels[0]}</p>
              <h3 className="mt-3 text-3xl font-light text-white">{project.title}</h3>
            </div>
          </button>

          {visibleLowerGallery.length ? (
            <>
              <div className="flex items-center justify-between gap-4">
                <p className="text-xs uppercase tracking-[0.24em] text-[#D69A66]">{text("portfolioCase.gallery.moreLabel", "Дополнительные ракурсы")}</p>
                {lowerGallery.length > 3 ? (
                  <div className="flex gap-2">
                    <button
                      type="button"
                      aria-label={text("portfolioCase.gallery.prevCardsAria", "Предыдущие ракурсы")}
                      onClick={() => setGalleryOffset((current) => (current - 1 + lowerGallery.length) % lowerGallery.length)}
                      className="grid h-10 w-10 place-items-center rounded-full border border-white/15 bg-white/[0.05] text-xl text-white transition hover:border-[#D69A66]/60 hover:text-[#D69A66]"
                    >
                      ‹
                    </button>
                    <button
                      type="button"
                      aria-label={text("portfolioCase.gallery.nextCardsAria", "Следующие ракурсы")}
                      onClick={() => setGalleryOffset((current) => (current + 1) % lowerGallery.length)}
                      className="grid h-10 w-10 place-items-center rounded-full border border-white/15 bg-white/[0.05] text-xl text-white transition hover:border-[#D69A66]/60 hover:text-[#D69A66]"
                    >
                      ›
                    </button>
                  </div>
                ) : null}
              </div>

              <div ref={galleryStripRef} className="grid gap-5 md:grid-cols-3">
                {visibleLowerGallery.map(({ image, index }) => (
                  <button
                    type="button"
                    key={`${image}-${index}`}
                    onClick={() => setLightboxIndex(index)}
                    className="case-gallery-card group relative min-h-[210px] overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.03] text-left transition duration-300 hover:-translate-y-1 hover:border-[#D69A66]/60"
                  >
                    <CinematicImage
                      frames={[image, gallery[(index + 1) % gallery.length], related[index % Math.max(related.length, 1)]?.image]}
                      alt={`${project.title}: ${labelFor(index)}`}
                      fill
                      hint="view"
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-[#050505]/72 via-transparent to-transparent" />
                    <div className="absolute bottom-5 left-5 right-5">
                      <p className="text-xs uppercase text-[#D69A66]">0{index + 1}</p>
                      <h3 className="mt-2 text-xl font-light text-white">{labelFor(index)}</h3>
                    </div>
                  </button>
                ))}
              </div>
            </>
          ) : null}
        </div>
      </div>

      {lightboxImage && (
        <div
          className="fixed inset-0 z-[140] flex items-center justify-center bg-[#050505]/90 p-4 backdrop-blur-xl md:p-8"
          role="dialog"
          aria-modal="true"
          aria-label={`${text("portfolioCase.lightboxAria", "Просмотр изображения проекта")} ${project.title}`}
          onClick={() => setLightboxIndex(null)}
        >
          <button
            type="button"
            aria-label={text("portfolioCase.closeViewAria", "Закрыть просмотр")}
            onClick={() => setLightboxIndex(null)}
            className="absolute right-5 top-5 z-10 grid h-11 w-11 place-items-center rounded-full border border-white/15 bg-white/10 text-2xl leading-none text-white transition hover:border-[#D69A66]/60 hover:text-[#D69A66]"
          >
            ×
          </button>
          <button
            type="button"
            aria-label={text("portfolioCase.prevImageAria", "Предыдущее изображение")}
            onClick={(event) => {
              event.stopPropagation();
              setLightboxIndex((current) => (current === null ? current : (current - 1 + gallery.length) % gallery.length));
            }}
            className="absolute left-5 top-1/2 z-10 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-full border border-white/15 bg-white/10 text-2xl text-white transition hover:border-[#D69A66]/60 hover:text-[#D69A66]"
          >
            ‹
          </button>
          <img
            src={lightboxImage}
            alt={`${project.title} ${currentLightboxIndex + 1}`}
            className="max-h-[88vh] w-full max-w-6xl rounded-[1.5rem] object-contain shadow-[0_40px_140px_rgba(0,0,0,0.55)]"
            onClick={(event) => event.stopPropagation()}
          />
          <button
            type="button"
            aria-label={text("portfolioCase.nextImageAria", "Следующее изображение")}
            onClick={(event) => {
              event.stopPropagation();
              setLightboxIndex((current) => (current === null ? current : (current + 1) % gallery.length));
            }}
            className="absolute right-5 top-1/2 z-10 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-full border border-white/15 bg-white/10 text-2xl text-white transition hover:border-[#D69A66]/60 hover:text-[#D69A66]"
          >
            ›
          </button>
          <div className="absolute bottom-5 left-1/2 -translate-x-1/2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs text-white/65 backdrop-blur">
            {currentLightboxIndex + 1} / {gallery.length}
          </div>
        </div>
      )}
    </section>
  );
}

function CompareBlock({
  project,
  beforeImage,
  afterImage,
}: {
  project: Project;
  beforeImage: string;
  afterImage: string;
}) {
  const text = useCmsText();
  const [compare, setCompare] = useState(52);
  if (!beforeImage || !afterImage) return null;

  return (
    <section id="case-compare" className="scroll-mt-36 border-y border-white/10 px-5 py-24 md:px-10 lg:px-16">
      <div className="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.82fr_1.18fr] lg:items-center">
        <div>
          <SectionLabel>{text("portfolioCase.compare.label", "До / после")}</SectionLabel>
          <h2 className="text-4xl font-light leading-tight md:text-6xl">{text("portfolioCase.compare.title", "Сравнение визуального сценария")}</h2>
          <p className="mt-6 text-lg leading-relaxed text-[#D6D1CA]">
            {text("portfolioCase.compare.text", "Ползунок помогает быстро увидеть разницу между исходным состоянием и финальной проектной подачей.")}
          </p>
        </div>

        <div className="group relative overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.025]">
          <img
            src={afterImage}
            alt={`${project.title}: ${text("portfolioCase.compare.after", "после")}`}
            className="h-[560px] w-full object-cover transition duration-700 group-hover:scale-[1.02]"
          />
          <div className="absolute inset-y-0 left-0 overflow-hidden" style={{ width: `${compare}%` }}>
            <img
              src={beforeImage}
              alt={`${project.title}: ${text("portfolioCase.compare.before", "до")}`}
              className="h-[560px] w-[calc(100vw-40px)] max-w-none object-cover transition duration-700 group-hover:scale-[1.02] lg:w-[760px]"
            />
          </div>
          <div className="absolute inset-0 bg-gradient-to-t from-[#050505]/30 via-transparent to-transparent" />
          <div
            className="absolute inset-y-0 w-px bg-[#D69A66] shadow-[0_0_28px_rgba(214,154,102,0.9)]"
            style={{ left: `${compare}%` }}
          />
          <div
            className="pointer-events-none absolute top-1/2 h-10 w-10 -translate-x-1/2 -translate-y-1/2 rounded-full border border-[#D69A66]/80 bg-[#050505]/70 shadow-[0_0_30px_rgba(214,154,102,0.35)] backdrop-blur"
            style={{ left: `${compare}%` }}
          />
          <div className="absolute left-5 top-5 rounded-full border border-white/15 bg-[#050505]/55 px-4 py-2 text-xs text-white/70 backdrop-blur">
            {text("portfolioCase.compare.beforeLabel", "До")}
          </div>
          <div className="absolute right-5 top-5 rounded-full border border-[#D69A66]/35 bg-[#050505]/55 px-4 py-2 text-xs text-[#D69A66] backdrop-blur">
            {text("portfolioCase.compare.afterLabel", "После")}
          </div>
          <input
            aria-label={text("portfolioCase.compareAria", "Сравнение до и после")}
            type="range"
            min="0"
            max="100"
            value={compare}
            onChange={(event) => setCompare(Number(event.target.value))}
            className="absolute inset-x-8 bottom-8 accent-[#D69A66]"
          />
        </div>
      </div>
    </section>
  );
}

function ProcessBlock({ copy }: { copy: ProjectCaseCopy }) {
  const text = useCmsText();
  const items = copy.process.filter(hasText);
  if (!items.length) return null;

  return (
    <section className="px-5 py-24 md:px-10 lg:px-16">
      <div className="mx-auto max-w-7xl">
        <div className="mb-12 grid gap-8 md:grid-cols-[1fr_0.8fr] md:items-end">
          <div>
            <SectionLabel>{text("portfolioCase.process.label", "Процесс")}</SectionLabel>
            <h2 className="text-4xl font-light leading-tight md:text-6xl">{text("portfolioCase.process.title", "Как проект становится понятным")}</h2>
          </div>
          <p className="text-lg leading-relaxed text-[#D6D1CA]">
            {text("portfolioCase.process.text", "Каждый этап делает следующую встречу короче: меньше догадок, больше проверенных решений и ясных материалов для согласования.")}
          </p>
        </div>

        <div className="grid gap-px overflow-hidden rounded-[2rem] border border-white/10 bg-white/10 md:grid-cols-5">
          {items.map((step, index) => (
            <div key={step} className="bg-[#15130f]/90 p-6">
              <span className="mb-12 block text-sm text-[#D69A66]">0{index + 1}</span>
              <h3 className="text-xl font-light leading-tight text-white">{step}</h3>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

function RelatedProjects({ project, related }: { project: Project; related: Project[] }) {
  const text = useCmsText();
  if (!related.length) return null;

  return (
    <section id="case-related" className="scroll-mt-36 border-t border-white/10 px-5 py-24 md:px-10 lg:px-16">
      <div className="mx-auto max-w-7xl">
        <div className="mb-12 flex flex-col justify-between gap-6 md:flex-row md:items-end">
          <div>
            <SectionLabel>{text("portfolioCase.related.label", "Похожие проекты")}</SectionLabel>
            <h2 className="text-4xl font-light leading-tight md:text-6xl">{text("portfolioCase.related.title", "Можно открыть дальше")}</h2>
          </div>
          <Link href="/portfolio" className="text-sm uppercase text-[#D69A66] transition hover:text-[#F5F2EC]">
            {text("portfolioCase.related.allButton", "Все портфолио →")}
          </Link>
        </div>

        <div className="grid gap-5 md:grid-cols-3">
          {related.map((item) => (
            <Link
              key={item.slug}
              href={`/portfolio/${item.slug}`}
              className="group overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.03] transition duration-300 hover:-translate-y-2 hover:border-[#D69A66]/60"
            >
              <div className="relative h-72 overflow-hidden">
                <CinematicImage frames={[item.image, project.image, item.afterImage]} alt={item.title} fill hint="open" />
                <div className="absolute inset-0 bg-gradient-to-t from-[#050505]/70 via-transparent to-[#D69A66]/10" />
                <div className="absolute bottom-5 left-5 right-5">
                  <p className="text-xs uppercase text-[#D69A66]">{item.category}</p>
                  <h3 className="mt-2 text-2xl font-light text-white">{item.title}</h3>
                </div>
              </div>
              <div className="p-6">
                <p className="text-sm leading-relaxed text-[#D6D1CA]">{item.description}</p>
              </div>
            </Link>
          ))}
        </div>
      </div>
    </section>
  );
}

function ProjectCta({ project }: { project: Project }) {
  const text = useCmsText();
  return (
    <section className="px-5 pb-28 pt-10 md:px-10 lg:px-16">
      <div className="mx-auto overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.03] md:max-w-7xl">
        <div className="grid lg:grid-cols-[0.9fr_1.1fr]">
          <div className="relative min-h-[360px]">
            <CinematicImage frames={[project.image, project.afterImage, project.beforeImage]} alt={project.title} fill hint="case" />
            <div className="absolute inset-0 bg-gradient-to-t from-[#050505]/60 via-transparent to-transparent" />
          </div>
          <div className="p-7 md:p-10 lg:p-12">
            <p className="text-xs uppercase text-[#D69A66]">{text("portfolioCase.cta.label", "Следующий шаг")}</p>
            <h2 className="mt-4 max-w-3xl text-4xl font-light leading-tight text-white md:text-6xl">
              {text("portfolioCase.cta.title", "Нужен проект с такой же ясной подачей?")}
            </h2>
            <p className="mt-6 max-w-2xl text-lg leading-relaxed text-[#D6D1CA]">
              {text("portfolioCase.cta.text", "Расскажите о задаче, площади, сроках и формате работы. Мы предложим понятный маршрут: от брифа до визуальной или рабочей документации.")}
            </p>
            <div className="mt-9 flex flex-wrap gap-3">
              <Link
                href="/kontakty"
                className="rounded-full bg-[#D69A66] px-6 py-4 text-xs uppercase text-[#050505] transition hover:bg-[#F5F2EC]"
              >
                {text("portfolioCase.cta.primaryButton", "Обсудить проект")}
              </Link>
              <Link
                href="/services"
                className="rounded-full border border-white/15 px-6 py-4 text-xs uppercase text-[#D6D1CA] transition hover:border-[#D69A66] hover:text-white"
              >
                {text("portfolioCase.cta.secondaryButton", "Услуги студии")}
              </Link>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

function PortfolioProjectPage({ project }: { project: Project }) {
  const { projects } = useCms();
  const text = useCmsText();
  const currentProject = projects.find((item) => item.slug === project.slug) ?? project;
  const related = useMemo(() => getRelatedProjects(projects.length ? projects : [project], currentProject), [currentProject, project, projects]);
  const copy = getProjectCopy(currentProject, text);
  const gallery = useMemo(
    () => {
      const cmsGallery = currentProject.galleryImages ?? [];

      return uniqueImages(
        cmsGallery.length
          ? cmsGallery
          : [currentProject.image, currentProject.afterImage, currentProject.beforeImage],
      );
    },
    [currentProject],
  );
  const heroImages = useMemo(
    () => uniqueImages(currentProject.heroImages?.length ? currentProject.heroImages : [currentProject.image, ...gallery]),
    [currentProject, gallery],
  );
  const beforeImage = currentProject.beforeImage || "";
  const afterImage = currentProject.afterImage || "";
  const virtualTourScenes = currentProject.virtualTour?.scenes?.filter((scene) => scene.panorama) ?? [];
  const hasStory = hasStoryContent(currentProject);
  const hasDeliverables = hasDeliverablesContent(currentProject);
  const hasVirtualTour = Boolean(currentProject.isVirtualTour && virtualTourScenes.length);
  const hasGallery = gallery.length > 0;
  const hasCompare = Boolean(beforeImage && afterImage);

  return (
    <article className="page-in">
      <ProjectHero project={currentProject} gallery={heroImages} copy={copy} />
      <CaseNavigation
        hasStory={hasStory}
        hasDeliverables={hasDeliverables}
        hasVirtualTour={hasVirtualTour}
        hasGallery={hasGallery}
        hasCompare={hasCompare}
        hasRelated={related.length > 0}
      />
      <MetricStrip project={currentProject} copy={copy} />
      {hasVirtualTour ? (
        <VirtualTourDemo
          sectionId="case-virtual-tour"
          scenes={virtualTourScenes}
          eyebrow={currentProject.virtualTour?.eyebrow || text("portfolioCase.virtualTour.eyebrow", "Demo / virtual tour")}
          title={currentProject.virtualTour?.title || text("portfolioCase.virtualTour.title", "Пример тура по проекту из сшитых 360° панорам")}
          text={
            currentProject.virtualTour?.text ||
            text(
              "portfolioCase.virtualTour.text",
              "Внутри можно крутиться мышью или пальцем, приближать колесом и переходить между точками через хотспоты и мини-план.",
            )
          }
          buttonLabel={currentProject.virtualTour?.buttonLabel || text("portfolioCase.virtualTour.button", "Открыть полноэкранный тур")}
          fullscreenHref={`/virtualnyj-3d-tur-demo?project=${encodeURIComponent(currentProject.slug)}`}
          formatTitle={text("portfolioCase.virtualTour.formatTitle", "Сцены проекта")}
          formatItems={virtualTourScenes.map((scene, index) => scene.title || `${index + 1}`)}
          credit={text("portfolioCase.virtualTour.credit", "Панорамы загружены для этого проекта.")}
        />
      ) : null}
      {hasStory ? <ProjectStory project={currentProject} copy={copy} /> : null}
      {hasDeliverables ? <Deliverables copy={copy} /> : null}
      {hasGallery ? <ProjectGallery project={currentProject} gallery={gallery} related={related} /> : null}
      {hasCompare ? <CompareBlock project={currentProject} beforeImage={beforeImage} afterImage={afterImage} /> : null}
      <RelatedProjects project={currentProject} related={related} />
      <ProjectCta project={currentProject} />
    </article>
  );
}

export default PortfolioProjectPage;
