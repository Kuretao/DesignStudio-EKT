"use client";

import { createContext, useContext, useEffect, useMemo, useState } from "react";
import { useTranslation } from "react-i18next";
import { optimizeImageUrl } from "@/src/utils/images";
import {
  awards,
  careerVacancies,
  contactInfo,
  contentPages,
  faq,
  messengerLinks,
  newsArticles,
  partners,
  projects,
  requiredHomeHeroTitle,
  promos,
  reviewStats,
  serviceNavigationGroups as fallbackServiceNavigationGroups,
  servicePageItems as fallbackServicePageItems,
  services,
  testimonials,
} from "@/src/data";

type SiteSettings = {
  siteName: string;
  seoTitle: string;
  seoDescription: string;
  logo: string | null;
  logoSmall: string | null;
  favicon: string | null;
  appleTouchIcon: string | null;
  socialPreviewImage: string | null;
  socialLinks: {
    vk: string | null;
    linkedin: string | null;
    behance: string | null;
    pinterest: string | null;
  };
  compareEyebrow: string;
  compareTitle: string;
  compareText: string;
};

const DEFAULT_SITE_LOGO = "/logo.png";

type HomeHero = {
  eyebrow: string;
  title: string;
  text: string;
  linkLabel: string;
  linkHref: string;
};

type HomeStory = {
  eyebrow: string;
  text: string;
};

type AnimationControls = {
  enabled: boolean;
  smoothScroll: boolean;
  pageReveal: boolean;
};

type SiteLocale = "ru" | "en";

type UiText = {
  ru?: string | null;
  en?: string | null;
  group?: string | null;
  label?: string | null;
};

type CmsData = {
  siteSettings: SiteSettings;
  uiTexts: Record<string, UiText>;
  homeHero: HomeHero;
  homeStory: HomeStory;
  projects: typeof projects;
  servicePageItems: typeof fallbackServicePageItems;
  serviceNavigationGroups: typeof fallbackServiceNavigationGroups;
  services: typeof services;
  newsArticles: typeof newsArticles;
  promos: typeof promos;
  testimonials: typeof testimonials;
  faq: typeof faq;
  contactInfo: typeof contactInfo;
  messengerLinks: typeof messengerLinks;
  animationControls: AnimationControls;
  contentPages: typeof contentPages;
  careerVacancies: typeof careerVacancies;
  reviewStats: typeof reviewStats;
  awards: typeof awards;
  partners: typeof partners;
  menuItems: {
    href: string;
    label: string;
    labelRu?: string;
    labelEn?: string | null;
  }[];
  ready: boolean;
};

const fallbackData: CmsData = {
  siteSettings: {
    siteName: "3D Smart Design Studio",
    seoTitle: `${requiredHomeHeroTitle} | 3D Smart Design Studio`,
    seoDescription:
      "Дизайн интерьера, архитектурное проектирование, 3D-визуализация, ландшафтный дизайн, комплектация и авторский надзор в Самаре.",
    logo: DEFAULT_SITE_LOGO,
    logoSmall: null,
    favicon: null,
    appleTouchIcon: null,
    socialPreviewImage: null,
    socialLinks: {
      vk: "https://vk.com/3dsmartdesign",
      linkedin: "https://www.linkedin.com/in/3dsmartdesignstudio",
      behance: "https://www.behance.net/3dsmartdesign",
      pinterest: "https://ru.pinterest.com/3D_SMART_DESIGN_STUDIO/",
    },
    compareEyebrow: "Render / Blueprint",
    compareTitle: "Сравнение до / после",
    compareText:
      "Ползунок показывает, как меняется восприятие объекта после визуальной проработки. Здесь можно заменить демо на реальные чертежи, рендеры или фото объекта.",
  },
  uiTexts: {},
  homeHero: {
    eyebrow: "3D Smart Design Studio",
    title: requiredHomeHeroTitle,
    text: "Создаем интерьеры, архитектуру, 3D-визуализацию и ландшафтные проекты: от концепции до рабочей документации, комплектации и сопровождения.",
    linkLabel: "Обсудить проект",
    linkHref: "/kontakty",
  },
  homeStory: {
    eyebrow: "Философия проекта",
    text: "Мы проектируем не стены, а сценарии жизни: утренний свет, маршрут взгляда, тишину материалов и точную документацию для реализации.",
  },
  projects,
  servicePageItems: fallbackServicePageItems,
  serviceNavigationGroups: fallbackServiceNavigationGroups,
  services,
  newsArticles,
  promos,
  testimonials,
  faq,
  contactInfo,
  messengerLinks,
  animationControls: {
    enabled: true,
    smoothScroll: true,
    pageReveal: true,
  },
  contentPages,
  careerVacancies,
  reviewStats,
  awards,
  partners,
  menuItems: [
    { href: "/o-nas", label: "О нас" },
    { href: "/portfolio", label: "Портфолио" },
    { href: "/services", label: "Услуги" },
    { href: "/blog", label: "Блог" },
    { href: "/kontakty", label: "Контакты" },
  ],
  ready: false,
};

const CmsContext = createContext<CmsData>(fallbackData);

function makeProjectSlug(project: {
  slug?: string;
  title?: string;
  id?: number;
}) {
  if (project.slug) return project.slug;

  const base = project.title || `project-${project.id ?? ""}`;
  return base
    .toLowerCase()
    .replace(/["'«»]/g, "")
    .replace(/[^a-z0-9а-яё]+/gi, "-")
    .replace(/^-+|-+$/g, "");
}

function normalizeAsset(path?: string | null) {
  const value = path?.trim();
  if (!value) return null;

  if (
    /^(https?:)?\/\//i.test(value) ||
    value.startsWith("data:") ||
    value.startsWith("/")
  ) {
    return value;
  }

  return `/storage/${value.replace(/^\/+/, "")}`;
}

function normalizeImageList(value: unknown): string[] {
  const rawItems = Array.isArray(value)
    ? value
    : typeof value === "string"
      ? value.split(/\r?\n/u)
      : [];

  return Array.from(
    new Set(
      rawItems
        .map((item) => normalizeAsset(typeof item === "string" ? item : ""))
        .map((item) => optimizeImageUrl(item, 1400, 76))
        .filter((item): item is string => Boolean(item)),
    ),
  );
}

function localeSuffix(locale: SiteLocale) {
  return locale === "en" ? "En" : "Ru";
}

function hasText(value: unknown): value is string {
  return typeof value === "string" && value.trim().length > 0;
}

function decodeCmsText(value: string) {
  return value
    .replace(/&amp;quot;|&#34;|&quot;/giu, '"')
    .replace(/&amp;#039;|&#039;|&apos;/giu, "'")
    .replace(/&amp;laquo;|&laquo;/giu, "«")
    .replace(/&amp;raquo;|&raquo;/giu, "»")
    .replace(/&amp;amp;/giu, "&");
}

function localizeString(
  item: Record<string, any> | null | undefined,
  base: string,
  locale: SiteLocale,
  fallback: string | null = "",
) {
  const localized = item?.[`${base}${localeSuffix(locale)}`];
  if (hasText(localized)) return decodeCmsText(localized);

  const ru = item?.[`${base}Ru`];
  if (hasText(ru)) return decodeCmsText(ru);

  const direct = item?.[base];
  if (hasText(direct)) return decodeCmsText(direct);

  return typeof fallback === "string" ? decodeCmsText(fallback) : fallback;
}

function localizeArray<T>(
  item: Record<string, any> | null | undefined,
  base: string,
  locale: SiteLocale,
  fallback: T[] = [],
) {
  const localized = item?.[`${base}${localeSuffix(locale)}`];
  if (Array.isArray(localized) && localized.length)
    return localized.map((value) =>
      typeof value === "string" ? decodeCmsText(value) : value,
    ) as T[];

  const ru = item?.[`${base}Ru`];
  if (Array.isArray(ru) && ru.length)
    return ru.map((value) =>
      typeof value === "string" ? decodeCmsText(value) : value,
    ) as T[];

  const direct = item?.[base];
  if (Array.isArray(direct) && direct.length)
    return direct.map((value) =>
      typeof value === "string" ? decodeCmsText(value) : value,
    ) as T[];

  return fallback;
}

function normalizeBlock(block: any, locale: SiteLocale) {
  const images = normalizeImageList(
    block?.images?.length ? block.images : block?.image,
  );

  return {
    ...block,
    type: block?.type ?? "text",
    visualVariant: block?.visualVariant ?? "default",
    mediaPosition: block?.mediaPosition ?? null,
    motionPreset: block?.motionPreset ?? "motion",
    cardState: block?.cardState ?? "normal",
    eyebrow: localizeString(block, "eyebrow", locale, null),
    title: localizeString(block, "title", locale, null),
    subtitle: localizeString(block, "subtitle", locale, null),
    text: localizeString(block, "text", locale, null),
    image: images[0] ?? null,
    images,
    imageAlt: localizeString(block, "imageAlt", locale, null),
    linkLabel: localizeString(block, "linkLabel", locale, null),
    linkHref: block?.linkHref ?? null,
    settings:
      block?.settings && typeof block.settings === "object"
        ? block.settings
        : {},
  };
}

function localizeProject(project: any, locale: SiteLocale) {
  return {
    ...project,
    title: localizeString(project, "title", locale, project?.title ?? "") ?? "",
    image: optimizeImageUrl(project?.image, 1600, 76),
    beforeImage: optimizeImageUrl(project?.beforeImage, 1200, 74),
    afterImage: optimizeImageUrl(project?.afterImage, 1200, 74),
    featuredLabel:
      localizeString(project, "featuredLabel", locale, project?.featuredLabel ?? "") ??
      "",
    featuredTitle:
      localizeString(project, "featuredTitle", locale, project?.featuredTitle ?? "") ??
      "",
    featuredDescription:
      localizeString(
        project,
        "featuredDescription",
        locale,
        project?.featuredDescription ?? "",
      ) ?? "",
    featuredImage: optimizeImageUrl(project?.featuredImage, 1600, 76),
    category:
      localizeString(project, "category", locale, project?.category ?? "") ??
      "",
    location:
      localizeString(project, "location", locale, project?.location ?? "") ??
      "",
    description:
      localizeString(
        project,
        "description",
        locale,
        project?.description ?? "",
      ) ?? "",
  };
}

function localizeService(service: any, locale: SiteLocale) {
  const images = normalizeImageList(
    service?.images?.length ? service.images : service?.heroImages,
  );
  const deliverableImages = normalizeImageList(service?.deliverableImages);

  return {
    ...service,
    title: localizeString(service, "title", locale, service?.title ?? "") ?? "",
    eyebrow:
      localizeString(service, "eyebrow", locale, service?.eyebrow ?? "") ?? "",
    text: localizeString(service, "text", locale, service?.text ?? "") ?? "",
    image: images[0] ?? optimizeImageUrl(service?.image, 1400, 76),
    images,
    deliverableImages,
    price: localizeString(service, "price", locale, service?.price ?? "") ?? "",
    timeline:
      localizeString(service, "timeline", locale, service?.timeline ?? "") ??
      "",
    pdfUrl: service?.pdfUrl ?? "",
    pdfTitle:
      localizeString(service, "pdfTitle", locale, service?.pdfTitle ?? "") ??
      "",
    deliverables: localizeArray<string>(
      service,
      "deliverables",
      locale,
      service?.deliverables ?? [],
    ),
    benefits: localizeArray<string>(
      service,
      "benefits",
      locale,
      service?.benefits ?? [],
    ),
    process: localizeArray<string>(
      service,
      "process",
      locale,
      service?.process ?? [],
    ),
  };
}

function localizeNewsArticle(article: any, locale: SiteLocale) {
  const images = normalizeImageList(
    article?.images?.length ? article.images : article?.heroImages,
  );

  return {
    ...article,
    title: localizeString(article, "title", locale, article?.title ?? "") ?? "",
    date: localizeString(article, "date", locale, article?.date ?? "") ?? "",
    category:
      localizeString(article, "category", locale, article?.category ?? "") ??
      "",
    preview:
      localizeString(article, "preview", locale, article?.preview ?? "") ?? "",
    image: images[0] ?? optimizeImageUrl(article?.image, 1400, 76),
    images,
    readingTime:
      localizeString(
        article,
        "readingTime",
        locale,
        article?.readingTime ?? "",
      ) ?? "",
    body: localizeArray(article, "body", locale, article?.body ?? []),
  };
}

function localizePromo(promo: any, locale: SiteLocale) {
  return {
    ...promo,
    badge: localizeString(promo, "badge", locale, promo?.badge ?? "") ?? "",
    title: localizeString(promo, "title", locale, promo?.title ?? "") ?? "",
    highlight:
      localizeString(promo, "highlight", locale, promo?.highlight ?? "") ?? "",
    validUntil:
      localizeString(promo, "validUntil", locale, promo?.validUntil ?? "") ??
      "",
    description:
      localizeString(promo, "description", locale, promo?.description ?? "") ??
      "",
    image: optimizeImageUrl(promo?.image, 1400, 76),
    conditions: localizeArray<string>(
      promo,
      "conditions",
      locale,
      promo?.conditions ?? [],
    ),
  };
}

function localizeReview(review: any, locale: SiteLocale) {
  return {
    ...review,
    name: localizeString(review, "name", locale, review?.name ?? "") ?? "",
    date: localizeString(review, "date", locale, review?.date ?? "") ?? "",
    service:
      localizeString(review, "service", locale, review?.service ?? "") ?? "",
    title: localizeString(review, "title", locale, review?.title ?? "") ?? "",
    text: localizeString(review, "text", locale, review?.text ?? "") ?? "",
    adminReply:
      localizeString(review, "adminReply", locale, review?.adminReply ?? "") ??
      "",
    image: optimizeImageUrl(review?.image, 1200, 74),
  };
}

function localizeFaq(item: any, locale: SiteLocale) {
  return {
    ...item,
    q: localizeString(item, "q", locale, item?.q ?? "") ?? "",
    a: localizeString(item, "a", locale, item?.a ?? "") ?? "",
  };
}

function localizeVacancy(vacancy: any, locale: SiteLocale) {
  return {
    ...vacancy,
    title: localizeString(vacancy, "title", locale, vacancy?.title ?? "") ?? "",
    employment:
      localizeString(
        vacancy,
        "employment",
        locale,
        vacancy?.employment ?? "",
      ) ?? "",
    location:
      localizeString(vacancy, "location", locale, vacancy?.location ?? "") ??
      "",
    salary:
      localizeString(vacancy, "salary", locale, vacancy?.salary ?? "") ?? "",
    description:
      localizeString(
        vacancy,
        "description",
        locale,
        vacancy?.description ?? "",
      ) ?? "",
    requirements: localizeArray<string>(
      vacancy,
      "requirements",
      locale,
      vacancy?.requirements ?? [],
    ),
    responsibilities: localizeArray<string>(
      vacancy,
      "responsibilities",
      locale,
      vacancy?.responsibilities ?? [],
    ),
  };
}

function localizeNamedItem(item: any, locale: SiteLocale) {
  return {
    ...item,
    name: localizeString(item, "name", locale, item?.name ?? "") ?? "",
    note: localizeString(item, "note", locale, item?.note ?? "") ?? "",
  };
}

function localizeAward(award: any, locale: SiteLocale) {
  return {
    ...award,
    title: localizeString(award, "title", locale, award?.title ?? "") ?? "",
    issuer: localizeString(award, "issuer", locale, award?.issuer ?? "") ?? "",
    description:
      localizeString(award, "description", locale, award?.description ?? "") ??
      "",
    image: optimizeImageUrl(award?.image, 1200, 74),
  };
}

function mergeServiceItems(payloadServices: any[]) {
  const byId = new Map(
    payloadServices
      .map((service: any) => ({ ...service, id: service.id ?? service.slug }))
      .filter((service: any) => service.id)
      .map((service: any) => [service.id, service]),
  );

  const merged = fallbackServicePageItems.map((fallback) => {
    const service = byId.get(fallback.id);

    if (!service) return fallback;

    byId.delete(fallback.id);

    return {
      ...fallback,
      ...service,
      deliverables:
        Array.isArray(service.deliverables) && service.deliverables.length
          ? service.deliverables
          : fallback.deliverables,
      benefits:
        Array.isArray(service.benefits) && service.benefits.length
          ? service.benefits
          : fallback.benefits,
      process:
        Array.isArray(service.process) && service.process.length
          ? service.process
          : fallback.process,
      deliverableImages:
        Array.isArray(service.deliverableImages) &&
        service.deliverableImages.length
          ? service.deliverableImages
          : [],
      image: service.image || fallback.image,
      price: service.price || fallback.price,
      timeline: service.timeline || fallback.timeline,
      eyebrow: service.eyebrow || fallback.eyebrow,
      text: service.text || fallback.text,
    };
  });

  return [...merged, ...Array.from(byId.values())];
}

function normalizeServiceNavigationGroups(
  payloadGroups: any[],
  locale: SiteLocale,
) {
  const groups = payloadGroups
    .map((group: any, index: number) => {
      const href = typeof group?.href === "string" ? group.href : "";
      const title = localizeString(group, "title", locale, "") ?? "";

      if (!href || !title) {
        return null;
      }

      const items = Array.isArray(group?.items)
        ? group.items
            .map((item: any) => ({
              ...item,
              label: localizeString(item, "label", locale, "") ?? "",
              labelRu:
                typeof item?.labelRu === "string" ? item.labelRu : item?.label,
              labelEn: typeof item?.labelEn === "string" ? item.labelEn : null,
              href: typeof item?.href === "string" ? item.href : "",
            }))
            .filter((item: any) => item.label && item.href)
        : [];

      return {
        id: String(group.id ?? href ?? `service-group-${index}`),
        title,
        titleRu: typeof group?.titleRu === "string" ? group.titleRu : title,
        titleEn: typeof group?.titleEn === "string" ? group.titleEn : null,
        href,
        description: localizeString(group, "description", locale, "") ?? "",
        descriptionRu:
          typeof group?.descriptionRu === "string"
            ? group.descriptionRu
            : typeof group?.description === "string"
              ? group.description
              : "",
        descriptionEn:
          typeof group?.descriptionEn === "string" ? group.descriptionEn : null,
        items,
      };
    })
    .filter(Boolean);

  return groups as typeof fallbackServiceNavigationGroups;
}

function normalizePayload(payload: any, locale: SiteLocale): CmsData {
  const settings = payload?.settings ?? {};
  const payloadServices = Array.isArray(payload?.services)
    ? payload.services.map((service: any) => localizeService(service, locale))
    : [];
  const apiServices = payloadServices.length
    ? mergeServiceItems(payloadServices)
    : fallbackServicePageItems;
  const apiProjects = (
    Array.isArray(payload?.projects) && payload.projects.length
      ? payload.projects
      : projects
  ).map((project: any) => {
    const localized = localizeProject(project, locale);

    return {
      ...localized,
      slug: makeProjectSlug(localized),
    };
  });
  const payloadPages = Array.isArray(payload?.pages)
    ? payload.pages.map((page: any) => ({
        ...page,
        title: localizeString(page, "title", locale, page?.title ?? "") ?? "",
        body: localizeString(page, "body", locale, page?.body ?? "") ?? "",
        seoTitle:
          localizeString(page, "seoTitle", locale, page?.seoTitle ?? "") ?? "",
        seoDescription:
          localizeString(
            page,
            "seoDescription",
            locale,
            page?.seoDescription ?? "",
          ) ?? "",
        blocks: Array.isArray(page?.blocks)
          ? page.blocks.map((block: any) => normalizeBlock(block, locale))
          : [],
      }))
    : [];
  const homePage = payloadPages.find(
    (page: any) => page.slug === "home" || page.id === "home",
  );
  const homeBlocks = Array.isArray(homePage?.blocks) ? homePage.blocks : [];
  const homeBlock =
    homeBlocks.find((block: any) => block.type === "hero") ??
    homeBlocks[0] ??
    {};
  const homeStoryBlock = homeBlocks.find((block: any) => block.type === "text");
  const homeTitle =
    typeof homeBlock.title === "string" ? homeBlock.title.trim() : "";
  const normalizedHomeTitle = homeTitle.toLowerCase();
  const shouldUseRequiredHomeTitle =
    !homeTitle ||
    ["дизайн с умом", "дизайн с умом."].includes(normalizedHomeTitle) ||
    (normalizedHomeTitle.includes("дизайн интерьера") &&
      normalizedHomeTitle.includes("архитектур") &&
      !normalizedHomeTitle.includes("ландшафт"));
  const contentPayloadPages = payloadPages.filter(
    (page: any) => page.slug !== "home" && page.id !== "home",
  );
  const apiPages = contentPayloadPages.length
    ? contentPayloadPages.map((page: any) => {
        const blocks = Array.isArray(page.blocks) ? page.blocks : [];
        const hero = blocks.find((block: any) => block.type === "hero") ?? {};
        return {
          ...page,
          id: page.id ?? page.slug,
          slug: page.slug,
          title: page.title,
          template: page.template,
          body: page.body,
          eyebrow: hero.eyebrow ?? "3D Smart Design Studio",
          text: hero.text ?? hero.subtitle ?? page.seoDescription ?? "",
          image: hero.image ?? apiProjects[0]?.image,
          images: hero.images ?? [],
          blocks,
        };
      })
    : contentPages;

  return {
    siteSettings: {
      siteName: settings.siteName ?? fallbackData.siteSettings.siteName,
      seoTitle: settings.seoTitle ?? fallbackData.siteSettings.seoTitle,
      seoDescription:
        settings.seoDescription ?? fallbackData.siteSettings.seoDescription,
      logo: settings.logo || fallbackData.siteSettings.logo,
      logoSmall: settings.logoSmall ?? null,
      favicon: settings.favicon ?? null,
      appleTouchIcon: settings.appleTouchIcon ?? null,
      socialPreviewImage: settings.socialPreviewImage ?? null,
      socialLinks: {
        vk: settings.socials?.vk ?? fallbackData.siteSettings.socialLinks.vk,
        linkedin:
          settings.socials?.linkedin ??
          fallbackData.siteSettings.socialLinks.linkedin,
        behance:
          settings.socials?.behance ??
          fallbackData.siteSettings.socialLinks.behance,
        pinterest:
          settings.socials?.pinterest ??
          fallbackData.siteSettings.socialLinks.pinterest,
      },
      compareEyebrow:
        settings.compareEyebrow ?? fallbackData.siteSettings.compareEyebrow,
      compareTitle:
        settings.compareTitle ?? fallbackData.siteSettings.compareTitle,
      compareText:
        settings.compareText ?? fallbackData.siteSettings.compareText,
    },
    uiTexts:
      payload?.uiTexts && typeof payload.uiTexts === "object"
        ? payload.uiTexts
        : {},
    homeHero: homePage
      ? {
          eyebrow: homeBlock.eyebrow ?? "",
          title: shouldUseRequiredHomeTitle ? requiredHomeHeroTitle : homeTitle,
          text: homeBlock.subtitle ?? homeBlock.text ?? "",
          linkLabel: homeBlock.linkLabel ?? "",
          linkHref: homeBlock.linkHref ?? "",
        }
      : fallbackData.homeHero,
    homeStory: homeStoryBlock
      ? {
          eyebrow: homeStoryBlock.eyebrow ?? fallbackData.homeStory.eyebrow,
          text: homeStoryBlock.text ?? fallbackData.homeStory.text,
        }
      : fallbackData.homeStory,
    projects: apiProjects,
    servicePageItems: apiServices,
    serviceNavigationGroups: Array.isArray(payload?.serviceNavigationGroups)
      ? normalizeServiceNavigationGroups(
          payload.serviceNavigationGroups,
          locale,
        )
      : fallbackServiceNavigationGroups,
    services: apiServices.map((item: any) => ({
      title: item.title,
      price: item.price,
      text: item.text,
    })),
    newsArticles:
      Array.isArray(payload?.news) && payload.news.length
        ? payload.news.map((article: any) =>
            localizeNewsArticle(article, locale),
          )
        : newsArticles,
    promos:
      Array.isArray(payload?.promos) && payload.promos.length
        ? payload.promos.map((promo: any) => localizePromo(promo, locale))
        : promos,
    testimonials:
      Array.isArray(payload?.reviews) && payload.reviews.length
        ? payload.reviews.map((review: any) => localizeReview(review, locale))
        : testimonials,
    faq:
      Array.isArray(payload?.faqs) && payload.faqs.length
        ? payload.faqs.map((item: any) => localizeFaq(item, locale))
        : faq,
    contactInfo: {
      ...contactInfo,
      phone: settings.phone ?? contactInfo.phone,
      phoneHref: settings.phoneHref ?? contactInfo.phoneHref,
      emails: settings.emails?.length ? settings.emails : contactInfo.emails,
      schedule: settings.schedule ?? contactInfo.schedule,
      address: settings.address ?? contactInfo.address,
      mapSrc: settings.mapSrc ?? contactInfo.mapSrc,
    },
    messengerLinks: {
      ...messengerLinks,
      phoneHref: settings.messengers?.phoneHref ?? messengerLinks.phoneHref,
      max: settings.messengers?.max ?? messengerLinks.max,
      telegram: settings.messengers?.telegram ?? messengerLinks.telegram,
      vk: settings.messengers?.vk ?? messengerLinks.vk,
    },
    animationControls: {
      enabled:
        settings.animations?.enabled ?? fallbackData.animationControls.enabled,
      smoothScroll:
        settings.animations?.smoothScroll ??
        fallbackData.animationControls.smoothScroll,
      pageReveal:
        settings.animations?.pageReveal ??
        fallbackData.animationControls.pageReveal,
    },
    contentPages: apiPages,
    careerVacancies: Array.isArray(payload?.vacancies)
      ? payload.vacancies.map((vacancy: any) =>
          localizeVacancy(vacancy, locale),
        )
      : careerVacancies,
    reviewStats,
    awards:
      Array.isArray(payload?.awards) && payload.awards.length
        ? payload.awards.map((award: any) => localizeAward(award, locale))
        : awards,
    partners:
      Array.isArray(payload?.partners) && payload.partners.length
        ? payload.partners.map((partner: any) =>
            localizeNamedItem(partner, locale),
          )
        : partners,
    menuItems:
      Array.isArray(payload?.menuItems) && payload.menuItems.length
        ? payload.menuItems.map((item: any) => ({
            ...item,
            label:
              localizeString(item, "label", locale, item?.label ?? "") ?? "",
          }))
        : fallbackData.menuItems,
    ready: true,
  };
}

const POLL_INTERVAL = 30_000;

export function CmsProvider({ children }: { children: React.ReactNode }) {
  const [payload, setPayload] = useState<any | null>(null);
  const { i18n } = useTranslation();
  const locale: SiteLocale = i18n.language?.startsWith("en") ? "en" : "ru";

  useEffect(() => {
    const baseUrl = process.env.NEXT_PUBLIC_API_BASE_URL || "/api/v1";
    let cmsAvailable = true;

    const load = (signal: AbortSignal) =>
      fetch(`${baseUrl}/all`, {
        signal,
        headers: { Accept: "application/json" },
        cache: "no-store",
      })
        .then((response) => {
          if (!response.ok)
            throw new Error(`CMS request failed: ${response.status}`);
          return response.json();
        })
        .then((payload) => {
          cmsAvailable = true;
          setPayload(payload);
        })
        .catch((error) => {
          if (error.name !== "AbortError") {
            cmsAvailable = false;

            if (process.env.NODE_ENV !== "production") {
              console.warn("CMS fallback data is active", error);
            }
          }
        });

    const controller = new AbortController();
    load(controller.signal);

    const timer = setInterval(() => {
      if (!cmsAvailable) return;

      const c = new AbortController();
      load(c.signal);
    }, POLL_INTERVAL);

    return () => {
      controller.abort();
      clearInterval(timer);
    };
  }, []);

  const value = useMemo(
    () => (payload ? normalizePayload(payload, locale) : fallbackData),
    [locale, payload],
  );

  return <CmsContext.Provider value={value}>{children}</CmsContext.Provider>;
}

export function useCms() {
  return useContext(CmsContext);
}

export function useCmsText() {
  const { uiTexts } = useCms();
  const { i18n } = useTranslation();
  const locale = i18n.language?.startsWith("en") ? "en" : "ru";

  return (key: string, fallback = "") => {
    const item = uiTexts[key];
    const localized = locale === "en" ? item?.en : item?.ru;
    const translatedFallback = i18n.t(key, { defaultValue: fallback });

    return localized?.trim() || item?.ru?.trim() || translatedFallback;
  };
}

export function useLocalizedField() {
  const { i18n } = useTranslation();
  const locale = i18n.language?.startsWith("en") ? "en" : "ru";

  return <T extends Record<string, any>>(
    item: T | null | undefined,
    base: string,
    fallback = "",
  ) => {
    if (!item) return fallback;

    const suffix = locale === "en" ? "En" : "Ru";
    const localized = item[`${base}${suffix}`];

    if (typeof localized === "string" && localized.trim()) {
      return localized;
    }

    const baseValue = item[base];

    return typeof baseValue === "string" && baseValue.trim()
      ? baseValue
      : fallback;
  };
}

export async function submitLead(payload: Record<string, unknown>) {
  const baseUrl = process.env.NEXT_PUBLIC_API_BASE_URL || "/api/v1";
  const response = await fetch(`${baseUrl}/leads`, {
    method: "POST",
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
    },
    body: JSON.stringify(payload),
  });

  if (!response.ok) {
    throw new Error(`Lead request failed: ${response.status}`);
  }

  return response.json();
}
