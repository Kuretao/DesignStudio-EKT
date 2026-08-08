import type { Metadata } from "next";

const siteUrl = (process.env.NEXT_PUBLIC_SITE_URL ?? "https://3dsmartdesign.ru").replace(
  /\/$/u,
  "",
);

const defaultTitle =
  "Студия дизайна интерьера, архитектуры и ландшафта в Самаре | 3D Smart Design Studio";

const defaultDescription =
  "3D Smart Design Studio: дизайн интерьера, архитектурное проектирование, 3D-визуализация, ландшафтный дизайн, комплектация и авторский надзор в Самаре.";

type CmsSettings = {
  siteName?: string | null;
  seoTitle?: string | null;
  seoDescription?: string | null;
  logo?: string | null;
  favicon?: string | null;
  appleTouchIcon?: string | null;
  socialPreviewImage?: string | null;
  updatedAt?: number | string | null;
};

export function absoluteSiteUrl(value?: string | null) {
  const rawValue = value?.trim();
  if (!rawValue) return undefined;

  if (/^https?:\/\//i.test(rawValue)) {
    return rawValue;
  }

  if (/^\/\//u.test(rawValue)) {
    return `https:${rawValue}`;
  }

  if (rawValue.startsWith("data:")) {
    return rawValue;
  }

  return `${siteUrl}${rawValue.startsWith("/") ? rawValue : `/${rawValue}`}`;
}

function cmsApiUrl() {
  const apiBase =
    process.env.CMS_API_INTERNAL_URL ||
    process.env.NEXT_PUBLIC_API_BASE_URL ||
    "http://localhost:8080/api/v1";

  if (/^https?:\/\//i.test(apiBase)) {
    return `${apiBase.replace(/\/$/u, "")}/all`;
  }

  return `${siteUrl}${apiBase.startsWith("/") ? apiBase : `/${apiBase}`}/all`;
}

export async function loadCmsPayload(): Promise<Record<string, any> | null> {
  try {
    const response = await fetch(cmsApiUrl(), {
      headers: { Accept: "application/json" },
      cache: "no-store",
    });

    if (!response.ok) return null;

    return await response.json();
  } catch {
    return null;
  }
}

export async function loadSiteMetadataSettings(): Promise<CmsSettings | null> {
  const payload = await loadCmsPayload();

  return payload?.settings ?? null;
}

function socialPreviewUrl(settings?: CmsSettings | null) {
  const source = settings?.socialPreviewImage || settings?.logo;
  const version = encodeURIComponent(String(settings?.updatedAt || source || "default-preview"));
  return `${siteUrl}/social-preview-image?v=${version}`;
}

export async function getSiteMetadata(): Promise<Metadata> {
  const settings = await loadSiteMetadataSettings();
  const title = settings?.seoTitle?.trim() || defaultTitle;
  const description = settings?.seoDescription?.trim() || defaultDescription;
  const siteName = settings?.siteName?.trim() || "3D Smart Design Studio";
  const previewImage = socialPreviewUrl(settings);
  const iconVersion = encodeURIComponent(
    String(settings?.updatedAt || settings?.favicon || settings?.logo || "favicon"),
  );
  const favicon = `/site-icon.png?v=${iconVersion}`;
  const appleIcon = `/site-icon.png?apple=1&v=${iconVersion}`;

  return {
    metadataBase: new URL(siteUrl),
    title,
    description,
    keywords: [
      "дизайн интерьера Самара",
      "архитектура Самара",
      "ландшафтный дизайн Самара",
      "3D-визуализация",
      "3D Smart Design Studio",
    ],
    verification: {
      google:
        process.env.GOOGLE_SITE_VERIFICATION ??
        "HlopiXKhbxQ7ylgQsea3aHhSYGyqjgy6Xgq55kBJffc",
      yandex: process.env.YANDEX_SITE_VERIFICATION,
    },
    icons: {
      icon: [{ url: favicon, sizes: "64x64", type: "image/png" }],
      shortcut: [{ url: favicon, sizes: "64x64", type: "image/png" }],
      apple: appleIcon,
    },
    openGraph: {
      type: "website",
      locale: "ru_RU",
      url: siteUrl,
      siteName,
      title,
      description,
      images: previewImage
        ? [
            {
              url: previewImage,
              width: 1200,
              height: 630,
              type: "image/png",
              alt: siteName,
            },
          ]
        : undefined,
    },
    twitter: {
      card: "summary_large_image",
      title,
      description,
      images: previewImage ? [previewImage] : undefined,
    },
  };
}

export async function getSocialPreviewImage() {
  const settings = await loadSiteMetadataSettings();
  return socialPreviewUrl(settings);
}
