"use client";

import { useEffect, useMemo, useState } from "react";
import { useCmsText } from "@/src/cms";
import { optimizeImageUrl } from "@/src/utils/images";

type HeroBackdropSlide = {
  image?: string;
  alt?: string;
};

type HeroBackdropSliderProps = {
  slides: HeroBackdropSlide[];
  intervalMs?: number;
  className?: string;
  controlsClassName?: string;
};

export default function HeroBackdropSlider({
  slides,
  intervalMs = 5600,
  className = "",
  controlsClassName = "",
}: HeroBackdropSliderProps) {
  const text = useCmsText();
  const cleanSlides = useMemo(
    () =>
      Array.from(
        new Map(
          slides
            .filter((slide) => slide.image)
            .map((slide) => [
              slide.image,
              {
                image: optimizeImageUrl(slide.image as string, 1600, 76),
                alt: slide.alt ?? "",
              },
            ]),
        ).values(),
      ),
    [slides],
  );
  const [activeSlide, setActiveSlide] = useState(0);
  const [loadedSlides, setLoadedSlides] = useState<Record<string, boolean>>({});

  useEffect(() => {
    if (cleanSlides.length < 2) return;

    const mediaQuery = window.matchMedia("(prefers-reduced-motion: reduce)");
    if (mediaQuery.matches) return;

    const interval = window.setInterval(() => {
      setActiveSlide((current) => (current + 1) % cleanSlides.length);
    }, intervalMs);

    return () => window.clearInterval(interval);
  }, [cleanSlides.length, intervalMs]);

  useEffect(() => {
    if (activeSlide <= cleanSlides.length - 1) return;
    setActiveSlide(0);
  }, [activeSlide, cleanSlides.length]);

  if (!cleanSlides.length) return null;

  const moveSlide = (direction: number) => {
    setActiveSlide((current) => (current + direction + cleanSlides.length) % cleanSlides.length);
  };

  return (
    <>
      <div className={`absolute inset-0 ${className}`}>
        {cleanSlides.map((slide, index) => (
          <div
            key={slide.image}
            className={`media-frame absolute inset-0 transition duration-[1100ms] ease-out ${
              loadedSlides[slide.image] ? "media-frame-loaded" : ""
            } ${
              activeSlide === index ? "opacity-100" : "opacity-0"
            }`}
            aria-hidden={activeSlide !== index}
          >
            <img
              src={slide.image}
              alt={activeSlide === index ? slide.alt : ""}
              className={`media-frame-image h-full w-full object-cover transition duration-[1400ms] ease-out ${
                activeSlide === index ? "scale-100 brightness-[0.82] saturate-[1.08]" : "scale-[1.06] brightness-[0.62] saturate-[0.9]"
              }`}
              loading={index === 0 ? "eager" : "lazy"}
              decoding="async"
              fetchPriority={index === 0 ? "high" : "auto"}
              onLoad={() =>
                setLoadedSlides((current) => ({ ...current, [slide.image]: true }))
              }
            />
          </div>
        ))}

        <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_78%_28%,rgba(214,154,102,.28),transparent_30%)] mix-blend-screen" />
        <div className="pointer-events-none absolute -inset-x-20 bottom-0 h-52 bg-gradient-to-t from-[#050505] to-transparent" />
        <div className="pointer-events-none absolute inset-y-0 right-0 w-1/2 overflow-hidden opacity-35">
          <div className="absolute right-[8%] top-[18%] h-[52vh] w-[52vh] rounded-full border border-white/15 animate-[spin_28s_linear_infinite]" />
          <div className="absolute right-[18%] top-[30%] h-[34vh] w-[34vh] rounded-full border border-[#D69A66]/30 animate-[spin_18s_linear_infinite_reverse]" />
        </div>
      </div>

      {cleanSlides.length > 1 && (
        <div className={`absolute bottom-8 left-5 right-5 z-20 mx-auto flex max-w-7xl items-center justify-between gap-4 md:left-10 md:right-10 lg:left-16 lg:right-16 ${controlsClassName}`}>
          <div className="flex items-center gap-2">
            {cleanSlides.map((slide, index) => (
              <button
                key={`${slide.image}-dot`}
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
      )}
    </>
  );
}
