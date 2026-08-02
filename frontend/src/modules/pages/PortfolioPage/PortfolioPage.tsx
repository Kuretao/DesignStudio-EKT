"use client";

import { useEffect, useMemo, useRef, useState } from "react";
import Link from "next/link";
import gsap from "gsap";
import { useCms, useCmsText } from "@/src/cms";
import type { Project } from "@/src/types";
import CinematicImage from "@/src/components/common/CinematicImage";
import CustomSelect from "@/src/components/forms/CustomSelect";
import SectionLabel from "@/src/components/common/SectionLabel";
import { GlassPanel } from "@/src/ui";

type PortfolioProps = {
  activeProject: Project;
  setActiveProject: (project: Project) => void;
};

type PortfolioGridProps = {
  onSelectProject: (project: Project) => void;
};

function scrollToProjectShowcase() {
  window.setTimeout(() => {
    document.getElementById("project-showcase")?.scrollIntoView({
      behavior: "smooth",
      block: "start",
    });
  }, 60);
}

function PortfolioHeroSlider({ onSelectProject }: PortfolioGridProps) {
  const { projects } = useCms();
  const text = useCmsText();
  const heroSlides = projects.slice(0, 4).map((project, index) => ({
    project,
    kicker:
      [
        text("portfolio.hero.kicker1", "Featured interior"),
        text("portfolio.hero.kicker2", "City apartment"),
        text("portfolio.hero.kicker3", "Landscape story"),
        text("portfolio.hero.kicker4", "Commercial space"),
      ][index] || text("portfolio.hero.kickerDefault", "Selected project"),
  }));
  const [activeSlide, setActiveSlide] = useState(0);
  const heroRef = useRef<HTMLDivElement | null>(null);
  const slide = heroSlides[activeSlide];

  useEffect(() => {
    const interval = window.setInterval(() => {
      setActiveSlide((current) => (current + 1) % heroSlides.length);
    }, 5600);

    return () => window.clearInterval(interval);
  }, []);

  useEffect(() => {
    const scope = heroRef.current;
    if (!scope) return;

    gsap.fromTo(
      scope.querySelectorAll(".hero-copy > *"),
      { autoAlpha: 0, y: 28 },
      { autoAlpha: 1, y: 0, duration: 0.72, stagger: 0.08, ease: "power3.out" },
    );

    gsap.fromTo(
      scope.querySelector(".hero-image-active"),
      { scale: 1.08, filter: "brightness(0.58) saturate(0.82)" },
      { scale: 1, filter: "brightness(0.82) saturate(1.08)", duration: 1.2, ease: "expo.out" },
    );
  }, [activeSlide]);

  const moveSlide = (direction: number) => {
    setActiveSlide((current) => (current + direction + heroSlides.length) % heroSlides.length);
  };

  return (
    <section ref={heroRef} className="relative min-h-screen overflow-hidden px-5 md:px-10 lg:px-16">
      {heroSlides.map(({ project }, index) => (
        <div
          key={project.id}
          className={`absolute inset-0 transition duration-[1100ms] ease-out ${
            activeSlide === index ? "opacity-100" : "opacity-0"
          }`}
          aria-hidden={activeSlide !== index}
        >
          <img
            src={project.image}
            alt=""
            className={`h-full w-full object-cover ${activeSlide === index ? "hero-image-active" : ""}`}
          />
        </div>
      ))}

      <div className="absolute inset-0 bg-[linear-gradient(90deg,rgba(5,5,5,.92)_0%,rgba(5,5,5,.62)_44%,rgba(5,5,5,.18)_100%)]" />
      <div className="absolute inset-0 bg-[radial-gradient(circle_at_78%_28%,rgba(214,154,102,.34),transparent_30%)] mix-blend-screen" />
      <div className="pointer-events-none absolute -inset-x-20 bottom-0 h-52 bg-gradient-to-t from-[#050505] to-transparent" />
      <div className="pointer-events-none absolute inset-y-0 right-0 w-1/2 overflow-hidden opacity-45">
        <div className="absolute right-[8%] top-[18%] h-[52vh] w-[52vh] rounded-full border border-white/15 animate-[spin_28s_linear_infinite]" />
        <div className="absolute right-[18%] top-[30%] h-[34vh] w-[34vh] rounded-full border border-[#D69A66]/30 animate-[spin_18s_linear_infinite_reverse]" />
      </div>

      <div className="relative z-10 mx-auto flex min-h-screen max-w-7xl items-end pb-24 pt-36 md:items-center md:pb-0">
        <div className="hero-copy max-w-4xl">
          <p className="text-xs uppercase tracking-[0.32em] text-[#D69A66]">{slide.kicker}</p>
          <h1 className="mt-5 max-w-4xl text-[clamp(3rem,6.4vw,6.2rem)] font-light leading-[0.94] tracking-[-0.045em] text-white">
            {slide.project.title}
          </h1>
          <p className="mt-7 max-w-2xl text-base leading-relaxed text-[#E8E0D8]/82 md:text-xl">{slide.project.description}</p>

          <div className="mt-9 flex flex-wrap items-center gap-3">
            <button
              type="button"
              onClick={() => {
                onSelectProject(slide.project);
                scrollToProjectShowcase();
              }}
              className="rounded-full border border-[#D69A66] bg-[#D69A66] px-6 py-4 text-xs uppercase tracking-[0.24em] text-[#050505] transition duration-300 hover:-translate-y-0.5 hover:bg-[#E3AD7B]"
            >
              {text("portfolio.hero.viewButton", "Смотреть проект")}
            </button>
            <span className="rounded-full border border-white/15 bg-black/25 px-5 py-4 text-xs uppercase tracking-[0.24em] text-white/75 backdrop-blur">
              {slide.project.category} / {slide.project.location}
            </span>
          </div>
        </div>
      </div>

      <div className="absolute bottom-8 left-5 right-5 z-20 mx-auto flex max-w-7xl items-center justify-between gap-4 md:left-10 md:right-10 lg:left-16 lg:right-16">
        <div className="flex items-center gap-2">
          {heroSlides.map(({ project }, index) => (
            <button
              key={project.id}
              type="button"
              aria-label={text("slider.dotAria", "Слайд {number}").replace("{number}", String(index + 1))}
              onClick={() => setActiveSlide(index)}
              className={`h-1.5 rounded-full transition-all duration-500 ${
                activeSlide === index ? "w-12 bg-[#D69A66]" : "w-5 bg-white/30 hover:bg-white/60"
              }`}
            />
          ))}
        </div>

        <div className="flex items-center gap-2">
          <button
            type="button"
            aria-label={text("slider.prevAria", "Предыдущий слайд")}
            onClick={() => moveSlide(-1)}
            className="grid h-12 w-12 place-items-center rounded-full border border-white/15 bg-black/25 text-xl text-white backdrop-blur transition hover:border-[#D69A66]/70 hover:text-[#D69A66]"
          >
            ‹
          </button>
          <button
            type="button"
            aria-label={text("slider.nextAria", "Следующий слайд")}
            onClick={() => moveSlide(1)}
            className="grid h-12 w-12 place-items-center rounded-full border border-white/15 bg-black/25 text-xl text-white backdrop-blur transition hover:border-[#D69A66]/70 hover:text-[#D69A66]"
          >
            ›
          </button>
        </div>
      </div>
    </section>
  );
}

export function PortfolioGrid({ onSelectProject }: PortfolioGridProps) {
  const { projects } = useCms();
  const text = useCmsText();
  const squareOptions = [
    { value: "compact", label: text("portfolio.filters.square.compact", "до 100 м²") },
    { value: "medium", label: text("portfolio.filters.square.medium", "100-250 м²") },
    { value: "large", label: text("portfolio.filters.square.large", "250+ м²") },
  ];
  const toneOptions = [
    { value: "warm", label: text("portfolio.filters.tone.warm", "Тёплый") },
    { value: "neutral", label: text("portfolio.filters.tone.neutral", "Нейтральный") },
    { value: "dark", label: text("portfolio.filters.tone.dark", "Тёмный") },
  ];
  const directionOptions = [text("portfolio.filters.allProjects", "All projects"), ...Array.from(new Set(projects.map((project) => project.category)))];
  const directionSelectOptions = directionOptions.slice(1).map((direction) => ({ value: direction, label: direction }));
  const [searchQuery, setSearchQuery] = useState("");
  const [activeDirection, setActiveDirection] = useState("");
  const [activeSquare, setActiveSquare] = useState("");
  const [activeTone, setActiveTone] = useState("");
  const gridRef = useRef<HTMLDivElement | null>(null);

  const filteredProjects = useMemo(() => {
    const query = searchQuery.trim().toLowerCase();
    const byDirection = activeDirection ? projects.filter((project) => project.category === activeDirection) : projects;

    if (!query) return byDirection;

    return byDirection.filter((project) =>
      [project.title, project.category, project.location, project.year, project.description].some((value) =>
        value.toLowerCase().includes(query),
      ),
    );
  }, [activeDirection, searchQuery]);

  useEffect(() => {
    const cards = gridRef.current?.querySelectorAll(".grid-card");
    if (!cards?.length) return;

    gsap.fromTo(
      cards,
      { autoAlpha: 0, y: 28, scale: 0.97 },
      {
        autoAlpha: 1,
        y: 0,
        scale: 1,
        duration: 0.55,
        stagger: 0.06,
        ease: "power3.out",
        clearProps: "transform,opacity,visibility",
      },
    );
  }, [filteredProjects]);

  return (
    <div id="portfolio" className="relative mx-auto w-full max-w-7xl">
      <GlassPanel className="relative z-[80] mb-10 overflow-visible rounded-[2.15rem] p-4 md:p-6">
        <div className="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
          <div className="grid gap-3 rounded-[1.55rem] border border-white/10 bg-white/[0.045] p-3 shadow-[inset_0_1px_0_rgba(255,255,255,0.06)] backdrop-blur-[1px] md:grid-cols-3 lg:w-[620px]">
            <div>
              <span className="mb-2 block px-1 text-xs font-medium text-[#D69A66]">{text("portfolio.filters.directions", "Directions")}</span>
              <CustomSelect
                value={activeDirection}
                onChange={setActiveDirection}
                placeholder={text("portfolio.filters.allProjects", "All projects")}
                options={directionSelectOptions}
              />
            </div>

            <div>
              <span className="mb-2 block px-1 text-xs font-medium text-[#D69A66]">{text("portfolio.filters.square", "Square")}</span>
              <CustomSelect
                value={activeSquare}
                onChange={setActiveSquare}
                placeholder={text("portfolio.filters.allOptions", "All options")}
                options={squareOptions}
              />
            </div>

            <div>
              <span className="mb-2 block px-1 text-xs font-medium text-[#D69A66]">{text("portfolio.filters.tone", "Tone")}</span>
              <CustomSelect
                value={activeTone}
                onChange={setActiveTone}
                placeholder={text("portfolio.filters.allOptions", "All options")}
                options={toneOptions}
              />
            </div>
          </div>

          <label className="relative block lg:w-[438px]">
            <input
              type="search"
              value={searchQuery}
              onChange={(event) => setSearchQuery(event.target.value)}
              placeholder={text("portfolio.filters.searchPlaceholder", "Country, City")}
              className="h-[54px] w-full rounded-[1.55rem] border border-white/10 bg-white/[0.055] px-8 pr-16 text-sm text-white shadow-[inset_0_1px_0_rgba(255,255,255,0.06)] outline-none backdrop-blur-[1px] transition placeholder:text-[#C4A898]/70 focus:border-[#D69A66]/45 focus:bg-white/[0.075]"
            />
            <span className="pointer-events-none absolute right-8 top-1/2 h-4 w-4 -translate-y-1/2 rounded-full border-2 border-white" />
            <span className="pointer-events-none absolute right-[27px] top-[31px] h-2 w-0.5 -rotate-45 bg-white" />
          </label>
        </div>
      </GlassPanel>

      <div ref={gridRef} className="relative z-0 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        {filteredProjects.map((project, index) => (
          <button
            type="button"
            key={project.id}
            onClick={() => {
              onSelectProject(project);
              scrollToProjectShowcase();
            }}
            className="grid-card group overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.03] text-left transition duration-300 will-change-transform hover:-translate-y-2 hover:border-[#D69A66]/60"
          >
            <div className="relative h-80 overflow-hidden">
              <CinematicImage
                frames={[
                  project.image,
                  project.afterImage,
                  project.beforeImage,
                  filteredProjects[(index + 1) % filteredProjects.length]?.image,
                  filteredProjects[(index + 2) % filteredProjects.length]?.image,
                ]}
                alt={project.title}
                fill
                hint="preview"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-black/58 via-black/8 to-[#D69A66]/10 transition duration-500 group-hover:from-black/48" />
              <div className="absolute inset-4 rounded-[1.55rem] border border-white/0 transition duration-500 group-hover:border-white/25" />
              <div className="absolute bottom-5 left-5 right-5">
                <p className="mb-2 text-xs uppercase tracking-[0.28em] text-[#D69A66]">{project.category}</p>
                <h3 className="text-3xl font-light tracking-[-0.04em] transition duration-500 group-hover:translate-x-1">
                  {project.title}
                </h3>
              </div>
            </div>
            <div className="p-6">
              <p className="text-sm leading-relaxed text-[#D6D1CA]">{project.description}</p>
              <span className="mt-5 inline-flex items-center gap-2 text-xs uppercase tracking-[0.22em] text-[#D69A66] transition group-hover:gap-3">
                {text("portfolio.preview", "Предпросмотр")} <span>→</span>
              </span>
            </div>
          </button>
        ))}
      </div>
    </div>
  );
}

export function ProjectShowcase({ project }: { project: Project }) {
  const { projects, siteSettings } = useCms();
  const text = useCmsText();
  const [compare, setCompare] = useState(52);
  const [lightboxIndex, setLightboxIndex] = useState<number | null>(null);
  const fallbackImages = [project.image, ...projects.map((item) => item.image)].filter(Boolean);
  const fallbackImage = fallbackImages[0] || "";
  const fallbackAfterImage = project.afterImage || projects[1]?.image || projects[0]?.afterImage || fallbackImage;
  const fallbackBeforeImage = project.beforeImage || projects[2]?.image || projects[0]?.beforeImage || fallbackImage;
  const gallery = Array.from(new Set([project.image || fallbackImage, fallbackAfterImage, fallbackBeforeImage].filter(Boolean)));
  const lightboxImage = lightboxIndex === null ? null : gallery[lightboxIndex];
  const currentLightboxIndex = lightboxIndex ?? 0;

  useEffect(() => {
    if (lightboxIndex === null) return;

    const onKeyDown = (event: KeyboardEvent) => {
      if (event.key === "Escape") setLightboxIndex(null);
      if (event.key === "ArrowRight") setLightboxIndex((current) => (current === null ? current : (current + 1) % gallery.length));
      if (event.key === "ArrowLeft") setLightboxIndex((current) => (current === null ? current : (current - 1 + gallery.length) % gallery.length));
    };

    document.body.style.overflow = "hidden";
    window.addEventListener("keydown", onKeyDown);

    return () => {
      document.body.style.overflow = "";
      window.removeEventListener("keydown", onKeyDown);
    };
  }, [gallery.length, lightboxIndex]);

  return (
    <section id="project-showcase" className="scroll-mt-28 px-5 py-28 md:px-10 lg:px-16">
      <div className="mx-auto max-w-7xl">
        <div className="relative overflow-hidden rounded-[2.5rem] border border-white/10 bg-white/[0.025]">
          <div className="absolute inset-0 bg-[radial-gradient(circle_at_18%_18%,rgba(214,154,102,.16),transparent_30%)]" />
          <div className="relative grid gap-0 lg:grid-cols-[1.08fr_0.92fr]">
            <div className="group relative min-h-[620px] overflow-hidden">
              <CinematicImage
                frames={[
                  project.image,
                  project.afterImage,
                  project.beforeImage,
                  projects[(project.id + 1) % projects.length]?.image,
                  projects[(project.id + 3) % projects.length]?.image,
                ]}
                alt={project.title}
                fill
                hint="tour"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-[#050505]/68 via-[#050505]/8 to-transparent" />
              <div className="absolute bottom-6 left-6 right-6 flex flex-wrap items-center gap-3">
                <span className="rounded-full border border-[#D69A66]/40 bg-[#050505]/55 px-4 py-2 text-xs uppercase tracking-[0.24em] text-[#D69A66] backdrop-blur">
                  {project.category}
                </span>
                <span className="rounded-full border border-white/15 bg-[#050505]/45 px-4 py-2 text-xs text-white/70 backdrop-blur">
                  {project.location}
                </span>
                <span className="rounded-full border border-white/15 bg-[#050505]/45 px-4 py-2 text-xs text-white/70 backdrop-blur">
                  {project.year}
                </span>
              </div>
            </div>

            <div className="relative flex flex-col justify-between p-7 md:p-10">
              <div>
                <SectionLabel>{text("portfolio.selectedProjectLabel", "Выбранный проект")}</SectionLabel>
                <h2 className="text-5xl font-light leading-[0.95] tracking-[-0.055em] md:text-7xl">{project.title}</h2>
                <p className="mt-7 text-lg leading-relaxed text-[#D6D1CA]">{project.description}</p>
                <Link
                  href={`/portfolio/${project.slug}`}
                  className="mt-8 inline-flex rounded-full border border-[#D69A66] bg-[#D69A66] px-6 py-4 text-xs uppercase tracking-[0.24em] text-[#050505] transition duration-300 hover:-translate-y-0.5 hover:bg-[#F5F2EC]"
                >
                  {text("portfolio.detailsButton", "Подробная информация")}
                </Link>
              </div>

              <div className="mt-10 grid gap-4">
                {[
                  [text("portfolio.taskTitle", "Задача"), text("portfolio.taskText", "Собрать цельный визуальный код объекта: планировка, материалы, свет и настроение.")],
                  [text("portfolio.resultTitle", "Результат"), text("portfolio.resultText", "Проект можно презентовать, согласовывать с подрядчиками и использовать как базу реализации.")],
                  [text("portfolio.formatTitle", "Формат"), text("portfolio.formatText", "3D-ракурсы, подбор решений, рабочая логика и визуальная подача для клиента.")],
                ].map(([title, text]) => (
                  <GlassPanel key={title} className="rounded-[1.25rem] p-5">
                    <span className="text-xs uppercase tracking-[0.28em] text-[#D69A66]">{title}</span>
                    <p className="mt-3 text-sm leading-relaxed text-[#D6D1CA]">{text}</p>
                  </GlassPanel>
                ))}
              </div>
            </div>
          </div>
        </div>

        <div className="mt-8 grid gap-5 md:grid-cols-3">
          {gallery.map((image, index) => (
            <button
              type="button"
              key={`${project.id}-${image}-${index}`}
              onClick={() => setLightboxIndex(index)}
              className="group relative overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.03] transition duration-300 will-change-transform hover:-translate-y-2 hover:border-[#D69A66]/60 hover:shadow-[0_24px_80px_rgba(0,0,0,0.42)]"
            >
              <CinematicImage
                frames={[image, gallery[(index + 1) % gallery.length], gallery[(index + 2) % gallery.length]]}
                alt={`${project.title} gallery ${index + 1}`}
                className="h-80 w-full"
                hint="frames"
                mode="frames"
              />
              <div className="pointer-events-none absolute inset-0 bg-gradient-to-t from-[#050505]/45 via-transparent to-[#D69A66]/10 opacity-0 transition duration-500 group-hover:opacity-100" />
              <span className="absolute bottom-5 left-5 text-xs uppercase tracking-[0.24em] text-[#D69A66]">0{index + 1}</span>
              <span className="absolute bottom-5 right-5 rounded-full border border-white/15 bg-black/35 px-3 py-1.5 text-[10px] uppercase tracking-[0.2em] text-white/70 opacity-0 backdrop-blur transition group-hover:opacity-100">
                {text("portfolio.viewImage", "Смотреть")}
              </span>
            </button>
          ))}
        </div>

        {lightboxImage && (
          <div
            className="fixed inset-0 z-[140] flex items-center justify-center bg-[#050505]/88 p-4 backdrop-blur-xl md:p-8"
            role="dialog"
            aria-modal="true"
            aria-label={`${text("portfolio.lightboxAria", "Просмотр изображения проекта")} ${project.title}`}
            onClick={() => setLightboxIndex(null)}
          >
            <button
              type="button"
              aria-label={text("portfolio.closeViewAria", "Закрыть просмотр")}
              onClick={() => setLightboxIndex(null)}
              className="absolute right-5 top-5 z-10 grid h-11 w-11 place-items-center rounded-full border border-white/15 bg-white/10 text-2xl leading-none text-white transition hover:border-[#D69A66]/60 hover:text-[#D69A66]"
            >
              ×
            </button>
            <button
              type="button"
              aria-label={text("portfolio.prevImageAria", "Предыдущее изображение")}
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
              aria-label={text("portfolio.nextImageAria", "Следующее изображение")}
              onClick={(event) => {
                event.stopPropagation();
                setLightboxIndex((current) => (current === null ? current : (current + 1) % gallery.length));
              }}
              className="absolute right-5 top-1/2 z-10 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-full border border-white/15 bg-white/10 text-2xl text-white transition hover:border-[#D69A66]/60 hover:text-[#D69A66]"
            >
              ›
            </button>
            <div className="absolute bottom-5 left-1/2 -translate-x-1/2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs uppercase tracking-[0.2em] text-white/65 backdrop-blur">
              {currentLightboxIndex + 1} / {gallery.length}
            </div>
          </div>
        )}

        <div className="mt-20 grid gap-8 lg:grid-cols-[0.8fr_1.2fr] lg:items-center">
          <div>
            <SectionLabel className="mb-4">{siteSettings.compareEyebrow}</SectionLabel>
            <h3 className="text-4xl font-light tracking-[-0.045em] md:text-6xl">{siteSettings.compareTitle}</h3>
            <p className="mt-5 text-[#D6D1CA]">{siteSettings.compareText}</p>
          </div>

          <div className="group relative overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.025] transition duration-500 hover:-translate-y-1 hover:border-[#D69A66]/55 hover:shadow-[0_28px_90px_rgba(214,154,102,0.12)]">
            <img
              src={fallbackBeforeImage}
              alt="before"
              className="h-[520px] w-full object-cover transition duration-700 group-hover:scale-[1.03] group-hover:saturate-125"
            />
            <div className="absolute inset-y-0 left-0 overflow-hidden" style={{ width: `${compare}%` }}>
              <img
                src={fallbackAfterImage}
                alt="after"
                className="h-[520px] w-[calc(100vw-40px)] max-w-none object-cover transition duration-700 group-hover:scale-[1.03] group-hover:brightness-110 group-hover:saturate-125 lg:w-[760px]"
              />
            </div>
            <div className="absolute inset-0 bg-gradient-to-t from-[#050505]/30 via-transparent to-transparent opacity-0 transition duration-500 group-hover:opacity-100" />
            <div
              className="absolute inset-y-0 w-px bg-[#D69A66] shadow-[0_0_28px_rgba(214,154,102,0.9)] transition duration-300 group-hover:w-0.5"
              style={{ left: `${compare}%` }}
            />
            <div
              className="pointer-events-none absolute top-1/2 h-9 w-9 -translate-x-1/2 -translate-y-1/2 rounded-full border border-[#D69A66]/80 bg-[#050505]/65 shadow-[0_0_30px_rgba(214,154,102,0.35)] backdrop-blur transition duration-300 group-hover:scale-110 group-hover:bg-[#D69A66] group-hover:shadow-[0_0_42px_rgba(214,154,102,0.75)]"
              style={{ left: `${compare}%` }}
            />
            <input
              aria-label={text("portfolio.compareAria", "Сравнение до и после")}
              type="range"
              min="0"
              max="100"
              value={compare}
              onChange={(event) => setCompare(Number(event.target.value))}
              className="absolute inset-x-8 bottom-8 accent-[#D69A66]"
            />
          </div>
        </div>
      </div>
    </section>
  );
}

function PortfolioPage({ activeProject, setActiveProject }: PortfolioProps) {
  const { projects } = useCms();
  return (
    <div className="page-in">
      <PortfolioHeroSlider onSelectProject={setActiveProject} />
      <section className="px-5 py-24 md:px-10 lg:px-16">
        <PortfolioGrid onSelectProject={setActiveProject} />
      </section>
      <ProjectShowcase project={activeProject} />
    </div>
  );
}

export default PortfolioPage;
