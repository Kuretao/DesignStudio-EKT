"use client";

import { useEffect, useMemo, useRef, useState } from "react";
import Link from "next/link";

export type TourScene = {
  id: string;
  title?: string | null;
  label?: string | null;
  panorama: string;
  yaw?: number;
  pitch?: number;
  plan?: { x: number; y: number; width?: number; height?: number };
  next?: {
    id: string;
    text?: string | null;
    yaw?: number;
    pitch?: number;
    targetYaw?: number | null;
  }[];
};

type NormalizedTourScene = {
  id: string;
  title: string;
  label: string;
  panorama: string;
  yaw: number;
  pitch: number;
  plan: { x: number; y: number; width: number; height: number };
  next: {
    id: string;
    text: string;
    yaw: number;
    pitch: number;
    targetYaw?: number | null;
  }[];
};

type VirtualTourDemoProps = {
  sectionId?: string;
  scenes?: TourScene[];
  eyebrow?: string;
  title?: string;
  text?: string;
  buttonLabel?: string;
  fullscreenHref?: string;
  formatTitle?: string;
  formatItems?: string[];
  credit?: string;
};

type PannellumViewer = {
  loadScene: (sceneId: string, pitch?: number, yaw?: number, hfov?: number) => void;
  destroy: () => void;
  on?: (event: string, handler: (sceneId: string) => void) => void;
};

type PannellumApi = {
  viewer: (container: HTMLElement, config: Record<string, unknown>) => PannellumViewer;
};

declare global {
  interface Window {
    pannellum?: PannellumApi;
  }
}

const demoScenes: TourScene[] = [
  {
    id: "living",
    title: "Гостиная зона",
    label: "01 / Living",
    panorama: "/panoramas/demo-house-living.jpg",
    yaw: 95,
    pitch: -2,
    plan: { x: 34, y: 58, width: 36, height: 42 },
    next: [
      { id: "hall", text: "В холл", yaw: 144, pitch: -4, targetYaw: 12 },
      { id: "bedroom", text: "В спальню", yaw: 62, pitch: -3, targetYaw: -92 },
    ],
  },
  {
    id: "hall",
    title: "Холл и переход",
    label: "02 / Hall",
    panorama: "/panoramas/demo-house-hall.jpg",
    yaw: 8,
    pitch: -2,
    plan: { x: 56, y: 42, width: 34, height: 42 },
    next: [
      { id: "living", text: "В гостиную", yaw: -72, pitch: -3, targetYaw: 116 },
      { id: "bedroom", text: "В спальню", yaw: 88, pitch: -2, targetYaw: -18 },
    ],
  },
  {
    id: "bedroom",
    title: "Спальня",
    label: "03 / Bedroom",
    panorama: "/panoramas/demo-house-bedroom.jpg",
    yaw: -52,
    pitch: -4,
    plan: { x: 72, y: 64, width: 38, height: 28 },
    next: [
      { id: "hall", text: "В холл", yaw: -132, pitch: -5, targetYaw: 22 },
      { id: "living", text: "К гостиной", yaw: 154, pitch: -2, targetYaw: 95 },
    ],
  },
];

function withBasePath(path: string) {
  if (/^(https?:)?\/\//i.test(path) || path.startsWith("data:")) return path;

  const basePath = process.env.NEXT_PUBLIC_BASE_PATH ?? "";
  return `${basePath}${path}`;
}

function loadPannellumScript() {
  if (window.pannellum) return Promise.resolve(window.pannellum);

  return new Promise<PannellumApi>((resolve, reject) => {
    const existingScript = document.querySelector<HTMLScriptElement>('script[data-pannellum="true"]');

    if (existingScript) {
      existingScript.addEventListener("load", () => {
        if (window.pannellum) resolve(window.pannellum);
        else reject(new Error("Pannellum script loaded without API"));
      });
      existingScript.addEventListener("error", () => reject(new Error("Pannellum script failed to load")));
      return;
    }

    const script = document.createElement("script");
    script.src = withBasePath("/vendor/pannellum/pannellum.js");
    script.async = true;
    script.dataset.pannellum = "true";
    script.onload = () => {
      if (window.pannellum) resolve(window.pannellum);
      else reject(new Error("Pannellum script loaded without API"));
    };
    script.onerror = () => reject(new Error("Pannellum script failed to load"));
    document.body.appendChild(script);
  });
}

function normalizeScene(scene: TourScene, index: number): NormalizedTourScene {
  const fallbackX = 22 + ((index * 23) % 55);
  const fallbackY = 24 + ((index * 17) % 48);

  return {
    id: scene.id || `scene-${index + 1}`,
    title: scene.title || `Точка ${index + 1}`,
    label: scene.label || `${String(index + 1).padStart(2, "0")} / 360`,
    panorama: scene.panorama,
    yaw: Number(scene.yaw ?? 0),
    pitch: Number(scene.pitch ?? 0),
    plan: {
      x: Math.max(0, Math.min(100, Number(scene.plan?.x ?? fallbackX))),
      y: Math.max(0, Math.min(100, Number(scene.plan?.y ?? fallbackY))),
      width: Math.max(16, Math.min(82, Number(scene.plan?.width ?? 34))),
      height: Math.max(14, Math.min(82, Number(scene.plan?.height ?? 30))),
    },
    next: Array.isArray(scene.next)
      ? scene.next.map((link) => ({
          id: link.id,
          text: link.text || "Открыть",
          yaw: Number(link.yaw ?? 0),
          pitch: Number(link.pitch ?? 0),
          targetYaw: link.targetYaw ?? undefined,
        }))
      : [],
  };
}

export default function VirtualTourDemo({
  sectionId,
  scenes: inputScenes,
  eyebrow = "Demo / virtual tour",
  title = "Пример тура по дому из сшитых 360° панорам",
  text = "Внутри можно крутиться мышью или пальцем, приближать колесом, переходить между точками через хотспоты и мини-план. Для реального проекта подключаются панорамы из 3ds Max, Corona/V-Ray или съемки 360-камерой.",
  buttonLabel = "Открыть полноэкранный тур",
  fullscreenHref = "/virtualnyj-3d-tur-demo",
  formatTitle = "Формат загрузки",
  formatItems = [
    "JPEG/PNG/WebP, equirectangular 2:1.",
    "Оптимально 4096x2048 или 8192x4096.",
    "Каждая точка получает свои переходы и стартовый ракурс.",
  ],
  credit = "В рабочем проекте сюда подключаются панорамы заказчика.",
}: VirtualTourDemoProps) {
  const scenes = useMemo(
    () => (inputScenes?.length ? inputScenes : demoScenes).filter((scene) => scene.panorama).map(normalizeScene),
    [inputScenes],
  );
  const sceneById = useMemo(() => new Map(scenes.map((scene) => [scene.id, scene])), [scenes]);
  const firstSceneId = scenes[0]?.id ?? "living";
  const containerRef = useRef<HTMLDivElement | null>(null);
  const viewerRef = useRef<PannellumViewer | null>(null);
  const [activeSceneId, setActiveSceneId] = useState(firstSceneId);
  const [viewerReady, setViewerReady] = useState(false);
  const [viewerError, setViewerError] = useState<string | null>(null);
  const activeScene = sceneById.get(activeSceneId) ?? scenes[0];

  const pannellumConfig = useMemo(
    () => ({
      default: {
        firstScene: firstSceneId,
        sceneFadeDuration: 900,
        autoLoad: true,
        showControls: true,
        compass: true,
        hfov: 105,
        minHfov: 55,
        maxHfov: 120,
        keyboardZoom: true,
        mouseZoom: true,
      },
      scenes: Object.fromEntries(
        scenes.map((scene) => [
          scene.id,
          {
            title: scene.title,
            type: "equirectangular",
            panorama: withBasePath(scene.panorama),
            yaw: scene.yaw,
            pitch: scene.pitch,
            hotSpots: scene.next
              .filter((link) => sceneById.has(link.id))
              .map((link) => ({
                type: "scene",
                text: link.text,
                sceneId: link.id,
                yaw: link.yaw,
                pitch: link.pitch,
                targetYaw: link.targetYaw ?? undefined,
                cssClass: "virtual-tour-hotspot",
              })),
          },
        ]),
      ),
    }),
    [firstSceneId, sceneById, scenes],
  );

  useEffect(() => {
    setActiveSceneId(firstSceneId);
    setViewerReady(false);
    setViewerError(null);
  }, [firstSceneId]);

  useEffect(() => {
    if (!scenes.length) return;

    let mounted = true;

    const init = async () => {
      if (!containerRef.current) return;

      try {
        const pannellum = await loadPannellumScript();
        if (!mounted || !containerRef.current) return;

        viewerRef.current?.destroy();
        const viewer = pannellum.viewer(containerRef.current, pannellumConfig) as PannellumViewer;

        viewerRef.current = viewer;
        viewer.on?.("scenechange", (sceneId: string) => {
          if (sceneById.has(sceneId)) setActiveSceneId(sceneId);
        });
        setViewerReady(true);
      } catch (error) {
        console.error("Virtual tour viewer failed to initialize", error);
        setViewerError("Не удалось загрузить 360° просмотр. Обновите страницу или проверьте файл панорамы.");
      }
    };

    init();

    return () => {
      mounted = false;
      viewerRef.current?.destroy();
      viewerRef.current = null;
    };
  }, [pannellumConfig, sceneById, scenes.length]);

  const loadScene = (sceneId: string) => {
    const target = sceneById.get(sceneId);
    if (!target) return;

    setActiveSceneId(sceneId);
    viewerRef.current?.loadScene(sceneId, target.pitch, target.yaw, 105);
  };

  if (!scenes.length || !activeScene) return null;

  return (
    <section id={sectionId} className="scroll-mt-36 border-t border-white/10 bg-[#050505] px-5 py-24 text-white md:px-10 lg:px-16">
      <div className="mx-auto max-w-7xl">
        <div className="mb-10 grid gap-6 lg:grid-cols-[0.78fr_1fr] lg:items-end">
          <div>
            <p className="text-xs uppercase tracking-[0.42em] text-[#D69A66]">{eyebrow}</p>
            <h2 className="mt-4 text-4xl font-light leading-tight tracking-[-0.045em] md:text-6xl">{title}</h2>
          </div>
          <div className="grid gap-5">
            <p className="max-w-2xl leading-relaxed text-[#D6D1CA]">{text}</p>
            {fullscreenHref ? (
              <Link
                href={fullscreenHref}
                className="w-fit rounded-full bg-[#D69A66] px-5 py-3 text-xs uppercase tracking-[0.22em] text-[#050505] transition hover:bg-white"
              >
                {buttonLabel}
              </Link>
            ) : null}
          </div>
        </div>

        <div className="grid gap-5 lg:grid-cols-[1fr_360px]">
          <div
            className="relative min-h-[420px] overflow-hidden rounded-[1.5rem] border border-white/10 bg-black shadow-[0_30px_120px_rgba(0,0,0,0.42)] md:min-h-[620px]"
            data-native-scroll
          >
            <div ref={containerRef} className="h-[420px] w-full md:h-[620px]" />

            {!viewerReady && !viewerError ? (
              <div className="absolute inset-0 grid place-items-center bg-[#050505]">
                <div className="text-center">
                  <div className="mx-auto mb-4 h-12 w-12 rounded-full border border-[#D69A66]/30 border-t-[#D69A66]" />
                  <p className="text-xs uppercase tracking-[0.28em] text-white/50">Загрузка панорамы</p>
                </div>
              </div>
            ) : null}

            {viewerError ? (
              <div className="absolute inset-0 grid place-items-center bg-[#050505] p-6 text-center">
                <p className="max-w-sm text-sm leading-relaxed text-[#D6D1CA]">{viewerError}</p>
              </div>
            ) : null}

            <div className="pointer-events-none absolute left-4 top-4 rounded-full border border-white/12 bg-black/45 px-4 py-2 text-xs uppercase tracking-[0.2em] text-white/72 backdrop-blur">
              {activeScene.label}
            </div>
          </div>

          <aside className="grid gap-5">
            <div className="rounded-[1.5rem] border border-white/10 bg-white/[0.035] p-5">
              <div className="mb-5 flex items-start justify-between gap-4">
                <div>
                  <p className="text-xs uppercase tracking-[0.24em] text-white/38">Сейчас</p>
                  <h3 className="mt-2 text-3xl font-light tracking-[-0.04em]">{activeScene.title}</h3>
                </div>
                <span className="rounded-full bg-[#D69A66] px-3 py-1 text-xs font-medium text-[#050505]">360°</span>
              </div>

              <div className="relative h-72 overflow-hidden rounded-[1.2rem] border border-white/10 bg-[#111]">
                <div className="absolute inset-5 rounded-[1rem] border border-white/16" />
                <div className="absolute left-[12%] top-[46%] h-px w-[76%] bg-white/12" />
                <div className="absolute left-[49%] top-[14%] h-[72%] w-px bg-white/12" />

                {scenes.map((scene) => {
                  const active = scene.id === activeSceneId;
                  const width = scene.plan.width;
                  const height = scene.plan.height;
                  const left = Math.max(0, Math.min(100 - width, scene.plan.x - width / 2));
                  const top = Math.max(0, Math.min(100 - height, scene.plan.y - height / 2));

                  return (
                    <button
                      key={`${scene.id}-zone`}
                      type="button"
                      onClick={() => loadScene(scene.id)}
                      className={`absolute rounded-[0.8rem] border transition ${
                        active
                          ? "border-[#D69A66]/50 bg-[#D69A66]/12 shadow-[0_0_28px_rgba(214,154,102,0.16)]"
                          : "border-white/16 bg-white/[0.035] hover:border-[#D69A66]/38 hover:bg-[#D69A66]/8"
                      }`}
                      style={{ left: `${left}%`, top: `${top}%`, width: `${width}%`, height: `${height}%` }}
                      aria-label={`Открыть зону ${scene.title}`}
                    />
                  );
                })}

                {scenes.map((scene, index) => {
                  const active = scene.id === activeSceneId;

                  return (
                    <button
                      key={scene.id}
                      type="button"
                      onClick={() => loadScene(scene.id)}
                      className={`absolute grid h-9 w-9 -translate-x-1/2 -translate-y-1/2 place-items-center rounded-full border text-xs transition ${
                        active
                          ? "border-[#D69A66] bg-[#D69A66] text-[#050505]"
                          : "border-white/24 bg-black/50 text-white hover:border-[#D69A66] hover:text-[#D69A66]"
                      }`}
                      style={{ left: `${scene.plan.x}%`, top: `${scene.plan.y}%` }}
                      aria-label={`Открыть ${scene.title}`}
                    >
                      {index + 1}
                    </button>
                  );
                })}
              </div>
            </div>

            <div className="grid gap-2">
              {scenes.map((scene) => (
                <button
                  key={scene.id}
                  type="button"
                  onClick={() => loadScene(scene.id)}
                  className={`flex items-center justify-between gap-4 rounded-[1.1rem] border px-4 py-3 text-left transition ${
                    scene.id === activeSceneId
                      ? "border-[#D69A66]/60 bg-[#D69A66]/10 text-white"
                      : "border-white/10 bg-white/[0.035] text-white/58 hover:border-white/22 hover:text-white"
                  }`}
                >
                  <span>{scene.title}</span>
                  <span className="text-xs uppercase tracking-[0.18em] text-[#D69A66]">открыть</span>
                </button>
              ))}
            </div>

            <div className="rounded-[1.5rem] border border-white/10 bg-white/[0.035] p-5">
              <p className="text-xs uppercase tracking-[0.24em] text-[#D69A66]">{formatTitle}</p>
              <ul className="mt-5 space-y-3 text-sm leading-relaxed text-[#D6D1CA]">
                {formatItems.map((item) => (
                  <li key={item}>{item}</li>
                ))}
              </ul>
            </div>

            <div className="rounded-[1.5rem] border border-white/10 bg-white/[0.025] p-5 text-xs leading-relaxed text-white/42">
              {credit}
            </div>
          </aside>
        </div>
      </div>
    </section>
  );
}
