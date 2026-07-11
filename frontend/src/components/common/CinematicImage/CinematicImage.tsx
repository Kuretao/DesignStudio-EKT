"use client";

import { useState } from "react";
import { optimizeImageList } from "@/src/utils/images";

type CinematicImageProps = {
  frames: Array<string | undefined>;
  alt: string;
  className?: string;
  imageClassName?: string;
  overlayClassName?: string;
  hint?: string;
  fill?: boolean;
  mode?: "preview" | "frames";
};

export default function CinematicImage({
  frames,
  alt,
  className = "",
  imageClassName = "",
  overlayClassName = "",
  hint = "motion",
  fill = false,
  mode = "preview",
}: CinematicImageProps) {
  const frameKey = frames.filter(Boolean).join("|");
  const cleanFrames = optimizeImageList(
    Array.from(new Set(frameKey.split("|").filter(Boolean))),
    1200,
    74,
  );
  const baseFrame = cleanFrames[0];
  const [loaded, setLoaded] = useState(false);
  const baseFrameClassName = fill
    ? "absolute inset-0 h-full w-full"
    : "relative block h-auto w-full";

  if (!baseFrame) return null;

  return (
    <div
      className={`cinematic-image media-frame group/cinema overflow-hidden bg-[#17130f] bg-cover bg-center ${
        fill ? "absolute inset-0" : "relative"
      } ${loaded ? "media-frame-loaded" : ""} ${mode === "frames" ? "cinematic-image-frames" : "cinematic-image-preview"} ${className}`}
      style={{ backgroundImage: `url(${baseFrame})` }}
    >
      <img
        src={baseFrame}
        alt={alt}
        loading="eager"
        decoding="async"
        onLoad={() => setLoaded(true)}
        className={`media-frame-image cinematic-frame cinematic-frame-base ${baseFrameClassName} object-cover transition duration-500 ease-out ${imageClassName}`}
      />

      {cleanFrames.slice(1).map((frame, frameIndex) => {
        const index = frameIndex + 1;

        return (
        <img
          key={frame}
          src={frame}
          alt=""
          aria-hidden="true"
          loading="lazy"
          decoding="async"
          className={`cinematic-frame cinematic-frame-layer cinematic-frame-layer-${index} absolute inset-0 h-full w-full object-cover opacity-0 transition duration-300 ease-out ${imageClassName}`}
        />
        );
      })}

      <div className={`pointer-events-none absolute inset-0 ${overlayClassName}`} />
      <div className="cinematic-sheen pointer-events-none absolute inset-0" />
      <div className="cinematic-scan pointer-events-none absolute inset-0 opacity-0 transition duration-500 group-hover/cinema:opacity-100 group-focus-within/cinema:opacity-100" />

      {cleanFrames.length > 1 && (
        <div className="pointer-events-none absolute right-4 top-4 flex items-center gap-2 rounded-full border border-white/15 bg-[#050505]/45 px-3 py-1.5 text-[10px] uppercase tracking-[0.2em] text-white/65 backdrop-blur transition duration-300 group-hover/cinema:border-[#D69A66]/55 group-hover/cinema:text-[#D69A66]">
          <span className="relative flex h-2 w-2">
            <span className="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#D69A66] opacity-55" />
            <span className="relative inline-flex h-2 w-2 rounded-full bg-[#D69A66]" />
          </span>
          {hint}
        </div>
      )}
    </div>
  );
}
