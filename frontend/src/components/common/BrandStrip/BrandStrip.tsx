"use client";

import { useCms, useCmsText } from "@/src/cms";
import { resolvePartnerLogo } from "@/src/components/common/legacyPartnerLogos";

export default function BrandStrip() {
  const { partners } = useCms();
  const text = useCmsText();

  if (!partners.length) return null;

  return (
    <section className="border-y border-white/8 bg-[#0c0b09]/45 px-5 py-12 md:px-10 lg:px-16">
      <div className="mx-auto max-w-7xl">
        <div className="mb-7 grid gap-5 xl:grid-cols-[0.55fr_1fr] xl:items-end">
          <div>
            <p className="text-xs uppercase tracking-[0.38em] text-[#D69A66]">
              {text("brandStrip.label", "Нам доверяют")}
            </p>
            <h2 className="mt-3 text-[clamp(2rem,3.8vw,3.2rem)] font-light leading-tight tracking-normal text-white md:tracking-[-0.025em]">
              {text("brandStrip.title", "Девелоперы и застройщики")}
            </h2>
          </div>
          <p className="max-w-2xl text-sm leading-relaxed text-[#D6D1CA] xl:justify-self-end xl:text-right">
            {text(
              "brandStrip.text",
              "Проекты студии выбирают частные клиенты, девелоперы и строительные компании, которым важны понятная визуализация и аккуратная реализация.",
            )}
          </p>
        </div>
        <div className="grid grid-cols-2 gap-px overflow-hidden rounded-[1.5rem] border border-white/10 bg-white/10 xl:grid-cols-4">
          {partners.map((brand) => {
            const logo = resolvePartnerLogo(brand.logo);

            return (
              <div
                key={brand.name}
                className="group flex min-h-32 flex-col items-center justify-center bg-[#11100d]/88 px-4 py-6 text-center transition hover:bg-white/[0.055] md:px-5"
              >
                <img
                  src={logo ?? ""}
                  alt={brand.name}
                  loading="lazy"
                  className="h-16 w-full max-w-[190px] object-contain opacity-75 grayscale transition duration-300 group-hover:opacity-100 group-hover:grayscale-0"
                />
                <p className="mt-4 line-clamp-2 text-sm font-medium tracking-[0.04em] text-white/68">
                  {brand.name}
                </p>
                <p className="mt-1 line-clamp-2 text-xs leading-snug text-white/34">
                  {brand.note}
                </p>
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
}
