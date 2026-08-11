"use client";

import { useEffect, useRef } from "react";
import { usePathname } from "next/navigation";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import { useCms, useCmsText } from "@/src/cms";
import { isStandaloneExperiencePath } from "@/src/data";
import { GlobalStyle, Noise } from "@/src/ui";

gsap.registerPlugin(ScrollTrigger);

const floatingMessengers = [
  {
    label: "VK",
    icon: "vk",
  },
  {
    label: "MAX",
    icon: "max",
  },
  {
    label: "TG",
    icon: "telegram",
  },
];

function MessengerIcon({ icon }: { icon: string }) {
  if (icon === "vk") {
    return (
      <svg viewBox="0 0 24 24" fill="currentColor" className="h-5 w-5" aria-hidden="true">
        <path d="M12.9 17.2c-5.47 0-8.6-3.75-8.73-9.99h2.74c.09 4.58 2.11 6.52 3.71 6.92V7.21h2.58v3.95c1.58-.17 3.24-1.97 3.8-3.95h2.58a7.62 7.62 0 0 1-3.51 4.98 7.91 7.91 0 0 1 4.11 5.01h-2.84c-.61-1.9-2.13-3.37-4.14-3.57v3.57h-.3Z" />
      </svg>
    );
  }

  if (icon === "telegram") {
    return (
      <svg viewBox="0 0 24 24" fill="none" className="h-5 w-5" aria-hidden="true">
        <path
          d="M20.2 4.8 3.9 11.1c-1.1.44-1.1 1.05-.2 1.33l4.18 1.3 1.6 4.9c.2.56.1.78.68.78.44 0 .64-.2.9-.44l2.16-2.1 4.5 3.32c.82.45 1.42.22 1.63-.76l2.95-13.9c.3-1.2-.45-1.74-1.78-1.23Z"
          fill="currentColor"
        />
        <path d="m8.08 13.48 9.68-6.1c.45-.28.86-.13.52.17l-8.28 7.48-.32 3.42" stroke="#0c0b09" strokeOpacity="0.5" strokeWidth="0.8" strokeLinecap="round" strokeLinejoin="round" />
      </svg>
    );
  }

  return (
    <svg viewBox="0 0 24 24" fill="none" className="h-5 w-5" aria-hidden="true">
      <path
        d="M5.2 15.8V8.2c0-1.55 1.72-2.48 3-1.62l3.8 2.55 3.8-2.55c1.28-.86 3 .07 3 1.62v7.6c0 1.55-1.72 2.48-3 1.62L12 14.87l-3.8 2.55c-1.28.86-3-.07-3-1.62Z"
        stroke="currentColor"
        strokeWidth="1.8"
        strokeLinejoin="round"
      />
      <path d="M8.15 9.55 12 12.1l3.85-2.55M8.15 14.45 12 11.9l3.85 2.55" stroke="currentColor" strokeWidth="1.45" strokeLinecap="round" />
    </svg>
  );
}

export default function ClientShell({ children }: { children: React.ReactNode }) {
  const mainRef = useRef<HTMLDivElement | null>(null);
  const { animationControls, messengerLinks, ready } = useCms();
  const text = useCmsText();
  const pathname = usePathname();
  const isStandaloneExperience = isStandaloneExperiencePath(pathname);

  useEffect(() => {
    const cleanups: Array<() => void> = [];
    if (!animationControls.enabled || !ready) return;

    const ctx = gsap.context(() => {
      const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
      const desktopViewport = window.matchMedia("(min-width: 900px)").matches;
      const mobileViewport = window.matchMedia("(max-width: 767px)").matches;
      const tabletViewport = window.matchMedia("(min-width: 768px) and (max-width: 1199px)").matches;

      ScrollTrigger.refresh();

      const motionProgress = mainRef.current?.querySelector<HTMLElement>(".motion-progress");

      if (motionProgress) {
        gsap.set(motionProgress, { scaleX: 0, transformOrigin: "left center" });
        gsap.to(motionProgress, {
          scaleX: 1,
          ease: "none",
          scrollTrigger: {
            start: 0,
            end: () => ScrollTrigger.maxScroll(window),
            scrub: 0.2,
          },
        });
      }

      if (reducedMotion) return;

      const motionCurtain = mainRef.current?.querySelector<HTMLElement>(".motion-curtain");
      if (motionCurtain) {
        gsap
          .timeline({ defaults: { ease: "expo.inOut" } })
          .set(motionCurtain, { autoAlpha: 1, scaleX: 1, transformOrigin: "center" })
          .to(motionCurtain, { autoAlpha: 0, scaleX: 0, duration: 1.1 });
      }

      const header = mainRef.current?.querySelector<HTMLElement>("header");
      if (header) {
        gsap.fromTo(
          header,
          { y: -18, autoAlpha: 0 },
          { y: 0, autoAlpha: 1, duration: 0.9, delay: 0.22, ease: "power3.out" },
        );
      }

      if (animationControls.pageReveal) {
        const revealTargets = gsap.utils.toArray<HTMLElement>(
          ".section-in, .review-card, section:not(.snap-section):not(.style-lab-section) > .mx-auto",
        );

        if (revealTargets.length) {
          gsap.set(revealTargets, { autoAlpha: 0, y: mobileViewport ? 14 : 24 });
          ScrollTrigger.batch(revealTargets, {
            start: mobileViewport ? "top 96%" : "top 88%",
            once: true,
            interval: 0.035,
            batchMax: 10,
            onEnter: (batch) => {
              gsap.to(batch, {
                autoAlpha: 1,
                y: 0,
                duration: mobileViewport ? 0.34 : 0.46,
                stagger: mobileViewport ? 0.02 : 0.035,
                ease: "power2.out",
                overwrite: "auto",
                clearProps: "opacity,visibility,transform",
              });
            },
          });
        }

        if (pathname === "/") {
          const homeFlow = gsap.utils.toArray<HTMLElement>(".home-flow-reveal");
          if (homeFlow.length) {
            gsap.set(homeFlow, { autoAlpha: 0, y: mobileViewport ? 18 : 26 });
            ScrollTrigger.batch(homeFlow, {
              start: mobileViewport ? "top 97%" : "top 92%",
              once: true,
              interval: 0.04,
              batchMax: 4,
              onEnter: (batch) => {
                gsap.to(batch, {
                  autoAlpha: 1,
                  y: 0,
                  duration: mobileViewport ? 0.38 : 0.5,
                  stagger: mobileViewport ? 0.025 : 0.045,
                  ease: "power2.out",
                  overwrite: "auto",
                  clearProps: "opacity,visibility,transform",
                });
              },
            });
          }

        }

        const revealCards = gsap.utils.toArray<HTMLElement>("[data-reveal-card]");

        if (revealCards.length) {
          gsap.set(revealCards, { autoAlpha: 0, y: mobileViewport ? 12 : 18, scale: 0.992 });
          ScrollTrigger.batch(revealCards, {
            start: mobileViewport ? "top 96%" : "top 91%",
            once: true,
            interval: 0.03,
            batchMax: mobileViewport ? 4 : 12,
            onEnter: (batch) => {
              gsap.to(batch, {
                autoAlpha: 1,
                y: 0,
                scale: 1,
                duration: mobileViewport ? 0.32 : 0.4,
                stagger: mobileViewport ? 0.025 : 0.03,
                ease: "power2.out",
                overwrite: "auto",
                clearProps: "opacity,visibility,transform",
              });
            },
          });
        }
      }

      if (pathname !== "/") {
        gsap.fromTo(
          ".page-in",
          { autoAlpha: 0, y: 34 },
          { autoAlpha: 1, y: 0, duration: 0.9, delay: 0.16, ease: "power3.out", clearProps: "opacity,visibility,transform" },
        );
      }

      if (pathname === "/") {
        const heroTimeline = gsap.timeline();

        if (desktopViewport) {
          heroTimeline.fromTo(
              ".hero-video",
              { scale: 1.12, filter: "brightness(0.68) contrast(1.1) saturate(0.9)" },
              { scale: 1, filter: "brightness(0.9) contrast(1.02) saturate(0.88)", duration: 1.35, ease: "power3.out" },
              0,
            );
        }

        heroTimeline
          .from(
            ".hero-reveal",
            {
              y: desktopViewport ? 76 : 28,
              autoAlpha: 0,
              duration: desktopViewport ? 1 : 0.55,
              stagger: 0.08,
              ease: "power3.out",
              clearProps: "opacity,visibility,transform",
            },
            0.24,
          )
          .fromTo(
            ".hero-copper-line",
            { scaleX: 0, transformOrigin: "left center" },
            { scaleX: 1, duration: 1.05, ease: "power4.out" },
            0.82,
          );

        const storyWords = gsap.utils.toArray<HTMLElement>(".story-word");
        if (storyWords.length) {
          gsap.set(storyWords, { opacity: mobileViewport ? 0.42 : 0.24, y: mobileViewport ? 7 : 14, color: "rgba(245,242,236,0.38)" });
          gsap.to(storyWords, {
            opacity: 1,
            y: 0,
            color: "#F5F2EC",
            stagger: mobileViewport ? 0.018 : 0.035,
            ease: "none",
            scrollTrigger: {
              trigger: ".story-section",
              start: mobileViewport ? "top 82%" : "top center",
              end: mobileViewport ? "center 38%" : "bottom center",
              scrub: mobileViewport ? 0.16 : 0.45,
            },
          });
        }

        const stackSections = gsap.utils
          .toArray<HTMLElement>(".slides-wrap .snap-section")
          .slice(0, 3);
        const parallaxShift = mobileViewport ? 1.25 : tabletViewport ? 1.8 : 3;
        const parallaxScale = mobileViewport ? 1.09 : tabletViewport ? 1.07 : 1.045;

        stackSections.forEach((section) => {
          const media = section.querySelector<HTMLElement>(
            ".hero-video, .project-bg, .story-backdrop",
          );

          if (!media) return;

          gsap.fromTo(
            media,
            { yPercent: -parallaxShift, scale: parallaxScale, force3D: true },
            {
              yPercent: parallaxShift,
              scale: parallaxScale,
              force3D: true,
              ease: "none",
              scrollTrigger: {
                trigger: section,
                start: "top bottom",
                end: "bottom top",
                scrub: mobileViewport || tabletViewport ? true : 0.35,
                invalidateOnRefresh: true,
              },
            },
          );
        });

      }

      window.setTimeout(() => ScrollTrigger.refresh(), 120);
    }, mainRef);

    return () => {
      cleanups.forEach((cleanup) => cleanup());
      ctx.revert();
    };
  }, [animationControls.enabled, animationControls.pageReveal, animationControls.smoothScroll, pathname, ready]);

  return (
    <main ref={mainRef} className="relative min-h-screen bg-[#0c0b09] text-[#F5F2EC] antialiased">
      <GlobalStyle />
      <div className="motion-progress fixed left-0 top-0 z-[100] h-px w-full origin-left scale-x-0 bg-[#D69A66]" />
      <div className="motion-curtain pointer-events-none fixed inset-0 z-[98] origin-left scale-x-0" />
      <Noise />
      {children}
      {!isStandaloneExperience && (
        <div className="fixed bottom-[calc(env(safe-area-inset-bottom)+16px)] right-4 z-[70] flex flex-col gap-2 md:bottom-8 md:right-8">
          {floatingMessengers.map((messenger) => {
            const href = messenger.icon === "telegram" ? messengerLinks.telegram : messenger.icon === "vk" ? messengerLinks.vk : messengerLinks.max;

            return (
              <a
                key={messenger.label}
                href={href}
                target="_blank"
                rel="noreferrer"
                aria-label={text(`fixed.${messenger.icon}`, messenger.label)}
                className="grid h-12 w-12 place-items-center rounded-full border border-white/18 bg-[#050505]/45 text-white shadow-[0_12px_34px_rgba(0,0,0,0.28)] backdrop-blur-md transition duration-300 hover:-translate-y-0.5 hover:border-white/34 hover:bg-white/18"
              >
                <MessengerIcon icon={messenger.icon} />
              </a>
            );
          })}
        </div>
      )}
    </main>
  );
}
