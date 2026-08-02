"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useCms, useCmsText, useLocalizedField } from "@/src/cms";
import { isSeoLandingPath, isStandaloneExperiencePath } from "@/src/data";

const socials = [
  {
    label: "VK",
    href: "https://vk.com/3dsmartdesign",
    color: "#D69A66",
    bg: "rgba(214,154,102,0.12)",
    border: "rgba(214,154,102,0.35)",
    icon: (
      <svg viewBox="0 0 24 24" fill="currentColor" className="h-5 w-5">
        <path d="M12.9 17.2c-5.47 0-8.6-3.75-8.73-9.99h2.74c.09 4.58 2.11 6.52 3.71 6.92V7.21h2.58v3.95c1.58-.17 3.24-1.97 3.8-3.95h2.58a7.62 7.62 0 0 1-3.51 4.98 7.91 7.91 0 0 1 4.11 5.01h-2.84c-.61-1.9-2.13-3.37-4.14-3.57v3.57h-.3Z" />
      </svg>
    ),
  },
  {
    label: "Telegram",
    href: "https://t.me/+79879421242",
    color: "#D69A66",
    bg: "rgba(214,154,102,0.12)",
    border: "rgba(214,154,102,0.35)",
    icon: (
      <svg viewBox="0 0 24 24" fill="currentColor" className="h-5 w-5">
        <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z" />
      </svg>
    ),
  },
  {
    label: "LinkedIn",
    href: "https://www.linkedin.com/in/3dsmartdesignstudio",
    color: "#D69A66",
    bg: "rgba(214,154,102,0.12)",
    border: "rgba(214,154,102,0.35)",
    icon: (
      <svg viewBox="0 0 24 24" fill="currentColor" className="h-5 w-5">
        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
      </svg>
    ),
  },
  {
    label: "Behance",
    href: "https://www.behance.net/3dsmartdesign",
    color: "#D69A66",
    bg: "rgba(214,154,102,0.12)",
    border: "rgba(214,154,102,0.35)",
    icon: (
      <svg viewBox="0 0 24 24" fill="currentColor" className="h-5 w-5">
        <path d="M6.938 4.503c.702 0 1.34.06 1.92.188.577.13 1.07.33 1.485.61.41.28.733.65.96 1.12.225.47.34 1.05.34 1.73 0 .74-.17 1.36-.507 1.86-.338.5-.837.9-1.502 1.22.906.26 1.576.72 2.022 1.37.448.66.665 1.45.665 2.36 0 .75-.13 1.39-.41 1.93-.28.55-.67 1-1.16 1.35-.48.348-1.05.6-1.68.768-.63.166-1.3.254-2.006.254H0V4.51h6.938zm-.412 5.477c.583 0 1.06-.14 1.425-.42.36-.28.548-.72.548-1.32 0-.33-.06-.61-.175-.837-.115-.226-.27-.408-.47-.54-.2-.13-.43-.222-.69-.278-.26-.054-.535-.08-.823-.08H3.59v3.475h2.936zm.185 5.71c.32 0 .624-.03.906-.093.283-.06.535-.16.75-.3.217-.14.39-.33.517-.57.126-.24.19-.54.19-.9 0-.72-.207-1.23-.62-1.54-.415-.31-.966-.465-1.647-.465H3.59v3.87h3.12zm10.95-3.965c.42.41.646 1.01.646 1.8h-5.24c.03.73.27 1.28.7 1.66.43.38.95.57 1.57.57.59 0 1.07-.15 1.46-.44.38-.29.62-.62.73-.99h2.34c-.37 1.12-.95 1.95-1.73 2.48-.78.54-1.73.8-2.84.8-.77 0-1.47-.13-2.09-.39-.62-.26-1.15-.62-1.58-1.09-.43-.47-.77-1.03-.99-1.68-.23-.65-.35-1.37-.35-2.14 0-.75.12-1.45.37-2.1.25-.64.6-1.19 1.05-1.66.45-.47.99-.84 1.62-1.1.62-.26 1.31-.39 2.06-.39 1.69 0 3.02.58 3.97 1.76.95 1.17 1.42 2.75 1.42 4.72v.39zm-2.01-2.52c-.33-.37-.82-.56-1.46-.56-.43 0-.79.07-1.08.22-.29.15-.53.33-.72.55-.19.22-.32.46-.4.72-.08.26-.13.5-.13.73h4.21c-.07-.72-.3-1.29-.63-1.66zM15.68 8.12h4.45v1.29H15.68V8.12z" />
      </svg>
    ),
  },
  {
    label: "Pinterest",
    href: "https://ru.pinterest.com/3D_SMART_DESIGN_STUDIO/",
    color: "#D69A66",
    bg: "rgba(214,154,102,0.12)",
    border: "rgba(214,154,102,0.35)",
    icon: (
      <svg viewBox="0 0 24 24" fill="currentColor" className="h-5 w-5">
        <path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z" />
      </svg>
    ),
  },
];

function FooterLogo() {
  return (
    <svg
      viewBox="-1.12 -1.12 2.24 2.24"
      className="h-10 w-10 shrink-0"
      fill="none"
    >
      <g stroke="rgba(255,255,255,0.55)" strokeWidth="0.038">
        {[0, 60, 120, 180, 240, 300].map((a) => (
          <circle
            key={a}
            cx={0.52}
            cy={0}
            r={0.52}
            transform={`rotate(${a})`}
          />
        ))}
        <animateTransform
          attributeName="transform"
          type="rotate"
          from="0"
          to="360"
          dur="32s"
          repeatCount="indefinite"
        />
      </g>
      <g stroke="rgba(255,255,255,0.28)" strokeWidth="0.028">
        {[30, 90, 150, 210, 270, 330].map((a) => (
          <circle
            key={a}
            cx={0.34}
            cy={0}
            r={0.34}
            transform={`rotate(${a})`}
          />
        ))}
        <animateTransform
          attributeName="transform"
          type="rotate"
          from="0"
          to="-360"
          dur="22s"
          repeatCount="indefinite"
        />
      </g>
      <circle
        cx={0}
        cy={0}
        r={0.19}
        fill="#0c0b09"
        stroke="rgba(255,255,255,0.42)"
        strokeWidth="0.032"
      />
    </svg>
  );
}

export default function Footer() {
  const {
    contactInfo,
    serviceNavigationGroups,
    menuItems,
    messengerLinks,
    siteSettings,
  } = useCms();
  const text = useCmsText();
  const localize = useLocalizedField();
  const pathname = usePathname();
  const localizedMenuItems = menuItems.map((item) => ({
    ...item,
    label: localize(item, "label", item.label),
  }));
  const localizedServiceNavigationGroups = serviceNavigationGroups.map((group) => ({
    ...group,
    title: localize(group, "title", group.title),
    description: localize(group, "description", group.description),
    items: group.items.map((item) => ({
      ...item,
      label: localize(item, "label", item.label),
    })),
  }));
  const menuByHref = new Map(localizedMenuItems.map((item) => [item.href, item]));
  const footerNavigation = [
    menuByHref.get("/o-nas") ?? { href: "/o-nas", label: text("menu.about", "О нас") },
    menuByHref.get("/portfolio") ?? { href: "/portfolio", label: text("menu.portfolio", "Портфолио") },
    menuByHref.get("/services") ?? { href: "/services", label: text("header.servicesRoot", "Услуги") },
    menuByHref.get("/blog") ?? { href: "/blog", label: text("menu.blog", "Блог") },
    menuByHref.get("/kontakty") ?? { href: "/kontakty", label: text("menu.contacts", "Контакты") },
    { href: "/karera", label: text("footer.career", "Карьера") },
    { href: "/partneram", label: text("footer.partners", "Партнёрам") },
  ];
  const socialItems = socials.map((social) =>
    social.label === "VK"
      ? { ...social, href: siteSettings.socialLinks.vk ?? messengerLinks.vk }
      : social.label === "Telegram"
        ? { ...social, href: messengerLinks.telegram }
        : social.label === "LinkedIn"
          ? { ...social, href: siteSettings.socialLinks.linkedin ?? social.href }
          : social.label === "Behance"
            ? { ...social, href: siteSettings.socialLinks.behance ?? social.href }
            : social.label === "Pinterest"
              ? { ...social, href: siteSettings.socialLinks.pinterest ?? social.href }
              : social,
  ).filter((social) => Boolean(social.href));
  const scheduleLines = contactInfo.schedule
    .split(/\r?\n|,\s*/u)
    .map((item) => item.trim())
    .filter(Boolean);
  const addressLines = contactInfo.address
    .split(/\r?\n/u)
    .map((item) => item.trim())
    .filter(Boolean);
  const footerLinkCls =
    "group flex items-start gap-2 text-sm leading-snug text-white/45 transition-colors duration-200 hover:text-white";

  if (isSeoLandingPath(pathname) || isStandaloneExperiencePath(pathname))
    return null;

  return (
    <footer className="premium-footer relative overflow-hidden border-t border-white/10">
      {/* ── Main grid ── */}
      <div className="mx-auto max-w-7xl px-5 pb-12 pt-16 md:px-10 lg:px-16">
        <div className="grid gap-12 lg:grid-cols-[1.1fr_1.75fr_0.82fr_1.08fr]">
          {/* ── Col 1 · Brand ── */}
          <div>
            <div className="mb-5 flex items-center gap-3">
              {siteSettings.logo ? (
                <img
                  src={siteSettings.logo}
                  alt={siteSettings.siteName}
                  className="block max-h-14 max-w-[220px] object-contain"
                />
              ) : (
                <FooterLogo />
              )}
              <div>
                <p className="text-sm uppercase tracking-[0.32em] text-white">
                  {siteSettings.siteName}
                </p>
                <p className="text-[10px] uppercase tracking-[0.22em] text-white/35">
                  {text("footer.brandKind", "Студия")}
                </p>
              </div>
            </div>
            <p className="max-w-xs text-sm leading-relaxed text-white/45">
              {text(
                "footer.brandText",
                "Студия дизайна интерьеров, архитектурного проектирования, 3D-визуализации и ландшафтного дизайна.",
              )}
            </p>

            {/* Social pills with icons */}
            <div className="mt-6 flex flex-wrap gap-2">
              {socialItems.map((s) => (
                <a
                  key={s.label}
                  href={s.href}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="group flex items-center gap-2.5 rounded-full border border-white/18 bg-white/12 px-4 py-2.5 text-sm text-white/85 transition duration-300 hover:-translate-y-0.5 hover:border-[#D69A66]/35 hover:bg-[#D69A66]/12 hover:text-[#D69A66]"
                >
                  <span className="transition duration-300 group-hover:scale-110">
                    {s.icon}
                  </span>
                  <span className="text-xs font-medium tracking-[0.06em]">
                    {s.label}
                  </span>
                </a>
              ))}
            </div>
          </div>

          <div className="lg:col-span-1">
            <p className="mb-5 text-[10px] uppercase tracking-[0.38em] text-[#D69A66]">
              {text("footer.services", "Услуги")}
            </p>
            <div className="grid gap-x-10 gap-y-7 sm:grid-cols-2">
              {localizedServiceNavigationGroups.map((group) => (
                <div
                  key={group.id}
                  className="border-t border-white/10 pt-4 first:border-t-0 first:pt-0 sm:[&:nth-child(-n+2)]:border-t-0 sm:[&:nth-child(-n+2)]:pt-0"
                >
                  <Link
                    href={group.href}
                    className="group flex items-start justify-between gap-3 text-sm font-medium leading-snug text-white/82 transition hover:text-[#D69A66]"
                  >
                    <span className="[overflow-wrap:anywhere]">
                      {group.title}
                    </span>
                    <span className="mt-0.5 shrink-0 text-[#D69A66] transition group-hover:translate-x-0.5">
                      →
                    </span>
                  </Link>
                  <ul className="mt-3 space-y-2.5">
                    {group.items.map((child) => {
                      const serviceTitle = child.label;

                      return (
                        <li key={child.href}>
                          <Link
                            href={child.href}
                            className="group flex items-start gap-2 text-[13px] leading-snug text-white/38 transition-colors duration-200 hover:text-white/78"
                          >
                            <span className="mt-2 h-px w-2.5 shrink-0 bg-white/14 transition-all duration-200 group-hover:w-4 group-hover:bg-[#D69A66]" />
                            <span className="[overflow-wrap:anywhere]">
                              {serviceTitle}
                            </span>
                          </Link>
                        </li>
                      );
                    })}
                  </ul>
                </div>
              ))}
            </div>
          </div>

          {/* ── Col 3 · Навигация ── */}
          <div>
            <p className="mb-5 text-[10px] uppercase tracking-[0.38em] text-[#D69A66]">
              {text("footer.sections", "Разделы")}
            </p>
            <ul className="space-y-2.5">
              {footerNavigation.map((l) => (
                <li key={l.href}>
                  <Link href={l.href} className={footerLinkCls}>
                    <span className="mt-2 h-px w-3 shrink-0 bg-white/15 transition-all duration-200 group-hover:w-4 group-hover:bg-[#D69A66]" />
                    {l.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* ── Col 4 · Контакты ── */}
          <div>
            <p className="mb-5 text-[10px] uppercase tracking-[0.38em] text-[#D69A66]">
              {text("footer.contacts", "Контакты")}
            </p>
            <div className="space-y-5">
              {/* Phone */}
              <div>
                <p className="mb-1 text-[10px] uppercase tracking-[0.22em] text-white/25">
                  {text("footer.phone", "Телефон")}
                </p>
                <a
                  href={contactInfo.phoneHref}
                  className="text-base font-light text-white transition-colors duration-200 hover:text-[#D69A66]"
                >
                  {contactInfo.phone}
                </a>
              </div>

              {/* Email */}
              <div>
                <p className="mb-1 text-[10px] uppercase tracking-[0.22em] text-white/25">
                  {text("footer.email", "Почта")}
                </p>
                <div className="space-y-1">
                  {contactInfo.emails.map((e) => (
                    <a
                      key={e}
                      href={`mailto:${e.split(" - ")[0]}`}
                      className="block text-sm text-white/60 transition-colors duration-200 hover:text-[#D69A66]"
                    >
                      {e}
                    </a>
                  ))}
                </div>
              </div>

              {/* Schedule */}
              <div>
                <p className="mb-1 text-[10px] uppercase tracking-[0.22em] text-white/25">
                  {text("footer.schedule", "График")}
                </p>
                {scheduleLines.map((line) => (
                  <p key={line} className="text-sm text-white/55">{line}</p>
                ))}
              </div>

              {/* Location */}
              <div>
                <p className="mb-1 text-[10px] uppercase tracking-[0.22em] text-white/25">
                  {text("footer.address", "Адрес")}
                </p>
                {addressLines.map((line, index) => (
                  <p key={line} className={index === 0 ? "text-sm text-white/55" : "text-xs text-white/30"}>{line}</p>
                ))}
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* ── Bottom bar ── */}
      <div className="border-t border-white/8">
        <div className="mx-auto flex max-w-7xl flex-col gap-4 px-5 py-6 md:flex-row md:items-center md:justify-between md:px-10 lg:px-16">
          <div className="flex flex-col gap-1 text-xs text-white/30 md:flex-row md:items-center md:gap-4">
            <span>© 2026 3D Smart Design Studio</span>
            <span className="hidden md:block h-3 w-px bg-white/15" />
            <span>{text("footer.owner", "ИП Шакинене Екатерина Игоревна")}</span>
          </div>
          <div className="flex flex-wrap gap-4 text-xs text-white/30">
            <Link
              href="/politika-konfidencialnosti"
              className="transition-colors duration-200 hover:text-white/60"
            >
              {text("footer.privacy", "Политика конфиденциальности")}
            </Link>
            <Link
              href="/user/agreement"
              className="transition-colors duration-200 hover:text-white/60"
            >
              {text("footer.agreement", "Пользовательское соглашение")}
            </Link>
          </div>
        </div>
      </div>
    </footer>
  );
}
