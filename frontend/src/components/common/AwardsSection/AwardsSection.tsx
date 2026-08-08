"use client";

import { useEffect, useMemo, useRef, useState } from "react";
import Link from "next/link";
import gsap from "gsap";
import { useCms, useCmsText } from "@/src/cms";
import SectionLabel from "@/src/components/common/SectionLabel";

type AwardsSectionProps = {
  compact?: boolean;
  showLink?: boolean;
};

function AwardDocument({ title, image }: { title: string; image?: string }) {
  if (image) {
    return (
      <img
        src={image}
        alt={title}
        loading="lazy"
        decoding="async"
        className="absolute inset-0 h-full w-full object-cover"
      />
    );
  }

  return (
    <div className="absolute inset-0 bg-[#F5F2EC] p-5 text-[#171511]">
      <div className="h-full border border-[#D69A66]/45 p-4">
        <div className="mx-auto mb-6 h-12 w-12 rounded-full border border-[#D69A66]/45" />
        <div className="mx-auto h-2 w-28 rounded-full bg-[#D69A66]/45" />
        <div className="mx-auto mt-3 h-1.5 w-36 rounded-full bg-[#171511]/16" />
        <div className="mt-10 space-y-2">
          <div className="h-1.5 rounded-full bg-[#171511]/14" />
          <div className="h-1.5 rounded-full bg-[#171511]/10" />
          <div className="h-1.5 w-3/4 rounded-full bg-[#171511]/10" />
        </div>
      </div>
    </div>
  );
}

export default function AwardsSection({
  compact = false,
  showLink = true,
}: AwardsSectionProps) {
  const { awards } = useCms();
  const text = useCmsText();
  const [offset, setOffset] = useState(0);
  const awardsGridRef = useRef<HTMLDivElement | null>(null);
  const animatingRef = useRef(false);
  const sliderEnabled = !compact && awards.length > 3;
  const visibleAwards = useMemo(
    () =>
      sliderEnabled
        ? Array.from(
            { length: 3 },
            (_, index) => awards[(offset + index) % awards.length],
          )
        : awards,
    [awards, offset, sliderEnabled],
  );
  const move = (direction: number) => {
    if (animatingRef.current || !sliderEnabled) return;

    const cards = awardsGridRef.current?.querySelectorAll("[data-award-card]");
    if (!cards?.length) {
      setOffset((current) => (current + direction + awards.length) % awards.length);
      return;
    }

    animatingRef.current = true;
    gsap.to(cards, {
      autoAlpha: 0,
      y: direction > 0 ? -16 : 16,
      scale: 0.985,
      duration: 0.22,
      stagger: 0.035,
      ease: "power2.in",
      onComplete: () => {
        setOffset((current) => (current + direction + awards.length) % awards.length);
      },
    });
  };

  useEffect(() => {
    if (!sliderEnabled) return;

    const cards = awardsGridRef.current?.querySelectorAll("[data-award-card]");
    if (!cards?.length) return;

    gsap.fromTo(
      cards,
      { autoAlpha: 0, y: 18, scale: 0.985 },
      {
        autoAlpha: 1,
        y: 0,
        scale: 1,
        duration: 0.42,
        stagger: 0.055,
        ease: "power2.out",
        clearProps: "opacity,visibility,transform",
        onComplete: () => {
          animatingRef.current = false;
        },
      },
    );
  }, [offset, sliderEnabled]);

  if (!awards.length) return null;

  return (
    <section className={`border-t border-white/10 px-5 ${compact ? "py-20" : "py-28"} md:px-10 lg:px-16`}>
      <div className="mx-auto max-w-7xl">
        <div className="mb-12 grid gap-8 xl:grid-cols-[0.9fr_1fr] xl:items-end">
          <div>
            <SectionLabel>{text("awards.label", "Награды и дипломы")}</SectionLabel>
            <h2 className="mt-4 max-w-4xl text-[clamp(2.6rem,5.4vw,4.8rem)] font-light leading-[0.98] tracking-normal md:tracking-[-0.035em]">
              {text("awards.title", "Подтверждения опыта и доверия")}
            </h2>
          </div>
          <div className="xl:justify-self-end xl:text-right">
            <p className="max-w-2xl text-base leading-relaxed text-[#D6D1CA] md:text-lg">
              {text(
                "awards.text",
                "Дипломы, сертификаты и благодарности показывают, что студия работает открыто, системно и подтверждает качество не только портфолио.",
              )}
            </p>
            <div className="mt-6 flex flex-wrap gap-3 xl:justify-end">
              {sliderEnabled ? (
                <div className="flex gap-2">
                  <button
                    type="button"
                    onClick={() => move(-1)}
                    aria-label={text("awards.prevAria", "Предыдущие награды")}
                    className="grid h-11 w-11 place-items-center rounded-full border border-white/15 bg-white/[0.04] text-xl text-white transition hover:border-[#D69A66]/60 hover:text-[#D69A66]"
                  >
                    ‹
                  </button>
                  <button
                    type="button"
                    onClick={() => move(1)}
                    aria-label={text("awards.nextAria", "Следующие награды")}
                    className="grid h-11 w-11 place-items-center rounded-full border border-white/15 bg-white/[0.04] text-xl text-white transition hover:border-[#D69A66]/60 hover:text-[#D69A66]"
                  >
                    ›
                  </button>
                </div>
              ) : null}
              {showLink ? (
                <Link
                  href="/nagrady-i-diplomy"
                  className="inline-flex rounded-full border border-[#D69A66]/55 px-6 py-3 text-xs uppercase tracking-[0.22em] text-[#D69A66] transition hover:bg-[#D69A66] hover:text-[#050505]"
                >
                  {text("awards.allButton", "Все награды")}
                </Link>
              ) : null}
            </div>
          </div>
        </div>

        <div ref={awardsGridRef} className="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
          {visibleAwards.map((award, index) => (
            <article
              key={`${award.title}-${award.year}-${index}`}
              data-award-card
              className="group overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.03] transition duration-500 hover:-translate-y-1 hover:border-[#D69A66]/55"
            >
              <div className="relative aspect-[4/5] overflow-hidden bg-[#171511]">
                <AwardDocument title={award.title} image={award.image} />
                <div className="absolute inset-0 bg-gradient-to-t from-[#050505]/85 via-transparent to-transparent opacity-75" />
                <span className="absolute left-5 top-5 rounded-full border border-white/15 bg-black/25 px-3 py-1 text-xs text-white/72 backdrop-blur">
                  {award.year}
                </span>
              </div>
              <div className="p-6">
                <p className="line-clamp-2 text-xs uppercase tracking-[0.24em] text-[#D69A66]">
                  {award.issuer}
                </p>
                <h3 className="mt-3 line-clamp-3 text-2xl font-light leading-tight tracking-normal">
                  {award.title}
                </h3>
                <p className="mt-4 line-clamp-3 text-sm leading-relaxed text-[#D6D1CA]">
                  {award.description}
                </p>
              </div>
            </article>
          ))}
        </div>
      </div>
    </section>
  );
}
