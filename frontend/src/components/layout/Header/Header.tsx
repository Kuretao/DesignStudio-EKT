"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { usePathname } from "next/navigation";
import { useTranslation } from "react-i18next";
import { useCms, useCmsText, useLocalizedField } from "@/src/cms";
import { isSeoLandingPath, isStandaloneExperiencePath } from "@/src/data";
import ContactModal from "@/src/modals/ContactModal";

function LogoMark() {
  return (
    <span className="flex min-w-[128px] flex-col items-center gap-1 text-center">
      <svg
        viewBox="-1.12 -1.12 2.24 2.24"
        className="mx-auto block h-7 w-7 shrink-0"
        fill="none"
        aria-hidden="true"
      >
        <g stroke="rgba(255,255,255,0.82)" strokeWidth="0.038">
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
        <g stroke="rgba(255,255,255,0.52)" strokeWidth="0.028">
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
          fill="rgba(7,7,7,0.62)"
          stroke="rgba(255,255,255,0.7)"
          strokeWidth="0.032"
        />
      </svg>
      <span className="text-center text-[9px] font-medium leading-none tracking-[0.24em] text-white">
        3D Smart
        <span className="mt-1 block tracking-[0.2em]">Design Studio</span>
      </span>
    </span>
  );
}

function MenuIcon() {
  return (
    <span
      className="relative block h-[12px] w-[27px] text-white"
      aria-hidden="true"
    >
      <span className="absolute left-0 top-[2px] h-px w-[27px] bg-current" />
      <span className="absolute left-0 top-[10px] h-px w-[27px] bg-current" />
    </span>
  );
}

function CloseIcon() {
  return (
    <span
      className="relative block h-[14px] w-[14px] text-white"
      aria-hidden="true"
    >
      <span className="absolute left-1/2 top-1/2 h-px w-[15px] -translate-x-1/2 -translate-y-1/2 rotate-45 bg-current" />
      <span className="absolute left-1/2 top-1/2 h-px w-[15px] -translate-x-1/2 -translate-y-1/2 -rotate-45 bg-current" />
    </span>
  );
}

export default function Header() {
  const currentPath = usePathname();
  const { menuItems, serviceNavigationGroups, siteSettings } = useCms();
  const { i18n } = useTranslation();
  const text = useCmsText();
  const localize = useLocalizedField();
  const [modalOpen, setModalOpen] = useState(false);
  const [menuOpen, setMenuOpen] = useState(false);
  const [menuView, setMenuView] = useState<
    "main" | "services" | "serviceGroup"
  >("main");
  const [activeServiceGroupId, setActiveServiceGroupId] = useState<
    string | null
  >(null);

  useEffect(() => {
    const onKey = (event: KeyboardEvent) =>
      event.key === "Escape" && setMenuOpen(false);
    document.addEventListener("keydown", onKey);
    document.body.style.overflow = menuOpen ? "hidden" : "";
    return () => {
      document.removeEventListener("keydown", onKey);
      document.body.style.overflow = "";
    };
  }, [menuOpen]);

  const closeMenu = () => {
    setMenuOpen(false);
    setMenuView("main");
    setActiveServiceGroupId(null);
  };
  const openMenu = () => {
    setMenuView("main");
    setActiveServiceGroupId(null);
    setMenuOpen(true);
  };
  const openContact = () => {
    closeMenu();
    setModalOpen(true);
  };
  const changeLanguage = (locale: "ru" | "en") => {
    i18n.changeLanguage(locale);
    closeMenu();
  };

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

  const serviceHrefs = new Set([
    "/services",
    ...localizedServiceNavigationGroups.flatMap((group) => [
      group.href,
      ...group.items.map((item) => item.href),
    ]),
  ]);
  const menuByHref = new Map(
    localizedMenuItems
      .filter((item) => !serviceHrefs.has(item.href))
      .map((item) => [item.href, item]),
  );
  const mainMenuLinks = [
    menuByHref.get("/o-nas") ?? { href: "/o-nas", label: text("menu.about", "О нас") },
    menuByHref.get("/portfolio") ?? { href: "/portfolio", label: text("menu.portfolio", "Портфолио") },
    { href: "/services", label: text("header.servicesRoot", "Услуги") },
    menuByHref.get("/blog") ?? { href: "/blog", label: text("menu.blog", "Блог") },
    menuByHref.get("/kontakty") ?? { href: "/kontakty", label: text("menu.contacts", "Контакты") },
  ];
  const primaryMenuLinks = [...mainMenuLinks];
  const isServicesActive =
    currentPath === "/services" ||
    Array.from(serviceHrefs).some((href) => currentPath === href);
  const activeServiceGroup =
    localizedServiceNavigationGroups.find(
      (group) => group.id === activeServiceGroupId,
    ) ??
    localizedServiceNavigationGroups.find(
      (group) =>
        group.href === currentPath ||
        group.items.some((item) => item.href === currentPath),
    ) ??
    localizedServiceNavigationGroups[0];

  const menuLinkCls = (path: string) =>
    `block text-right text-[13px] font-semibold uppercase tracking-[0.17em] transition-[opacity,transform] duration-[820ms] ease-[cubic-bezier(.19,1,.22,1)] hover:text-[#D69A66] md:text-[17px] ${
      currentPath === path || (path === "/services" && isServicesActive)
        ? "text-white/78"
        : "text-white/40"
    }`;
  const menuItemStyle = (index: number) => ({
    opacity: menuOpen ? 1 : 0,
    transform: menuOpen ? "translate3d(0, 0, 0)" : "translate3d(-96px, 0, 0)",
    transitionDelay: menuOpen ? `${220 + index * 70}ms` : "0ms",
    transitionProperty: "opacity, transform",
  });

  if (isSeoLandingPath(currentPath) || isStandaloneExperiencePath(currentPath))
    return null;

  return (
    <>
      <header className="fixed left-0 right-0 top-0 z-50 border-b border-white/16 bg-[#050505]/10 px-5 text-white backdrop-blur-[8px] md:px-10 lg:px-12">
        <div className="mx-auto grid h-[72px] max-w-[1680px] grid-cols-[1fr_auto_1fr] items-center md:h-[76px]">
          <button
            type="button"
            onClick={openMenu}
            aria-expanded={menuOpen}
            aria-controls="rolls-menu"
            className="flex w-[142px] cursor-pointer items-center gap-[18px] justify-self-start text-[10px] font-semibold uppercase tracking-[0.24em] transition duration-300 hover:text-white/68"
          >
            <MenuIcon />
            <span>{text("header.menu", "Меню")}</span>
          </button>

          <Link
            href="/"
            aria-label={siteSettings.siteName}
            onClick={closeMenu}
            className="flex min-w-[128px] flex-col items-center gap-0.5 justify-self-center text-center transition duration-300 hover:opacity-75"
          >
            {siteSettings.logoSmall || siteSettings.logo ? (
              <>
                <img
                  src={siteSettings.logoSmall ?? siteSettings.logo ?? ""}
                  alt=""
                  aria-hidden="true"
                  className="block h-auto max-h-12 w-auto max-w-[164px] object-contain"
                />
                <span className="text-[9px] font-medium leading-none tracking-[0.24em] text-white">
                  3D Smart
                  <span className="mt-1 block tracking-[0.2em]">Design Studio</span>
                </span>
              </>
            ) : (
              <LogoMark />
            )}
          </Link>

          <button
            type="button"
            onClick={openContact}
            className="w-[142px] justify-self-end text-right text-[10px] font-semibold uppercase tracking-[0.24em] transition duration-300 hover:text-white/68"
          >
            {text("header.contact", "Написать нам")}
          </button>
        </div>
      </header>

      <div
        className={`fixed inset-0 z-[55] bg-black/5 backdrop-blur-[8px] transition-opacity duration-[650ms] ease-[cubic-bezier(.77,0,.18,1)] ${
          menuOpen
            ? "pointer-events-auto opacity-100"
            : "pointer-events-none opacity-0"
        }`}
        onClick={closeMenu}
      />

      <section
        id="rolls-menu"
        className={`fixed left-0 top-0 z-[70] h-dvh w-full max-w-[720px] overflow-hidden bg-[#050505]/18 text-white shadow-[38px_0_120px_rgba(0,0,0,0.18)] backdrop-blur-[18px] transition-transform duration-[760ms] ease-[cubic-bezier(.77,0,.18,1)] ${
          menuOpen ? "translate-x-0" : "-translate-x-full"
        }`}
      >
        <button
          type="button"
          onClick={closeMenu}
          className="absolute left-6 top-8 z-10 flex cursor-pointer items-center gap-4 text-[11px] font-bold uppercase tracking-[0.18em] text-white transition hover:text-white/66 md:left-[75px] md:top-[50px]"
        >
          <CloseIcon />
          {text("header.close", "Закрыть")}
        </button>

        <div
          className="flex h-dvh touch-pan-y flex-col items-end overflow-y-auto overscroll-contain px-6 pb-28 pt-24 [-webkit-overflow-scrolling:touch] sm:px-8 md:pb-32 md:pr-[92px] md:pt-28 lg:pr-[118px]"
          data-native-scroll
        >
          <nav
            aria-label={text("header.mainMenuAria", "Основное меню")}
            className="relative w-full max-w-[520px]"
          >
            <div
              className={`flex flex-col items-end gap-[20px] transition duration-500 md:gap-[25px] ${
                menuView === "main"
                  ? "translate-x-0 opacity-100"
                  : "-translate-x-8 pointer-events-none absolute inset-x-0 top-0 opacity-0"
              }`}
            >
              {primaryMenuLinks.map((item, index) =>
                item.href === "/services" ? (
                  <button
                    key="services-menu"
                    type="button"
                    onClick={() => setMenuView("services")}
                    className={`${menuLinkCls("/services")} flex w-full items-center justify-end gap-4 text-right`}
                    style={menuItemStyle(index)}
                  >
                    <span>{item.label}</span>
                    <span className="text-[#D69A66]">→</span>
                  </button>
                ) : (
                  <Link
                    key={item.href}
                    href={item.href}
                    onClick={closeMenu}
                    className={menuLinkCls(item.href)}
                    style={menuItemStyle(index)}
                  >
                    {item.label}
                  </Link>
                ),
              )}
            </div>

            <div
              className={`grid gap-4 text-right transition duration-500 ${
                menuView === "services"
                  ? "translate-x-0 opacity-100"
                  : "pointer-events-none absolute inset-x-0 top-0 translate-x-8 opacity-0"
              }`}
            >
              <button
                type="button"
                onClick={() => setMenuView("main")}
                className="justify-self-end text-[11px] font-bold uppercase tracking-[0.22em] text-white/42 transition hover:text-[#D69A66]"
              >
                {text("header.backToMenu", "← Меню")}
              </button>
              <div>
                <Link
                  href="/services"
                  onClick={closeMenu}
                  className="block text-[13px] font-semibold uppercase tracking-[0.18em] text-white/78 transition hover:text-[#D69A66] md:text-[17px]"
                >
                  {text("header.servicesRoot", "Услуги")}
                </Link>
                <p className="mt-2 max-w-sm justify-self-end text-sm leading-relaxed text-white/38">
                  {text(
                    "header.servicesDescription",
                    "Категории сгруппированы по составу работ. Внутри каждого направления - посадочные страницы услуг.",
                  )}
                </p>
              </div>
              <div className="grid gap-2.5">
                {localizedServiceNavigationGroups.map((group) => {
                  const active =
                    group.href === currentPath ||
                    group.items.some((item) => item.href === currentPath);

                  return (
                    <button
                      key={group.id}
                      type="button"
                      onClick={() => {
                        setActiveServiceGroupId(group.id);
                        setMenuView("serviceGroup");
                      }}
                      className={`group rounded-[1.25rem] border px-4 py-3.5 text-right transition md:px-5 ${
                        active
                          ? "border-[#D69A66]/45 bg-[#D69A66]/10"
                          : "border-white/10 bg-white/[0.035] hover:border-white/24 hover:bg-white/[0.055]"
                      }`}
                    >
                      <span className="flex items-center justify-end gap-3 text-[12px] font-semibold uppercase tracking-[0.14em] text-white/76 md:text-sm">
                        {group.title}
                        <span className="shrink-0 text-[#D69A66] transition group-hover:translate-x-1">
                          →
                        </span>
                      </span>
                      <span className="mt-1.5 block text-sm leading-relaxed text-white/34">
                        {group.description}
                      </span>
                    </button>
                  );
                })}
              </div>
            </div>

            <div
              className={`grid gap-4 text-right transition duration-500 ${
                menuView === "serviceGroup"
                  ? "translate-x-0 opacity-100"
                  : "pointer-events-none absolute inset-x-0 top-0 translate-x-8 opacity-0"
              }`}
            >
              <button
                type="button"
                onClick={() => setMenuView("services")}
                className="justify-self-end text-[11px] font-bold uppercase tracking-[0.22em] text-white/42 transition hover:text-[#D69A66]"
              >
                {text("header.backToServices", "← Услуги")}
              </button>

              {activeServiceGroup && (
                <>
                  <div>
                    <Link
                      href={activeServiceGroup.href}
                      onClick={closeMenu}
                      className={`block text-[13px] font-semibold uppercase tracking-[0.18em] transition hover:text-[#D69A66] md:text-[17px] ${
                        currentPath === activeServiceGroup.href
                          ? "text-white/82"
                          : "text-white/72"
                      }`}
                    >
                      {activeServiceGroup.title}
                    </Link>
                    <p className="mt-2 max-w-sm justify-self-end text-sm leading-relaxed text-white/38">
                      {activeServiceGroup.description}
                    </p>
                  </div>

                  <div className="grid gap-2">
                    {activeServiceGroup.items.map((child, index) => (
                      <Link
                        key={child.href}
                        href={child.href}
                        onClick={closeMenu}
                        className={`group flex items-center justify-between gap-4 rounded-[1.15rem] border px-4 py-3.5 text-left transition md:px-5 ${
                          currentPath === child.href
                            ? "border-[#D69A66]/45 bg-[#D69A66]/10 text-white"
                            : "border-white/10 bg-white/[0.035] text-white/58 hover:border-white/24 hover:text-white"
                        }`}
                      >
                        <span className="text-[11px] uppercase tracking-[0.2em] text-[#D69A66]">
                          0{index + 1}
                        </span>
                        <span className="flex-1 text-right text-sm leading-snug [overflow-wrap:anywhere]">
                          {child.label}
                        </span>
                      </Link>
                    ))}
                  </div>
                </>
              )}
            </div>
          </nav>

          <div
            className={`mt-10 flex w-full max-w-[520px] justify-start gap-5 text-[10px] font-bold uppercase tracking-[0.22em] transition-[opacity,transform] duration-[620ms] ease-[cubic-bezier(.77,0,.18,1)] ${
              menuOpen
                ? "translate-x-0 opacity-100 delay-[760ms]"
                : "-translate-x-8 opacity-0"
            }`}
          >
            <button
              type="button"
              onClick={() => changeLanguage("ru")}
              className={`transition hover:text-white/78 ${i18n.language === "ru" ? "text-white/78" : "text-white/34"}`}
            >
              {text("header.languageRu", "РУ")}
            </button>
            <button
              type="button"
              onClick={() => changeLanguage("en")}
              className={`transition hover:text-white/78 ${i18n.language === "en" ? "text-white/78" : "text-white/34"}`}
            >
              {text("header.languageEn", "ENG")}
            </button>
          </div>
        </div>
      </section>

      <ContactModal open={modalOpen} onClose={() => setModalOpen(false)} />
    </>
  );
}
