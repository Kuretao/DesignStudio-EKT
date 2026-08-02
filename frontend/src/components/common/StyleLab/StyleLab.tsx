"use client";

import { useMemo, useState } from "react";
import Link from "next/link";
import { useCmsText } from "@/src/cms";
import { GlassPanel } from "@/src/ui";

type StyleOption = {
  id: string;
  label: string;
  headline: string;
  image: string;
  mood: string;
  colors: string[];
};

type MaterialOption = {
  id: string;
  label: string;
  texture: string;
  accent: string;
  image: string;
};

type LightOption = {
  id: string;
  label: string;
  overlay: string;
  note: string;
  image: string;
};

function listFromText(value: string, fallback: string[]) {
  const items = value
    .split(/[\n,]+/u)
    .map((item) => item.trim())
    .filter(Boolean);

  return items.length ? items : fallback;
}

const styles: StyleOption[] = [
  {
    id: "minimal",
    label: "Современный",
    headline: "Спокойный интерьер с чистой геометрией и воздухом",
    image: "/images/cms/river-park-interior.webp",
    mood: "минимализм, контемпорари, скрытое хранение",
    colors: ["#F5F2EC", "#9EA79A", "#2A2C27", "#D69A66"],
  },
  {
    id: "classic",
    label: "Неоклассика",
    headline: "Мягкая элегантность без перегруза и музейности",
    image: "/images/cms/country-house-interior.webp",
    mood: "симметрия, уют, благородные материалы",
    colors: ["#E8DDCE", "#BFA98A", "#604D3B", "#F5F2EC"],
  },
  {
    id: "loft",
    label: "Лофт",
    headline: "Фактурное пространство с сильным характером",
    image: "/images/cms/natural-living-room.webp",
    mood: "камень, металл, открытая планировка",
    colors: ["#C2B8A5", "#6B6358", "#1E1D1B", "#9A6A43"],
  },
  {
    id: "eco",
    label: "Эко",
    headline: "Светлый сценарий с природной палитрой и тактильностью",
    image: "/images/cms/modern-living-room.webp",
    mood: "дерево, лен, зелень, естественный свет",
    colors: ["#F2EBDD", "#A5A875", "#52634C", "#C7A987"],
  },
];

const materials: MaterialOption[] = [
  { id: "wood", label: "Теплое дерево", texture: "лиственница / дуб", accent: "#B78352", image: "/images/cms/country-house-interior.webp" },
  { id: "stone", label: "Крупный камень", texture: "травертин / кварц", accent: "#CFC3AD", image: "/images/cms/natural-living-room.webp" },
  { id: "textile", label: "Мягкий текстиль", texture: "лен / букле", accent: "#E1D8C8", image: "/images/cms/modern-living-room.webp" },
  { id: "metal", label: "Темный металл", texture: "графит / бронза", accent: "#4C4841", image: "/images/cms/river-park-interior.webp" },
];

const lights: LightOption[] = [
  {
    id: "morning",
    label: "Утро",
    overlay: "linear-gradient(120deg, rgba(245,242,236,.22), rgba(214,154,102,.08), rgba(5,5,5,.18))",
    note: "мягкий дневной свет",
    image: "/images/cms/modern-living-room.webp",
  },
  {
    id: "evening",
    label: "Вечер",
    overlay: "linear-gradient(120deg, rgba(214,154,102,.24), rgba(70,54,43,.12), rgba(5,5,5,.32))",
    note: "теплые акцентные группы",
    image: "/images/cms/country-house-interior.webp",
  },
  {
    id: "gallery",
    label: "Галерея",
    overlay: "linear-gradient(120deg, rgba(245,242,236,.1), rgba(126,139,116,.14), rgba(5,5,5,.24))",
    note: "контраст и точечные акценты",
    image: "/images/cms/natural-living-room.webp",
  },
];

export default function StyleLab() {
  const text = useCmsText();
  const [styleId, setStyleId] = useState(styles[0].id);
  const [materialId, setMaterialId] = useState(materials[0].id);
  const [lightId, setLightId] = useState(lights[0].id);
  const [imageSource, setImageSource] = useState<"style" | "material" | "light">("style");
  const [saved, setSaved] = useState(false);

  const localizedStyles = styles.map((item) => ({
    ...item,
    label: text(`styleLab.styles.${item.id}.label`, item.label),
    headline: text(`styleLab.styles.${item.id}.headline`, item.headline),
    mood: text(`styleLab.styles.${item.id}.mood`, item.mood),
    image: text(`styleLab.styles.${item.id}.image`, item.image),
    colors: listFromText(
      text(`styleLab.styles.${item.id}.colors`, item.colors.join("\n")),
      item.colors,
    ),
  }));
  const localizedMaterials = materials.map((item) => ({
    ...item,
    label: text(`styleLab.materials.${item.id}.label`, item.label),
    texture: text(`styleLab.materials.${item.id}.texture`, item.texture),
    accent: text(`styleLab.materials.${item.id}.accent`, item.accent),
    image: text(`styleLab.materials.${item.id}.image`, item.image),
  }));
  const localizedLights = lights.map((item) => ({
    ...item,
    label: text(`styleLab.lights.${item.id}.label`, item.label),
    note: text(`styleLab.lights.${item.id}.note`, item.note),
    overlay: text(`styleLab.lights.${item.id}.overlay`, item.overlay),
    image: text(`styleLab.lights.${item.id}.image`, item.image),
  }));

  const activeStyle = useMemo(() => localizedStyles.find((item) => item.id === styleId) ?? localizedStyles[0], [localizedStyles, styleId]);
  const activeMaterial = useMemo(() => localizedMaterials.find((item) => item.id === materialId) ?? localizedMaterials[0], [localizedMaterials, materialId]);
  const activeLight = useMemo(() => localizedLights.find((item) => item.id === lightId) ?? localizedLights[0], [localizedLights, lightId]);
  const activeImage =
    imageSource === "light"
      ? activeLight.image || activeStyle.image
      : imageSource === "material"
        ? activeMaterial.image || activeStyle.image
        : activeStyle.image;

  const saveConcept = () => {
    const payload = {
      style: activeStyle.label,
      material: activeMaterial.label,
      light: activeLight.label,
      createdAt: new Date().toISOString(),
    };

    localStorage.setItem("homeStyleLabConcept", JSON.stringify(payload));
    setSaved(true);
    window.setTimeout(() => setSaved(false), 2600);
  };

  return (
    <section className="style-lab-section relative z-[2] min-h-[136vh] overflow-hidden bg-[#0c0b09] px-5 py-36 md:px-10 md:py-44 lg:px-16">
      <div className="absolute inset-0">
        <img
          key={`bg-${activeImage}`}
          src={activeImage}
          alt=""
          aria-hidden="true"
          className="absolute inset-0 h-full w-full scale-110 object-cover blur-sm opacity-45 transition duration-700"
        />
        <div className="absolute inset-0 bg-[linear-gradient(90deg,rgba(5,5,5,.92)_0%,rgba(13,12,10,.78)_44%,rgba(5,5,5,.54)_100%)]" />
        <div className="absolute inset-0 bg-[linear-gradient(180deg,#0c0b09_0%,rgba(12,11,9,.5)_18%,rgba(12,11,9,.42)_72%,#0c0b09_100%)]" />
        <div
          className="absolute inset-0 opacity-45"
          style={{
            backgroundImage:
              "linear-gradient(rgba(245,242,236,.045) 1px, transparent 1px), linear-gradient(90deg, rgba(245,242,236,.035) 1px, transparent 1px)",
            backgroundSize: "120px 120px",
          }}
        />
      </div>

      <div className="relative z-10 mx-auto grid min-h-[calc(136vh-22rem)] max-w-7xl gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-stretch">
        <div className="flex flex-col justify-between">
          <div>
            <p className="mb-5 text-xs uppercase tracking-[0.45em] text-[#D69A66]">{text("styleLab.eyebrow", "Подбор стиля")}</p>
            <h2 className="max-w-3xl text-5xl font-light leading-tight text-[#F5F2EC] md:text-7xl">
              {text("styleLab.title", "Соберите настроение будущего интерьера")}
            </h2>
            <p className="mt-6 max-w-2xl text-lg leading-relaxed text-[#D6D1CA]">
              {text(
                "styleLab.text",
                "Быстрый интерактивный эскиз показывает, как меняется ощущение проекта от стиля, материала и светового сценария.",
              )}
            </p>
          </div>

          <GlassPanel className="mt-10 rounded-[2rem] p-6 md:p-7">
            <p className="text-xs uppercase tracking-[0.3em] text-white/35">{text("styleLab.briefLabel", "Бриф в один взгляд")}</p>
            <h3 className="mt-4 text-3xl font-light leading-tight text-[#F5F2EC]">{activeStyle.headline}</h3>
            <div className="mt-6 grid gap-4 text-sm text-[#D6D1CA] sm:grid-cols-3">
              <div>
                <span className="block text-white/35">{text("styleLab.styleLabel", "Стиль")}</span>
                <span className="mt-1 block text-[#F5F2EC]">{activeStyle.mood}</span>
              </div>
              <div>
                <span className="block text-white/35">{text("styleLab.materialLabel", "Материал")}</span>
                <span className="mt-1 block text-[#F5F2EC]">{activeMaterial.texture}</span>
              </div>
              <div>
                <span className="block text-white/35">{text("styleLab.lightLabel", "Свет")}</span>
                <span className="mt-1 block text-[#F5F2EC]">{activeLight.note}</span>
              </div>
            </div>
          </GlassPanel>
        </div>

        <GlassPanel className="grid overflow-hidden rounded-[2rem] lg:grid-rows-[1fr_auto]">
          <div className="relative min-h-[560px] overflow-hidden md:min-h-[760px]">
            <img
              key={activeImage}
              src={activeImage}
              alt={activeStyle.label}
              className="absolute inset-0 h-full w-full object-cover transition duration-700"
            />
            <div className="absolute inset-0 transition duration-700" style={{ background: activeLight.overlay }} />
            <div className="absolute inset-0 bg-gradient-to-t from-[#050505]/82 via-[#050505]/14 to-transparent" />

            <div className="absolute bottom-6 left-6 right-6 md:bottom-8 md:left-8 md:right-8">
              <div className="mb-4 flex flex-wrap gap-2">
                {activeStyle.colors.map((color) => (
                  <span
                    key={color}
                    className="h-9 w-9 rounded-full border border-white/25 shadow-[0_10px_28px_rgba(0,0,0,.26)]"
                    style={{ background: color }}
                    aria-label={`${text("styleLab.colorAriaPrefix", "Цвет")} ${color}`}
                  />
                ))}
                <span
                  className="h-9 w-9 rounded-full border border-white/25 shadow-[0_10px_28px_rgba(0,0,0,.26)]"
                  style={{ background: activeMaterial.accent }}
                  aria-label={text("styleLab.accentMaterialAria", "Акцентный материал")}
                />
              </div>
              <div className="flex flex-wrap items-end justify-between gap-4">
                <div>
                  <p className="text-xs uppercase tracking-[0.32em] text-[#D69A66]">{activeStyle.label}</p>
                  <p className="mt-2 max-w-2xl text-3xl font-light leading-tight text-white md:text-5xl">{activeStyle.headline}</p>
                </div>
                <div className="rounded-full border border-white/15 bg-black/35 px-4 py-2 text-xs uppercase tracking-[0.2em] text-white/70 backdrop-blur">
                  {activeMaterial.label} · {activeLight.label}
                </div>
              </div>
            </div>
          </div>

          <div className="grid gap-6 border-t border-white/10 p-5 md:p-7">
            <div>
              <p className="mb-3 text-[10px] uppercase tracking-[0.3em] text-white/35">{text("styleLab.styleLabel", "Стиль")}</p>
              <div className="grid gap-2 sm:grid-cols-4">
                {localizedStyles.map((item) => (
                  <button
                    key={item.id}
                    type="button"
                    aria-pressed={item.id === activeStyle.id}
                    onClick={() => {
                      setStyleId(item.id);
                      setImageSource("style");
                    }}
                    className={`min-h-12 rounded-2xl border px-3 text-sm transition ${
                      item.id === activeStyle.id
                        ? "border-[#D69A66]/70 bg-[#D69A66]/12 text-[#F5F2EC]"
                        : "border-white/10 bg-white/[0.03] text-white/50 hover:border-white/25 hover:text-white"
                    }`}
                  >
                    {item.label}
                  </button>
                ))}
              </div>
            </div>

            <div className="grid gap-5 lg:grid-cols-[1fr_0.8fr]">
              <div>
                <p className="mb-3 text-[10px] uppercase tracking-[0.3em] text-white/35">{text("styleLab.materialLabel", "Материал")}</p>
                <div className="grid gap-2 sm:grid-cols-2">
                  {localizedMaterials.map((item) => (
                    <button
                      key={item.id}
                      type="button"
                      aria-pressed={item.id === activeMaterial.id}
                      onClick={() => {
                        setMaterialId(item.id);
                        setImageSource("material");
                      }}
                      className={`flex min-h-12 items-center gap-3 rounded-2xl border px-3 text-left text-sm transition ${
                        item.id === activeMaterial.id
                          ? "border-[#D69A66]/70 bg-[#D69A66]/12 text-[#F5F2EC]"
                          : "border-white/10 bg-white/[0.03] text-white/50 hover:border-white/25 hover:text-white"
                      }`}
                    >
                      <span className="h-6 w-6 shrink-0 rounded-full border border-white/20" style={{ background: item.accent }} />
                      {item.label}
                    </button>
                  ))}
                </div>
              </div>

              <div>
                <p className="mb-3 text-[10px] uppercase tracking-[0.3em] text-white/35">{text("styleLab.lightLabel", "Свет")}</p>
                <div className="grid grid-cols-3 gap-2">
                  {localizedLights.map((item) => (
                    <button
                      key={item.id}
                      type="button"
                      aria-pressed={item.id === activeLight.id}
                      onClick={() => {
                        setLightId(item.id);
                        setImageSource("light");
                      }}
                      className={`min-h-12 rounded-2xl border px-3 text-sm transition ${
                        item.id === activeLight.id
                          ? "border-[#D69A66]/70 bg-[#D69A66]/12 text-[#F5F2EC]"
                          : "border-white/10 bg-white/[0.03] text-white/50 hover:border-white/25 hover:text-white"
                      }`}
                    >
                      {item.label}
                    </button>
                  ))}
                </div>
              </div>
            </div>

            <div className="flex flex-wrap items-center gap-3 border-t border-white/10 pt-5">
              <button
                type="button"
                onClick={saveConcept}
                className="rounded-full bg-[#D69A66] px-6 py-3 text-xs uppercase tracking-[0.22em] text-[#050505] transition hover:bg-[#F5F2EC]"
              >
                {text("styleLab.saveButton", "Сохранить подборку")}
              </button>
              <Link
                href="#project-quiz"
                className="rounded-full border border-white/12 px-6 py-3 text-xs uppercase tracking-[0.22em] text-white/60 transition hover:border-[#D69A66]/55 hover:text-[#D69A66]"
              >
                {text("styleLab.calculateButton", "Рассчитать проект")}
              </Link>
              {saved && <span className="text-sm text-[#D6D1CA]">{text("styleLab.savedMessage", "Подборка сохранена")}</span>}
            </div>
          </div>
        </GlassPanel>
      </div>
    </section>
  );
}
