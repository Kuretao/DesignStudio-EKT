"use client";

import Link from "next/link";
import { useCms, useCmsText } from "@/src/cms";
import CinematicImage from "@/src/components/common/CinematicImage";
import { GlassPanel } from "@/src/ui";
import SectionLabel from "@/src/components/common/SectionLabel";
import { imageFrames, projectImageAt } from "@/src/utils/images";

function AboutPage() {
  const { projects } = useCms();
  const text = useCmsText();
  const studioFacts = [
    { value: text("about.fact1.value", "10 лет"), label: text("about.fact1.label", "практики в интерьерах, архитектуре и 3D") },
    { value: text("about.fact2.value", "25+"), label: text("about.fact2.label", "специалистов под разные масштабы задач") },
    { value: text("about.fact3.value", "90%"), label: text("about.fact3.label", "клиентов возвращаются с новыми проектами") },
  ];
  const studioPrinciples = [
    text("about.principle1", "начинаем с сценария жизни, а не с декора"),
    text("about.principle2", "проверяем красоту чертежами, светом и бюджетом"),
    text("about.principle3", "ведем проект до реализации, комплектации и замен"),
  ];

  return (
    <section id="o-nas" className="relative overflow-hidden border-t border-white/10 px-5 py-28 md:px-10 lg:px-16">
      <div className="pointer-events-none absolute right-0 top-20 h-[520px] w-[520px] rounded-full bg-[#D69A66]/[0.06] blur-3xl" />
      <div className="mx-auto grid max-w-7xl gap-8 lg:grid-cols-[0.95fr_1.05fr] lg:items-center">
        <div>
          <SectionLabel>{text("about.label", "О нас")}</SectionLabel>
          <h2 className="max-w-4xl text-[clamp(2.6rem,5vw,4.8rem)] font-light leading-[0.98] tracking-normal md:tracking-[-0.035em]">
            {text("about.title", "Студия, которая собирает проект как систему")}
          </h2>
          <p className="mt-7 max-w-2xl text-lg leading-relaxed text-[#D6D1CA]">
            {text(
              "about.text",
              "3D Smart Design Studio соединяет интерьер, архитектуру, визуализацию, ландшафт и комплектацию в один спокойный процесс. Клиент видит не набор картинок, а понятный маршрут от идеи до реализации.",
            )}
          </p>

          <div className="mt-10 grid gap-3 sm:grid-cols-3">
            {studioFacts.map((fact) => (
              <GlassPanel key={fact.value} className="rounded-[1.25rem] p-5">
                <strong className="block text-3xl font-light text-[#D69A66]">{fact.value}</strong>
                <span className="mt-3 block text-sm leading-snug text-[#D6D1CA]">{fact.label}</span>
              </GlassPanel>
            ))}
          </div>

          <div className="mt-10 flex flex-wrap gap-3">
            <Link
              href="/o-nas"
              className="rounded-full bg-[#D69A66] px-6 py-4 text-xs uppercase tracking-[0.24em] text-[#050505] transition hover:bg-[#F5F2EC]"
            >
              {text("about.moreButton", "Подробнее о студии")}
            </Link>
            <Link
              href="/kontakty"
              className="rounded-full border border-white/15 px-6 py-4 text-xs uppercase tracking-[0.24em] text-[#D6D1CA] transition hover:border-[#D69A66] hover:text-white"
            >
              {text("about.contactButton", "Обсудить проект")}
            </Link>
          </div>
        </div>

        <div className="grid gap-5 md:grid-cols-[0.86fr_1.14fr]">
          <div className="grid gap-5">
            {studioPrinciples.map((item, index) => (
              <GlassPanel key={item} className="rounded-[1.5rem] p-6 transition duration-500 hover:-translate-y-1 hover:border-[#D69A66]/50">
                <span className="text-sm text-[#D69A66]">0{index + 1}</span>
                <p className="mt-8 text-lg leading-relaxed text-[#F5F2EC]">{item}</p>
              </GlassPanel>
            ))}
          </div>

          <div className="group relative min-h-[620px] overflow-hidden rounded-[2.5rem] border border-white/10">
            <CinematicImage
              frames={[projectImageAt(projects, 0), projectImageAt(projects, 1), projectImageAt(projects, 4)]}
              alt={text("about.imageAlt", "Интерьерный проект 3D Smart Design Studio")}
              fill
              hint="studio"
            />
            <div className="absolute inset-0 bg-gradient-to-t from-[#050505]/90 via-[#050505]/20 to-transparent" />
            <div className="absolute bottom-6 left-6 right-6">
              <p className="mb-3 text-xs uppercase tracking-[0.32em] text-[#D69A66]">{text("about.imageEyebrow", "Дизайн с логикой")}</p>
              <h3 className="max-w-md text-3xl font-light tracking-[-0.04em]">
                {text("about.imageTitle", "Красивое решение должно выдерживать стройку, бюджет и повседневную жизнь.")}
              </h3>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

export default AboutPage;
