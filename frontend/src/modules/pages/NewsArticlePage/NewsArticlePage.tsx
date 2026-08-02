"use client";

import Link from "next/link";
import { useCms, useCmsText } from "@/src/cms";
import type { NewsArticle } from "@/src/data";
import CinematicImage from "@/src/components/common/CinematicImage";
import HeroBackdropSlider from "@/src/components/common/HeroBackdropSlider";
import { GlassPanel } from "@/src/ui";

function BodyBlock({ block }: { block: NewsArticle["body"][number] }) {
  if (block.type === "heading") {
    return (
      <h2 className="mt-12 text-3xl font-light tracking-normal text-[#F5F2EC] [overflow-wrap:anywhere] md:text-4xl">
        {block.text}
      </h2>
    );
  }
  if (block.type === "list") {
    return (
      <ul className="mt-5 space-y-3">
        {block.items?.map((item, i) => (
          <li key={i} className="flex gap-4 leading-relaxed text-[#D6D1CA]">
            <span className="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-[#D69A66]" />
            <span className="min-w-0 [overflow-wrap:anywhere]">{item}</span>
          </li>
        ))}
      </ul>
    );
  }
  return (
    <p className="mt-6 text-lg leading-relaxed text-[#D6D1CA] [overflow-wrap:anywhere]">
      {block.text}
    </p>
  );
}

function NewsArticlePage({ article }: { article: NewsArticle }) {
  const { newsArticles } = useCms();
  const text = useCmsText();
  const currentArticle =
    newsArticles.find((a) => a.slug === article.slug) ?? article;
  const related = newsArticles
    .filter((a) => a.slug !== currentArticle.slug)
    .slice(0, 3);
  const articleImages = Array.isArray((currentArticle as any).images)
    ? ((currentArticle as any).images as string[])
    : [];
  const heroSlides = articleImages.length
    ? articleImages.map((image, index) => ({
        image,
        alt:
          index === 0
            ? currentArticle.title
            : `${currentArticle.title} - слайд ${index + 1}`,
      }))
    : [
        { image: currentArticle.image, alt: currentArticle.title },
        { image: related[0]?.image, alt: related[0]?.title },
        { image: related[1]?.image, alt: related[1]?.title },
      ];

  return (
    <article className="page-in">
      {/* Hero */}
      <section className="relative flex min-h-[70vh] items-end overflow-hidden px-5 pb-16 md:px-10 lg:px-16">
        <HeroBackdropSlider slides={heroSlides} controlsClassName="bottom-6" />
        <div className="absolute inset-0 bg-gradient-to-t from-[#050505] via-[#050505]/55 to-[#050505]/10" />
        <div className="absolute inset-0 bg-gradient-to-r from-[#050505]/60 to-transparent" />
        <div className="relative z-10 mx-auto w-full max-w-5xl">
          <div className="mb-6 flex flex-wrap items-center gap-4">
            <span className="rounded-full border border-[#D69A66]/45 bg-[#050505]/50 px-4 py-1.5 text-[10px] uppercase tracking-[0.3em] text-[#D69A66] backdrop-blur">
              {currentArticle.category}
            </span>
            <span className="text-sm text-white/50">{currentArticle.date}</span>
            <span className="text-sm text-white/35">
              {text("news.readingTimeSuffix", "{time} чтения").replace("{time}", currentArticle.readingTime)}
            </span>
          </div>
          <h1 className="max-w-4xl text-[clamp(2.7rem,5.2vw,5.4rem)] font-light leading-[1] tracking-normal [overflow-wrap:anywhere]">
            {currentArticle.title}
          </h1>
        </div>
      </section>

      {/* Body */}
      <section className="px-5 py-16 md:px-10 lg:px-16">
        <div className="mx-auto max-w-5xl">
          <div className="grid gap-12 lg:grid-cols-[1fr_280px] lg:items-start">
            {/* Article text */}
            <div>
              <p className="text-xl font-light leading-relaxed text-[#F5F2EC]">
                {currentArticle.preview}
              </p>
              <div className="mt-2 border-t border-white/10" />
              {currentArticle.body.map((block, i) => (
                <BodyBlock key={i} block={block} />
              ))}

              {/* Back link */}
              <div className="mt-16 border-t border-white/10 pt-8">
                <Link
                  href="/novosti"
                  className="inline-flex items-center gap-3 text-sm uppercase tracking-[0.22em] text-[#D69A66] transition hover:gap-5"
                >
                  {text("newsArticle.backButton", "← Все новости")}
                </Link>
              </div>
            </div>

            {/* Sidebar */}
            <aside className="space-y-4 lg:sticky lg:top-32">
              <GlassPanel className="rounded-[1.75rem] p-6">
                <p className="mb-4 text-xs uppercase tracking-[0.3em] text-[#D69A66]">
                  {text("newsArticle.author.label", "Об авторе")}
                </p>
                <p className="text-sm leading-relaxed text-[#D6D1CA]">
                  {text(
                    "newsArticle.author.text",
                    "Команда 3D Smart Design Studio — студия концептуального дизайна. Интерьеры, архитектура, ландшафт и 3D-визуализация.",
                  )}
                </p>
              </GlassPanel>
              <GlassPanel className="rounded-[1.75rem] p-6">
                <p className="mb-4 text-xs uppercase tracking-[0.3em] text-[#D69A66]">
                  {text("newsArticle.cta.label", "Обсудить проект")}
                </p>
                <p className="mb-5 text-sm leading-relaxed text-[#D6D1CA]">
                  {text("newsArticle.cta.text", "Есть идея или вопрос? Свяжитесь с нами — ответим в течение рабочего дня.")}
                </p>
                <Link
                  href="/kontakty"
                  className="inline-flex w-full items-center justify-center rounded-full bg-[#D69A66] px-5 py-3 text-xs uppercase tracking-[0.22em] text-[#050505] transition hover:bg-[#F5F2EC]"
                >
                  {text("newsArticle.cta.button", "Написать нам")}
                </Link>
              </GlassPanel>
            </aside>
          </div>
        </div>
      </section>

      {/* Related articles */}
      {related.length > 0 && (
        <section className="border-t border-white/10 px-5 py-20 md:px-10 lg:px-16">
          <div className="mx-auto max-w-7xl">
            <p className="mb-3 text-xs uppercase tracking-[0.45em] text-[#D69A66]">
              {text("newsArticle.related.label", "Читать также")}
            </p>
            <h2 className="mb-10 text-4xl font-light tracking-[-0.045em] md:text-6xl">
              {text("newsArticle.related.title", "Другие новости")}
            </h2>
            <div className="grid gap-5 md:grid-cols-3">
              {related.map((a, index) => (
                <Link
                  key={a.slug}
                  href={`/novosti/${a.slug}`}
                  className="group overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.02] transition duration-500 hover:-translate-y-2 hover:border-[#D69A66]/55 hover:shadow-[0_24px_80px_rgba(0,0,0,0.42)]"
                >
                  <div className="relative h-52 overflow-hidden">
                    <CinematicImage
                      frames={[
                        a.image,
                        related[(index + 1) % related.length]?.image,
                        related[(index + 2) % related.length]?.image,
                      ]}
                      alt={a.title}
                      fill
                      hint="read"
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-[#050505]/80 via-transparent to-[#D69A66]/10" />
                    <span className="absolute left-4 top-4 rounded-full border border-[#D69A66]/35 bg-[#050505]/60 px-3 py-1 text-[10px] uppercase tracking-[0.22em] text-[#D69A66] backdrop-blur">
                      {a.category}
                    </span>
                  </div>
                  <div className="p-5">
                    <p className="mb-2 text-xs text-white/40">{a.date}</p>
                    <h3 className="text-lg font-light leading-snug tracking-normal [overflow-wrap:anywhere] transition duration-500 group-hover:translate-x-0.5">
                      {a.title}
                    </h3>
                  </div>
                </Link>
              ))}
            </div>
          </div>
        </section>
      )}
    </article>
  );
}

export default NewsArticlePage;
