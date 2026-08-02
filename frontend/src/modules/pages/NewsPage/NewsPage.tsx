"use client";

import Link from "next/link";
import { useCms } from "@/src/cms";
import CinematicImage from "@/src/components/common/CinematicImage";
import HeroBackdropSlider from "@/src/components/common/HeroBackdropSlider";

function NewsPage() {
  const { newsArticles } = useCms();
  const [featured, ...rest] = newsArticles;
  const featuredImages = Array.isArray((featured as any).images)
    ? ((featured as any).images as string[])
    : [];
  const featuredSlides = featuredImages.length
    ? featuredImages.map((image, index) => ({
        image,
        alt:
          index === 0
            ? featured.title
            : `${featured.title} - слайд ${index + 1}`,
      }))
    : [
        { image: featured.image, alt: featured.title },
        { image: rest[0]?.image, alt: rest[0]?.title },
        { image: rest[1]?.image, alt: rest[1]?.title },
      ];

  return (
    <div className="page-in">
      <section className="relative min-h-screen overflow-hidden px-5 pb-16 pt-28 md:px-10 lg:px-16">
        <HeroBackdropSlider slides={featuredSlides} />
        <div className="absolute inset-0 bg-[linear-gradient(90deg,rgba(5,5,5,.96)_0%,rgba(5,5,5,.70)_48%,rgba(5,5,5,.18)_100%)]" />
        <div className="absolute inset-0 bg-[linear-gradient(0deg,#050505_0%,rgba(5,5,5,.35)_34%,transparent_76%)]" />

        <div className="relative z-10 mx-auto grid min-h-[calc(100vh-7rem)] max-w-7xl gap-10 lg:grid-cols-[1fr_0.78fr] lg:items-end">
          <div className="pb-8">
            <p className="text-xs uppercase tracking-[0.38em] text-[#D69A66]">
              Журнал студии
            </p>
            <h1 className="mt-5 max-w-5xl text-[clamp(3rem,6.4vw,6.2rem)] font-light leading-[0.94] tracking-[-0.045em] text-white">
              Блог и новости
            </h1>
            <p className="mt-7 max-w-2xl text-lg leading-relaxed text-[#E8E0D8]/85 md:text-xl">
              Пишем о проектах, решениях и деталях, которые делают интерьер
              дороже на вид, спокойнее в реализации и точнее для жизни.
            </p>
            <div className="mt-9 flex flex-wrap items-center gap-3">
              <Link
                href={`/novosti/${featured.slug}`}
                className="rounded-full border border-[#D69A66] bg-[#D69A66] px-6 py-4 text-xs uppercase tracking-[0.24em] text-[#050505] transition duration-300 hover:-translate-y-0.5 hover:bg-[#E3AD7B]"
              >
                Читать главное
              </Link>
              <span className="rounded-full border border-white/15 bg-black/25 px-5 py-4 text-xs uppercase tracking-[0.24em] text-white/75 backdrop-blur">
                {newsArticles.length} материала
              </span>
            </div>
          </div>

          <Link
            href={`/novosti/${featured.slug}`}
            className="group mb-8 overflow-hidden rounded-[2rem] border border-white/10 bg-[#050505]/42 p-6 backdrop-blur transition duration-500 hover:-translate-y-2 hover:border-[#D69A66]/60"
          >
            <div className="mb-5 flex flex-wrap items-center gap-3">
              <span className="rounded-full border border-[#D69A66]/40 px-3 py-1 text-[10px] uppercase tracking-[0.28em] text-[#D69A66]">
                {featured.category}
              </span>
              <span className="text-xs text-white/45">{featured.date}</span>
              <span className="text-xs text-white/35">
                {featured.readingTime} чтения
              </span>
            </div>
            <h2 className="text-3xl font-light leading-tight tracking-normal [overflow-wrap:anywhere] transition duration-500 group-hover:translate-x-1 md:text-5xl">
              {featured.title}
            </h2>
            <p className="mt-5 text-base leading-relaxed text-[#D6D1CA]">
              {featured.preview}
            </p>
            <div className="mt-8 inline-flex items-center gap-3 text-xs uppercase tracking-[0.22em] text-[#D69A66] transition duration-300 group-hover:gap-5">
              Открыть статью <span>→</span>
            </div>
          </Link>
        </div>
      </section>

      <section className="px-5 py-24 md:px-10 lg:px-16">
        <div className="mx-auto max-w-7xl">
          <p className="mb-5 text-xs uppercase tracking-[0.45em] text-[#D69A66]">
            Новости студии
          </p>
          <div className="mb-16 grid gap-8 md:grid-cols-[1fr_0.6fr] md:items-end">
            <h2 className="text-5xl font-light leading-[0.92] tracking-[-0.055em] md:text-7xl">
              Все материалы
            </h2>
            <p className="text-lg leading-relaxed text-[#D6D1CA]">
              Обновления студии, новые услуги, советы по проектированию и тренды
              рынка.
            </p>
          </div>

          <Link
            href={`/novosti/${featured.slug}`}
            className="group relative mb-5 flex min-h-[520px] overflow-hidden rounded-[2.5rem] border border-white/10 transition duration-500 hover:-translate-y-1 hover:border-[#D69A66]/50 hover:shadow-[0_34px_120px_rgba(0,0,0,0.5)]"
          >
            <CinematicImage
              frames={featuredSlides.map((slide) => slide.image)}
              alt={featured.title}
              fill
              hint="story"
            />
            <div className="absolute inset-0 bg-gradient-to-t from-[#050505] via-[#050505]/50 to-transparent" />
            <div className="relative mt-auto p-8 md:p-12">
              <div className="mb-4 flex flex-wrap items-center gap-3">
                <span className="rounded-full border border-[#D69A66]/40 px-3 py-1 text-[10px] uppercase tracking-[0.28em] text-[#D69A66]">
                  {featured.category}
                </span>
                <span className="text-xs text-white/45">{featured.date}</span>
                <span className="text-xs text-white/35">
                  {featured.readingTime} чтения
                </span>
              </div>
              <h2 className="max-w-3xl text-4xl font-light leading-tight tracking-normal [overflow-wrap:anywhere] transition duration-500 group-hover:translate-x-1 md:text-6xl">
                {featured.title}
              </h2>
              <p className="mt-5 max-w-2xl text-lg leading-relaxed text-[#D6D1CA]">
                {featured.preview}
              </p>
              <div className="mt-8 inline-flex items-center gap-3 text-sm uppercase tracking-[0.22em] text-[#D69A66] transition duration-300 group-hover:gap-5">
                Читать статью <span>→</span>
              </div>
            </div>
          </Link>

          <div className="grid gap-5 md:grid-cols-3">
            {rest.map((article, index) => {
              const articleImages = Array.isArray((article as any).images)
                ? ((article as any).images as string[])
                : [];
              const frames = articleImages.length
                ? articleImages
                : [
                    article.image,
                    newsArticles[(index + 2) % newsArticles.length]?.image,
                    newsArticles[(index + 3) % newsArticles.length]?.image,
                  ];

              return (
                <Link
                  key={article.slug}
                  href={`/novosti/${article.slug}`}
                  className="group overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.02] transition duration-500 hover:-translate-y-2 hover:border-[#D69A66]/55 hover:shadow-[0_24px_80px_rgba(0,0,0,0.42)]"
                >
                  <div className="relative h-56 overflow-hidden">
                    <CinematicImage
                      frames={frames}
                      alt={article.title}
                      fill
                      hint="read"
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-[#050505]/80 via-[#050505]/20 to-[#D69A66]/10" />
                    <span className="absolute left-5 top-5 rounded-full border border-[#D69A66]/35 bg-[#050505]/60 px-3 py-1 text-[10px] uppercase tracking-[0.22em] text-[#D69A66] backdrop-blur">
                      {article.category}
                    </span>
                  </div>
                  <div className="p-6">
                    <div className="mb-3 flex items-center gap-3 text-xs text-white/40">
                      <span>{article.date}</span>
                      <span>·</span>
                      <span>{article.readingTime} чтения</span>
                    </div>
                    <h3 className="text-xl font-light leading-snug tracking-normal [overflow-wrap:anywhere] transition duration-500 group-hover:translate-x-0.5">
                      {article.title}
                    </h3>
                    <p className="mt-3 text-sm leading-relaxed text-[#D6D1CA] line-clamp-3">
                      {article.preview}
                    </p>
                    <div className="mt-5 inline-flex items-center gap-2 text-xs uppercase tracking-[0.2em] text-[#D69A66] transition duration-300 group-hover:gap-3">
                      Читать <span>→</span>
                    </div>
                  </div>
                </Link>
              );
            })}
          </div>
        </div>
      </section>
    </div>
  );
}

export default NewsPage;
