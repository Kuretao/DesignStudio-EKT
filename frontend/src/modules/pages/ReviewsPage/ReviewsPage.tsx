"use client";

import Link from "next/link";
import { useCms, useCmsText } from "@/src/cms";
import { testimonials as fallbackTestimonials } from "@/src/data";
import CinematicImage from "@/src/components/common/CinematicImage";
import HeroBackdropSlider from "@/src/components/common/HeroBackdropSlider";
import { GlassPanel } from "@/src/ui";

function ReviewsHero({ featuredReview }: { featuredReview: (typeof fallbackTestimonials)[number] }) {
  const { reviewStats, testimonials } = useCms();
  const text = useCmsText();
  return (
    <section className="relative min-h-screen overflow-hidden px-5 pb-16 pt-28 md:px-10 lg:px-16">
      <HeroBackdropSlider
        slides={[
          { image: featuredReview.image, alt: featuredReview.title },
          { image: testimonials[1]?.image, alt: testimonials[1]?.title },
          { image: testimonials[2]?.image, alt: testimonials[2]?.title },
        ]}
      />
      <div className="absolute inset-0 bg-[linear-gradient(90deg,rgba(5,5,5,.97)_0%,rgba(5,5,5,.72)_50%,rgba(5,5,5,.18)_100%)]" />
      <div className="absolute inset-0 bg-[linear-gradient(0deg,#050505_0%,rgba(5,5,5,.42)_34%,transparent_78%)]" />

      <div className="relative z-10 mx-auto grid min-h-[calc(100vh-7rem)] max-w-7xl gap-10 lg:grid-cols-[0.96fr_1.04fr] lg:items-end">
        <div className="pb-8">
          <p className="text-xs uppercase tracking-[0.38em] text-[#D69A66]">{text("reviews.hero.label", "Client stories")}</p>
          <h1 className="mt-5 max-w-5xl text-[clamp(2.8rem,5vw,5.2rem)] font-light leading-[0.94] tracking-normal md:tracking-[-0.035em] text-white">
            {text("reviews.hero.title", "Отзывы о нас")}
          </h1>
          <p className="mt-7 max-w-2xl text-lg leading-relaxed text-[#E8E0D8]/85 md:text-xl">
            {text("reviews.hero.text", "Здесь важны не громкие обещания, а то, как клиенты описывают процесс: сроки, внимание к деталям, спокойную коммуникацию и результат, который хочется показывать.")}
          </p>
          <div className="mt-9 flex flex-wrap gap-3">
            <Link
              href="/kontakty"
              className="rounded-full border border-[#D69A66] bg-[#D69A66] px-6 py-4 text-xs uppercase tracking-[0.24em] text-[#050505] transition duration-300 hover:-translate-y-0.5 hover:bg-[#E3AD7B]"
            >
              {text("reviews.hero.primaryButton", "Стать клиентом")}
            </Link>
            <Link
              href="/portfolio"
              className="rounded-full border border-white/15 bg-black/25 px-6 py-4 text-xs uppercase tracking-[0.24em] text-white/75 backdrop-blur transition duration-300 hover:border-[#D69A66]/70 hover:text-white"
            >
              {text("reviews.hero.secondaryButton", "Смотреть портфолио")}
            </Link>
          </div>
        </div>

        <div className="mb-8 grid gap-4">
          <GlassPanel className="review-sheen overflow-hidden rounded-[2rem] p-6">
            <div className="mb-5 flex flex-wrap items-center gap-3">
              <span className="rounded-full border border-[#D69A66]/40 px-3 py-1 text-[10px] uppercase tracking-[0.26em] text-[#D69A66]">
                {featuredReview.service}
              </span>
              <span className="text-xs text-white/45">{featuredReview.date}</span>
            </div>
            <p className="text-3xl font-light leading-tight tracking-[-0.04em] md:text-5xl">{featuredReview.title}</p>
            <p className="mt-5 text-base leading-relaxed text-[#D6D1CA]">{featuredReview.text}</p>
            <div className="mt-7 border-t border-white/15 pt-5 text-lg text-white">{featuredReview.name}</div>
          </GlassPanel>

          <div className="grid gap-px overflow-hidden rounded-[1.5rem] border border-white/10 bg-white/10 sm:grid-cols-3">
            {reviewStats.map((stat) => (
              <GlassPanel key={stat.value} className="p-5">
                <span className="block text-2xl font-light text-[#D69A66]">{stat.value}</span>
                <span className="mt-2 block text-xs uppercase leading-relaxed tracking-[0.16em] text-[#D6D1CA]">{stat.label}</span>
              </GlassPanel>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}

function ReviewsPage() {
  const { reviewStats, testimonials } = useCms();
  const text = useCmsText();
  const featuredReview = testimonials[0];

  return (
    <div className="page-in">
      <ReviewsHero featuredReview={featuredReview} />
      <section className="px-5 py-24 md:px-10 lg:px-16">
        <div className="mx-auto max-w-7xl">
          <div className="grid gap-8 lg:grid-cols-[0.85fr_1.15fr] lg:items-end">
            <div>
              <p className="review-card mb-5 text-xs uppercase tracking-[0.45em] text-[#D69A66]">{text("reviews.list.label", "Опыт клиентов")}</p>
              <h2 className="review-card max-w-5xl text-[clamp(2.6rem,5vw,4.8rem)] font-light leading-[0.98] tracking-normal md:tracking-[-0.035em]">
                {text("reviews.list.title", "Слова, за которыми виден процесс")}
              </h2>
            </div>
            <div className="review-card">
              <p className="max-w-xl text-lg leading-relaxed text-[#D6D1CA]">
                {text("reviews.list.text", "Перенесли отзывы с исходной страницы и собрали их в живую презентационную витрину: реальные даты, направления работ, короткие выводы и ответы студии.")}
              </p>
            </div>
          </div>

          <div className="mt-16 grid gap-5 lg:grid-cols-[1.05fr_0.95fr]">
            <GlassPanel className="review-card review-sheen relative overflow-hidden rounded-[2.5rem] p-0">
              <div className="relative min-h-[620px] overflow-hidden">
                <CinematicImage frames={[featuredReview.image, testimonials[1]?.image, testimonials[2]?.image]} alt={featuredReview.title} fill hint="review" />
                <div className="absolute inset-0 bg-gradient-to-t from-[#050505] via-[#050505]/58 to-[#050505]/10" />
                <div className="absolute inset-x-6 bottom-6 md:inset-x-9 md:bottom-9">
                  <div className="mb-8 inline-flex rounded-full border border-[#D69A66]/45 bg-[#050505]/60 px-4 py-2 text-xs uppercase tracking-[0.24em] text-[#D69A66] backdrop-blur">
                    {featuredReview.service}
                  </div>
                  <p className="max-w-3xl text-3xl font-light leading-tight tracking-[-0.04em] md:text-5xl">
                    {featuredReview.title}
                  </p>
                  <p className="mt-6 max-w-2xl text-base leading-relaxed text-[#D6D1CA] md:text-lg">
                    {featuredReview.text}
                  </p>
                  <div className="mt-8 flex flex-wrap items-center gap-4 border-t border-white/15 pt-6">
                    <span className="text-xl text-white">{featuredReview.name}</span>
                    <span className="text-sm text-[#D6D1CA]">{featuredReview.date}</span>
                  </div>
                </div>
              </div>
            </GlassPanel>

            <div className="grid gap-5">
              {testimonials.slice(1).map((review, index) => (
                <GlassPanel
                  key={review.name}
                  className="review-card review-sheen relative overflow-hidden rounded-[2rem] p-6 transition duration-500 hover:-translate-y-2 hover:border-[#D69A66]/60 hover:shadow-[0_24px_90px_rgba(0,0,0,0.38)]"
                >
                  <div className="flex gap-5">
                    <div className="relative hidden h-28 w-24 shrink-0 overflow-hidden rounded-[1.25rem] sm:block">
                      <CinematicImage frames={[review.image, testimonials[0]?.image, testimonials[2]?.image]} alt={review.title} fill hint="view" />
                    </div>
                    <div>
                      <div className="mb-5 flex flex-wrap items-center gap-3">
                        <span className="rounded-full bg-[#D69A66]/12 px-3 py-1 text-[10px] uppercase tracking-[0.22em] text-[#D69A66]">
                          0{index + 2}
                        </span>
                        <span className="text-xs uppercase tracking-[0.18em] text-white/50">{review.service}</span>
                      </div>
                      <h2 className="text-3xl font-light leading-tight tracking-[-0.04em]">{review.title}</h2>
                      <p className="mt-4 leading-relaxed text-[#D6D1CA]">{review.text}</p>
                      <div className="mt-6 flex flex-wrap gap-x-4 gap-y-2 text-sm">
                        <span className="text-white">{review.name}</span>
                        <span className="text-[#D6D1CA]">{review.date}</span>
                      </div>
                    </div>
                  </div>
                </GlassPanel>
              ))}
            </div>
          </div>
        </div>
      </section>

      <section className="border-t border-white/10 px-5 py-28 md:px-10 lg:px-16">
        <div className="mx-auto grid max-w-7xl gap-8 lg:grid-cols-[0.8fr_1.2fr]">
          <div className="review-card">
            <p className="mb-5 text-xs uppercase tracking-[0.45em] text-[#D69A66]">{text("reviews.replies.label", "Диалог со студией")}</p>
            <h2 className="text-[clamp(2.6rem,5vw,4.8rem)] font-light leading-tight tracking-normal md:tracking-[-0.035em]">{text("reviews.replies.title", "Ответы администратора без сухой формальности")}</h2>
            <p className="mt-6 text-lg leading-relaxed text-[#D6D1CA]">
              {text("reviews.replies.text", "Для страницы оставили не только клиентские впечатления, но и короткий ответ студии — так блок выглядит живым и поддерживает доверие.")}
            </p>
          </div>

          <div className="relative">
            <div className="absolute left-5 top-0 hidden h-full w-px bg-gradient-to-b from-[#D69A66] via-white/15 to-transparent md:block" />
            <div className="grid gap-5">
              {testimonials.map((review, index) => (
                <div key={`${review.name}-reply`} className="review-card relative md:pl-14">
                  <span className="absolute left-0 top-7 hidden h-3 w-3 rounded-full bg-[#D69A66] shadow-[0_0_28px_rgba(214,154,102,0.8)] md:block" />
                  <GlassPanel className="rounded-[1.75rem] p-6 transition duration-500 hover:-translate-y-1 hover:border-[#D69A66]/50">
                    <div className="mb-4 flex flex-wrap items-center justify-between gap-3">
                      <span className="text-sm text-[#D69A66]">{text("reviews.replies.itemLabel", "Ответ на отзыв #{number}").replace("{number}", String(index + 1))}</span>
                      <span className="text-xs uppercase tracking-[0.18em] text-white/45">{text("reviews.replies.role", "Администратор")}</span>
                    </div>
                    <p className="text-lg leading-relaxed text-[#F5F2EC]">{review.adminReply}</p>
                  </GlassPanel>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      <section className="px-5 pb-28 md:px-10 lg:px-16">
        <div className="mx-auto max-w-7xl">
          <GlassPanel className="review-card overflow-hidden rounded-[2.5rem] p-0">
            <div className="grid lg:grid-cols-[0.95fr_1.05fr]">
              <div className="relative min-h-[420px] overflow-hidden">
                <CinematicImage frames={[testimonials[1].image, testimonials[2]?.image, testimonials[0]?.image]} alt={text("reviews.form.imageAlt", "Оставить отзыв")} fill hint="review" />
                <div className="absolute inset-0 bg-gradient-to-t from-[#050505] via-[#050505]/35 to-transparent" />
                <div className="absolute bottom-8 left-8 right-8">
                  <p className="mb-4 text-xs uppercase tracking-[0.36em] text-[#D69A66]">{text("reviews.form.label", "Оставить отзыв")}</p>
                  <h2 className="max-w-lg text-4xl font-light tracking-[-0.05em] md:text-6xl">
                    {text("reviews.form.title", "Расскажите, каким был ваш проект")}
                  </h2>
                </div>
              </div>
              <form className="grid gap-4 p-6 md:p-9">
                <input
                  className="rounded-2xl border border-white/10 bg-black/20 px-5 py-4 outline-none transition placeholder:text-white/35 focus:border-[#D69A66]"
                  placeholder={text("reviews.form.namePlaceholder", "Имя *")}
                />
                <input
                  className="rounded-2xl border border-white/10 bg-black/20 px-5 py-4 outline-none transition placeholder:text-white/35 focus:border-[#D69A66]"
                  placeholder={text("reviews.form.emailPlaceholder", "E-mail *")}
                />
                <textarea
                  className="min-h-44 rounded-2xl border border-white/10 bg-black/20 px-5 py-4 outline-none transition placeholder:text-white/35 focus:border-[#D69A66]"
                  placeholder={text("reviews.form.textPlaceholder", "Текст *")}
                />
                <p className="text-sm leading-relaxed text-[#D6D1CA]">
                  {text("reviews.form.notice", "Отправленное сообщение появится после проверки администратором сайта.")}
                </p>
                <button
                  type="button"
                  className="h-14 rounded-full bg-[#D69A66] px-7 text-sm uppercase tracking-[0.24em] text-[#050505] transition hover:bg-[#F5F2EC]"
                >
                  {text("reviews.form.button", "Отправить отзыв")}
                </button>
              </form>
            </div>
          </GlassPanel>
        </div>
      </section>
    </div>
  );
}

export default ReviewsPage;
