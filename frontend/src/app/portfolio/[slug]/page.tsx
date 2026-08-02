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

export default async function Page({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const project = await resolveProject(slug);

  if (!project) notFound();

  return <PortfolioProjectClient initialProject={project} />;
}

async function resolveProject(slug: string): Promise<Project | null> {
  const fallbackProject = projects.find((item) => item.slug === slug);
  if (fallbackProject) return fallbackProject;

  return loadCmsProject(slug);
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
      title: String(project.title),
      category: project.category || "Интерьеры",
      location: project.location || "",
      year: project.year || "",
      description: project.description || "",
      image: project.image || projects[0]?.image || "",
      beforeImage: project.beforeImage || undefined,
      afterImage: project.afterImage || undefined,
    } as Project;
  } catch {
    return null;
  }
}
