"use client";

import type { ReactNode } from "react";
import { useState } from "react";
import Link from "next/link";
import { contentPages as fallbackContentPages } from "@/src/data";
import { submitLead, useCms } from "@/src/cms";
import { GlassPanel } from "@/src/ui";
import CinematicImage from "@/src/components/common/CinematicImage";
import HeroBackdropSlider from "@/src/components/common/HeroBackdropSlider";
import SectionLabel from "@/src/components/common/SectionLabel";

type ContentBlock = {
  type?: string | null;
  eyebrow?: string | null;
  title?: string | null;
  subtitle?: string | null;
  text?: string | null;
  image?: string | null;
  images?: string[];
  imageAlt?: string | null;
  visualVariant?: string | null;
  mediaPosition?: string | null;
  motionPreset?: string | null;
  cardState?: string | null;
  settings?: Record<string, unknown>;
  linkLabel?: string | null;
  linkHref?: string | null;
};

type ContentPageItem = (typeof fallbackContentPages)[number] & {
  body?: string | null;
  template?: string | null;
  image?: string | null;
  images?: string[];
  blocks?: ContentBlock[];
};

export function ContentPagesOverview() {
  const { contentPages } = useCms();
  return (
    <section className="border-t border-white/10 px-5 py-28 md:px-10 lg:px-16">
      <div className="mx-auto max-w-7xl">
        <SectionLabel>Разделы сайта</SectionLabel>
        <h2 className="mb-12 max-w-4xl text-[clamp(2.6rem,5vw,4.8rem)] font-light leading-tight tracking-normal">
          Материалы, отзывы и полезные страницы
        </h2>
        <div className="grid gap-5 md:grid-cols-2 lg:grid-cols-4">
          {contentPages.map((page) => (
            <Link
              key={page.id}
              id={page.id}
              href={`/${page.id}`}
              className="group min-w-0 scroll-mt-28 rounded-[2rem] border border-white/10 bg-white/[0.03] p-7 transition duration-500 hover:-translate-y-2 hover:border-[#D69A66]/60"
            >
              <p className="mb-8 text-xs uppercase tracking-[0.32em] text-[#D69A66] [overflow-wrap:anywhere]">
                {page.eyebrow}
              </p>
              <h3 className="text-3xl font-light tracking-normal [overflow-wrap:anywhere] transition duration-500 group-hover:translate-x-1">
                {page.title}
              </h3>
              <p className="mt-5 leading-relaxed text-[#D6D1CA] [overflow-wrap:anywhere]">
                {page.text}
              </p>
            </Link>
          ))}
        </div>
      </div>
    </section>
  );
}

function ContentPage({ page }: { page: ContentPageItem }) {
  const { careerVacancies, contentPages, newsArticles, projects } = useCms();
  const currentPage: ContentPageItem =
    (contentPages.find((item) => item.id === page.id) as
      | ContentPageItem
      | undefined) ?? page;
  const allBlocks = currentPage.blocks?.filter(hasVisibleBlock) ?? [];
  const heroBlock = allBlocks.find((block) => block.type === "hero");
  const contentBlocks = allBlocks.filter((block) => block !== heroBlock);
  const isBlog = currentPage.id === "blog";
  const isCareer = currentPage.id === "karera";
  const isPartners = currentPage.id === "partneram";
  const hasBody = Boolean(currentPage.body?.trim());
  const [partnerName, setPartnerName] = useState("");
  const [partnerContact, setPartnerContact] = useState("");
  const [partnerCompany, setPartnerCompany] = useState("");
  const [partnerMessage, setPartnerMessage] = useState("");
  const [sent, setSent] = useState(false);

  const heroImages = uniqueImages([
    ...imagesFromBlock(heroBlock),
    ...(currentPage.images ?? []),
    currentPage.image,
    ...contentBlocks.flatMap(imagesFromBlock),
  ]);
  const fallbackSlides = projects.slice(0, 3).map((project) => project.image);
  const sliderImages = heroImages.length ? heroImages : fallbackSlides;
  const heroTitle = heroBlock?.title || currentPage.title;
  const heroEyebrow = heroBlock?.eyebrow || currentPage.eyebrow;
  const heroText = heroBlock?.subtitle || heroBlock?.text || currentPage.text;

  const handlePartnerSubmit = async () => {
    if (!partnerContact.trim()) return;

    await submitLead({
      source: "partner-page",
      name: partnerName.trim(),
      contact: partnerContact.trim(),
      service: partnerCompany.trim(),
      message: partnerMessage.trim(),
    });
    setSent(true);
    setTimeout(() => setSent(false), 4000);
  };

  const inputCls =
    "w-full rounded-2xl border border-white/10 bg-white/[0.04] px-5 py-4 text-sm text-[#F5F2EC] outline-none transition placeholder:text-white/25 focus:border-[#D69A66]/60 focus:bg-white/[0.07]";

  return (
    <div className="page-in">
      <section className="relative overflow-hidden px-5 pb-28 pt-40 md:px-10 md:pb-28 md:pt-44 lg:px-16">
        <HeroBackdropSlider
          slides={sliderImages.map((image) => ({ image, alt: heroTitle }))}
        />
        <div className="absolute inset-0 bg-[#050505]/70" />
        <div className="absolute inset-0 bg-[linear-gradient(90deg,#050505,rgba(5,5,5,.82),rgba(5,5,5,.45))]" />
        <div className="relative mx-auto max-w-7xl">
          <SectionLabel>{heroEyebrow}</SectionLabel>
          <h1 className="max-w-5xl text-[clamp(2.9rem,6.1vw,5.9rem)] font-light leading-[0.98] tracking-normal [overflow-wrap:anywhere]">
            {heroTitle}
          </h1>
          {heroText ? (
            <p className="mt-8 max-w-3xl text-xl leading-relaxed text-[#D6D1CA] [overflow-wrap:anywhere]">
              {heroText}
            </p>
          ) : null}
          {heroBlock?.linkHref && heroBlock.linkLabel ? (
            <SmartLink
              href={heroBlock.linkHref}
              className="mt-9 inline-flex rounded-full bg-[#D69A66] px-7 py-4 text-sm font-medium uppercase tracking-[0.22em] text-[#050505] transition hover:bg-[#F5F2EC]"
            >
              {heroBlock.linkLabel}
            </SmartLink>
          ) : null}
        </div>
      </section>

      <section className="px-5 py-24 md:px-10 lg:px-16">
        <div className="mx-auto max-w-7xl space-y-8">
          {hasBody ? (
            <GlassPanel
              className={`cms-rich-panel rounded-[2rem] p-7 md:p-10 ${currentPage.template === "legal" ? "mx-auto max-w-4xl" : ""}`}
            >
              <div
                className="cms-rich-text"
                dangerouslySetInnerHTML={{ __html: currentPage.body ?? "" }}
              />
            </GlassPanel>
          ) : null}

          {contentBlocks.length ? (
            <CmsBlocks
              blocks={contentBlocks}
              pageTitle={currentPage.title}
              partnerForm={
                isPartners
                  ? {
                      inputCls,
                      partnerName,
                      partnerCompany,
                      partnerContact,
                      partnerMessage,
                      sent,
                      onNameChange: setPartnerName,
                      onCompanyChange: setPartnerCompany,
                      onContactChange: setPartnerContact,
                      onMessageChange: setPartnerMessage,
                      onSubmit: handlePartnerSubmit,
                    }
                  : undefined
              }
            />
          ) : hasBody ? null : isCareer ? (
            <CareerContent careerVacancies={careerVacancies} />
          ) : isPartners ? (
            <PartnersContent
              inputCls={inputCls}
              partnerName={partnerName}
              partnerCompany={partnerCompany}
              partnerContact={partnerContact}
              partnerMessage={partnerMessage}
              sent={sent}
              onNameChange={setPartnerName}
              onCompanyChange={setPartnerCompany}
              onContactChange={setPartnerContact}
              onMessageChange={setPartnerMessage}
              onSubmit={handlePartnerSubmit}
            />
          ) : isBlog ? (
            <BlogContent newsArticles={newsArticles} />
          ) : (
            <EmptyContent page={currentPage} />
          )}
        </div>
      </section>
    </div>
  );
}

function CmsBlocks({
  blocks,
  pageTitle,
  partnerForm,
}: {
  blocks: ContentBlock[];
  pageTitle: string;
  partnerForm?: PartnerFormProps;
}) {
  return (
    <div className="space-y-8">
      {blocks.map((block, index) => {
        const key = `${block.type ?? "block"}-${index}-${block.title ?? ""}`;

        if (block.type === "list") {
          return <ListBlock key={key} block={block} />;
        }

        if (block.type === "form" && partnerForm) {
          return <PartnerFormBlock key={key} block={block} form={partnerForm} />;
        }

        if (block.type === "gallery") {
          return <GalleryBlock key={key} block={block} pageTitle={pageTitle} />;
        }

        if (block.type === "quote") {
          return <QuoteBlock key={key} block={block} />;
        }

        if (block.type === "cta") {
          return <CtaBlock key={key} block={block} />;
        }

        return (
          <MediaBlock
            key={key}
            block={block}
            index={index}
            pageTitle={pageTitle}
          />
        );
      })}
    </div>
  );
}

function ListBlock({ block }: { block: ContentBlock }) {
  const items = String(block.text ?? "")
    .split(/\r?\n/u)
    .map((item) => item.trim())
    .filter(Boolean);

  if (!items.length && !block.title && !block.subtitle) return null;

  return (
    <GlassPanel className="rounded-[2rem] p-7 md:p-9">
      {block.eyebrow ? (
        <SectionLabel className="mb-5">{block.eyebrow}</SectionLabel>
      ) : null}
      {block.title ? (
        <h2 className="text-4xl font-light leading-tight tracking-normal [overflow-wrap:anywhere] md:text-5xl">
          {block.title}
        </h2>
      ) : null}
      {block.subtitle ? (
        <p className="mt-5 max-w-3xl text-lg leading-relaxed text-[#D6D1CA] [overflow-wrap:anywhere]">
          {block.subtitle}
        </p>
      ) : null}
      {items.length ? (
        <div className="mt-8 grid gap-4 md:grid-cols-2">
          {items.map((item, index) => (
            <div
              key={`${item}-${index}`}
              className="flex min-w-0 gap-4 rounded-[1.5rem] border border-white/10 bg-white/[0.03] p-5"
            >
              <span className="text-sm text-[#D69A66]">
                {String(index + 1).padStart(2, "0")}
              </span>
              <p className="leading-relaxed text-[#D6D1CA] [overflow-wrap:anywhere]">
                {item}
              </p>
            </div>
          ))}
        </div>
      ) : null}
    </GlassPanel>
  );
}

function MediaBlock({
  block,
  index,
  pageTitle,
}: {
  block: ContentBlock;
  index: number;
  pageTitle: string;
}) {
  const images = imagesFromBlock(block);
  const hasImage = images.length > 0;
  const imageBackground = hasImage && block.mediaPosition === "background";
  const imageFirst =
    block.mediaPosition === "left" || (!block.mediaPosition && index % 2 === 1);
  const imageTop = block.mediaPosition === "top";
  const motionPreset = block.motionPreset ?? "slides";
  const frames = motionPreset === "none" ? images.slice(0, 1) : images;
  const contentPadding =
    block.visualVariant === "compact"
      ? "p-6 md:p-7"
      : block.visualVariant === "wide"
        ? "p-8 md:p-12"
        : "p-7 md:p-9";
  const panelClassName = [
    "overflow-hidden rounded-[2rem]",
    block.visualVariant === "accent"
      ? "border-[#D69A66]/45 bg-[#D69A66]/[0.055]"
      : "",
    block.cardState === "featured"
      ? "border-[#D69A66]/45 bg-[#D69A66]/[0.055]"
      : "",
    block.cardState === "muted" ? "opacity-82" : "",
    block.cardState === "disabled" ? "opacity-55 grayscale" : "",
  ]
    .filter(Boolean)
    .join(" ");
  const copy = (
    <div className={`min-w-0 self-center ${contentPadding}`}>
      {block.eyebrow ? (
        <SectionLabel className="mb-5">{block.eyebrow}</SectionLabel>
      ) : null}
      {block.title ? (
        <h2 className="text-4xl font-light leading-tight tracking-normal [overflow-wrap:anywhere] md:text-5xl">
          {block.title}
        </h2>
      ) : null}
      {block.subtitle ? (
        <p className="mt-5 text-lg leading-relaxed text-[#D6D1CA] [overflow-wrap:anywhere]">
          {block.subtitle}
        </p>
      ) : null}
      {block.text ? (
        <p className="cms-copy mt-5 whitespace-pre-line leading-relaxed text-[#E8E0D8]/80 [overflow-wrap:anywhere]">
          {block.text}
        </p>
      ) : null}
      {block.linkHref && block.linkLabel ? (
        <SmartLink
          href={block.linkHref}
          className="mt-7 inline-flex rounded-full border border-[#D69A66]/70 px-6 py-3 text-xs uppercase tracking-[0.22em] text-[#D69A66] transition hover:bg-[#D69A66] hover:text-[#050505]"
        >
          {block.linkLabel}
        </SmartLink>
      ) : null}
    </div>
  );
  const media = hasImage ? (
    <div className="relative min-h-[320px] overflow-hidden rounded-[1.5rem] lg:min-h-[440px]">
      <CinematicImage
        frames={frames}
        alt={block.imageAlt || block.title || pageTitle}
        fill
        mode={frames.length > 1 ? "frames" : "preview"}
        hint={motionPreset === "none" ? "" : motionPreset}
        overlayClassName="bg-gradient-to-t from-[#050505]/55 via-transparent to-transparent"
      />
    </div>
  ) : null;

  if (imageBackground) {
    return (
      <GlassPanel className={`${panelClassName} relative min-h-[460px]`}>
        <CinematicImage
          frames={frames}
          alt={block.imageAlt || block.title || pageTitle}
          fill
          mode={frames.length > 1 ? "frames" : "preview"}
          hint={motionPreset === "none" ? "" : motionPreset}
          overlayClassName="bg-gradient-to-r from-[#050505]/88 via-[#050505]/62 to-[#050505]/22"
        />
        <div className="relative z-10 max-w-3xl">{copy}</div>
      </GlassPanel>
    );
  }

  return (
    <GlassPanel className={panelClassName}>
      <div
        className={`grid min-w-0 gap-0 ${
          hasImage && !imageTop
            ? block.visualVariant === "split"
              ? "lg:grid-cols-2"
              : "lg:grid-cols-[0.92fr_1.08fr]"
            : ""
        }`}
      >
        {imageFirst ? media : copy}
        {imageFirst ? copy : media}
      </div>
    </GlassPanel>
  );
}

function GalleryBlock({
  block,
  pageTitle,
}: {
  block: ContentBlock;
  pageTitle: string;
}) {
  const images = imagesFromBlock(block);
  if (!images.length)
    return <MediaBlock block={block} index={0} pageTitle={pageTitle} />;

  return (
    <section className="min-w-0">
      <div className="mb-6 max-w-3xl">
        {block.eyebrow ? (
          <SectionLabel className="mb-5">{block.eyebrow}</SectionLabel>
        ) : null}
        {block.title ? (
          <h2 className="text-4xl font-light leading-tight tracking-normal [overflow-wrap:anywhere] md:text-5xl">
            {block.title}
          </h2>
        ) : null}
        {block.subtitle || block.text ? (
          <p className="mt-5 whitespace-pre-line text-lg leading-relaxed text-[#D6D1CA] [overflow-wrap:anywhere]">
            {block.subtitle || block.text}
          </p>
        ) : null}
      </div>
      <div className="grid gap-4 md:grid-cols-3">
        {images.map((image, index) => (
          <div
            key={image}
            className={`relative min-h-[260px] overflow-hidden rounded-[1.5rem] border border-white/10 ${index === 0 ? "md:col-span-2 md:min-h-[360px]" : ""}`}
          >
            <img
              src={image}
              alt={
                index === 0 ? block.imageAlt || block.title || pageTitle : ""
              }
              className="h-full w-full object-cover"
              loading={index === 0 ? "eager" : "lazy"}
              decoding="async"
            />
            <div className="absolute inset-0 bg-gradient-to-t from-[#050505]/65 via-transparent to-transparent" />
            <span className="absolute bottom-5 left-5 text-sm text-[#D69A66]">
              0{index + 1}
            </span>
          </div>
        ))}
      </div>
    </section>
  );
}

function QuoteBlock({ block }: { block: ContentBlock }) {
  const quote = block.text || block.subtitle;

  return (
    <GlassPanel
      className={`rounded-[2rem] p-8 md:p-12 ${block.cardState === "featured" ? "border-[#D69A66]/45 bg-[#D69A66]/[0.055]" : ""}`}
    >
      {block.eyebrow ? (
        <SectionLabel className="mb-6">{block.eyebrow}</SectionLabel>
      ) : null}
      {quote ? (
        <blockquote className="max-w-5xl text-3xl font-light leading-tight tracking-normal [overflow-wrap:anywhere] md:text-5xl">
          {quote}
        </blockquote>
      ) : null}
      {block.title ? (
        <p className="mt-7 text-lg text-[#D69A66] [overflow-wrap:anywhere]">
          {block.title}
        </p>
      ) : null}
    </GlassPanel>
  );
}

function CtaBlock({ block }: { block: ContentBlock }) {
  return (
    <section
      className={`rounded-[2rem] border border-[#D69A66]/35 bg-[#D69A66]/10 p-8 md:p-12 ${block.cardState === "muted" ? "opacity-82" : ""}`}
    >
      {block.eyebrow ? (
        <SectionLabel className="mb-6">{block.eyebrow}</SectionLabel>
      ) : null}
      {block.title ? (
        <h2 className="max-w-4xl text-4xl font-light leading-tight tracking-normal [overflow-wrap:anywhere] md:text-6xl">
          {block.title}
        </h2>
      ) : null}
      {block.subtitle || block.text ? (
        <p className="mt-6 max-w-3xl whitespace-pre-line text-lg leading-relaxed text-[#E8E0D8]/85 [overflow-wrap:anywhere]">
          {block.subtitle || block.text}
        </p>
      ) : null}
      {block.linkHref && block.linkLabel ? (
        <SmartLink
          href={block.linkHref}
          className="mt-8 inline-flex rounded-full bg-[#D69A66] px-7 py-4 text-sm font-medium uppercase tracking-[0.22em] text-[#050505] transition hover:bg-[#F5F2EC]"
        >
          {block.linkLabel}
        </SmartLink>
      ) : null}
    </section>
  );
}

function CareerContent({ careerVacancies }: { careerVacancies: any[] }) {
  return (
    <div className="grid gap-5 lg:grid-cols-[1.1fr_0.9fr]">
      <GlassPanel className="rounded-[2rem] p-7 md:p-9">
        <SectionLabel className="mb-5">Вакансии</SectionLabel>
        {careerVacancies.length > 0 ? (
          <div className="space-y-4">
            {careerVacancies.map((vacancy) => {
              const requirements = Array.isArray(vacancy.requirements)
                ? vacancy.requirements
                : String(vacancy.requirements ?? "")
                    .split(/\r?\n/u)
                    .filter(Boolean);
              const format =
                vacancy.format ?? vacancy.employment ?? vacancy.location;
              const workload = vacancy.workload ?? vacancy.salary;

              return (
                <article
                  key={vacancy.title}
                  className="min-w-0 rounded-[1.5rem] border border-white/10 bg-white/[0.03] p-6"
                >
                  <div className="flex flex-wrap items-center gap-3 text-xs uppercase tracking-[0.22em] text-[#D69A66]">
                    {format ? <span>{format}</span> : null}
                    {format && workload ? (
                      <span className="h-1 w-1 rounded-full bg-white/25" />
                    ) : null}
                    {workload ? <span>{workload}</span> : null}
                  </div>
                  <h2 className="mt-4 text-3xl font-light tracking-normal [overflow-wrap:anywhere]">
                    {vacancy.title}
                  </h2>
                  <p className="mt-4 leading-relaxed text-[#D6D1CA] [overflow-wrap:anywhere]">
                    {vacancy.description}
                  </p>
                  {requirements.length ? (
                    <ul className="mt-5 space-y-2 text-sm text-white/55">
                      {requirements.map((item: string) => (
                        <li
                          key={item}
                          className="flex gap-3 [overflow-wrap:anywhere]"
                        >
                          <span className="mt-2 h-px w-4 shrink-0 bg-[#D69A66]" />
                          {item}
                        </li>
                      ))}
                    </ul>
                  ) : null}
                </article>
              );
            })}
          </div>
        ) : (
          <div className="rounded-[1.5rem] border border-white/10 bg-white/[0.03] p-7">
            <h2 className="text-3xl font-light tracking-normal">
              Открытых вакансий пока нет
            </h2>
            <p className="mt-4 max-w-2xl leading-relaxed text-[#D6D1CA]">
              Резюме и портфолио можно отправить на почту студии.
            </p>
          </div>
        )}
      </GlassPanel>
      <GlassPanel className="rounded-[2rem] p-7 md:p-9">
        <SectionLabel className="mb-5">Отклик</SectionLabel>
        <h2 className="text-3xl font-light tracking-normal">
          Прислать портфолио
        </h2>
        <p className="mt-4 leading-relaxed text-[#D6D1CA]">
          Напишите пару строк о себе, приложите ссылку на портфолио и укажите
          направление: интерьер, архитектура или ландшафт.
        </p>
        <a
          href="mailto:3dsmartdesign@bk.ru?subject=Отклик%20на%20вакансию%20с%20сайта"
          className="mt-8 inline-flex rounded-full bg-[#D69A66] px-7 py-4 text-sm font-medium uppercase tracking-[0.22em] text-[#050505] transition hover:bg-[#F5F2EC]"
        >
          Отправить отклик
        </a>
      </GlassPanel>
    </div>
  );
}

type PartnerFormProps = {
  inputCls: string;
  partnerName: string;
  partnerCompany: string;
  partnerContact: string;
  partnerMessage: string;
  sent: boolean;
  onNameChange: (value: string) => void;
  onCompanyChange: (value: string) => void;
  onContactChange: (value: string) => void;
  onMessageChange: (value: string) => void;
  onSubmit: () => void;
};

function PartnerFormBlock({
  block,
  form,
}: {
  block?: ContentBlock;
  form: PartnerFormProps;
}) {
  const settings = block?.settings ?? {};
  const setting = (key: string, fallback: string) => {
    const value = settings[key];

    return typeof value === "string" && value.trim() ? value : fallback;
  };

  return (
    <GlassPanel className="rounded-[2rem] p-7 md:p-9">
      <div className="grid gap-8 lg:grid-cols-[0.85fr_1.15fr]">
        <div>
          <SectionLabel className="mb-5">
            {block?.eyebrow || "Обратная связь"}
          </SectionLabel>
          <h2 className="text-4xl font-light leading-tight tracking-normal [overflow-wrap:anywhere] md:text-5xl">
            {block?.title || "Предложить сотрудничество"}
          </h2>
          {block?.subtitle || block?.text ? (
            <p className="mt-5 whitespace-pre-line leading-relaxed text-[#D6D1CA] [overflow-wrap:anywhere]">
              {block.subtitle || block.text}
            </p>
          ) : null}
        </div>
        <div className="grid gap-3">
          <input
            className={form.inputCls}
            placeholder={setting("namePlaceholder", "Ваше имя")}
            value={form.partnerName}
            onChange={(event) => form.onNameChange(event.target.value)}
          />
          <input
            className={form.inputCls}
            placeholder={setting("companyPlaceholder", "Компания или направление")}
            value={form.partnerCompany}
            onChange={(event) => form.onCompanyChange(event.target.value)}
          />
          <input
            className={form.inputCls}
            placeholder={setting("contactPlaceholder", "Телефон, e-mail или Telegram")}
            value={form.partnerContact}
            onChange={(event) => form.onContactChange(event.target.value)}
          />
          <textarea
            className={`${form.inputCls} min-h-[140px] resize-none`}
            placeholder={setting("messagePlaceholder", "Коротко о предложении")}
            value={form.partnerMessage}
            onChange={(event) => form.onMessageChange(event.target.value)}
          />
          <button
            type="button"
            onClick={form.onSubmit}
            className="h-14 rounded-full bg-[#D69A66] text-sm font-medium uppercase tracking-[0.22em] text-[#050505] transition hover:bg-[#F5F2EC]"
          >
            {form.sent
              ? setting("sentLabel", "Заявка подготовлена")
              : block?.linkLabel || "Отправить"}
          </button>
        </div>
      </div>
    </GlassPanel>
  );
}

function PartnersContent({
  inputCls,
  partnerName,
  partnerCompany,
  partnerContact,
  partnerMessage,
  sent,
  onNameChange,
  onCompanyChange,
  onContactChange,
  onMessageChange,
  onSubmit,
}: PartnerFormProps) {
  return (
    <div className="grid gap-5 lg:grid-cols-[0.9fr_1.1fr]">
      <GlassPanel className="rounded-[2rem] p-7 md:p-9">
        <SectionLabel className="mb-5">Форматы</SectionLabel>
        <div className="space-y-5">
          {[
            "Материалы, мебель, свет и декор для комплектации проектов",
            "Подрядные команды для ремонта, строительства и монтажа",
            "Девелоперские и коммерческие проекты для визуализации",
            "Коллаборации с шоурумами, брендами и профильными специалистами",
          ].map((item, index) => (
            <div
              key={item}
              className="flex min-w-0 gap-4 rounded-[1.5rem] border border-white/10 bg-white/[0.03] p-5"
            >
              <span className="text-sm text-[#D69A66]">0{index + 1}</span>
              <p className="leading-relaxed text-[#D6D1CA] [overflow-wrap:anywhere]">
                {item}
              </p>
            </div>
          ))}
        </div>
      </GlassPanel>
      <PartnerFormBlock
        form={{
          inputCls,
          partnerName,
          partnerCompany,
          partnerContact,
          partnerMessage,
          sent,
          onNameChange,
          onCompanyChange,
          onContactChange,
          onMessageChange,
          onSubmit,
        }}
      />
    </div>
  );
}

function BlogContent({ newsArticles }: { newsArticles: any[] }) {
  return (
    <div className="grid gap-5 md:grid-cols-3">
      {newsArticles.slice(1).map((article, index) => (
        <Link
          key={article.slug}
          href={`/novosti/${article.slug}`}
          className="group min-w-0 overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.03] transition duration-500 hover:-translate-y-2 hover:border-[#D69A66]/60"
        >
          <div className="relative h-64 overflow-hidden">
            <CinematicImage
              frames={[
                article.image,
                newsArticles[(index + 2) % newsArticles.length]?.image,
                newsArticles[(index + 3) % newsArticles.length]?.image,
              ]}
              alt={article.title}
              fill
              hint="read"
            />
            <div className="absolute inset-0 bg-gradient-to-t from-[#050505]/85 via-transparent to-transparent" />
            <span className="absolute bottom-5 left-5 text-xs uppercase tracking-[0.28em] text-[#D69A66]">
              {article.category}
            </span>
          </div>
          <div className="p-6">
            <h2 className="text-2xl font-light leading-tight tracking-normal [overflow-wrap:anywhere]">
              {article.title}
            </h2>
            <p className="mt-4 text-sm leading-relaxed text-[#D6D1CA] [overflow-wrap:anywhere]">
              {article.preview}
            </p>
          </div>
        </Link>
      ))}
    </div>
  );
}

function EmptyContent({ page }: { page: ContentPageItem }) {
  return (
    <GlassPanel className="rounded-[2rem] p-7 md:p-10">
      <SectionLabel className="mb-5">{page.eyebrow}</SectionLabel>
      <h2 className="text-4xl font-light tracking-normal [overflow-wrap:anywhere]">
        {page.title}
      </h2>
      <p className="mt-5 max-w-3xl leading-relaxed text-[#D6D1CA] [overflow-wrap:anywhere]">
        {page.text}
      </p>
    </GlassPanel>
  );
}

function SmartLink({
  href,
  className,
  children,
}: {
  href: string;
  className: string;
  children: ReactNode;
}) {
  if (/^https?:\/\//i.test(href)) {
    return (
      <a href={href} className={className} target="_blank" rel="noreferrer">
        {children}
      </a>
    );
  }

  return (
    <Link href={href} className={className}>
      {children}
    </Link>
  );
}

function hasVisibleBlock(block: ContentBlock) {
  return Boolean(
    block.eyebrow?.trim() ||
    block.title?.trim() ||
    block.subtitle?.trim() ||
    block.text?.trim() ||
    imagesFromBlock(block).length ||
    block.linkLabel?.trim(),
  );
}

function imagesFromBlock(block?: ContentBlock | null) {
  if (!block) return [];
  return uniqueImages([...(block.images ?? []), block.image]);
}

function uniqueImages(images: Array<string | null | undefined>) {
  return Array.from(
    new Set(
      images
        .map((image) => image?.trim())
        .filter((image): image is string => Boolean(image)),
    ),
  );
}

export default ContentPage;
