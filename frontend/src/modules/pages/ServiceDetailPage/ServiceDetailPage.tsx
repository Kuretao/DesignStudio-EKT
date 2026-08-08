"use client";

import { useEffect, useMemo, useState } from "react";
import Link from "next/link";
import {
  getServiceLandingCopy,
  servicePageItems as fallbackServicePageItems,
} from "@/src/data";
import { useCms, useCmsText } from "@/src/cms";
import { GlassPanel } from "@/src/ui";
import BrandStrip from "@/src/components/common/BrandStrip";
import HeroBackdropSlider from "@/src/components/common/HeroBackdropSlider";
import ProjectQuiz from "@/src/components/common/ProjectQuiz";
import SectionLabel from "@/src/components/common/SectionLabel";
import VirtualTourDemo from "@/src/components/common/VirtualTourDemo";
import { ContactSection } from "@/src/modules/pages/ContactPage";
import type { Project, ProjectCategory } from "@/src/types";

type ServicePageItem = (typeof fallbackServicePageItems)[number];

function getPortfolioCategory(item: ServicePageItem): ProjectCategory {
  const value = `${item.id} ${item.title}`.toLowerCase();

  if (
    value.includes("landshaft") ||
    value.includes("ландшафт") ||
    value.includes("озелен")
  )
    return "Ландшафт";
  if (
    value.includes("arhitektur") ||
    value.includes("архитект") ||
    value.includes("3d")
  )
    return "Архитектура";

  return "Интерьеры";
}

function ServiceCompareBlock({
  item,
  projects,
}: {
  item: ServicePageItem;
  projects: Project[];
}) {
  const cmsText = useCmsText();
  const [compare, setCompare] = useState(52);
  const category = getPortfolioCategory(item);
  const project =
    projects.find((entry) => entry.category === category) ?? projects[0];
  const serviceCompare = item as ServicePageItem & {
    compareEyebrow?: string;
    compareTitle?: string;
    compareText?: string;
    compareBeforeImage?: string;
    compareAfterImage?: string;
  };
  const beforeImage =
    serviceCompare.compareBeforeImage ||
    project?.beforeImage ||
    projects[2]?.image ||
    item.image;
  const afterImage =
    serviceCompare.compareAfterImage || project?.afterImage || item.image;
  const text = (key: string, fallback: string) => {
    if (key === "serviceDetail.compare.label") {
      return serviceCompare.compareEyebrow || cmsText(key, fallback);
    }

    if (key === "serviceDetail.compare.title") {
      return serviceCompare.compareTitle || cmsText(key, fallback);
    }

    if (key === "serviceDetail.compare.text") {
      return serviceCompare.compareText || cmsText(key, fallback);
    }

    return cmsText(key, fallback);
  };

  return (
    <section className="border-t border-white/10 px-5 py-24 md:px-10 lg:px-16">
      <div className="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.78fr_1.22fr] lg:items-center">
        <div>
          <SectionLabel>{text("serviceDetail.compare.label", "До / После")}</SectionLabel>
          <h2 className="mt-4 text-4xl font-light tracking-[-0.045em] md:text-6xl">
            {text("serviceDetail.compare.title", "Как идея превращается в готовое пространство")}
          </h2>
          <p className="mt-5 text-lg leading-relaxed text-[#D6D1CA]">
            {text(
              "serviceDetail.compare.text",
              "Здесь можно поставить реальные пары изображений по услуге: исходное состояние и итоговый результат после проектирования.",
            )}
          </p>
        </div>

        <div className="group relative overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.025]">
          <img
            src={beforeImage}
            alt="До проектирования"
            className="h-[520px] w-full object-cover"
          />
          <div
            className="absolute inset-y-0 left-0 overflow-hidden"
            style={{ width: `${compare}%` }}
          >
            <img
              src={afterImage}
              alt="После проектирования"
              className="h-[520px] w-[calc(100vw-40px)] max-w-none object-cover lg:w-[760px]"
            />
          </div>
          <div className="absolute left-5 top-5 rounded-full border border-white/15 bg-[#050505]/62 px-4 py-2 text-xs uppercase tracking-[0.2em] text-white/72 backdrop-blur">
            {text("serviceDetail.compare.before", "До")}
          </div>
          <div className="absolute right-5 top-5 rounded-full border border-[#D69A66]/40 bg-[#050505]/62 px-4 py-2 text-xs uppercase tracking-[0.2em] text-[#D69A66] backdrop-blur">
            {text("serviceDetail.compare.after", "После")}
          </div>
          <div
            className="absolute inset-y-0 w-px bg-[#D69A66] shadow-[0_0_28px_rgba(214,154,102,0.9)]"
            style={{ left: `${compare}%` }}
          />
          <div
            className="pointer-events-none absolute top-1/2 h-10 w-10 -translate-x-1/2 -translate-y-1/2 rounded-full border border-[#D69A66]/80 bg-[#050505]/70 shadow-[0_0_30px_rgba(214,154,102,0.35)] backdrop-blur"
            style={{ left: `${compare}%` }}
          />
          <input
            aria-label={text("serviceDetail.compare.aria", "Сравнение до и после")}
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

function ServicePortfolioBlock({
  item,
  projects,
}: {
  item: ServicePageItem;
  projects: Project[];
}) {
  const text = useCmsText();
  const category = getPortfolioCategory(item);
  const targetProjects = projects.filter(
    (project) => project.category === category,
  );
  const visibleProjects = (
    targetProjects.length ? targetProjects : projects
  ).slice(0, 9);

  return (
    <section className="border-t border-white/10 px-5 py-24 md:px-10 lg:px-16">
      <div className="mx-auto max-w-7xl">
        <div className="mb-10 grid gap-6 md:grid-cols-[1fr_0.75fr] md:items-end">
          <div>
            <SectionLabel>{text("serviceDetail.portfolio.label", "Тематическое портфолио")}</SectionLabel>
            <h2 className="mt-4 text-4xl font-light tracking-[-0.045em] md:text-6xl">
              {text("serviceDetail.portfolio.title", "Целевые проекты по услуге")}
            </h2>
          </div>
          <p className="text-lg leading-relaxed text-[#D6D1CA]">
            {text(
              "serviceDetail.portfolio.text",
              "Карточки ведут на индивидуальные страницы проектов с уникальным URL. Здесь собраны релевантные кейсы по выбранному направлению.",
            )}
          </p>
        </div>

        <div className="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
          {visibleProjects.map((project) => (
            <Link
              key={project.slug}
              href={`/portfolio/${project.slug}`}
              className="group overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.03] transition duration-300 hover:-translate-y-2 hover:border-[#D69A66]/60"
            >
              <div className="relative h-72 overflow-hidden">
                <img
                  src={project.image}
                  alt={project.title}
                  className="h-full w-full object-cover transition duration-700 group-hover:scale-[1.04]"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-[#050505]/68 via-transparent to-transparent" />
                <div className="absolute bottom-5 left-5 right-5">
                  <p className="mb-2 text-xs uppercase tracking-[0.24em] text-[#D69A66]">
                    {project.category}
                  </p>
                  <h3 className="text-2xl font-light tracking-[-0.035em]">
                    {project.title}
                  </h3>
                </div>
              </div>
              <p className="p-6 text-sm leading-relaxed text-[#D6D1CA]">
                {project.description}
              </p>
            </Link>
          ))}
        </div>
      </div>
    </section>
  );
}

function ServiceDocumentsBlock({
  item,
  projects,
}: {
  item: ServicePageItem;
  projects: Project[];
}) {
  const text = useCmsText();
  const [activeIndex, setActiveIndex] = useState(0);
  const [zoomImage, setZoomImage] = useState<string | null>(null);
  const documentImages = useMemo(
    () =>
      Array.isArray((item as any).deliverableImages)
        ? ((item as any).deliverableImages as string[]).filter(Boolean)
        : [],
    [item],
  );
  const docs = useMemo(
    () =>
      item.deliverables.map((title, index) => ({
        title,
        image:
          documentImages[index] ??
          projects[(index + 2) % projects.length]?.image ??
          item.image,
      })),
    [documentImages, item.deliverables, item.image, projects],
  );
  const activeDoc = docs[activeIndex] ?? docs[0];

  useEffect(() => {
    setActiveIndex(0);
  }, [item.id]);

  useEffect(() => {
    if (typeof window === "undefined") {
      return;
    }

    docs.forEach((doc) => {
      if (!doc.image) {
        return;
      }

      const image = new window.Image();
      image.src = doc.image;
      image.decode?.().catch(() => undefined);
    });
  }, [docs]);

  return (
    <section className="border-t border-white/10 px-5 py-24 md:px-10 lg:px-16">
      <div className="mx-auto max-w-7xl">
        <div className="mb-10 grid gap-6 md:grid-cols-[1fr_0.75fr] md:items-end">
          <div>
            <SectionLabel>{text("serviceDetail.documents.label", "Документация")}</SectionLabel>
            <h2 className="mt-4 text-4xl font-light tracking-[-0.045em] md:text-6xl">
              {text("serviceDetail.documents.title", "Что входит в состав рабочей документации")}
            </h2>
          </div>
          <p className="text-lg leading-relaxed text-[#D6D1CA]">
            {text(
              "serviceDetail.documents.text",
              "Блок готов под реальные чертежи, ведомости, дендропланы и схемы инженерии. Изображение можно открыть крупно.",
            )}
          </p>
        </div>

        <div className="grid gap-5 lg:grid-cols-[0.42fr_0.58fr]">
          <div className="grid gap-3">
            {docs.map((doc, index) => (
              <button
                key={`${doc.title}-${index}`}
                type="button"
                onClick={() => setActiveIndex(index)}
                className={`flex items-center justify-between gap-4 rounded-2xl border px-5 py-4 text-left transition ${
                  index === activeIndex
                    ? "border-[#D69A66]/60 bg-[#D69A66]/10 text-white"
                    : "border-white/10 bg-white/[0.035] text-white/55 hover:border-white/22 hover:text-white"
                }`}
              >
                <span>{doc.title}</span>
                <span className="text-[#D69A66]">0{index + 1}</span>
              </button>
            ))}
          </div>

          {activeDoc && (
            <button
              type="button"
              onClick={() => setZoomImage(activeDoc.image)}
              className="group relative min-h-[520px] overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.03] text-left"
            >
              <img
                src={activeDoc.image}
                alt={activeDoc.title}
                loading="eager"
                decoding="sync"
                fetchPriority="high"
                className="absolute inset-0 h-full w-full object-cover opacity-62 grayscale transition duration-700 group-hover:scale-[1.03] group-hover:opacity-78"
              />
              <div className="absolute inset-0 bg-[linear-gradient(135deg,rgba(5,5,5,.88),rgba(5,5,5,.42)),linear-gradient(rgba(245,242,236,.08)_1px,transparent_1px),linear-gradient(90deg,rgba(245,242,236,.08)_1px,transparent_1px)] bg-[length:auto,42px_42px,42px_42px]" />
              <div className="absolute inset-6 flex flex-col justify-between rounded-[1.5rem] border border-white/16 p-6">
                <span className="text-xs uppercase tracking-[0.28em] text-[#D69A66]">
                  {text("serviceDetail.documents.sheetLabel", "Лист")} 0{activeIndex + 1}
                </span>
                <div>
                  <h3 className="max-w-xl text-4xl font-light tracking-normal [overflow-wrap:anywhere]">
                    {activeDoc.title}
                  </h3>
                  <p className="mt-4 text-sm uppercase tracking-[0.22em] text-white/48">
                    {text("serviceDetail.documents.zoomHint", "Нажмите, чтобы увеличить")}
                  </p>
                </div>
              </div>
            </button>
          )}
        </div>
        <div aria-hidden="true" className="hidden">
          {docs.map((doc) => (
            <img
              key={`preload-${doc.image}`}
              src={doc.image}
              alt=""
              loading="eager"
              decoding="async"
            />
          ))}
        </div>
      </div>

      {zoomImage && (
        <div
          className="fixed inset-0 z-[140] flex items-center justify-center bg-[#050505]/88 p-4 backdrop-blur-xl md:p-8"
          role="dialog"
          aria-modal="true"
          onClick={() => setZoomImage(null)}
        >
          <button
            type="button"
            aria-label={text("serviceDetail.documents.closeAria", "Закрыть просмотр")}
            onClick={() => setZoomImage(null)}
            className="absolute right-5 top-5 z-10 grid h-11 w-11 place-items-center rounded-full border border-white/15 bg-white/10 text-2xl leading-none text-white transition hover:border-[#D69A66]/60 hover:text-[#D69A66]"
          >
            ×
          </button>
          <img
            src={zoomImage}
            alt={text("serviceDetail.documents.zoomAlt", "Увеличенный лист документации")}
            className="max-h-[88vh] w-full max-w-6xl rounded-[1.5rem] object-contain shadow-[0_40px_140px_rgba(0,0,0,0.55)]"
          />
        </div>
      )}
    </section>
  );
}

function ExpertFooter() {
  const { reviewStats } = useCms();
  const text = useCmsText();

  return (
    <section className="border-t border-white/10 px-5 py-24 md:px-10 lg:px-16">
      <div className="mx-auto max-w-7xl">
        <GlassPanel className="overflow-hidden rounded-[2rem] p-7 md:p-10">
          <div className="grid gap-8 lg:grid-cols-[0.72fr_1fr] lg:items-end">
            <div>
              <SectionLabel>{text("serviceDetail.expert.label", "Экспертность")}</SectionLabel>
              <h2 className="mt-4 text-4xl font-light tracking-[-0.045em] md:text-6xl">
                {text("serviceDetail.expert.title", "Остались вопросы по проекту? Давайте обсудим")}
              </h2>
            </div>
            <div className="grid gap-4 sm:grid-cols-3">
              {reviewStats.slice(0, 3).map((stat) => (
                <div
                  key={stat.value}
                  className="rounded-2xl border border-white/10 bg-white/[0.035] p-5"
                >
                  <strong className="block text-3xl font-light text-[#D69A66]">
                    {stat.value}
                  </strong>
                  <span className="mt-3 block text-sm leading-relaxed text-[#D6D1CA]">
                    {stat.label}
                  </span>
                </div>
              ))}
            </div>
          </div>
        </GlassPanel>
      </div>
    </section>
  );
}

function ServiceDetailPage({ item }: { item: ServicePageItem }) {
  const { servicePageItems, projects } = useCms();
  const text = useCmsText();
  const currentItem =
    servicePageItems.find((service) => service.id === item.id) ?? item;
  const isVirtualTour = currentItem.id === "virtualnyj-3d-tur-360";
  const landingCopy = getServiceLandingCopy(currentItem);
  const heroSlides = useMemo(() => {
    const serviceImages = Array.isArray((currentItem as any).images)
      ? ((currentItem as any).images as string[])
      : [];
    const category = getPortfolioCategory(currentItem);
    const relatedProjectImages = projects
      .filter((project) => project.category === category)
      .flatMap((project) => [
        project.image,
        ...(project.heroImages ?? []),
        ...(project.galleryImages ?? []),
      ]);
    const fallbackImages = [
      currentItem.image,
      ...relatedProjectImages,
      ...projects.flatMap((project) => [project.image, ...(project.heroImages ?? [])]),
    ].filter(Boolean);
    const images = Array.from(new Set(serviceImages.length > 1 ? serviceImages : fallbackImages)).slice(0, 9);

    return images.map((image, index) => ({
      image,
      alt:
        index === 0
          ? currentItem.title
          : `${currentItem.title} - слайд ${index + 1}`,
    }));
  }, [currentItem, projects]);
  const processGridClass =
    currentItem.process.length <= 1
      ? "md:grid-cols-1"
      : currentItem.process.length === 2
        ? "md:grid-cols-2"
        : currentItem.process.length === 3
          ? "md:grid-cols-3"
          : currentItem.process.length === 4
            ? "md:grid-cols-4"
            : "md:grid-cols-5";

  return (
    <div className="page-in">
      <section className="relative min-h-screen overflow-hidden px-5 pb-20 pt-36 md:px-10 md:pt-32 lg:px-16">
        <HeroBackdropSlider slides={heroSlides} />
        <div className="absolute inset-0 bg-[#050505]/45" />
        <div className="absolute inset-0 bg-[linear-gradient(90deg,rgba(5,5,5,.98),rgba(5,5,5,.70),rgba(5,5,5,.28)),radial-gradient(circle_at_72%_18%,rgba(214,154,102,.22),transparent_34%)]" />

        <div className="relative z-10 mx-auto grid min-h-[calc(100vh-8rem)] max-w-7xl gap-10 lg:grid-cols-[0.95fr_1.05fr] lg:items-end">
          <div>
            <p className="mb-5 max-w-3xl text-xs uppercase tracking-[0.22em] text-[#D69A66] [overflow-wrap:anywhere]">
              {currentItem.eyebrow || currentItem.title}
            </p>
            <h1 className="max-w-4xl text-[clamp(2.15rem,4.25vw,4.65rem)] font-light leading-[1.04] tracking-normal [overflow-wrap:anywhere]">
              {currentItem.title}
            </h1>
            <div
              className="cms-rich-text mt-7 max-w-2xl text-lg leading-relaxed text-[#D6D1CA] md:text-xl"
              dangerouslySetInnerHTML={{ __html: currentItem.text }}
            />
            <div className="mt-10 flex flex-wrap gap-3">
              <Link
                href="#project-quiz"
                className="rounded-full bg-[#D69A66] px-6 py-4 text-xs uppercase tracking-[0.24em] text-[#050505] transition hover:bg-[#F5F2EC]"
              >
                {text("serviceDetail.hero.calcButton", "Рассчитать стоимость проекта")}
              </Link>
              <Link
                href="/services"
                className="rounded-full border border-white/15 px-6 py-4 text-xs uppercase tracking-[0.24em] text-[#D6D1CA] transition hover:border-[#D69A66] hover:text-white"
              >
                {text("serviceDetail.hero.allServicesButton", "Все услуги")}
              </Link>
            </div>
          </div>

          <GlassPanel className="rounded-[2rem] p-6 md:p-8">
            <div className="grid gap-4 sm:grid-cols-2">
              <GlassPanel className="rounded-[1.25rem] p-5">
                <span className="text-xs uppercase tracking-[0.28em] text-white/40">
                  {text("serviceDetail.hero.priceLabel", "Стоимость")}
                </span>
                <strong className="mt-3 block text-3xl font-light leading-tight text-[#D69A66] [overflow-wrap:anywhere]">
                  {currentItem.price}
                </strong>
              </GlassPanel>
              <GlassPanel className="rounded-[1.25rem] p-5">
                <span className="text-xs uppercase tracking-[0.28em] text-white/40">
                  {text("serviceDetail.hero.timelineLabel", "Срок")}
                </span>
                <strong className="mt-3 block text-3xl font-light leading-tight text-white [overflow-wrap:anywhere]">
                  {currentItem.timeline}
                </strong>
              </GlassPanel>
            </div>
            <div className="mt-5 grid gap-3">
              {currentItem.deliverables.map((entry) => (
                <div
                  key={entry}
                  className="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/[0.035] px-4 py-3 text-sm text-[#D6D1CA]"
                >
                  <span className="h-1.5 w-1.5 shrink-0 rounded-full bg-[#D69A66]" />
                  <span className="min-w-0 [overflow-wrap:anywhere]">
                    {entry}
                  </span>
                </div>
              ))}
            </div>
          </GlassPanel>
        </div>
      </section>

      <BrandStrip />
      {isVirtualTour && <VirtualTourDemo />}
      <ServiceCompareBlock item={currentItem} projects={projects} />
      <ServicePortfolioBlock item={currentItem} projects={projects} />
      <ServiceDocumentsBlock item={currentItem} projects={projects} />

      <section className="border-t border-white/10 px-5 py-24 md:px-10 lg:px-16">
        <div className="mx-auto grid max-w-7xl gap-6 lg:grid-cols-[0.8fr_1.2fr]">
          <div>
            <p className="mb-5 text-xs uppercase tracking-[0.45em] text-[#D69A66]">
              {text("serviceDetail.benefits.label", "Почему это работает")}
            </p>
            <h2 className="text-4xl font-light tracking-[-0.045em] md:text-6xl">
              {text("serviceDetail.benefits.title", "Страница собрана из реальной структуры услуги")}
            </h2>
          </div>
          <div className="grid gap-4 md:grid-cols-2">
            {currentItem.benefits.map((benefit, index) => (
              <GlassPanel key={benefit} className="rounded-[1.5rem] p-6">
                <span className="text-sm text-[#D69A66]">0{index + 1}</span>
                <h3 className="mt-6 text-2xl font-light [overflow-wrap:anywhere]">
                  {benefit}
                </h3>
              </GlassPanel>
            ))}
          </div>
        </div>
      </section>

      <section className="px-5 py-24 md:px-10 lg:px-16">
        <div className="mx-auto max-w-7xl">
          <div className="mb-10 flex flex-col justify-between gap-5 md:flex-row md:items-end">
            <h2 className="max-w-3xl text-4xl font-light tracking-[-0.045em] md:text-6xl">
              {text("serviceDetail.process.title", "Как движется проект")}
            </h2>
            <p className="max-w-xl text-[#D6D1CA]">
              {text(
                "serviceDetail.process.text",
                "Процесс коротко пересобран из старых страниц: от заявки и исходных данных до финальных файлов, чертежей или сопровождения.",
              )}
            </p>
          </div>
          <div className={`grid gap-px overflow-hidden rounded-[2rem] border border-white/10 bg-white/10 shadow-[0_24px_90px_rgba(0,0,0,0.24)] ${processGridClass}`}>
            {currentItem.process.map((step, index) => (
              <GlassPanel key={step} className="p-6">
                <span className="mb-12 block text-sm text-[#D69A66]">
                  0{index + 1}
                </span>
                <h3 className="text-xl font-light [overflow-wrap:anywhere]">
                  {step}
                </h3>
              </GlassPanel>
            ))}
          </div>
        </div>
      </section>

      <ProjectQuiz
        kind={landingCopy.quizKind}
        serviceTitle={currentItem.title}
        pdfUrl={(currentItem as any).pdfUrl}
        pdfTitle={(currentItem as any).pdfTitle}
      />
      <ExpertFooter />
      <ContactSection />
    </div>
  );
}

export default ServiceDetailPage;
