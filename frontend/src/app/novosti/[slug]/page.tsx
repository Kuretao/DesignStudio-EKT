import { notFound } from "next/navigation";
import { newsArticles, type NewsArticle } from "@/src/data";
import NewsArticlePage from "@/src/modules/pages/NewsArticlePage";

export const dynamicParams = true;
export const dynamic = "force-dynamic";

export default async function Page({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const article = await resolveNewsArticle(slug);

  if (!article) notFound();

  return <NewsArticlePage article={article} />;
}

async function resolveNewsArticle(slug: string): Promise<NewsArticle | null> {
  const cmsArticle = await loadCmsNewsArticle(slug);
  if (cmsArticle) return cmsArticle;

  return process.env.NODE_ENV !== "production"
    ? newsArticles.find((article) => article.slug === slug) ?? null
    : null;
}

async function loadCmsNewsArticle(slug: string): Promise<NewsArticle | null> {
  const cmsBaseUrl = process.env.CMS_API_INTERNAL_URL || "http://localhost:8080/api/v1";

  try {
    const response = await fetch(`${cmsBaseUrl}/news/${encodeURIComponent(slug)}`, {
      cache: "no-store",
      headers: { Accept: "application/json" },
    });

    if (!response.ok) return null;

    const article = await response.json();

    if (!article?.slug || !article?.title) return null;

    return {
      slug: String(article.slug),
      title: decodeCmsText(String(article.title)),
      date: decodeCmsText(String(article.date || "")),
      dateIso: String(article.dateIso || ""),
      category: decodeCmsText(String(article.category || "")),
      preview: decodeCmsText(String(article.preview || "")),
      image: normalizeAsset(article.image),
      images: normalizeImageList(article.images?.length ? article.images : article.heroImages),
      readingTime: decodeCmsText(String(article.readingTime || "")),
      body: normalizeBody(article.body),
    } as NewsArticle & { images?: string[] };
  } catch {
    return null;
  }
}

function normalizeAsset(path?: string | null) {
  const value = path?.trim();
  if (!value) return "";

  if (/^(https?:)?\/\//i.test(value) || value.startsWith("data:") || value.startsWith("/")) {
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
        .filter(Boolean),
    ),
  );
}

function normalizeBody(value: unknown): NewsArticle["body"] {
  if (!Array.isArray(value)) return [];

  return value
    .map((block: any) => {
      if (block?.type === "list") {
        const items = Array.isArray(block.items)
          ? block.items.map((item: unknown) => decodeCmsText(String(item))).filter(Boolean)
          : [];

        return { type: "list" as const, items };
      }

      const type: "heading" | "paragraph" =
        block?.type === "heading" ? "heading" : "paragraph";

      return {
        type,
        text: decodeCmsText(String(block?.text || "")),
      };
    })
    .filter((block) => Boolean(block.text || block.items?.length));
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
