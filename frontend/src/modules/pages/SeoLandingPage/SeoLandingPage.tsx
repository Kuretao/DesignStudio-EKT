import Link from "next/link";
import type { CSSProperties } from "react";
import {
  getServiceLandingCopy,
  projects,
  seoLandingPageItems,
} from "@/src/data";
import VirtualTourDemo from "@/src/components/common/VirtualTourDemo";
import { imageFrames, projectImageAt } from "@/src/utils/images";

type SeoLandingItem = (typeof seoLandingPageItems)[number];

type LandingVariant = {
  accent: string;
  accentSoft: string;
  surface: string;
  surfaceAlt: string;
  text: string;
  muted: string;
  hero: "atelier" | "render" | "garden" | "tour" | "blueprint";
  label: string;
  promise: string;
  result: string;
  audience: string;
  images: string[];
};

const fallbackVariant: LandingVariant = {
  accent: "#D69A66",
  accentSoft: "rgba(214,154,102,0.16)",
  surface: "#050505",
  surfaceAlt: "#101010",
  text: "#F5F2EC",
  muted: "#D6D1CA",
  hero: "atelier",
  label: "3D Smart Design Studio",
  promise: "Проектируем пространство до понятного результата",
  result: "готовый комплект для обсуждения, реализации и закупок",
  audience: "для частных клиентов и бизнеса",
  images: [projectImageAt(projects, 0), projectImageAt(projects, 1), projectImageAt(projects, 2)],
};

const variants: Record<string, LandingVariant> = {
  "dizajn-proekt-kvartiry": {
    accent: "#D69A66",
    accentSoft: "rgba(214,154,102,0.17)",
    surface: "#090807",
    surfaceAlt: "#15100D",
    text: "#F8F2EA",
    muted: "#D9CEC2",
    hero: "atelier",
    label: "Квартира / проект ремонта",
    promise: "Квартира собирается в цельный сценарий до начала ремонта",
    result: "планировка, визуализация, чертежи и ведомости",
    audience: "новостройки, вторичное жилье, квартиры под сдачу",
    images: [
      projectImageAt(projects, 1),
      projectImageAt(projects, 0),
      projectImageAt(projects, 1, "afterImage"),
    ],
  },
  "3d-vizualizaciya-interera-i-arhitektury": {
    accent: "#7DD3FC",
    accentSoft: "rgba(125,211,252,0.15)",
    surface: "#05080A",
    surfaceAlt: "#0B1216",
    text: "#EEF8FC",
    muted: "#C2D6DF",
    hero: "render",
    label: "Render / CGI",
    promise: "Показываем объект так, чтобы его можно было согласовать или продать",
    result: "модель, материалы, свет, ракурсы и финальные рендеры",
    audience: "интерьеры, фасады, мебель, девелопмент",
    images: [projectImageAt(projects, 4), projectImageAt(projects, 5), projectImageAt(projects, 0)],
  },
  "landshaftnyj-dizajn-uchastka": {
    accent: "#A7C957",
    accentSoft: "rgba(167,201,87,0.15)",
    surface: "#070A07",
    surfaceAlt: "#10150D",
    text: "#F3F7EA",
    muted: "#D1DBC4",
    hero: "garden",
    label: "Участок / сад / благоустройство",
    promise: "Участок становится системой маршрутов, света, растений и отдыха",
    result: "генплан, дендроплан, покрытия, освещение и 3D",
    audience: "частные дома, дачи, коттеджи, загородные участки",
    images: [projectImageAt(projects, 2), projectImageAt(projects, 4), projectImageAt(projects, 5)],
  },
  "virtualnyj-3d-tur-dlya-biznesa": {
    accent: "#C084FC",
    accentSoft: "rgba(192,132,252,0.16)",
    surface: "#080711",
    surfaceAlt: "#12101D",
    text: "#F4F0FF",
    muted: "#D4C8E8",
    hero: "tour",
    label: "360 / virtual tour",
    promise: "Клиент изучает пространство до визита и быстрее принимает решение",
    result: "панорамы, переходы, точки интереса и ссылка для сайта",
    audience: "отели, рестораны, шоурумы, квартиры, офисы",
    images: [projectImageAt(projects, 3), projectImageAt(projects, 1), projectImageAt(projects, 0)],
  },
  "arhitekturnoe-proektirovanie-domov": {
    accent: "#E6C27A",
    accentSoft: "rgba(230,194,122,0.14)",
    surface: "#08090A",
    surfaceAlt: "#111315",
    text: "#F7F4ED",
    muted: "#D7D2C8",
    hero: "blueprint",
    label: "Дом / архитектура / эскиз",
    promise: "Будущий дом получает форму, посадку и понятную проектную базу",
    result: "эскиз, планы, фасады, разрезы и 3D-образ",
    audience: "частные дома, коттеджи, загородные участки",
    images: [projectImageAt(projects, 5), projectImageAt(projects, 4), projectImageAt(projects, 2)],
  },
};

const mainLinks = [
  { href: "/", label: "Основной сайт" },
  { href: "/portfolio", label: "Портфолио" },
  { href: "/services", label: "Услуги" },
  { href: "/kontakty", label: "Контакты" },
];

function getVariant(item: SeoLandingItem) {
  return variants[item.id] ?? fallbackVariant;
}

function getStyle(variant: LandingVariant) {
  return {
    "--landing-accent": variant.accent,
    "--landing-accent-soft": variant.accentSoft,
    "--landing-surface": variant.surface,
    "--landing-surface-alt": variant.surfaceAlt,
    "--landing-text": variant.text,
    "--landing-muted": variant.muted,
  } as CSSProperties;
}

function HeroVisual({
  item,
  variant,
}: {
  item: SeoLandingItem;
  variant: LandingVariant;
}) {
  if (variant.hero === "render") {
    return (
      <div className="relative min-h-[560px] overflow-hidden rounded-[0.75rem] border border-white/12 bg-[var(--landing-surface-alt)] p-4 shadow-[0_34px_120px_rgba(0,0,0,0.42)]">
        <div className="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,.07)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,.06)_1px,transparent_1px)] bg-[length:34px_34px]" />
        <img
          src={variant.images[0]}
          alt={item.title}
          className="relative h-[380px] w-full rounded-[0.55rem] object-cover"
        />
        <div className="relative mt-4 grid gap-3 sm:grid-cols-3">
          {["Model", "Light", "Final"].map((step, index) => (
            <div
              key={step}
              className="border border-white/10 bg-black/28 p-4 backdrop-blur"
            >
              <span className="text-[10px] uppercase tracking-[0.22em] text-[var(--landing-accent)]">
                0{index + 1}
              </span>
              <p className="mt-8 text-sm text-white/78">{step}</p>
            </div>
          ))}
        </div>
      </div>
    );
  }

  if (variant.hero === "garden") {
    return (
      <div className="relative min-h-[560px] overflow-hidden rounded-t-[4rem] border border-white/12 bg-[var(--landing-surface-alt)]">
        <img
          src={variant.images[0]}
          alt={item.title}
          className="h-[560px] w-full object-cover"
        />
        <div className="absolute inset-0 bg-[linear-gradient(0deg,rgba(7,10,7,.92),rgba(7,10,7,.18)_58%,rgba(167,201,87,.08))]" />
        <div className="absolute bottom-6 left-6 right-6 grid gap-3 sm:grid-cols-3">
          {["генплан", "растения", "свет"].map((entry) => (
            <div
              key={entry}
              className="rounded-full border border-white/14 bg-black/24 px-4 py-3 text-center text-xs uppercase tracking-[0.18em] text-white/82 backdrop-blur"
            >
              {entry}
            </div>
          ))}
        </div>
      </div>
    );
  }

  if (variant.hero === "tour") {
    return (
      <div className="relative grid min-h-[560px] gap-4 rounded-[2rem] border border-white/12 bg-[var(--landing-surface-alt)] p-4 shadow-[0_34px_120px_rgba(0,0,0,0.42)] md:grid-cols-[1fr_0.42fr]">
        <div className="relative overflow-hidden rounded-[1.35rem]">
          <img
            src={variant.images[0]}
            alt={item.title}
            className="h-full min-h-[390px] w-full object-cover"
          />
          <div className="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,transparent_0_18%,rgba(8,7,17,.22)_32%,rgba(8,7,17,.82)_100%)]" />
          <div className="absolute left-1/2 top-1/2 h-24 w-24 -translate-x-1/2 -translate-y-1/2 rounded-full border border-[var(--landing-accent)] bg-black/28 backdrop-blur" />
          <div className="absolute left-1/2 top-1/2 h-3 w-3 -translate-x-1/2 -translate-y-1/2 rounded-full bg-[var(--landing-accent)]" />
        </div>
        <div className="grid gap-4">
          {variant.images.slice(1).map((image, index) => (
            <div key={image} className="overflow-hidden rounded-[1.2rem]">
              <img
                src={image}
                alt={`${item.title} ${index + 1}`}
                className="h-44 w-full object-cover"
              />
            </div>
          ))}
          <div className="rounded-[1.2rem] border border-white/10 bg-black/24 p-5">
            <span className="text-xs uppercase tracking-[0.22em] text-[var(--landing-accent)]">
              360 link
            </span>
            <p className="mt-8 text-2xl font-light">готово для сайта</p>
          </div>
        </div>
      </div>
    );
  }

  if (variant.hero === "blueprint") {
    return (
      <div className="relative min-h-[560px] overflow-hidden border border-white/12 bg-[var(--landing-surface-alt)] p-6 shadow-[0_34px_120px_rgba(0,0,0,0.42)]">
        <div className="absolute inset-0 bg-[linear-gradient(rgba(230,194,122,.08)_1px,transparent_1px),linear-gradient(90deg,rgba(230,194,122,.08)_1px,transparent_1px)] bg-[length:42px_42px]" />
        <img
          src={variant.images[0]}
          alt={item.title}
          className="relative ml-auto h-[330px] w-[82%] object-cover opacity-88 grayscale"
        />
        <div className="relative -mt-12 grid max-w-lg gap-3">
          {["планы", "фасады", "3D-образ", "документация"].map((entry) => (
            <div
              key={entry}
              className="flex items-center justify-between border-b border-[var(--landing-accent)]/30 py-3 text-sm uppercase tracking-[0.18em]"
            >
              <span>{entry}</span>
              <span className="text-[var(--landing-accent)]">готово</span>
            </div>
          ))}
        </div>
      </div>
    );
  }

  return (
    <div className="relative min-h-[560px]">
      <div className="absolute right-0 top-0 h-[74%] w-[74%] overflow-hidden rounded-[0.75rem] border border-white/12">
        <img
          src={variant.images[0]}
          alt={item.title}
          className="h-full w-full object-cover"
        />
      </div>
      <div className="absolute bottom-8 left-0 h-[48%] w-[58%] overflow-hidden rounded-[0.75rem] border border-white/12 shadow-[0_28px_90px_rgba(0,0,0,0.42)]">
        <img
          src={variant.images[1]}
          alt={`${item.title}: пример`}
          className="h-full w-full object-cover"
        />
      </div>
      <div className="absolute bottom-0 right-8 w-64 border border-white/12 bg-[var(--landing-surface-alt)] p-5 shadow-[0_24px_70px_rgba(0,0,0,0.34)]">
        <span className="text-xs uppercase tracking-[0.24em] text-[var(--landing-accent)]">
          Moodboard
        </span>
        <div className="mt-8 grid grid-cols-3 gap-2">
          {[variant.accent, "#F5F2EC", "#2F4F4F"].map((color) => (
            <span
              key={color}
              className="h-12 border border-white/10"
              style={{ backgroundColor: color }}
            />
          ))}
        </div>
      </div>
    </div>
  );
}

function SeoLandingPage({ item }: { item: SeoLandingItem }) {
  const copy = getServiceLandingCopy(item);
  const variant = getVariant(item);
  const hasVirtualTourDemo = item.id === "virtualnyj-3d-tur-dlya-biznesa";
  const metrics = [
    ["Стоимость", item.price],
    ["Срок", item.timeline],
    ["Кому", variant.audience],
  ];

  return (
    <div
      className="page-in min-h-screen bg-[var(--landing-surface)] text-[var(--landing-text)]"
      style={getStyle(variant)}
    >
      <header className="absolute left-0 right-0 top-0 z-20 px-5 py-5 md:px-10 lg:px-16">
        <div className="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 border-b border-white/10 pb-5">
          <Link
            href="/"
            className="text-xs font-semibold uppercase tracking-[0.28em] text-white/86 transition hover:text-[var(--landing-accent)]"
          >
            3D Smart Design Studio
          </Link>
          <nav className="flex flex-wrap gap-x-4 gap-y-2 text-[11px] uppercase tracking-[0.18em] text-white/54">
            {mainLinks.map((link) => (
              <Link
                key={link.href}
                href={link.href}
                className="transition hover:text-white"
              >
                {link.label}
              </Link>
            ))}
          </nav>
        </div>
      </header>

      <section className="relative overflow-hidden px-5 pb-20 pt-32 md:px-10 lg:px-16">
        <div className="absolute inset-0 bg-[radial-gradient(circle_at_18%_10%,var(--landing-accent-soft),transparent_30%),linear-gradient(180deg,transparent,#050505_94%)]" />
        <div className="relative z-10 mx-auto grid min-h-[calc(100vh-8rem)] max-w-7xl gap-12 lg:grid-cols-[0.95fr_1.05fr] lg:items-center">
          <div>
            <p className="text-xs uppercase tracking-[0.4em] text-[var(--landing-accent)]">
              {variant.label}
            </p>
            <h1 className="mt-5 max-w-5xl text-[clamp(2.55rem,5.4vw,6rem)] font-light leading-[0.94] tracking-[-0.045em]">
              {copy.offerTitle}
            </h1>
            <p className="mt-7 max-w-2xl text-lg leading-relaxed text-[var(--landing-muted)] md:text-xl">
              {variant.promise}. {item.text}
            </p>

            <div className="mt-8 grid gap-2 sm:grid-cols-3">
              {metrics.map(([label, value]) => (
                <div
                  key={label}
                  className="border border-white/10 bg-white/[0.035] p-4"
                >
                  <span className="text-[10px] uppercase tracking-[0.22em] text-white/36">
                    {label}
                  </span>
                  <strong className="mt-3 block text-lg font-light leading-snug text-white">
                    {value}
                  </strong>
                </div>
              ))}
            </div>

            <div className="mt-9 flex flex-wrap gap-3">
              <Link
                href="/kontakty"
                className="rounded-full bg-[var(--landing-accent)] px-6 py-4 text-xs uppercase tracking-[0.22em] text-[#050505] transition hover:bg-white"
              >
                Обсудить проект
              </Link>
              <Link
                href="/"
                className="rounded-full border border-white/18 bg-black/18 px-6 py-4 text-xs uppercase tracking-[0.22em] text-white/78 backdrop-blur transition hover:border-[var(--landing-accent)] hover:text-white"
              >
                На основной сайт
              </Link>
            </div>
          </div>

          <HeroVisual item={item} variant={variant} />
        </div>
      </section>

      {hasVirtualTourDemo && <VirtualTourDemo />}

      <section className="border-t border-white/10 px-5 py-20 md:px-10 lg:px-16">
        <div className="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.58fr_1fr]">
          <div>
            <p className="text-xs uppercase tracking-[0.38em] text-[var(--landing-accent)]">
              Состав работ
            </p>
            <h2 className="mt-4 text-4xl font-light tracking-[-0.045em] md:text-6xl">
              Что вы получаете в результате
            </h2>
            <p className="mt-6 max-w-xl leading-relaxed text-[var(--landing-muted)]">
              {variant.result}. Состав уточняется после брифа, площади и
              исходных материалов.
            </p>
          </div>
          <div className="grid gap-3 md:grid-cols-2">
            {item.deliverables.map((entry, index) => (
              <div
                key={entry}
                className="border border-white/10 bg-[var(--landing-surface-alt)] p-5"
              >
                <span className="text-sm text-[var(--landing-accent)]">
                  0{index + 1}
                </span>
                <p className="mt-5 text-lg leading-snug text-white/86">
                  {entry}
                </p>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="border-t border-white/10 px-5 py-20 md:px-10 lg:px-16">
        <div className="mx-auto max-w-7xl">
          <div className="mb-10 grid gap-6 md:grid-cols-[1fr_0.7fr] md:items-end">
            <h2 className="text-4xl font-light tracking-[-0.045em] md:text-6xl">
              Почему с проектом проще принимать решения
            </h2>
            <p className="leading-relaxed text-[var(--landing-muted)]">
              До старта реализации видно объем, настроение, технические решения
              и точки, где обычно появляются лишние расходы.
            </p>
          </div>
          <div className="grid gap-3 md:grid-cols-4">
            {item.benefits.map((benefit, index) => (
              <div
                key={benefit}
                className={`min-h-52 border border-white/10 p-6 ${
                  index % 2 === 0
                    ? "bg-white/[0.035]"
                    : "bg-[var(--landing-accent-soft)]"
                }`}
              >
                <span className="text-sm text-[var(--landing-accent)]">
                  0{index + 1}
                </span>
                <p className="mt-16 text-xl font-light leading-snug">
                  {benefit}
                </p>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="border-t border-white/10 px-5 py-20 md:px-10 lg:px-16">
        <div className="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.44fr_1fr]">
          <div>
            <p className="text-xs uppercase tracking-[0.38em] text-[var(--landing-accent)]">
              Процесс
            </p>
            <h2 className="mt-4 text-4xl font-light tracking-[-0.045em] md:text-6xl">
              От брифа до готового комплекта
            </h2>
          </div>
          <div className="grid gap-4">
            {item.process.map((step, index) => (
              <div
                key={step}
                className="grid gap-4 border-b border-white/10 pb-5 md:grid-cols-[80px_1fr_auto] md:items-center"
              >
                <span className="text-[var(--landing-accent)]">
                  0{index + 1}
                </span>
                <h3 className="text-2xl font-light">{step}</h3>
                <span className="hidden h-px w-28 bg-[var(--landing-accent)]/45 md:block" />
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="px-5 py-20 md:px-10 lg:px-16">
        <div className="mx-auto grid max-w-7xl gap-8 border border-[var(--landing-accent)]/35 bg-[var(--landing-accent-soft)] p-7 md:p-10 lg:grid-cols-[1fr_0.42fr] lg:items-end">
          <div>
            <p className="text-xs uppercase tracking-[0.38em] text-[var(--landing-accent)]">
              Заявка
            </p>
            <h2 className="mt-4 text-4xl font-light tracking-[-0.045em] md:text-6xl">
              Расскажите о задаче, а мы предложим состав и стоимость
            </h2>
          </div>
          <div className="flex flex-col gap-3">
            <Link
              href="/kontakty"
              className="rounded-full bg-[var(--landing-accent)] px-6 py-4 text-center text-xs uppercase tracking-[0.22em] text-[#050505] transition hover:bg-white"
            >
              Оставить заявку
            </Link>
            <Link
              href="/portfolio"
              className="rounded-full border border-white/18 px-6 py-4 text-center text-xs uppercase tracking-[0.22em] text-white/76 transition hover:border-white/42 hover:text-white"
            >
              Посмотреть работы
            </Link>
          </div>
        </div>
      </section>
    </div>
  );
}

export default SeoLandingPage;
