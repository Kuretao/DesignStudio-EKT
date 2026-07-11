export function optimizeImageUrl(src?: string | null, width = 1400, quality = 76) {
  const value = src?.trim();
  if (!value) return value ?? "";

  if (!/^https:\/\/images\.unsplash\.com\//i.test(value)) {
    return value;
  }

  try {
    const url = new URL(value);
    const currentWidth = Number(url.searchParams.get("w"));
    const currentQuality = Number(url.searchParams.get("q"));

    url.searchParams.set("auto", "format");
    url.searchParams.set("fit", url.searchParams.get("fit") || "crop");
    url.searchParams.set("w", String(currentWidth ? Math.min(currentWidth, width) : width));
    url.searchParams.set("q", String(currentQuality ? Math.min(currentQuality, quality) : quality));

    return url.toString();
  } catch {
    return value;
  }
}

export function optimizeImageList<T extends string | null | undefined>(
  images: T[],
  width = 1400,
  quality = 76,
) {
  return images.map((image) => optimizeImageUrl(image, width, quality)) as T[];
}
