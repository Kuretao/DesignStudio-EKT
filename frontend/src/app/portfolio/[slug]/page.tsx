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

  const fallbackProject = projects.find((item) => item.slug === slug);
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
      category: project.category || "Интерьеры",
      location: decodeCmsText(String(project.location || "")),
      year: decodeCmsText(String(project.year || "")),
      description: decodeCmsText(String(project.description || "")),
      image: project.image || projects[0]?.image || "",
      beforeImage: project.beforeImage || undefined,
      afterImage: project.afterImage || undefined,
      galleryEyebrow: project.galleryEyebrow ? decodeCmsText(String(project.galleryEyebrow)) : undefined,
      galleryTitle: project.galleryTitle ? decodeCmsText(String(project.galleryTitle)) : undefined,
      galleryText: project.galleryText ? decodeCmsText(String(project.galleryText)) : undefined,
      galleryImages: normalizeImageList(project.galleryImages),
      galleryLabels: normalizeStringList(project.galleryLabels).map(decodeCmsText),
      isFeatured: Boolean(project.isFeatured),
      featuredLabel: project.featuredLabel ? decodeCmsText(String(project.featuredLabel)) : undefined,
      featuredTitle: project.featuredTitle ? decodeCmsText(String(project.featuredTitle)) : undefined,
      featuredDescription: project.featuredDescription ? decodeCmsText(String(project.featuredDescription)) : undefined,
      featuredImage: project.featuredImage || undefined,
      featuredGalleryImages: normalizeImageList(project.featuredGalleryImages),
    } as Project;
  } catch {
    return null;
  }
}
