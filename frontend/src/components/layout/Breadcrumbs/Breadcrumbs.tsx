"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useCms, useCmsText, useLocalizedField } from "@/src/cms";
import { isSeoLandingPath, isStandaloneExperiencePath } from "@/src/data";

type Crumb = {
  label: string;
  href: string;
};

const routeLabels: Record<string, string> = {
  "/akcii-i-skidki": "Акции",
  "/blog": "Блог",
  "/nagrady-i-diplomy": "Награды и дипломы",
  "/kontakty": "Контакты",
  "/karera": "Карьера",
  "/novosti": "Новости",
  "/o-nas": "О нас",
  "/otzyvy-o-nas": "Отзывы",
  "/partneram": "Партнерам",
  "/politika-konfidencialnosti": "Политика конфиденциальности",
  "/portfolio": "Портфолио",
  "/services": "Услуги",
  "/user/agreement": "Пользовательское соглашение",
};

function slugToLabel(slug: string) {
  return slug
    .split("-")
    .filter(Boolean)
    .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
    .join(" ");
}

export default function Breadcrumbs() {
  const pathname = usePathname();
  const { newsArticles, projects, serviceNavigationGroups, servicePageItems } = useCms();
  const text = useCmsText();
  const localize = useLocalizedField();

  if (
    pathname === "/" ||
    isSeoLandingPath(pathname) ||
    isStandaloneExperiencePath(pathname)
  )
    return null;

  const normalizedPath = pathname.replace(/\/$/, "") || "/";
  const localizedServiceNavigationGroups = serviceNavigationGroups.map((group) => ({
    ...group,
    title: localize(group, "title", group.title),
    items: group.items.map((item) => ({
      ...item,
      label: localize(item, "label", item.label),
    })),
  }));
  const crumbs: Crumb[] = [{ label: text("breadcrumbs.home", "Главная"), href: "/" }];
  const serviceGroup = localizedServiceNavigationGroups.find((group) =>
    group.href === normalizedPath || group.items.some((item) => item.href === normalizedPath),
  );
  const serviceChild = serviceGroup?.items.find((item) => item.href === normalizedPath);

  if (serviceGroup) {
    crumbs.push({ label: text("header.servicesRoot", "Услуги"), href: "/services" });
    crumbs.push({ label: serviceGroup.title, href: serviceGroup.href });

    if (serviceChild && serviceChild.href !== serviceGroup.href) {
      const serviceTitle = servicePageItems.find((item) => `/${item.id}` === serviceChild.href)?.title ?? serviceChild.label;
      crumbs.push({ label: serviceTitle, href: serviceChild.href });
    }
  } else if (normalizedPath.startsWith("/portfolio/")) {
    const slug = normalizedPath.split("/").filter(Boolean).at(-1) ?? "";
    const project = projects.find((item) => item.slug === slug);
    crumbs.push({ label: "Портфолио", href: "/portfolio" });
    crumbs.push({ label: project?.title ?? slugToLabel(slug), href: normalizedPath });
  } else if (normalizedPath.startsWith("/novosti/")) {
    const slug = normalizedPath.split("/").filter(Boolean).at(-1) ?? "";
    const article = newsArticles.find((item) => item.slug === slug);
    crumbs.push({ label: "Новости", href: "/novosti" });
    crumbs.push({ label: article?.title ?? slugToLabel(slug), href: normalizedPath });
  } else {
    crumbs.push({ label: routeLabels[normalizedPath] ?? slugToLabel(normalizedPath.split("/").filter(Boolean).join("-")), href: normalizedPath });
  }

  return (
    <nav
      aria-label={text("breadcrumbs.aria", "Хлебные крошки")}
      itemScope
      itemType="https://schema.org/BreadcrumbList"
      className="fixed left-0 right-0 top-[72px] z-[45] border-b border-white/10 bg-[#050505]/58 px-5 text-white backdrop-blur-[10px] md:top-[76px] md:px-10 lg:px-12"
    >
      <ol className="mx-auto flex min-h-10 max-w-[1680px] items-center gap-2 overflow-x-auto py-2 text-[11px] leading-snug text-white/54 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        {crumbs.map((crumb, index) => {
          const isLast = index === crumbs.length - 1;

          return (
            <li
              key={`${crumb.href}-${index}`}
              itemProp="itemListElement"
              itemScope
              itemType="https://schema.org/ListItem"
              className="flex items-center gap-2"
            >
              {isLast ? (
                <span itemProp="name" className="block max-w-[58vw] whitespace-normal break-words text-white/78 md:max-w-none md:whitespace-nowrap">
                  {crumb.label}
                </span>
              ) : (
                <Link href={crumb.href} itemProp="item" className="whitespace-nowrap transition hover:text-[#D69A66]">
                  <span itemProp="name">{crumb.label}</span>
                </Link>
              )}
              <meta itemProp="position" content={`${index + 1}`} />
              {!isLast && <span className="text-white/24">→</span>}
            </li>
          );
        })}
      </ol>
    </nav>
  );
}
