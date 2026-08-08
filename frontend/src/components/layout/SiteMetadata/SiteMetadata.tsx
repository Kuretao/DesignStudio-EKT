"use client";

import { useEffect } from "react";
import { useCms } from "@/src/cms";

function upsertMeta(selector: string, attributes: Record<string, string>) {
  let element = document.head.querySelector<HTMLMetaElement>(selector);

  if (!element) {
    element = document.createElement("meta");
    document.head.appendChild(element);
  }

  Object.entries(attributes).forEach(([name, value]) => element?.setAttribute(name, value));
}

function upsertLink(rel: string, href: string) {
  let element = document.head.querySelector<HTMLLinkElement>(`link[rel="${rel}"]`);

  if (!element) {
    element = document.createElement("link");
    element.rel = rel;
    document.head.appendChild(element);
  }

  element.href = href;
}

function absoluteUrl(value: string) {
  if (/^(https?:)?\/\//i.test(value) || value.startsWith("data:")) {
    return value;
  }

  return `${window.location.origin}${value.startsWith("/") ? value : `/${value}`}`;
}

export default function SiteMetadata() {
  const { siteSettings } = useCms();

  useEffect(() => {
    document.title = siteSettings.seoTitle || siteSettings.siteName;

    if (siteSettings.seoDescription) {
      upsertMeta('meta[name="description"]', {
        name: "description",
        content: siteSettings.seoDescription,
      });
    }

    if (siteSettings.socialPreviewImage) {
      upsertMeta('meta[property="og:image"]', {
        property: "og:image",
        content: absoluteUrl(siteSettings.socialPreviewImage),
      });
    }

    const iconVersion = encodeURIComponent(
      String(siteSettings.updatedAt || siteSettings.favicon || siteSettings.logo || "favicon"),
    );
    const favicon = `/site-icon.png?v=${iconVersion}`;
    upsertLink("icon", favicon);
    upsertLink("shortcut icon", favicon);

    upsertLink("apple-touch-icon", `/site-icon.png?apple=1&v=${iconVersion}`);
  }, [siteSettings]);

  return null;
}
