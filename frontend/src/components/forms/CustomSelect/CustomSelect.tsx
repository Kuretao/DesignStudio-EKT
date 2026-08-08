"use client";

import { useEffect, useRef, useState } from "react";
import { createPortal } from "react-dom";

type Option = { value: string; label: string };

type Props = {
  options: Option[];
  value: string;
  onChange: (value: string) => void;
  placeholder?: string;
  className?: string;
};

export default function CustomSelect({
  options,
  value,
  onChange,
  placeholder = "Выберите...",
  className = "",
}: Props) {
  const [open, setOpen] = useState(false);
  const [mounted, setMounted] = useState(false);
  const [menuRect, setMenuRect] = useState({ left: 0, top: 0, width: 0 });
  const ref = useRef<HTMLDivElement>(null);
  const selected = options.find((option) => option.value === value);

  const updateMenuRect = () => {
    const rect = ref.current?.getBoundingClientRect();
    if (!rect) return;

    setMenuRect({
      left: rect.left,
      top: Math.min(rect.bottom + 6, window.innerHeight - 64),
      width: rect.width,
    });
  };

  useEffect(() => {
    setMounted(true);
  }, []);

  useEffect(() => {
    if (!open) return;

    updateMenuRect();
    window.addEventListener("resize", updateMenuRect);
    window.addEventListener("scroll", updateMenuRect, true);

    return () => {
      window.removeEventListener("resize", updateMenuRect);
      window.removeEventListener("scroll", updateMenuRect, true);
    };
  }, [open]);

  useEffect(() => {
    const handler = (event: MouseEvent) => {
      const target = event.target as Node;

      if (
        ref.current &&
        !ref.current.contains(target) &&
        !(target instanceof Element && target.closest("[data-custom-select-menu]"))
      ) {
        setOpen(false);
      }
    };

    document.addEventListener("mousedown", handler);
    return () => document.removeEventListener("mousedown", handler);
  }, []);

  const menu = (
    <div
      data-custom-select-menu
      className="fixed z-[1000] overflow-hidden rounded-2xl border border-white/12 bg-[#171511]/98 shadow-[0_28px_70px_rgba(0,0,0,0.52),inset_0_1px_0_rgba(255,255,255,0.07)] backdrop-blur-[2px] transition-all duration-200"
      style={{
        left: menuRect.left,
        top: menuRect.top,
        width: menuRect.width,
        opacity: open ? 1 : 0,
        transform: open ? "translateY(0) scale(1)" : "translateY(-6px) scale(0.98)",
        pointerEvents: open ? "auto" : "none",
      }}
    >
      <button
        type="button"
        onClick={() => {
          onChange("");
          setOpen(false);
        }}
        className={`w-full px-5 py-3.5 text-left text-sm transition-colors duration-150 hover:bg-white/[0.05] ${
          !value ? "text-[#D69A66]/60" : "text-white/25 hover:text-white/50"
        }`}
      >
        {placeholder}
      </button>

      <div className="mx-4 border-t border-white/8" />

      <div className="max-h-[min(320px,42vh)] overflow-y-auto py-1.5 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        {options.map((option) => {
          const active = option.value === value;

          return (
            <button
              key={option.value}
              type="button"
              onClick={() => {
                onChange(option.value);
                setOpen(false);
              }}
              className={`group flex w-full items-center gap-3 px-5 py-3 text-left text-sm transition-all duration-150 hover:bg-white/[0.05] ${
                active ? "text-[#D69A66]" : "text-white/55 hover:text-white"
              }`}
            >
              <span
                className={`h-1.5 w-1.5 shrink-0 rounded-full transition-all duration-150 ${
                  active
                    ? "bg-[#D69A66] shadow-[0_0_6px_rgba(214,154,102,0.7)]"
                    : "bg-white/15 group-hover:bg-white/35"
                }`}
              />
              <span className="min-w-0 flex-1 truncate">{option.label}</span>
              {active ? (
                <svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="2"
                  className="ml-auto h-3.5 w-3.5 shrink-0 text-[#D69A66]/70"
                  aria-hidden="true"
                >
                  <path strokeLinecap="round" strokeLinejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
              ) : null}
            </button>
          );
        })}
      </div>
    </div>
  );

  return (
    <div ref={ref} className={`relative ${open ? "z-[260]" : "z-10"} ${className}`}>
      <button
        type="button"
        onClick={() => {
          updateMenuRect();
          setOpen((current) => !current);
        }}
        className={`flex w-full cursor-pointer items-center justify-between rounded-2xl border px-5 py-4 text-sm transition-all duration-200 ${
          open
            ? "border-[#D69A66]/55 bg-white/[0.08] shadow-[inset_0_1px_0_rgba(255,255,255,0.07)] backdrop-blur-[1px]"
            : "border-white/10 bg-white/[0.045] shadow-[inset_0_1px_0_rgba(255,255,255,0.05)] backdrop-blur-[1px] hover:border-white/20 hover:bg-white/[0.065]"
        }`}
      >
        <span className={`min-w-0 truncate ${selected ? "text-[#F5F2EC]" : "text-white/30"}`}>
          {selected ? selected.label : placeholder}
        </span>
        <svg
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="1.5"
          className={`h-4 w-4 shrink-0 transition-all duration-250 ${
            open ? "rotate-180 text-[#D69A66]/70" : "text-white/25"
          }`}
          aria-hidden="true"
        >
          <path strokeLinecap="round" strokeLinejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
        </svg>
      </button>

      {mounted ? createPortal(menu, document.body) : null}
    </div>
  );
}
