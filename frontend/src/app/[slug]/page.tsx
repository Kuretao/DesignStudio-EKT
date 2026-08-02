import { notFound } from "next/navigation";
import type { Metadata } from "next";
import {
  contentPages,
  getServiceLandingCopy,
  seoLandingPageItems,
  servicePageItems,
} from "@/src/data";
import ServiceDetailPage from "@/src/modules/pages/ServiceDetailPage";
import SeoLandingPage from "@/src/modules/pages/SeoLandingPage";
import ContentPage from "@/src/modules/pages/ContentPage";

// Slugs that have their own dedicated app routes - exclude from [slug].
const DEDICATED_ROUTES = new Set(["novosti", "akcii-i-skidki", "otzyvy-o-nas"]);

export const dynamicParams = true;
export const dynamic = "force-dynamic";

export function generateStaticParams() {
  return [
    ...servicePageItems.map((item) => ({ slug: item.id })),
    ...seoLandingPageItems.map((item) => ({ slug: item.id })),
    ...contentPages
      .filter((page) => !DEDICATED_ROUTES.has(page.id))
      .map((page) => ({ slug: page.id })),
  ];
}

export async function generateMetadata({
  params,
}: {
  params: Promise<{ slug: string }>;
}): Promise<Metadata> {
  const { slug } = await params;
  const servicePage = servicePageItems.find((item) => item.id === slug);
  const seoLandingPage = seoLandingPageItems.find((item) => item.id === slug);
  const cmsServicePage = await loadCmsService(slug, servicePage);

  if (cmsServicePage || servicePage) {
    const item = cmsServicePage ?? servicePage!;
    const copy = getServiceLandingCopy(item);

    return {
      title: `${item.title || copy.offerTitle} | 3D Smart Design Studio`,
      description: copy.seoDescription,
      keywords: copy.seoKeywords,
      openGraph: {
        title: item.title || copy.offerTitle,
        description: copy.seoDescription,
        images: [item.image],
      },
    };
  }

  if (seoLandingPage) {
    const copy = getServiceLandingCopy(seoLandingPage);

    return {
      title: `${copy.offerTitle} | 3D Smart Design Studio`,
      description: copy.seoDescription,
      keywords: copy.seoKeywords,
      robots: {
        index: true,
        follow: true,
      },
      openGraph: {
        title: copy.offerTitle,
        description: copy.seoDescription,
        images: [seoLandingPage.image],
      },
    };
  }

  const contentPage = contentPages.find((page) => page.id === slug);
  if (contentPage) {
    return {
      title: `${contentPage.title} | 3D Smart Design Studio`,
      description: contentPage.text,
    };
  }

  return {
    title: "3D Smart Design Studio",
  };
}

export default async function Page({
  params,
}: {
  params: Promise<{ slug: string }>;
}) {
  const { slug } = await params;

  const servicePage = servicePageItems.find((item) => item.id === slug);
  const cmsServicePage = await loadCmsService(slug, servicePage);
  if (cmsServicePage) return <ServiceDetailPage item={cmsServicePage} />;

  if (servicePage) return <ServiceDetailPage item={servicePage} />;

  const seoLandingPage = seoLandingPageItems.find((item) => item.id === slug);
  if (seoLandingPage) return <SeoLandingPage item={seoLandingPage} />;

  const contentPage = contentPages.find((page) => page.id === slug);
  if (contentPage) return <ContentPage page={contentPage} />;

  const cmsPage = await loadCmsPage(slug);
  if (cmsPage) return <ContentPage page={cmsPage} />;

  notFound();
}

async function loadCmsService(
  slug: string,
  fallback?: (typeof servicePageItems)[number],
) {
  const cmsBaseUrl =
    process.env.CMS_API_INTERNAL_URL || "http://localhost:8080/api/v1";

  try {
    const response = await fetch(
      `${cmsBaseUrl}/services/${encodeURIComponent(slug)}`,
      {
        cache: "no-store",
        headers: { Accept: "application/json" },
      },
    );

    if (!response.ok) return null;

    return normalizeCmsService(await response.json(), fallback);
  } catch {
    return null;
  }
}

async function loadCmsPage(slug: string) {
  const cmsBaseUrl =
    process.env.CMS_API_INTERNAL_URL || "http://localhost:8080/api/v1";

  try {
    const response = await fetch(
      `${cmsBaseUrl}/pages/${encodeURIComponent(slug)}`,
      {
        cache: "no-store",
        headers: { Accept: "application/json" },
      },
    );

    if (!response.ok) return null;

    const page = await response.json();
    const blocks = Array.isArray(page.blocks)
      ? page.blocks.map(normalizeBlock)
      : [];
    const hero = blocks.find((block: any) => block.type === "hero") ?? {};

    return {
      id: page.id ?? page.slug,
      slug: page.slug,
      title: page.title,
      template: page.template,
      body: page.body,
      eyebrow: hero.eyebrow ?? "3D Smart Design Studio",
      text: hero.text ?? hero.subtitle ?? page.seoDescription ?? "",
      image: hero.image,
      images: hero.images ?? [],
      blocks,
    };
  } catch {
    return null;
  }
}

function normalizeCmsService(
  service: any,
  fallback?: (typeof servicePageItems)[number],
) {
  const images = normalizeImageList(
    service?.images?.length ? service.images : service?.heroImages,
  );
  const image = images[0] ?? normalizeAsset(service?.image) ?? fallback?.image ?? "";

  return {
    ...(fallback ?? {}),
    ...service,
    id: service?.id ?? service?.slug ?? fallback?.id ?? "",
    slug: service?.slug ?? service?.id ?? fallback?.id ?? "",
    title: service?.title ?? fallback?.title ?? "",
    eyebrow: service?.eyebrow ?? fallback?.eyebrow ?? "",
    text: service?.text ?? fallback?.text ?? "",
    image,
    images,
    price: service?.price ?? fallback?.price ?? "",
    timeline: service?.timeline ?? fallback?.timeline ?? "",
    deliverables:
      Array.isArray(service?.deliverables) && service.deliverables.length
        ? service.deliverables
        : fallback?.deliverables ?? [],
    benefits:
      Array.isArray(service?.benefits) && service.benefits.length
        ? service.benefits
        : fallback?.benefits ?? [],
    process:
      Array.isArray(service?.process) && service.process.length
        ? service.process
        : fallback?.process ?? [],
    deliverableImages: normalizeImageList(service?.deliverableImages),
  };
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
        .filter((item): item is string => Boolean(item)),
    ),
  );
}

function normalizeBlock(block: any) {
  const images = normalizeImageList(
    block?.images?.length ? block.images : block?.image,
  );

  return {
    type: block?.type ?? "text",
    eyebrow: block?.eyebrow ?? null,
    title: block?.title ?? null,
    subtitle: block?.subtitle ?? null,
    text: block?.text ?? null,
    image: images[0] ?? null,
    images,
    linkLabel: block?.linkLabel ?? null,
    linkHref: block?.linkHref ?? null,
  };
}
