"use client";

import type { ReactNode } from "react";
import { useCms } from "@/src/cms";
import HeroBackdropSlider from "@/src/components/common/HeroBackdropSlider";
import { projectImageAt } from "@/src/utils/images";

type LegalPageItem = {
  id?: string;
  slug?: string;
  title?: string | null;
  body?: string | null;
};

type LegalDocumentPageProps = {
  slug: string;
  fallbackTitle: string;
  projectIndexes: [number, number, number];
  slideAlts: [string, string, string];
  meta?: ReactNode;
  children: ReactNode;
};

export default function LegalDocumentPage({
  slug,
  fallbackTitle,
  projectIndexes,
  slideAlts,
  meta,
  children,
}: LegalDocumentPageProps) {
  const { contentPages, projects } = useCms();
  const page = (contentPages as LegalPageItem[]).find(
    (item) => item.slug === slug || item.id === slug,
  );
  const cmsBody = page?.body?.trim() ?? "";
  const title = page?.title?.trim() || fallbackTitle;

  return (
    <div className="page-in">
      <section className="relative min-h-[520px] overflow-hidden px-5 pb-24 pt-28 md:min-h-[580px] md:px-10 md:pt-32 lg:px-16">
        <HeroBackdropSlider
          slides={projectIndexes.map((projectIndex, index) => ({
            image: projectImageAt(projects, projectIndex),
            alt: slideAlts[index],
          }))}
          controlsClassName="bottom-7"
        />
        <div className="pointer-events-none absolute inset-0 bg-[linear-gradient(90deg,rgba(5,5,5,.96)_0%,rgba(5,5,5,.78)_52%,rgba(5,5,5,.34)_100%)]" />
        <div className="pointer-events-none absolute inset-0 bg-[linear-gradient(0deg,#050505_0%,rgba(5,5,5,.48)_32%,transparent_74%)]" />

        <div className="relative z-10 mx-auto max-w-4xl">
          <p className="mb-5 text-xs uppercase tracking-[0.45em] text-[#D69A66]">
            Документ
          </p>
          <h1 className="text-[1.8rem] font-light leading-[1.02] tracking-normal [hyphens:auto] sm:text-[2.7rem] md:text-[4rem] md:leading-[0.98] lg:text-[5.3rem]">
            {title}
          </h1>
          {meta ? <div className="mt-7 max-w-3xl">{meta}</div> : null}
        </div>
      </section>

      <section className="px-5 py-16 md:px-10 md:py-20 lg:px-16">
        <div className="mx-auto max-w-4xl">
          {cmsBody ? (
            <div className="cms-rich-panel rounded-[2rem] border border-white/10 bg-white/[0.025] p-8 md:p-12">
              <div
                className="cms-rich-text legal-rich-text text-[#D6D1CA]"
                dangerouslySetInnerHTML={{ __html: cmsBody }}
              />
            </div>
          ) : (
            children
          )}
        </div>
      </section>
    </div>
  );
}
