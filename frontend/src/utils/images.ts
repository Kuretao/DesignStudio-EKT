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

export function normalizeMediaPath(src?: string | null) {
  const value = src?.trim();
  if (!value) return "";

  if (
    /^(https?:)?\/\//i.test(value) ||
    value.startsWith("data:") ||
    value.startsWith("/")
  ) {
    return value;
  }

  return `/storage/${value.replace(/^\/+/, "")}`;
}

export function optimizeImageList<T extends string | null | undefined>(
  images: T[],
  width = 1400,
  quality = 76,
) {
  return images.map((image) => optimizeImageUrl(image, width, quality)) as T[];
}

const fallbackImages = [
  "/images/cms/country-house-interior.webp",
  "/images/cms/river-park-interior.webp",
  "/images/cms/office-space.webp",
  "/images/cms/greenwood-house.webp",
  "/images/cms/landscape-garden.webp",
  "/images/cms/villa-exterior.webp",
];

type ImageLike = {
  image?: string | null;
  beforeImage?: string | null;
  afterImage?: string | null;
};

export function fallbackImage(index = 0) {
  return fallbackImages[Math.abs(index) % fallbackImages.length];
}

export function projectImageAt(
  projects: ImageLike[] | undefined,
  index = 0,
  field: keyof ImageLike = "image",
) {
  const project = projects?.[index];
  const fieldImage = project?.[field];
  const directImage = project?.image;
  const firstAvailable = projects?.find((item) => item?.[field] || item?.image);

  return (
    fieldImage ||
    directImage ||
    firstAvailable?.[field] ||
    firstAvailable?.image ||
    fallbackImage(index)
  );
}

export function imageFrames(
  images: Array<string | null | undefined>,
  fallbackIndex = 0,
) {
  const frames = images.filter((image): image is string => Boolean(image));

  return frames.length ? frames : [fallbackImage(fallbackIndex)];
}

export function cmsImageFrames(
  value: string,
  fallbackFrames: Array<string | null | undefined>,
) {
  const configuredFrames = value
    .split(/\r?\n/u)
    .map((frame) => normalizeMediaPath(frame))
    .filter(Boolean);

  return imageFrames(configuredFrames.length ? configuredFrames : fallbackFrames);
}
