"use client";

import { useEffect, useRef, useState } from "react";
import { usePathname } from "next/navigation";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import { ScrollToPlugin } from "gsap/ScrollToPlugin";
import { useCms, useCmsText } from "@/src/cms";
import { isStandaloneExperiencePath } from "@/src/data";
import { GlobalStyle, Noise } from "@/src/ui";

gsap.registerPlugin(ScrollTrigger, ScrollToPlugin);

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
  const { animationControls, messengerLinks } = useCms();
  const text = useCmsText();
  const pathname = usePathname();
  const isStandaloneExperience = isStandaloneExperiencePath(pathname);
  const [isSiteLoading, setIsSiteLoading] = useState(true);

  useEffect(() => {
    let timeoutId = window.setTimeout(() => setIsSiteLoading(false), 1800);
    const finishLoading = () => {
      window.clearTimeout(timeoutId);
      timeoutId = window.setTimeout(() => setIsSiteLoading(false), 220);
    };

    if (document.readyState === "complete") {
      finishLoading();
    } else {
      window.addEventListener("load", finishLoading, { once: true });
    }

    return () => {
      window.clearTimeout(timeoutId);
      window.removeEventListener("load", finishLoading);
    };
  }, []);

  useEffect(() => {
    const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    const coarsePointer = window.matchMedia("(pointer: coarse)").matches;

    if (!animationControls.smoothScroll || reducedMotion || coarsePointer || window.innerWidth < 900) return;

    let targetScroll = window.scrollY;
    const maxScroll = () =>
      Math.max(
        0,
        document.documentElement.scrollHeight - window.innerHeight,
        document.body.scrollHeight - window.innerHeight,
        ScrollTrigger.maxScroll(window),
      );
    const syncTarget = () => {
      targetScroll = window.scrollY;
    };

    const onWheel = (event: WheelEvent) => {
      const target = event.target as HTMLElement | null;
      if (
        document.body.style.overflow === "hidden" ||
        target?.closest("[data-native-scroll], textarea, input, select, [role='dialog']")
      ) {
        return;
      }

      event.preventDefault();
      const delta = event.deltaMode === 1 ? event.deltaY * 32 : event.deltaY;
      const scrollBase = delta > 0 ? Math.max(targetScroll, window.scrollY) : Math.min(targetScroll, window.scrollY);
      targetScroll = gsap.utils.clamp(0, maxScroll(), scrollBase + delta);

      gsap.to(window, {
        scrollTo: targetScroll,
        duration: 0.05,
        ease: "power1.out",
        overwrite: "auto",
        onUpdate: ScrollTrigger.update,
      });
    };

    window.addEventListener("wheel", onWheel, { passive: false });
    window.addEventListener("scroll", syncTarget, { passive: true });

    return () => {
      window.removeEventListener("wheel", onWheel);
      window.removeEventListener("scroll", syncTarget);
    };
  }, [animationControls.smoothScroll]);

  useEffect(() => {
    const cleanups: Array<() => void> = [];
    if (!animationControls.enabled) return;

    const ctx = gsap.context(() => {
      const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
      const finePointer = window.matchMedia("(pointer: fine)").matches;

      ScrollTrigger.refresh();

      gsap.set(".motion-progress", { scaleX: 0, transformOrigin: "left center" });
      gsap.to(".motion-progress", {
        scaleX: 1,
        ease: "none",
        scrollTrigger: {
          start: 0,
          end: () => ScrollTrigger.maxScroll(window),
          scrub: 0.2,
        },
      });

      if (reducedMotion) return;

      gsap
        .timeline({ defaults: { ease: "expo.inOut" } })
        .set(".motion-curtain", { autoAlpha: 1, scaleX: 1, transformOrigin: "center" })
        .to(".motion-curtain", { autoAlpha: 0, scaleX: 0, duration: 1.1 });

      gsap.fromTo(
        "header",
        { y: -18, autoAlpha: 0 },
        { y: 0, autoAlpha: 1, duration: 0.9, delay: 0.22, ease: "power3.out" },
      );

      if (animationControls.pageReveal) {
        const revealTargets = gsap.utils.toArray<HTMLElement>(
          ".section-in, .grid-card, .review-card, .page-in section > div, footer .grid > div",
        );

        gsap.set(revealTargets, { autoAlpha: 0, y: 48, clipPath: "inset(12% 0 0 0)" });
        ScrollTrigger.batch(revealTargets, {
          start: "top 86%",
          once: true,
          interval: 0.08,
          batchMax: 8,
          onEnter: (batch) => {
            gsap.to(batch, {
              autoAlpha: 1,
              y: 0,
              clipPath: "inset(0% 0 0 0)",
              duration: 1,
              stagger: 0.08,
              ease: "power3.out",
              clearProps: "opacity,visibility,transform,clipPath",
            });
          },
        });
      }

      if (pathname !== "/") {
        gsap.fromTo(
          ".page-in",
          { autoAlpha: 0, y: 34 },
          { autoAlpha: 1, y: 0, duration: 0.9, delay: 0.16, ease: "power3.out", clearProps: "opacity,visibility,transform" },
        );
      }

      if (pathname === "/") {
        gsap
          .timeline()
          .fromTo(
            ".hero-video",
            { scale: 1.16, filter: "brightness(0.62) contrast(1.15) saturate(0.9)" },
            { scale: 1, filter: "brightness(0.9) contrast(1.02) saturate(0.88)", duration: 1.8, ease: "expo.out" },
            0,
          )
          .from(
            ".hero-reveal",
            {
              y: 110,
              autoAlpha: 0,
              clipPath: "inset(0 0 100% 0)",
              duration: 1.25,
              stagger: 0.13,
              ease: "expo.out",
              clearProps: "opacity,visibility,transform,clipPath",
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
        gsap.set(storyWords, { opacity: 0.24, y: 14, color: "rgba(245,242,236,0.38)" });
        gsap.to(storyWords, {
          opacity: 1,
          y: 0,
          color: "#F5F2EC",
          stagger: 0.035,
          ease: "none",
          scrollTrigger: {
            trigger: ".story-section",
            start: "top center",
            end: "bottom center",
            scrub: 0.8,
          },
        });

        // Card stacking effect — each section pins and scales back as the next slides over it
        const stackSections = gsap.utils.toArray<HTMLElement>(".slides-wrap .snap-section");

        stackSections.forEach((section, i) => {
          gsap.set(section, { zIndex: i + 1 });
        });

        stackSections.forEach((section, i) => {
          if (i === stackSections.length - 1) return;

          ScrollTrigger.create({
            trigger: section,
            start: "top top",
            pin: true,
            pinSpacing: false,
            id: `stack-pin-${i}`,
          });
        });

        stackSections.forEach((section, i) => {
          if (i === 0) return;

          const prev = stackSections[i - 1];

          gsap.to(prev, {
            scale: 0.88,
            borderRadius: "2rem",
            ease: "none",
            scrollTrigger: {
              trigger: section,
              start: "top bottom",
              end: "top top",
              scrub: true,
            },
          });

          // Incoming section slides in from slight offset for depth
          gsap.fromTo(
            section,
            { yPercent: 6 },
            {
              yPercent: 0,
              ease: "none",
              scrollTrigger: {
                trigger: section,
                start: "top bottom",
                end: "top top",
                scrub: true,
              },
            },
          );
        });
      }

      if (finePointer) {
        const cursor = mainRef.current?.querySelector<HTMLElement>(".motion-cursor");
        const cursorCore = mainRef.current?.querySelector<HTMLElement>(".motion-cursor-core");

        if (cursor && cursorCore) {
          gsap.set(cursor, { xPercent: -50, yPercent: -50, autoAlpha: 0 });
          const moveX = gsap.quickTo(cursor, "x", { duration: 0.36, ease: "power3.out" });
          const moveY = gsap.quickTo(cursor, "y", { duration: 0.36, ease: "power3.out" });

          const onMove = (event: PointerEvent) => {
            moveX(event.clientX);
            moveY(event.clientY);
            gsap.to(cursor, { autoAlpha: 1, duration: 0.05 });
          };
          const onEnter = (event: PointerEvent) => {
            if ((event.target as HTMLElement | null)?.closest("a, button, [role='button']")) {
              gsap.to(cursorCore, { scale: 1.3, borderColor: "rgba(252, 252, 252, 0.66)", backgroundColor: "rgba(255, 255, 255, 0.18)", duration: 0.16, ease: "power2.out" });
            }
          };
          const onLeave = () => {
            gsap.to(cursorCore, { scale: 1, borderColor: "rgba(255, 255, 255, 0.5)", backgroundColor: "rgba(255, 255, 255, 0.1)", duration: 0.16, ease: "power2.out" });
          };

          window.addEventListener("pointermove", onMove);
          document.addEventListener("pointerover", onEnter);
          document.addEventListener("pointerout", onLeave);

          cleanups.push(() => {
            window.removeEventListener("pointermove", onMove);
            document.removeEventListener("pointerover", onEnter);
            document.removeEventListener("pointerout", onLeave);
          });
        }

        gsap.utils.toArray<HTMLElement>(".grid-card, .review-card, .magnetic-card").forEach((card) => {
          const onMove = (event: PointerEvent) => {
            const rect = card.getBoundingClientRect();
            const x = (event.clientX - rect.left) / rect.width - 0.5;
            const y = (event.clientY - rect.top) / rect.height - 0.5;
            gsap.to(card, {
              rotationY: x * 3.5,
              rotationX: y * -3.5,
              y: -4,
              transformPerspective: 900,
              duration: 0.12,
              ease: "power1.out",
              overwrite: "auto",
            });
          };
          const onLeave = () => {
            gsap.to(card, { rotationX: 0, rotationY: 0, y: 0, duration: 0.24, ease: "power2.out", overwrite: "auto" });
          };

          card.addEventListener("pointermove", onMove);
          card.addEventListener("pointerleave", onLeave);

          cleanups.push(() => {
            card.removeEventListener("pointermove", onMove);
            card.removeEventListener("pointerleave", onLeave);
          });
        });
      }

      window.setTimeout(() => ScrollTrigger.refresh(), 120);
    }, mainRef);

    return () => {
      cleanups.forEach((cleanup) => cleanup());
      ctx.revert();
    };
  }, [animationControls.enabled, animationControls.pageReveal, animationControls.smoothScroll, pathname]);

  return (
    <main ref={mainRef} className="min-h-screen bg-[#0c0b09] text-[#F5F2EC] antialiased">
      <GlobalStyle />
      <div className={`site-loader ${isSiteLoading ? "" : "site-loader-hidden"}`} aria-hidden={!isSiteLoading}>
        <div className="site-loader-mark">
          <img src="/logo.png" alt="" className="site-loader-logo" />
          <div className="site-loader-line" />
        </div>
      </div>
      <div className="motion-progress fixed left-0 top-0 z-[100] h-px w-full origin-left scale-x-0 bg-[#D69A66]" />
      <div className="motion-curtain pointer-events-none fixed inset-0 z-[98] origin-left scale-x-0" />
      <div className="motion-cursor pointer-events-none fixed left-0 top-0 z-[120] hidden h-4 w-4 md:block">
        <div className="motion-cursor-core h-full w-full rounded-full border border-[rgba(214,154,102,0.5)] bg-[rgba(214,154,102,0.1)]" />
      </div>
      <Noise />
      {children}
      {!isStandaloneExperience && (
        <div className="fixed right-3 top-24 z-[70] flex flex-col gap-2 md:bottom-8 md:right-8 md:top-auto">
          {floatingMessengers.map((messenger) => {
            const href = messenger.icon === "telegram" ? messengerLinks.telegram : messenger.icon === "vk" ? messengerLinks.vk : messengerLinks.max;

            return (
              <a
                key={messenger.label}
                href={href}
                target="_blank"
                rel="noreferrer"
                aria-label={text(`fixed.${messenger.icon}`, messenger.label)}
                className="grid h-11 w-11 place-items-center rounded-full border border-white/18 bg-[#050505]/35 text-white backdrop-blur-md transition duration-300 hover:-translate-y-0.5 hover:border-white/34 hover:bg-white/18 md:h-12 md:w-12"
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
