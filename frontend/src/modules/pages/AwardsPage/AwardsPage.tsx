"use client";

import AwardsSection from "@/src/components/common/AwardsSection";
import HeroBackdropSlider from "@/src/components/common/HeroBackdropSlider";
import { useCms, useCmsText } from "@/src/cms";
import SectionLabel from "@/src/components/common/SectionLabel";

export default function AwardsPage() {
  const { projects } = useCms();
  const text = useCmsText();

  return (
    <div className="page-in">
      <section className="relative min-h-[72vh] overflow-hidden px-5 pb-16 pt-32 md:px-10 lg:px-16">
        <HeroBackdropSlider
          slides={[
            { image: projects[0]?.image, alt: text("awardsPage.hero.alt1", "Проект 3D Smart Design Studio") },
            { image: projects[1]?.image, alt: text("awardsPage.hero.alt2", "Интерьерный проект") },
            { image: projects[4]?.image, alt: text("awardsPage.hero.alt3", "Архитектурная визуализация") },
          ]}
          controlsClassName="hidden"
        />
        <div className="absolute inset-0 bg-[#050505]/72" />
        <div className="absolute inset-0 bg-[linear-gradient(90deg,#050505,rgba(5,5,5,.82),rgba(5,5,5,.42))]" />
        <div className="relative z-10 mx-auto flex min-h-[calc(72vh-8rem)] max-w-7xl items-end">
          <div>
            <SectionLabel>{text("awardsPage.hero.label", "Награды и дипломы")}</SectionLabel>
            <h1 className="mt-5 max-w-5xl text-[clamp(3rem,6.4vw,6.4rem)] font-light leading-[0.94] tracking-[-0.045em]">
              {text("awardsPage.hero.title", "Документы, которые подтверждают доверие")}
            </h1>
            <p className="mt-7 max-w-3xl text-lg leading-relaxed text-[#D6D1CA] md:text-xl">
              {text("awardsPage.hero.text", "Дипломы, сертификаты, благодарственные письма и профессиональные подтверждения студии собраны в одном разделе.")}
            </p>
          </div>
        </div>
      </section>
      <AwardsSection compact showLink={false} />
    </div>
  );
}
