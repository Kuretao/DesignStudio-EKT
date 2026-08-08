import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { projects } from "@/src/data";
import type { Project } from "@/src/types";
import PortfolioProjectClient from "@/src/modules/pages/PortfolioPage/PortfolioProjectClient";
import { absoluteSiteUrl, getSocialPreviewImage } from "../../siteMetadata";

export const dynamicParams = true;
export const dynamic = "force-dynamic";

export function generateStaticParams() {
  return projects.map((project) => ({ slug: project.slug }));
}

export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }): Promise<Metadata> {
  const { slug } = await params;
  const project = await resolveProject(slug);

  if (!project) {
    return {
      title: "Проект не найден | 3D Smart Design Studio",
    };
  }

  const previewImage = absoluteSiteUrl(project.image) ?? (await getSocialPreviewImage());

  return {
    title: `${project.title} | Портфолио 3D Smart Design Studio`,
    description: project.description,
    openGraph: {
      title: project.title,
      description: project.description,
      images: previewImage ? [{ url: previewImage, width: 1200, height: 630 }] : undefined,
    },
    twitter: {
      card: "summary_large_image",
      title: project.title,
      description: project.description,
      images: previewImage ? [previewImage] : undefined,
    },
  };
}

function normalizeAsset(path?: string | null) {
  const value = path?.trim();
  if (!value) return "";

  if (/^(https?:)?\/\//i.test(value) || value.startsWith("data:") || value.startsWith("/")) {
    return value;
  }

  return `/storage/${value.replace(/^\/+/, "")}`;
}

function normalizeStringList(value: unknown): string[] {
  const items = Array.isArray(value)
    ? value
    : typeof value === "string"
      ? value.split(/\r?\n/u)
      : [];

  return items
    .map((item) => (typeof item === "string" ? item.trim() : ""))
    .filter(Boolean);
}

function normalizeImageList(value: unknown): string[] {
  return Array.from(new Set(normalizeStringList(value).map(normalizeAsset).filter(Boolean)));
}

function decodeCmsText(value: string) {
  let decoded = value;

  for (let index = 0; index < 5; index += 1) {
    const next = decoded
      .replace(/&quot;|&#34;/giu, '"')
      .replace(/&#039;|&apos;/giu, "'")
      .replace(/&laquo;/giu, "«")
      .replace(/&raquo;/giu, "»")
      .replace(/&nbsp;/giu, " ")
      .replace(/&amp;/giu, "&");

    if (next === decoded) break;
    decoded = next;
  }

  return decoded;
}

export default async function Page({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const project = await resolveProject(slug);

  if (!project) notFound();

  return <PortfolioProjectClient initialProject={project} />;
}

async function resolveProject(slug: string): Promise<Project | null> {
  const cmsProject = await loadCmsProject(slug);
  if (cmsProject) return cmsProject;

  const fallbackProject = process.env.NODE_ENV !== "production"
    ? projects.find((item) => item.slug === slug)
    : null;
  if (fallbackProject) return fallbackProject;

  return null;
}

async function loadCmsProject(slug: string): Promise<Project | null> {
  const cmsBaseUrl = process.env.CMS_API_INTERNAL_URL || "http://localhost:8080/api/v1";

  try {
    const response = await fetch(`${cmsBaseUrl}/projects/${encodeURIComponent(slug)}`, {
      cache: "no-store",
      headers: { Accept: "application/json" },
    });

    if (!response.ok) return null;

    const project = await response.json();

    if (!project?.slug || !project?.title) return null;

    return {
      id: Number(project.id ?? 0),
      slug: String(project.slug),
      title: decodeCmsText(String(project.title)),
      category: decodeCmsText(String(project.category || "Интерьеры")),
      location: decodeCmsText(String(project.location || "")),
      year: decodeCmsText(String(project.year || "")),
      filterSquare: project.filterSquare ? decodeCmsText(String(project.filterSquare)) : null,
      filterTone: project.filterTone ? decodeCmsText(String(project.filterTone)) : null,
      description: decodeCmsText(String(project.description || "")),
      image: normalizeAsset(project.image),
      heroImages: normalizeImageList(project.heroImages),
      beforeImage: normalizeAsset(project.beforeImage) || undefined,
      afterImage: normalizeAsset(project.afterImage) || undefined,
      caseIntro: project.caseIntro ? decodeCmsText(String(project.caseIntro)) : undefined,
      galleryEyebrow: project.galleryEyebrow ? decodeCmsText(String(project.galleryEyebrow)) : undefined,
      galleryTitle: project.galleryTitle ? decodeCmsText(String(project.galleryTitle)) : undefined,
      galleryText: project.galleryText ? decodeCmsText(String(project.galleryText)) : undefined,
      galleryImages: normalizeImageList(project.galleryImages),
      galleryLabels: normalizeStringList(project.galleryLabels).map(decodeCmsText),
      isFeatured: Boolean(project.isFeatured),
      isSelected: Boolean(project.isSelected),
      isVirtualTour: Boolean(project.isVirtualTour),
      virtualTour: normalizeVirtualTour(project.virtualTour),
      selectedCards: Array.isArray(project.selectedCards)
        ? project.selectedCards.map((card: any) => ({
            title: card.title ? decodeCmsText(String(card.title)) : undefined,
            titleRu: card.titleRu ? decodeCmsText(String(card.titleRu)) : undefined,
            titleEn: card.titleEn ? decodeCmsText(String(card.titleEn)) : undefined,
            text: card.text ? decodeCmsText(String(card.text)) : undefined,
            textRu: card.textRu ? decodeCmsText(String(card.textRu)) : undefined,
            textEn: card.textEn ? decodeCmsText(String(card.textEn)) : undefined,
          }))
        : [],
      featuredLabel: project.featuredLabel ? decodeCmsText(String(project.featuredLabel)) : undefined,
      featuredTitle: project.featuredTitle ? decodeCmsText(String(project.featuredTitle)) : undefined,
      featuredDescription: project.featuredDescription ? decodeCmsText(String(project.featuredDescription)) : undefined,
      featuredImage: normalizeAsset(project.featuredImage) || undefined,
      featuredGalleryImages: normalizeImageList(project.featuredGalleryImages),
      storyChapters: Array.isArray(project.storyChapters)
        ? project.storyChapters.map((chapter: any) => ({
            title: chapter.title ? decodeCmsText(String(chapter.title)) : undefined,
            titleRu: chapter.titleRu ? decodeCmsText(String(chapter.titleRu)) : undefined,
            titleEn: chapter.titleEn ? decodeCmsText(String(chapter.titleEn)) : undefined,
            text: chapter.text ? decodeCmsText(String(chapter.text)) : undefined,
            textRu: chapter.textRu ? decodeCmsText(String(chapter.textRu)) : undefined,
            textEn: chapter.textEn ? decodeCmsText(String(chapter.textEn)) : undefined,
          }))
        : [],
      deliverables: normalizeStringList(project.deliverables).map(decodeCmsText),
    } as Project;
  } catch {
    return null;
  }
}

function normalizeVirtualTour(value: unknown): Project["virtualTour"] {
  if (!value || typeof value !== "object") return undefined;

  const tour = value as Record<string, any>;
  const scenes = Array.isArray(tour.scenes)
    ? tour.scenes
        .map((scene: any, index: number) => {
          const panorama = normalizeAsset(scene?.panorama);
          if (!panorama) return null;

          return {
            ...scene,
            id: String(scene?.id || `scene-${index + 1}`),
            title: scene?.title ? decodeCmsText(String(scene.title)) : null,
            titleRu: scene?.titleRu ? decodeCmsText(String(scene.titleRu)) : null,
            titleEn: scene?.titleEn ? decodeCmsText(String(scene.titleEn)) : null,
            label: scene?.label ? decodeCmsText(String(scene.label)) : null,
            panorama,
            yaw: Number(scene?.yaw || 0),
            pitch: Number(scene?.pitch || 0),
            plan: scene?.plan && typeof scene.plan === "object"
              ? {
                  x: Number(scene.plan.x ?? 50),
                  y: Number(scene.plan.y ?? 50),
                  width: Number(scene.plan.width ?? 34),
                  height: Number(scene.plan.height ?? 30),
                }
              : undefined,
            next: Array.isArray(scene?.next)
              ? scene.next.map((link: any) => ({
                  ...link,
                  id: String(link?.id || ""),
                  text: link?.text ? decodeCmsText(String(link.text)) : null,
                  textRu: link?.textRu ? decodeCmsText(String(link.textRu)) : null,
                  textEn: link?.textEn ? decodeCmsText(String(link.textEn)) : null,
                })).filter((link: any) => link.id)
              : [],
          };
        })
        .filter((scene: any): scene is NonNullable<typeof scene> => Boolean(scene))
    : [];

  return {
    ...tour,
    eyebrow: tour.eyebrow ? decodeCmsText(String(tour.eyebrow)) : null,
    eyebrowRu: tour.eyebrowRu ? decodeCmsText(String(tour.eyebrowRu)) : null,
    eyebrowEn: tour.eyebrowEn ? decodeCmsText(String(tour.eyebrowEn)) : null,
    title: tour.title ? decodeCmsText(String(tour.title)) : null,
    titleRu: tour.titleRu ? decodeCmsText(String(tour.titleRu)) : null,
    titleEn: tour.titleEn ? decodeCmsText(String(tour.titleEn)) : null,
    text: tour.text ? decodeCmsText(String(tour.text)) : null,
    textRu: tour.textRu ? decodeCmsText(String(tour.textRu)) : null,
    textEn: tour.textEn ? decodeCmsText(String(tour.textEn)) : null,
    buttonLabel: tour.buttonLabel ? decodeCmsText(String(tour.buttonLabel)) : null,
    buttonLabelRu: tour.buttonLabelRu ? decodeCmsText(String(tour.buttonLabelRu)) : null,
    buttonLabelEn: tour.buttonLabelEn ? decodeCmsText(String(tour.buttonLabelEn)) : null,
    scenes,
  };
}
