import { NextResponse } from "next/server";

export const dynamic = "force-dynamic";

const fallbackIcon = "/logo.png";

function getApiUrl() {
  const apiBase = process.env.NEXT_PUBLIC_API_BASE_URL || "/api/v1";

  if (/^https?:\/\//i.test(apiBase)) {
    return `${apiBase.replace(/\/$/u, "")}/all`;
  }

  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || "https://3dsmartdesign.ru";

  return `${siteUrl.replace(/\/$/u, "")}${apiBase.startsWith("/") ? apiBase : `/${apiBase}`}/all`;
}

function absoluteAssetUrl(value: string) {
  if (/^(https?:)?\/\//i.test(value) || value.startsWith("data:")) {
    return value;
  }

  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || "https://3dsmartdesign.ru";

  return `${siteUrl.replace(/\/$/u, "")}${value.startsWith("/") ? value : `/${value}`}`;
}

export async function GET() {
  try {
    const response = await fetch(getApiUrl(), {
      headers: { Accept: "application/json" },
      cache: "no-store",
    });

    if (response.ok) {
      const payload = await response.json();
      const favicon = payload?.settings?.favicon;

      if (typeof favicon === "string" && favicon.trim()) {
        return NextResponse.redirect(absoluteAssetUrl(favicon.trim()), 307);
      }
    }
  } catch {
    // Keep favicon requests resilient if the CMS API is temporarily unavailable.
  }

  return NextResponse.redirect(absoluteAssetUrl(fallbackIcon), 307);
}
