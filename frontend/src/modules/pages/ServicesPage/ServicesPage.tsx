"use client";

import Link from "next/link";
import { useCms, useCmsText, useLocalizedField } from "@/src/cms";
import CinematicImage from "@/src/components/common/CinematicImage";
import HeroBackdropSlider from "@/src/components/common/HeroBackdropSlider";
import { GlassPanel } from "@/src/ui";
import SectionLabel from "@/src/components/common/SectionLabel";

const serviceHeroStats = [
  ["10 лет", "опыта в интерьерах, архитектуре и 3D"],
  ["25+", "специалистов под масштаб проекта"],
  ["6", "направлений в единой системе"],
];

function ServicesHero() {
  const { projects, servicePageItems } = useCms();
  const heroCards = servicePageItems.slice(0, 3);

  return (
    <section className="relative min-h-screen overflow-hidden px-5 pb-16 pt-28 md:px-10 lg:px-16">
      <HeroBackdropSlider
        slides={[
          { image: projects[1].image, alt: "Премиальный дизайн интерьера" },
          { image: projects[0].image, alt: "Интерьерный проект" },
          { image: projects[4].image, alt: "Архитектурная визуализация" },
        ]}
      />
      <div className="absolute inset-0 bg-[linear-gradient(90deg,rgba(5,5,5,.96)_0%,rgba(5,5,5,.74)_48%,rgba(5,5,5,.24)_100%)]" />
      <div className="absolute inset-0 bg-[linear-gradient(0deg,#050505_0%,rgba(5,5,5,.4)_32%,transparent_72%)]" />

      <div className="relative z-10 mx-auto grid min-h-[calc(100vh-7rem)] max-w-7xl gap-10 lg:grid-cols-[0.94fr_1.06fr] lg:items-end">
        <div className="pb-6">
          <p className="text-xs uppercase tracking-[0.38em] text-[#D69A66]">
            Услуги / 3D Smart Design Studio
          </p>
          <h1 className="mt-5 max-w-5xl text-[clamp(3rem,6.4vw,6.2rem)] font-light leading-[0.94] tracking-[-0.045em] text-white">
            Услуги студии
          </h1>
          <p className="mt-7 max-w-2xl text-lg leading-relaxed text-[#E8E0D8]/85 md:text-xl">
            Проектируем интерьеры, архитектуру, ландшафт и визуализации так,
            чтобы заказчик видел не набор услуг, а продуманную систему будущего
            пространства.
          </p>
          <div className="mt-9 flex flex-wrap gap-3">
            <Link
              href="/kontakty"
              className="rounded-full border border-[#D69A66] bg-[#D69A66] px-6 py-4 text-xs uppercase tracking-[0.24em] text-[#050505] transition duration-300 hover:-translate-y-0.5 hover:bg-[#E3AD7B]"
            >
              Обсудить проект
            </Link>
            <Link
              href="/portfolio"
              className="rounded-full border border-white/15 bg-black/25 px-6 py-4 text-xs uppercase tracking-[0.24em] text-white/75 backdrop-blur transition duration-300 hover:border-[#D69A66]/70 hover:text-white"
            >
              Смотреть работы
            </Link>
          </div>
        </div>

        <div className="grid gap-4 pb-8">
          <div className="grid gap-px overflow-hidden rounded-[2rem] border border-white/10 bg-white/10 sm:grid-cols-3">
            {serviceHeroStats.map(([value, label]) => (
              <GlassPanel key={value} className="p-5">
                <strong className="block text-3xl font-light tracking-[-0.04em] text-[#D69A66]">
                  {value}
                </strong>
                <span className="mt-3 block text-xs uppercase leading-relaxed tracking-[0.18em] text-[#D6D1CA]">
                  {label}
                </span>
              </GlassPanel>
            ))}
          </div>

          <div className="grid gap-4 md:grid-cols-3">
            {heroCards.map((item) => (
              <Link
                key={item.id}
                href={`/${item.id}`}
                className="group relative min-h-56 overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.03] transition duration-300 will-change-transform hover:-translate-y-2 hover:border-[#D69A66]/60"
              >
                <CinematicImage
                  frames={[item.image, projects[0].image, projects[2].image]}
                  alt={item.title}
                  fill
                  hint="motion"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-[#050505]/68 via-[#050505]/16 to-transparent" />
                <div className="absolute inset-x-5 bottom-5">
                  <p className="mb-2 text-xs uppercase tracking-[0.22em] text-[#D69A66] [overflow-wrap:anywhere]">
                    {item.eyebrow}
                  </p>
                  <h2 className="text-xl font-light leading-tight tracking-normal [overflow-wrap:anywhere]">
                    {item.title}
                  </h2>
                </div>
              </Link>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}

export function ServicesSummary() {
  const { servicePageItems } = useCms();
  const text = useCmsText();
  const fallbackHomeServiceIds = [
    "arhitekturnoe-proektirovanie",
    "arhitekturnaya-3d-vizualizaciya",
    "dizajn-interyera",
    "komplektaciya-ob-ekta",
    "landshaftnyj-dizajn",
    "avtorskij-nadzor",
  ];
  const selectedHomeServices = servicePageItems.filter((item) =>
    Boolean((item as { isHomeItem?: boolean }).isHomeItem),
  );
  const fallbackHomeServices = fallbackHomeServiceIds
    .map((id) => servicePageItems.find((item) => item.id === id))
    .filter((service): service is NonNullable<typeof service> =>
      Boolean(service),
    );
  const mainServices = selectedHomeServices.length
    ? selectedHomeServices
    : fallbackHomeServices;

  return (
    <section
      id="services"
      className="border-t border-white/10 px-5 py-28 md:px-10 lg:px-16"
    >
      <div className="mx-auto max-w-7xl">
        <SectionLabel>
          {text("servicesSummary.label", "Услуги и цены")}
        </SectionLabel>
        <div className="mb-12 grid gap-8 md:grid-cols-[1fr_0.8fr] md:items-end">
          <h2 className="text-5xl font-light tracking-[-0.055em] md:text-7xl">
            {text("servicesSummary.title", "Услуги и цены")}
          </h2>
          <p className="text-[#D6D1CA]">
            {text(
              "servicesSummary.text",
              "Стоимость фиксируется после брифа и состава работ. Ниже - понятная стартовая структура по основным направлениям студии.",
            )}
          </p>
        </div>

        <div className="grid gap-4 md:grid-cols-2">
          {mainServices.map((service) => (
            <GlassPanel key={service.title} className="rounded-[2rem] p-7">
              <div className="mb-8 flex min-w-0 flex-col items-start justify-between gap-4 sm:flex-row">
                <h3 className="min-w-0 text-3xl font-light tracking-normal [overflow-wrap:anywhere]">
                  {service.title}
                </h3>
                <span className="max-w-full rounded-full bg-white/10 px-4 py-2 text-sm leading-snug text-[#D69A66] [overflow-wrap:anywhere]">
                  {service.price}
                </span>
              </div>
              <div
                className="cms-rich-text leading-relaxed text-[#D6D1CA]"
                dangerouslySetInnerHTML={{ __html: service.text }}
              />
            </GlassPanel>
          ))}
        </div>
      </div>
    </section>
  );
}

export function ServicePages() {
  const { projects, serviceNavigationGroups, servicePageItems } = useCms();
  const text = useCmsText();
  const localize = useLocalizedField();
  const localizedServiceNavigationGroups = serviceNavigationGroups.map(
    (group) => ({
      ...group,
      title: localize(group, "title", group.title),
      description: localize(group, "description", group.description),
      items: group.items.map((item) => ({
        ...item,
        label: localize(item, "label", item.label),
      })),
    }),
  );
  const serviceByHref = new Map(
    servicePageItems.map((item) => [`/${item.id}`, item]),
  );

  return (
    <section className="border-t border-white/10 px-5 py-28 md:px-10 lg:px-16">
      <div className="mx-auto max-w-7xl">
        <div className="mb-12 grid gap-8 md:grid-cols-[1fr_0.8fr] md:items-end">
          <div>
            <SectionLabel>
              {text("servicePages.label", "Направления услуг")}
            </SectionLabel>
            <h2 className="text-5xl font-light tracking-[-0.055em] md:text-7xl">
              {text("servicePages.title", "Направления 3D Smart Design Studio")}
            </h2>
          </div>
          <p className="text-lg leading-relaxed text-[#D6D1CA]">
            {text(
              "servicePages.text",
              "Основные направления держат SEO-вес, а посадочные страницы вложены как подпункты: так клиент быстрее ориентируется в услугах.",
            )}
          </p>
        </div>

        <div className="grid gap-5 md:grid-cols-2">
          {localizedServiceNavigationGroups.map((group, index) => {
            const mainService = serviceByHref.get(group.href);
            const image =
              mainService?.image ?? projects[index % projects.length]?.image;

            return (
              <GlassPanel
                key={group.id}
                className="overflow-hidden rounded-[2rem]"
              >
                <Link href={group.href} className="group block">
                  <div className="relative h-72 overflow-hidden">
                    <CinematicImage
                      frames={[
                        image,
                        projects[(index + 1) % projects.length]?.image,
                        projects[(index + 3) % projects.length]?.image,
                      ]}
                      alt={group.title}
                      fill
                      hint="motion"
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-[#050505]/68 via-[#050505]/12 to-[#D69A66]/10" />
                    <div className="absolute bottom-5 left-5 right-5">
                      <p className="mb-2 text-xs uppercase tracking-[0.28em] text-[#D69A66]">
                        {text(
                          "servicePages.categoryLabel",
                          "Основная категория",
                        )}
                      </p>
                      <h3 className="text-3xl font-light tracking-normal [overflow-wrap:anywhere]">
                        {group.title}
                      </h3>
                    </div>
                  </div>
                </Link>
                <div className="grid gap-5 p-6">
                  <p className="leading-relaxed text-[#D6D1CA]">
                    {group.description}
                  </p>
                  <div className="grid gap-2">
                    {group.items.map((child) => (
                      <Link
                        key={child.href}
                        href={child.href}
                        className="group flex items-center justify-between gap-4 rounded-2xl border border-white/10 bg-white/[0.035] px-4 py-3 text-sm text-white/62 transition hover:border-[#D69A66]/45 hover:text-white"
                      >
                        <span className="min-w-0 [overflow-wrap:anywhere]">
                          {child.label}
                        </span>
                        <span className="text-[#D69A66] transition group-hover:translate-x-1">
                          →
                        </span>
                      </Link>
                    ))}
                  </div>
                </div>
              </GlassPanel>
            );
          })}
        </div>
      </div>
    </section>
  );
}

export function Workflow() {
  const text = useCmsText();
  const steps = [
    text("workflow.steps.brief", "Бриф"),
    text("workflow.steps.concept", "Концепция"),
    text("workflow.steps.visualization", "Визуализация"),
    text("workflow.steps.drawings", "Чертежи"),
    text("workflow.steps.procurement", "Комплектация"),
    text("workflow.steps.support", "Сопровождение"),
  ];

  return (
    <section className="px-5 py-28 md:px-10 lg:px-16">
      <div className="mx-auto max-w-7xl">
        <SectionLabel>{text("workflow.label", "Этапы работы")}</SectionLabel>
        <h2 className="mb-14 max-w-4xl text-5xl font-light tracking-[-0.055em] md:text-7xl">
          {text(
            "workflow.title",
            "Этапы работы без хаоса и лишней коммуникации",
          )}
        </h2>
        <div className="grid gap-px overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.03] md:grid-cols-3 lg:grid-cols-6">
          {steps.map((step, index) => (
            <GlassPanel
              key={step}
              className="p-7 lg:px-6 xl:p-7"
              style={{
                background: "rgba(255, 255, 255, 0.03)",
                boxShadow: "none",
              }}
            >
              <span className="mb-14 block text-sm text-[#D69A66]">
                0{index + 1}
              </span>
              <h3 className="pr-2 text-[clamp(1rem,1.12vw,1.25rem)] font-light leading-tight [overflow-wrap:anywhere] md:text-2xl lg:text-[clamp(1rem,1.12vw,1.25rem)]">
                {step}
              </h3>
            </GlassPanel>
          ))}
        </div>
      </div>
    </section>
  );
}

function ServicesPage() {
  return (
    <div className="page-in">
      <ServicesHero />
      <ServicesSummary />
      <ServicePages />
    </div>
  );
}

export default ServicesPage;
