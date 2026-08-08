"use client";

import { useState, useEffect, useRef } from "react";
import { submitLead, useCms, useCmsText } from "@/src/cms";
import HeroBackdropSlider from "@/src/components/common/HeroBackdropSlider";
import SectionLabel from "@/src/components/common/SectionLabel";
import { imageFrames, projectImageAt } from "@/src/utils/images";
import CustomSelect from "@/src/components/forms/CustomSelect";

/* ─── icons ─────────────────────────────────────────────────── */
const TgIcon = () => (
  <svg viewBox="0 0 24 24" fill="currentColor" className="h-5 w-5">
    <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z" />
  </svg>
);
const LiIcon = () => (
  <svg viewBox="0 0 24 24" fill="currentColor" className="h-5 w-5">
    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
  </svg>
);
const BeIcon = () => (
  <svg viewBox="0 0 24 24" fill="currentColor" className="h-5 w-5">
    <path d="M6.938 4.503c.702 0 1.34.06 1.92.188.577.13 1.07.33 1.485.61.41.28.733.65.96 1.12.225.47.34 1.05.34 1.73 0 .74-.17 1.36-.507 1.86-.338.5-.837.9-1.502 1.22.906.26 1.576.72 2.022 1.37.448.66.665 1.45.665 2.36 0 .75-.13 1.39-.41 1.93-.28.55-.67 1-1.16 1.35-.48.348-1.05.6-1.68.768-.63.166-1.3.254-2.006.254H0V4.51h6.938zm-.412 5.477c.583 0 1.06-.14 1.425-.42.36-.28.548-.72.548-1.32 0-.33-.06-.61-.175-.837-.115-.226-.27-.408-.47-.54-.2-.13-.43-.222-.69-.278-.26-.054-.535-.08-.823-.08H3.59v3.475h2.936zm.185 5.71c.32 0 .624-.03.906-.093.283-.06.535-.16.75-.3.217-.14.39-.33.517-.57.126-.24.19-.54.19-.9 0-.72-.207-1.23-.62-1.54-.415-.31-.966-.465-1.647-.465H3.59v3.87h3.12zm10.95-3.965c.42.41.646 1.01.646 1.8h-5.24c.03.73.27 1.28.7 1.66.43.38.95.57 1.57.57.59 0 1.07-.15 1.46-.44.38-.29.62-.62.73-.99h2.34c-.37 1.12-.95 1.95-1.73 2.48-.78.54-1.73.8-2.84.8-.77 0-1.47-.13-2.09-.39-.62-.26-1.15-.62-1.58-1.09-.43-.47-.77-1.03-.99-1.68-.23-.65-.35-1.37-.35-2.14 0-.75.12-1.45.37-2.1.25-.64.6-1.19 1.05-1.66.45-.47.99-.84 1.62-1.1.62-.26 1.31-.39 2.06-.39 1.69 0 3.02.58 3.97 1.76.95 1.17 1.42 2.75 1.42 4.72v.39zm-2.01-2.52c-.33-.37-.82-.56-1.46-.56-.43 0-.79.07-1.08.22-.29.15-.53.33-.72.55-.19.22-.32.46-.4.72-.08.26-.13.5-.13.73h4.21c-.07-.72-.3-1.29-.63-1.66zM15.68 8.12h4.45v1.29H15.68V8.12z" />
  </svg>
);
const PinIcon = () => (
  <svg viewBox="0 0 24 24" fill="currentColor" className="h-5 w-5">
    <path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z" />
  </svg>
);
const VkIcon = () => (
  <svg viewBox="0 0 24 24" fill="currentColor" className="h-5 w-5">
    <path d="M12.9 17.2c-5.47 0-8.6-3.75-8.73-9.99h2.74c.09 4.58 2.11 6.52 3.71 6.92V7.21h2.58v3.95c1.58-.17 3.24-1.97 3.8-3.95h2.58a7.62 7.62 0 0 1-3.51 4.98 7.91 7.91 0 0 1 4.11 5.01h-2.84c-.61-1.9-2.13-3.37-4.14-3.57v3.57h-.3Z" />
  </svg>
);

/* ─── social links ───────────────────────────────────────────── */
const socials = [
  { label: "Telegram",  href: "https://t.me/+79879421242",                              icon: <TgIcon />,  color: "#D69A66", bg: "rgba(214,154,102,0.12)",  border: "rgba(214,154,102,0.35)" },
  { label: "LinkedIn",  href: "https://www.linkedin.com/in/3dsmartdesignstudio",        icon: <LiIcon />,  color: "#D69A66", bg: "rgba(214,154,102,0.12)",  border: "rgba(214,154,102,0.35)" },
  { label: "Behance",   href: "https://www.behance.net/3dsmartdesign",                  icon: <BeIcon />,  color: "#D69A66", bg: "rgba(214,154,102,0.12)",  border: "rgba(214,154,102,0.35)" },
  { label: "VK",        href: "https://vk.com/3dsmartdesign",                           icon: <VkIcon />,  color: "#D69A66", bg: "rgba(214,154,102,0.12)",  border: "rgba(214,154,102,0.35)" },
  { label: "Pinterest", href: "https://ru.pinterest.com/3D_SMART_DESIGN_STUDIO/",       icon: <PinIcon />, color: "#D69A66", bg: "rgba(214,154,102,0.12)",    border: "rgba(214,154,102,0.35)"  },
];

const assetPrefix = process.env.NEXT_PUBLIC_BASE_PATH ?? "";

/* ─── animated counter ───────────────────────────────────────── */
function Counter({ to, suffix = "" }: { to: number; suffix?: string }) {
  const [val, setVal] = useState(0);
  const ref = useRef<HTMLSpanElement>(null);
  useEffect(() => {
    const obs = new IntersectionObserver(([entry]) => {
      if (!entry.isIntersecting) return;
      obs.disconnect();
      let start = 0;
      const step = Math.ceil(to / 40);
      const t = setInterval(() => {
        start = Math.min(start + step, to);
        setVal(start);
        if (start >= to) clearInterval(t);
      }, 30);
    }, { threshold: 0.5 });
    if (ref.current) obs.observe(ref.current);
    return () => obs.disconnect();
  }, [to]);
  return <span ref={ref}>{val}{suffix}</span>;
}

/* ─── fade-in-up wrapper ─────────────────────────────────────── */
function FadeUp({ children, delay = 0, className = "" }: { children: React.ReactNode; delay?: number; className?: string }) {
  const [vis, setVis] = useState(false);
  const ref = useRef<HTMLDivElement>(null);
  useEffect(() => {
    const obs = new IntersectionObserver(([e]) => { if (e.isIntersecting) { setVis(true); obs.disconnect(); } }, { threshold: 0.1 });
    if (ref.current) obs.observe(ref.current);
    return () => obs.disconnect();
  }, []);
  return (
    <div
      ref={ref}
      className={className}
      style={{
        opacity: vis ? 1 : 0,
        transform: vis ? "translateY(0)" : "translateY(28px)",
        transition: `opacity 0.65s ease ${delay}ms, transform 0.65s ease ${delay}ms`,
      }}
    >
      {children}
    </div>
  );
}

const inputCls = "w-full rounded-2xl border border-white/10 bg-white/[0.04] px-5 py-4 text-sm text-[#F5F2EC] outline-none transition placeholder:text-white/25 focus:border-[#D69A66]/60 focus:bg-white/[0.07]";

const contactStats = [
  { val: 1, suffix: " час", label: "среднее время ответа" },
  { val: 7, suffix: " стран", label: "география проектов" },
  { val: 10, suffix: " лет", label: "на рынке дизайна" },
];

function ContactMapPanel() {
  const { contactInfo } = useCms();
  const text = useCmsText();
  return (
    <div className="flex flex-col gap-4">
      <div className="overflow-hidden rounded-[2.5rem] border border-white/10" style={{ minHeight: 360 }}>
        <iframe
          title={text("contact.mapTitle", "Карта 3D Smart Design Studio")}
          src={contactInfo.mapSrc}
          className="h-full min-h-[360px] w-full grayscale invert"
          style={{ filter: "invert(1) grayscale(1) brightness(0.85) contrast(1.1)" }}
        />
      </div>
      <div className="rounded-[2rem] border border-white/10 bg-white/[0.025] p-6">
        <div className="flex items-center justify-between">
          <div>
            <p className="text-xs uppercase tracking-[0.3em] text-[#D69A66]">{text("contact.mapEyebrow", "Студия")}</p>
            <p className="mt-1 text-lg font-light text-white">3D Smart Design Studio</p>
            <p className="mt-0.5 text-sm text-white/40">{text("contact.mapLocation", "Самара · работаем удаленно")}</p>
          </div>
          <div className="flex h-12 w-12 items-center justify-center rounded-2xl border border-white/10 bg-white/[0.04]">
            <div className="h-2.5 w-2.5 animate-pulse rounded-full bg-[#D69A66]" />
          </div>
        </div>
      </div>
    </div>
  );
}

function ContactHero() {
  const { contactInfo, projects } = useCms();
  const text = useCmsText();
  const localizedStats = contactStats.map((stat, index) => ({
    ...stat,
    suffix: text(`contact.stats.${index + 1}.suffix`, stat.suffix),
    label: text(`contact.stats.${index + 1}.label`, stat.label),
  }));
  return (
    <section className="relative min-h-screen overflow-hidden px-5 pb-16 pt-28 md:px-10 lg:px-16">
      <HeroBackdropSlider
        slides={[
          { image: projectImageAt(projects, 3), alt: "Студия 3D Smart Design Studio" },
          { image: projectImageAt(projects, 0), alt: "Интерьерный проект" },
          { image: projectImageAt(projects, 4), alt: "Архитектурная визуализация" },
        ]}
      />
      <div className="absolute inset-0 bg-[linear-gradient(90deg,rgba(5,5,5,.96)_0%,rgba(5,5,5,.72)_48%,rgba(5,5,5,.2)_100%)]" />
      <div className="absolute inset-0 bg-[linear-gradient(0deg,#050505_0%,rgba(5,5,5,.4)_34%,transparent_78%)]" />

      <div className="relative z-10 mx-auto flex min-h-[calc(100vh-7rem)] max-w-7xl items-end pb-24">
        <div className="max-w-6xl">
          <p className="mb-5 text-xs uppercase tracking-[0.38em] text-[#D69A66]">Контакты / 3D Smart Design Studio</p>
          <h1 className="max-w-5xl text-[clamp(3rem,6vw,6rem)] font-light leading-[0.94] tracking-normal text-white md:tracking-[-0.04em]">
            {text("contact.titleLine1", "Свяжитесь")} {text("contact.titleLine2", "с нами")}
          </h1>
          <p className="mt-7 max-w-2xl text-lg leading-relaxed text-[#E8E0D8]/85 md:text-xl">
            Расскажите о проекте, сроках и формате работы. Ответим в течение рабочего дня и предложим понятный следующий шаг.
          </p>
          <div className="mt-10 flex flex-wrap gap-3">
            <a
              href="#contact-form"
              className="rounded-full border border-[#D69A66] bg-[#D69A66] px-6 py-4 text-xs uppercase tracking-[0.24em] text-[#050505] transition duration-300 hover:-translate-y-0.5 hover:bg-[#E3AD7B]"
            >
              {text("contact.formTitle", "Оставить заявку")}
            </a>
            <a
              href={contactInfo.phoneHref}
              className="rounded-full border border-white/15 bg-black/25 px-6 py-4 text-xs uppercase tracking-[0.24em] text-white/75 backdrop-blur transition duration-300 hover:border-[#D69A66]/70 hover:text-white"
            >
              Позвонить
            </a>
          </div>
          <div className="mt-12 flex flex-wrap gap-8 border-t border-white/12 pt-8 md:gap-16">
            {localizedStats.map((stat) => (
              <div key={stat.label}>
                <p className="text-3xl font-light tracking-[-0.04em] text-white md:text-4xl">
                  <Counter to={stat.val} suffix={stat.suffix} />
                </p>
                <p className="mt-1 text-xs uppercase tracking-[0.25em] text-white/40">{stat.label}</p>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}

type ContactSectionProps = {
  showIntro?: boolean;
  compactTop?: boolean;
};

export function ContactSection({ showIntro = true, compactTop = false }: ContactSectionProps) {
  const { contactInfo, messengerLinks, servicePageItems, siteSettings } = useCms();
  const text = useCmsText();
  const scheduleLines = contactInfo.schedule
    .split(/\r?\n|,\s*/u)
    .map((item) => item.trim())
    .filter(Boolean);
  const addressLines = contactInfo.address
    .split(/\r?\n/u)
    .map((item) => item.trim())
    .filter(Boolean);
  const [name, setName] = useState("");
  const [contactVal, setContactVal] = useState("");
  const [service, setService] = useState("");
  const [message, setMessage] = useState("");
  const [sent, setSent] = useState(false);
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
  const localizedStats = contactStats.map((stat, index) => ({
    ...stat,
    suffix: text(`contact.stats.${index + 1}.suffix`, stat.suffix),
    label: text(`contact.stats.${index + 1}.label`, stat.label),
  }));

  const handleSubmit = async () => {
    if (!contactVal.trim()) return;

    await submitLead({
      source: "contact-form",
      name: name.trim(),
      contact: contactVal.trim(),
      service,
      message: message.trim(),
    });
    setSent(true);
    setTimeout(() => setSent(false), 4000);
  };

  return (
    <section id="contact" className={`relative overflow-hidden px-5 pb-28 ${compactTop ? "pt-16" : "pt-20"} md:px-10 lg:px-16`}>
      <img
        src={`${assetPrefix}/world-map-png.webp`}
        alt=""
        aria-hidden="true"
        loading="lazy"
        decoding="async"
        className="pointer-events-none absolute right-0 top-8 hidden h-auto w-[50vw] max-w-[760px] opacity-50 md:block"
      />
      <div className="pointer-events-none absolute inset-0 bg-[linear-gradient(90deg,#171511_0%,rgba(23,21,17,.9)_58%,rgba(23,21,17,.62)_100%)]" />

      <div className="relative z-10 mx-auto max-w-7xl">

        {/* ── Hero heading ── */}
        {showIntro && (
          <>
            <FadeUp delay={0}>
              <SectionLabel>{text("contact.label", "Контакты")}</SectionLabel>
            </FadeUp>
            <FadeUp delay={80}>
              <h1 className="text-[clamp(3rem,6vw,6rem)] font-light leading-[0.94] tracking-normal md:tracking-[-0.04em]">
                {text("contact.titleLine1", "Свяжитесь")}<br />
                <span className="text-[#D69A66]">{text("contact.titleLine2", "с нами")}</span>
              </h1>
            </FadeUp>
          </>
        )}

        {/* ── Stats strip ── */}
        {showIntro && (
          <FadeUp delay={200} className="mt-12 flex flex-wrap gap-8 border-t border-white/8 pt-8 md:gap-16">
            {localizedStats.map((s) => (
              <div key={s.label}>
                <p className="text-3xl font-light tracking-[-0.04em] text-white md:text-4xl">
                  <Counter to={s.val} suffix={s.suffix} />
                </p>
                <p className="mt-1 text-xs uppercase tracking-[0.25em] text-white/35">{s.label}</p>
              </div>
            ))}
          </FadeUp>
        )}

        {/* ── Contact info cards ── */}
        <div className={`${showIntro ? "mt-16" : "mt-0"} grid items-stretch gap-4 sm:grid-cols-2 lg:grid-cols-4`}>
          {[
            {
              label: text("contact.phoneLabel", "Телефон"),
              content: (
                <a href={contactInfo.phoneHref} className="text-2xl font-light text-white transition duration-300 hover:text-[#D69A66]">
                  {contactInfo.phone}
                </a>
              ),
              accent: "#D69A66",
              delay: 0,
              icon: (
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" className="h-5 w-5">
                  <path strokeLinecap="round" strokeLinejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                </svg>
              ),
            },
            {
              label: text("contact.emailLabel", "Почта"),
              content: (
                <div className="space-y-1.5">
                  {contactInfo.emails.map((e) => (
                    <a key={e} href={`mailto:${e.split(" - ")[0]}`} className="block text-sm text-white/75 transition hover:text-[#D69A66]">{e}</a>
                  ))}
                </div>
              ),
              accent: "#D69A66",
              delay: 80,
              icon: (
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" className="h-5 w-5">
                  <path strokeLinecap="round" strokeLinejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
              ),
            },
            {
              label: text("contact.scheduleLabel", "График"),
              content: (
                <div className="space-y-1">
                  {scheduleLines.map((line) => (
                    <p key={line} className="text-sm text-white/75">{line}</p>
                  ))}
                </div>
              ),
              accent: "#D69A66",
              delay: 160,
              icon: (
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" className="h-5 w-5">
                  <path strokeLinecap="round" strokeLinejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              ),
            },
            {
              label: text("contact.locationLabel", "Локация"),
              content: (
                <div className="space-y-1">
                  {addressLines.map((line, index) => (
                    <p key={line} className={index === 0 ? "text-sm text-white/75" : "text-xs text-white/35"}>{line}</p>
                  ))}
                </div>
              ),
              accent: "#D69A66",
              delay: 240,
              icon: (
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" className="h-5 w-5">
                  <path strokeLinecap="round" strokeLinejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path strokeLinecap="round" strokeLinejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                </svg>
              ),
            },
          ].map((card) => (
            <FadeUp key={card.label} delay={card.delay} className="h-full">
              <div
                className="group relative flex h-full flex-col overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.03] p-6 transition duration-500 hover:-translate-y-1 hover:border-white/20"
                style={{ boxShadow: "0 0 0 0 transparent", transition: "transform 0.4s ease, border-color 0.4s ease, box-shadow 0.4s ease" }}
                onMouseEnter={(e) => { (e.currentTarget as HTMLDivElement).style.boxShadow = `0 20px 60px ${card.accent}18`; }}
                onMouseLeave={(e) => { (e.currentTarget as HTMLDivElement).style.boxShadow = "none"; }}
              >
                <div className="pointer-events-none absolute right-4 top-4 h-20 w-20 rounded-full opacity-0 blur-2xl transition duration-500 group-hover:opacity-100" style={{ background: card.accent, opacity: 0 }}
                  ref={(el) => { if (el) { el.parentElement?.addEventListener("mouseenter", () => { el.style.opacity = "0.15"; }); el.parentElement?.addEventListener("mouseleave", () => { el.style.opacity = "0"; }); } }} />
                <div className="mb-4 flex items-center gap-2.5">
                  <div className="flex h-9 w-9 items-center justify-center rounded-xl border border-white/10 transition duration-300 group-hover:border-white/20" style={{ color: card.accent }}>
                    {card.icon}
                  </div>
                  <p className="text-[10px] uppercase tracking-[0.3em]" style={{ color: card.accent }}>{card.label}</p>
                </div>
                {card.content}
              </div>
            </FadeUp>
          ))}
        </div>

        {/* ── Social / Messenger row ── */}
        <FadeUp delay={100} className="mt-8">
          <div className="flex flex-wrap gap-3">
            {socialItems.map((s) => (
              <a
                key={s.label}
                href={s.href}
                target="_blank"
                rel="noopener noreferrer"
                className="group flex items-center gap-2.5 rounded-full border border-white/18 bg-white/12 px-4 py-2.5 text-sm text-white/85 transition duration-300 hover:-translate-y-0.5 hover:border-[#D69A66]/35 hover:bg-[#D69A66]/12 hover:text-[#D69A66]"
                style={{
                  boxShadow: "none",
                }}
                onMouseEnter={(e) => { (e.currentTarget as HTMLAnchorElement).style.boxShadow = `0 8px 30px ${s.color}30`; }}
                onMouseLeave={(e) => { (e.currentTarget as HTMLAnchorElement).style.boxShadow = "none"; }}
              >
                <span className="transition duration-300 group-hover:scale-110">{s.icon}</span>
                <span className="text-xs font-medium tracking-[0.06em]">{s.label}</span>
              </a>
            ))}
          </div>
        </FadeUp>

        {/* ── Form ── */}
        <div id="contact-form" className="mt-16 w-full scroll-mt-24 md:scroll-mt-28">
          {/* Form */}
          <FadeUp delay={0}>
            <div className="rounded-[2.5rem] border border-white/10 bg-white/[0.025] p-8 md:p-10">
              <SectionLabel className="mb-2">{text("contact.formLabel", "Написать нам")}</SectionLabel>
              <h2 className="mb-8 text-3xl font-light tracking-[-0.04em] text-white">{text("contact.formTitle", "Оставить заявку")}</h2>
              <div className="grid gap-3">
                <input
                  className={inputCls}
                  placeholder={text("contact.namePlaceholder", "Ваше имя")}
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                />
                <input
                  className={inputCls}
                  placeholder={text("contact.contactPlaceholder", "Телефон или e-mail")}
                  value={contactVal}
                  onChange={(e) => setContactVal(e.target.value)}
                />
                <CustomSelect
                  value={service}
                  onChange={setService}
                  placeholder={text("contact.servicePlaceholder", "Выберите услугу")}
                  options={servicePageItems.map((item) => ({ value: item.title, label: item.title }))}
                />
                <textarea
                  className={`${inputCls} min-h-[120px] resize-none`}
                  placeholder={text("contact.messagePlaceholder", "Коротко о проекте")}
                  value={message}
                  onChange={(e) => setMessage(e.target.value)}
                />
                <button
                  type="button"
                  onClick={handleSubmit}
                  className="relative h-14 overflow-hidden rounded-full text-sm font-medium uppercase tracking-[0.24em] transition duration-300"
                  style={{
                    background: sent ? "rgba(214,154,102,0.2)" : "#D69A66",
                    color: sent ? "#D69A66" : "#050505",
                    border: sent ? "1px solid rgba(214,154,102,0.4)" : "none",
                  }}
                >
                  <span className="relative z-10">{sent ? text("contact.sentButton", "✓ Заявка отправлена") : text("contact.submitButton", "Отправить заявку")}</span>
                  {!sent && (
                    <div
                      className="absolute inset-0 -translate-x-full bg-white/20 skew-x-12 transition duration-500 group-hover:translate-x-full"
                      style={{ transition: "transform 0.6s ease" }}
                    />
                  )}
                </button>
              </div>
            </div>
          </FadeUp>

        </div>

      </div>
    </section>
  );
}

export default function ContactPage() {
  return (
    <div className="page-in">
      <ContactHero />
      <ContactSection showIntro={false} compactTop />
    </div>
  );
}
