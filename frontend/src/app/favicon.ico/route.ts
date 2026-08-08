import { createElement } from "react";
import { ImageResponse } from "next/og";

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
  let icon = fallbackIcon;

  try {
    const response = await fetch(getApiUrl(), {
      headers: { Accept: "application/json" },
      cache: "no-store",
    });

    if (response.ok) {
      const payload = await response.json();
      const favicon = payload?.settings?.favicon;
      const logoSmall = payload?.settings?.logoSmall;
      const logo = payload?.settings?.logo;
      const source = favicon || logoSmall || logo;

      if (typeof source === "string" && source.trim()) {
        icon = source.trim();
      }
    }
  } catch {
    // Keep favicon requests resilient if the CMS API is temporarily unavailable.
  }

  const image = absoluteAssetUrl(icon);

  return new ImageResponse(
    createElement(
      "div",
      {
        style: {
          display: "flex",
          alignItems: "center",
          justifyContent: "center",
          width: "100%",
          height: "100%",
          padding: "10px",
          background: "#0c0b09",
        },
      },
      createElement("img", {
        src: image,
        alt: "",
        style: {
          width: "100%",
          height: "100%",
          objectFit: "contain",
        },
      }),
    ),
    {
      width: 64,
      height: 64,
      headers: {
        "Content-Type": "image/png",
        "Cache-Control": "public, max-age=0, s-maxage=3600",
      },
    },
  );
}
