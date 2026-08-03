export type ProjectCategory = "Интерьеры" | "Архитектура" | "Ландшафт";
export type FilterCategory = ProjectCategory | "Все";

export type Project = {
  id: number;
  slug: string;
  title: string;
  category: ProjectCategory;
  location: string;
  year: string;
  description: string;
  image: string;
  beforeImage?: string;
  afterImage?: string;
  galleryEyebrow?: string;
  galleryTitle?: string;
  galleryText?: string;
  galleryImages?: string[];
  galleryLabels?: string[];
  isFeatured?: boolean;
  featuredLabel?: string;
  featuredTitle?: string;
  featuredDescription?: string;
  featuredImage?: string;
  featuredGalleryImages?: string[];
};
