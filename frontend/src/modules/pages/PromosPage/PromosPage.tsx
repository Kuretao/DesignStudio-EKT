"use client";

import { useState } from "react";
import { useCms, useCmsText } from "@/src/cms";
import CinematicImage from "@/src/components/common/CinematicImage";
import HeroBackdropSlider from "@/src/components/common/HeroBackdropSlider";
import ContactModal from "@/src/modals/ContactModal";

const badgeColors: Record<string, string> = {
  Интерьер: "border-[#D69A66]/40 text-[#D69A66]",
  Визуализация: "border-[#D69A66]/40 text-[#D69A66]",
  Ландшафт: "border-[#D69A66]/40 text-[#D69A66]",
};

const highlightColors: Record<string, string> = {
  Интерьер: "text-[#D69A66]",
  Визуализация: "text-[#D69A66]",
  Ландшафт: "text-[#D69A66]",
};

const glowColors: Record<string, string> = {
  Интерьер: "hover:shadow-[0_32px_100px_rgba(214,154,102,0.12)]",
  Визуализация: "hover:shadow-[0_32px_100px_rgba(214,154,102,0.12)]",
  Ландшафт: "hover:shadow-[0_32px_100px_rgba(214,154,102,0.12)]",
};

export default function PromosPage() {
  const { projects, promos } = useCms();
  const text = useCmsText();
  const [modalOpen, setModalOpen] = useState(false);
  const [activePromo, setActivePromo] = useState<string | undefined>();

  const openModal = (title?: string) => {
    setActivePromo(title);
    setModalOpen(true);
  };

  return (
    <>
      <div className="page-in">
        {/* Hero */}
        <section className="relative min-h-[72vh] overflow-hidden px-5 pb-24 pt-20 md:px-10 lg:px-16">
          <HeroBackdropSlider
            slides={[
              { image: promos[0]?.image, alt: promos[0]?.title },
              { image: promos[1]?.image, alt: promos[1]?.title },
              { image: projects[2].image, alt: text("promos.hero.fallbackAlt", "Ландшафтный проект") },
            ]}
            controlsClassName="bottom-6"
          />
          <div className="absolute inset-0 bg-[linear-gradient(90deg,rgba(5,5,5,.94)_0%,rgba(5,5,5,.72)_52%,rgba(5,5,5,.26)_100%)]" />
          <div className="absolute inset-0 bg-[linear-gradient(0deg,#050505_0%,rgba(5,5,5,.42)_32%,transparent_74%)]" />
          <div className="relative z-10 mx-auto max-w-7xl">
            <p className="mb-5 text-xs uppercase tracking-[0.45em] text-[#D69A66]">{text("promos.hero.label", "Специальные условия")}</p>
            <div className="grid gap-8 md:grid-cols-[1fr_0.55fr] md:items-end">
              <h1 className="text-[clamp(3rem,6.2vw,6rem)] font-light leading-[0.96] tracking-[-0.045em]">
                {text("promos.hero.title", "Акции и скидки")}
              </h1>
              <div>
                <p className="text-lg leading-relaxed text-[#D6D1CA]">
                  {text("promos.hero.text", "Пакетные условия для комплексных проектов. Стоимость фиксируется после брифа — ниже стартовая структура.")}
                </p>
                <div className="mt-5 inline-flex items-center gap-2 rounded-full border border-white/15 px-4 py-2 text-xs text-white/50">
                  <span className="h-1.5 w-1.5 animate-pulse rounded-full bg-[#D69A66]" />
                  {text("promos.hero.validUntil", "Акции действуют до 31 января 2026")}
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* Promo cards */}
        <section className="px-5 py-12 md:px-10 lg:px-16">
          <div className="mx-auto max-w-7xl space-y-5">
            {promos.map((promo, idx) => (
              <div
                key={promo.id}
                className={`group relative overflow-hidden rounded-[2.5rem] border border-white/10 bg-white/[0.025] transition duration-500 hover:-translate-y-1 hover:border-white/20 ${glowColors[promo.badge]}`}
              >
                <div className="grid lg:grid-cols-[1fr_420px]">
                  {/* Content */}
                  <div className="relative z-10 flex flex-col justify-between p-8 md:p-12">
                    <div>
                      {/* Meta row */}
                      <div className="mb-6 flex flex-wrap items-center gap-3">
                        <span className={`rounded-full border px-3 py-1 text-[10px] uppercase tracking-[0.28em] ${badgeColors[promo.badge]}`}>
                          {promo.badge}
                        </span>
                        <span className="text-xs text-white/35">{text("promos.validUntilPrefix", "до {date}").replace("{date}", promo.validUntil)}</span>
                        <span className="text-xs text-white/25">· #{String(idx + 1).padStart(2, "0")}</span>
                      </div>

                      {/* Highlight price */}
                      <p className={`mb-3 text-4xl font-light tracking-[-0.04em] md:text-5xl ${highlightColors[promo.badge]}`}>
                        {promo.highlight}
                      </p>

                      {/* Title */}
                      <h2 className="max-w-xl text-3xl font-light leading-tight tracking-[-0.04em] text-[#F5F2EC] md:text-4xl">
                        {promo.title}
                      </h2>

                      {/* Description */}
                      <p className="mt-5 max-w-lg leading-relaxed text-[#D6D1CA]">
                        {promo.description}
                      </p>

                      {/* Conditions */}
                      <ul className="mt-7 space-y-2.5">
                        {promo.conditions.map((cond, i) => (
                          <li key={i} className="flex items-start gap-3 text-sm text-white/50">
                            <span className={`mt-1.5 h-1 w-1 shrink-0 rounded-full ${highlightColors[promo.badge]}`} />
                            {cond}
                          </li>
                        ))}
                      </ul>
                    </div>

                    {/* CTA */}
                    <div className="mt-10">
                      <button
                        onClick={() => openModal(promo.title)}
                        className={`group/btn inline-flex items-center gap-3 rounded-full border px-7 py-4 text-xs uppercase tracking-[0.24em] transition duration-300 ${
                          promo.badge === "Визуализация"
                            ? "border-[#D69A66]/50 text-[#D69A66] hover:bg-[#D69A66] hover:text-[#050505]"
                            : promo.badge === "Ландшафт"
                            ? "border-[#D69A66]/50 text-[#D69A66] hover:bg-[#D69A66] hover:text-[#050505]"
                            : "border-[#D69A66]/50 text-[#D69A66] hover:bg-[#D69A66] hover:text-[#050505]"
                        }`}
                      >
                        {text("promos.card.button", "Записаться на акцию")}
                        <span className="transition duration-300 group-hover/btn:translate-x-1">→</span>
                      </button>
                    </div>
                  </div>

                  {/* Image */}
                  <div className="relative hidden min-h-[400px] overflow-hidden lg:block">
                    <CinematicImage
                      frames={[
                        promo.image,
                        projects[(idx + 1) % projects.length]?.image,
                        projects[(idx + 3) % projects.length]?.image,
                      ]}
                      alt={promo.title}
                      fill
                      hint="motion"
                    />
                    <div className="absolute inset-0 bg-gradient-to-r from-[#050505] via-[#050505]/20 to-transparent" />
                    <div className="absolute inset-0 bg-gradient-to-t from-[#050505]/50 to-transparent" />
                  </div>
                </div>
              </div>
            ))}
          </div>
        </section>

        {/* Bottom CTA */}
        <section className="px-5 py-20 md:px-10 lg:px-16">
          <div className="mx-auto max-w-7xl">
            <div className="relative overflow-hidden rounded-[2.5rem] border border-white/10 bg-white/[0.025] px-8 py-14 text-center md:px-16">
              <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_50%_0%,rgba(214,154,102,0.1),transparent_60%)]" />
              <p className="relative mb-4 text-xs uppercase tracking-[0.45em] text-[#D69A66]">{text("promos.cta.label", "Нужна консультация?")}</p>
              <h2 className="relative mx-auto max-w-2xl text-4xl font-light tracking-[-0.045em] md:text-6xl">
                {text("promos.cta.title", "Расскажем подробнее о каждой акции")}
              </h2>
              <p className="relative mx-auto mt-5 max-w-lg text-lg leading-relaxed text-[#D6D1CA]">
                {text("promos.cta.text", "Свяжитесь с нами удобным способом — ответим в течение рабочего дня и подберём оптимальный пакет.")}
              </p>
              <button
                onClick={() => openModal()}
                className="relative mt-10 inline-flex items-center gap-3 rounded-full bg-[#D69A66] px-8 py-4 text-sm uppercase tracking-[0.24em] text-[#050505] transition duration-300 hover:bg-[#F5F2EC] hover:shadow-[0_0_48px_rgba(214,154,102,0.35)]"
              >
                {text("promos.cta.button", "Связаться с нами")}
                <span>→</span>
              </button>
            </div>
          </div>
        </section>
      </div>

      <ContactModal open={modalOpen} onClose={() => setModalOpen(false)} promoTitle={activePromo} />
    </>
  );
}
