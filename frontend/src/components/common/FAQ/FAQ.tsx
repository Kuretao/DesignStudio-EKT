"use client";

import { useState } from "react";
import { useCms, useCmsText, useLocalizedField } from "@/src/cms";
import SectionLabel from "@/src/components/common/SectionLabel";

function FAQItem({ q, a, index }: { q: string; a: string; index: number }) {
  const [open, setOpen] = useState(false);

  return (
    <div
      className={`rounded-[1.5rem] border bg-white/[0.03] transition-all duration-300 ${
        open
          ? "border-[#D69A66]/45 bg-white/[0.045] shadow-[0_8px_40px_rgba(214,154,102,0.06)]"
          : "border-white/10 hover:border-white/20 hover:bg-white/[0.04]"
      }`}
    >
      <button
        type="button"
        onClick={() => setOpen(!open)}
        className="flex w-full cursor-pointer items-start justify-between gap-6 px-5 py-6 text-left md:px-7"
      >
        <div className="flex min-w-0 items-start gap-4">
          <span className="mt-1 shrink-0 text-[11px] tabular-nums tracking-[0.2em] text-white/20">
            {String(index + 1).padStart(2, "0")}
          </span>
          <span className="line-clamp-3 min-w-0 text-xl font-light leading-snug tracking-normal text-white md:text-2xl">
            {q}
          </span>
        </div>
        <span
          className={`mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full border text-lg font-light leading-none transition-all duration-300 ${
            open
              ? "rotate-45 border-[#D69A66]/50 bg-[#D69A66]/10 text-[#D69A66]"
              : "border-white/15 text-white/40"
          }`}
        >
          +
        </span>
      </button>

      <div
        className="grid transition-all duration-400 ease-in-out"
        style={{ gridTemplateRows: open ? "1fr" : "0fr", transitionDuration: "350ms" }}
      >
        <div className="overflow-hidden">
          <p className="px-5 pb-7 pt-1 leading-relaxed text-[#D6D1CA] md:px-7 md:pl-[4.25rem]">
            {a}
          </p>
        </div>
      </div>
    </div>
  );
}

export default function FAQ() {
  const { faq } = useCms();
  const text = useCmsText();
  const localize = useLocalizedField();

  if (!faq.length) return null;

  return (
    <section id="faq" className="border-t border-white/10 px-5 py-28 md:px-10 lg:px-16">
      <div className="mx-auto max-w-7xl">
        <SectionLabel>{text("faq.label", "FAQ")}</SectionLabel>
        <h2 className="mb-12 text-[clamp(2.7rem,5.2vw,4.8rem)] font-light leading-tight tracking-normal md:tracking-[-0.035em]">
          {text("faq.title", "Частые вопросы")}
        </h2>
        <div className="w-full space-y-3">
          {faq.map((item, i) => (
            <FAQItem
              key={item.q}
              q={localize(item, "q", item.q)}
              a={localize(item, "a", item.a)}
              index={i}
            />
          ))}
        </div>
      </div>
    </section>
  );
}
