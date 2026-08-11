"use client";

import styled, { createGlobalStyle } from "styled-components";

export const GlobalStyle = createGlobalStyle`
  :root {
    --color-black: #0c0b09;
    --color-graphite: #181713;
    --color-graphite-soft: #24221d;
    --color-white: #F5F2EC;
    --color-copper: #D69A66;
  }

  html {
    scroll-behavior: smooth;
    background: var(--color-black);
  }

  body {
    background:
      radial-gradient(circle at 72% 18%, rgba(214,154,102, 0.13), transparent 28rem),
      radial-gradient(circle at 14% 72%, rgba(116, 128, 106, 0.1), transparent 25rem),
      linear-gradient(135deg, #0c0b09 0%, #15130f 42%, #1d1b17 100%);
    color: var(--color-white);
    overflow-x: hidden;
    scrollbar-width: none;
  }

  ::-webkit-scrollbar {
    display: none;
    width: 0;
    height: 0;
  }

  ::selection {
    background: var(--color-copper);
    color: var(--color-black);
  }

  .line-clamp-2,
  .line-clamp-3,
  .line-clamp-4,
  .line-clamp-5 {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .line-clamp-2 {
    -webkit-line-clamp: 2;
  }

  .line-clamp-3 {
    -webkit-line-clamp: 3;
  }

  .line-clamp-4 {
    -webkit-line-clamp: 4;
  }

  .line-clamp-5 {
    -webkit-line-clamp: 5;
  }

  .direction-scroll {
    scrollbar-width: thin;
    scrollbar-color: rgba(214, 154, 102, 0.62) rgba(245, 242, 236, 0.08);
  }

  .direction-scroll::-webkit-scrollbar {
    width: 6px;
  }

  .direction-scroll::-webkit-scrollbar-track {
    border-radius: 999px;
    background: rgba(245, 242, 236, 0.08);
  }

  .direction-scroll::-webkit-scrollbar-thumb {
    border-radius: 999px;
    background: rgba(214, 154, 102, 0.62);
  }

  a,
  button {
    -webkit-tap-highlight-color: transparent;
  }

  .motion-progress {
    box-shadow: 0 0 24px rgba(214,154,102, 0.55);
    will-change: transform;
  }

  .motion-curtain {
    background: rgba(10, 10, 10, 0.42);
    border-right: 1px solid rgba(245, 242, 236, 0.12);
    will-change: transform, opacity;
  }

  .hero-video,
  .project-bg {
    will-change: auto;
  }

  .snap-section {
    isolation: isolate;
  }

  /* Card-stacking scroll effect */
  .slides-wrap .snap-section {
    transform-origin: center top;
    border-radius: 0px;
    background-color: #0f0d0a;
    will-change: auto;
  }

  .page-in {
    position: relative;
    isolation: isolate;
    background:
      linear-gradient(rgba(245, 242, 236, 0.024) 1px, transparent 1px),
      linear-gradient(90deg, rgba(245, 242, 236, 0.022) 1px, transparent 1px),
      radial-gradient(circle at 82% 8%, rgba(214, 154, 102, 0.08), transparent 28rem),
      radial-gradient(circle at 10% 32%, rgba(126, 139, 116, 0.07), transparent 26rem),
      linear-gradient(180deg, #171511 0%, #12110e 44%, #171611 100%);
    background-size: 132px 132px, 132px 132px, auto, auto, auto;
  }

  .page-in > * {
    position: relative;
    z-index: 1;
  }

  .slides-wrap {
    background:
      linear-gradient(180deg, #11100d 0%, #18150f 34%, #121710 66%, #171511 100%);
    box-shadow: inset 0 -120px 160px rgba(8, 7, 5, 0.42);
  }

  .portfolio-grid-section {
    background:
      linear-gradient(rgba(245, 242, 236, 0.024) 1px, transparent 1px),
      linear-gradient(90deg, rgba(245, 242, 236, 0.022) 1px, transparent 1px),
      radial-gradient(circle at 82% 8%, rgba(214, 154, 102, 0.08), transparent 28rem),
      radial-gradient(circle at 10% 32%, rgba(126, 139, 116, 0.07), transparent 26rem),
      linear-gradient(180deg, #171511 0%, #12110e 44%, #171611 100%);
    background-size: 132px 132px, 132px 132px, auto, auto, auto;
  }

  .hero-video {
    filter: brightness(0.82) contrast(1.04) saturate(0.92);
  }

  .hero-light-field {
    background:
      linear-gradient(100deg, rgba(5, 5, 5, 0.36) 0%, rgba(5, 5, 5, 0.18) 48%, rgba(232, 221, 206, 0.1) 100%),
      repeating-linear-gradient(115deg, rgba(245, 242, 236, 0.045) 0 1px, transparent 1px 88px);
    mix-blend-mode: screen;
    opacity: 0.55;
  }

  .feature-project-light {
    background:
      radial-gradient(circle at 78% 15%, rgba(232, 221, 206, 0.25), transparent 26rem),
      radial-gradient(circle at 9% 74%, rgba(111, 128, 106, 0.2), transparent 24rem),
      linear-gradient(120deg, rgba(5, 5, 5, 0.08), rgba(232, 221, 206, 0.12));
    mix-blend-mode: soft-light;
  }

  .story-section {
    background:
      radial-gradient(circle at 16% 20%, rgba(214, 154, 102, 0.18), transparent 24rem),
      radial-gradient(circle at 84% 78%, rgba(126, 139, 116, 0.16), transparent 24rem),
      linear-gradient(115deg, #1c1914 0%, #222019 48%, #263025 100%);
  }

  .story-backdrop {
    background:
      radial-gradient(circle at 78% 18%, rgba(232, 221, 206, 0.08), transparent 20rem),
      linear-gradient(90deg, rgba(14, 12, 9, 0.18), rgba(24, 21, 16, 0.46) 52%, rgba(38, 48, 37, 0.28)),
      repeating-linear-gradient(90deg, rgba(245, 242, 236, 0.045) 0 1px, transparent 1px 132px);
  }

  .home-continuation {
    background:
      linear-gradient(rgba(245, 242, 236, 0.024) 1px, transparent 1px),
      linear-gradient(90deg, rgba(245, 242, 236, 0.022) 1px, transparent 1px),
      radial-gradient(circle at 82% 8%, rgba(214, 154, 102, 0.08), transparent 28rem),
      radial-gradient(circle at 10% 28%, rgba(126, 139, 116, 0.08), transparent 26rem),
      linear-gradient(180deg, #171511 0%, #12110e 44%, #171611 100%);
    background-size: 132px 132px, 132px 132px, auto, auto, auto;
  }

  .premium-footer {
    background:
      linear-gradient(rgba(245, 242, 236, 0.026) 1px, transparent 1px),
      linear-gradient(90deg, rgba(245, 242, 236, 0.024) 1px, transparent 1px),
      #171511;
    background-size: 132px 132px;
  }

  .premium-footer::before {
    content: none;
  }

  @media (max-width: 767px) {
    .hero-video {
      opacity: 0.62;
    }

    .hero-light-field {
      opacity: 0.46;
    }

  }

  .magnetic-card {
    transform-style: preserve-3d;
    will-change: transform;
  }

  .copper-text {
    color: var(--color-copper);
  }

  @keyframes reviewFloat {
    0%, 100% {
      transform: translate3d(0, 0, 0) rotate(0deg);
    }

    50% {
      transform: translate3d(0, -16px, 0) rotate(1.5deg);
    }
  }

  @keyframes reviewSweep {
    0% {
      transform: translateX(-120%);
    }

    100% {
      transform: translateX(120%);
    }
  }

  @keyframes cinematicPreviewZoom {
    0% {
      transform: scale(1) translate3d(0, 0, 0);
      filter: brightness(0.98) saturate(1);
    }

    50% {
      transform: scale(1.12) translate3d(0.25%, -0.25%, 0);
      filter: brightness(1.04) saturate(1.06);
    }

    100% {
      transform: scale(1) translate3d(0, 0, 0);
      filter: brightness(0.98) saturate(1);
    }
  }

  @keyframes cinematicSheen {
    0% {
      transform: translateX(-120%) skewX(-18deg);
      opacity: 0;
    }

    18% {
      opacity: 1;
    }

    100% {
      transform: translateX(145%) skewX(-18deg);
      opacity: 0;
    }
  }

  @keyframes cinematicScan {
    0% {
      transform: translateY(-18%);
    }

    100% {
      transform: translateY(18%);
    }
  }

  @keyframes cinematicFlipOne {
    0%, 24% {
      opacity: 1;
      transform: translate3d(0, 0, 0) scale(1.02);
    }

    32%, 92% {
      opacity: 0;
      transform: translate3d(-4%, 0, 0) scale(1.04);
    }

    100% {
      opacity: 1;
      transform: translate3d(0, 0, 0) scale(1.02);
    }
  }

  @keyframes cinematicFlipTwo {
    0%, 24% {
      opacity: 0;
      transform: translate3d(4%, 0, 0) scale(1.04);
    }

    32%, 56% {
      opacity: 1;
      transform: translate3d(0, 0, 0) scale(1.02);
    }

    64%, 100% {
      opacity: 0;
      transform: translate3d(-4%, 0, 0) scale(1.04);
    }
  }

  @keyframes cinematicFlipThree {
    0%, 56% {
      opacity: 0;
      transform: translate3d(4%, 0, 0) scale(1.04);
    }

    64%, 88% {
      opacity: 1;
      transform: translate3d(0, 0, 0) scale(1.02);
    }

    100% {
      opacity: 0;
      transform: translate3d(-4%, 0, 0) scale(1.04);
    }
  }

  @media (min-width: 1024px) and (pointer: fine) {
    .hero-video,
    .project-bg {
      will-change: transform;
    }

    .slides-wrap .snap-section {
      will-change: transform, opacity;
    }
  }

  @media (min-width: 1200px) and (pointer: fine) {
    .slides-wrap .snap-section {
      position: sticky;
      top: 0;
      min-height: 100vh;
    }

    .slides-wrap .snap-section:nth-child(1) {
      z-index: 1;
    }

    .slides-wrap .snap-section:nth-child(2) {
      z-index: 2;
    }

    .slides-wrap .snap-section:nth-child(3) {
      z-index: 3;
    }

    .slides-wrap .snap-section:nth-child(4) {
      z-index: 4;
    }

    .slides-wrap .snap-section:nth-child(5) {
      z-index: 5;
    }
  }

  @media (max-width: 1023px), (pointer: coarse) {
    .slides-wrap {
      box-shadow: none;
    }

    .slides-wrap .snap-section {
      transform: none !important;
      border-radius: 0 !important;
      will-change: auto;
      contain: paint;
    }

    .hero-video,
    .project-bg,
    .story-backdrop {
      filter: brightness(0.82) contrast(1.02) saturate(0.92);
      will-change: transform;
      backface-visibility: hidden;
      transform-origin: center center;
    }

    .motion-progress,
    .motion-curtain,
    .cinematic-frame,
    .magnetic-card {
      will-change: auto;
    }
  }

  @keyframes mediaFrameLoading {
    0% {
      background-position: -140% 0, center, center;
    }

    100% {
      background-position: 140% 0, center, center;
    }
  }

  @keyframes mediaFrameSpin {
    to {
      transform: rotate(360deg);
    }
  }

  .cinematic-image {
    background-color: #0c0b09;
    transform: translateZ(0);
  }

  .media-frame-loader {
    opacity: 1;
    transition: opacity 0.42s ease, visibility 0.42s ease;
  }

  .media-frame-loader::before {
    content: "";
    position: absolute;
    inset: 0;
    background:
      linear-gradient(110deg, transparent 0%, rgba(245,242,236,0.08) 42%, transparent 58%),
      radial-gradient(circle at 24% 18%, rgba(214,154,102,0.15), transparent 24rem),
      #17130f;
    animation: mediaFrameLoading 1.25s ease-in-out infinite;
  }

  .media-frame-loader__ring {
    position: relative;
    z-index: 1;
    width: 34px;
    height: 34px;
    border-radius: 999px;
    border: 1px solid rgba(245,242,236,0.18);
    border-top-color: rgba(214,154,102,0.9);
    animation: mediaFrameSpin 0.9s linear infinite;
  }

  .media-frame-loaded .media-frame-loader {
    opacity: 0;
    visibility: hidden;
  }

  .cinematic-frame {
    transform: scale(1);
    will-change: transform, opacity;
  }

  .cinematic-image-preview:hover .cinematic-frame-base,
  .cinematic-image-preview:focus-within .cinematic-frame-base,
  .group:hover .cinematic-image-preview .cinematic-frame-base,
  .group:focus-within .cinematic-image-preview .cinematic-frame-base {
    animation: cinematicPreviewZoom 13s ease-in-out infinite;
  }

  .cinematic-image-preview:hover .cinematic-frame-layer,
  .cinematic-image-preview:focus-within .cinematic-frame-layer,
  .group:hover .cinematic-image-preview .cinematic-frame-layer,
  .group:focus-within .cinematic-image-preview .cinematic-frame-layer {
    opacity: 0;
    animation: none;
  }

  .cinematic-image-preview:hover .cinematic-frame-layer-1,
  .cinematic-image-preview:focus-within .cinematic-frame-layer-1,
  .group:hover .cinematic-image-preview .cinematic-frame-layer-1,
  .group:focus-within .cinematic-image-preview .cinematic-frame-layer-1 {
    opacity: 0;
  }

  .cinematic-image-preview:hover .cinematic-frame-layer-2,
  .cinematic-image-preview:focus-within .cinematic-frame-layer-2,
  .group:hover .cinematic-image-preview .cinematic-frame-layer-2,
  .group:focus-within .cinematic-image-preview .cinematic-frame-layer-2 {
    opacity: 0;
  }

  .cinematic-image-preview:hover .cinematic-frame-layer-3,
  .cinematic-image-preview:focus-within .cinematic-frame-layer-3,
  .group:hover .cinematic-image-preview .cinematic-frame-layer-3,
  .group:focus-within .cinematic-image-preview .cinematic-frame-layer-3 {
    opacity: 0;
  }

  .cinematic-image-frames:hover .cinematic-frame-base,
  .cinematic-image-frames:focus-within .cinematic-frame-base,
  .group:hover .cinematic-image-frames .cinematic-frame-base,
  .group:focus-within .cinematic-image-frames .cinematic-frame-base {
    animation: cinematicFlipOne 5.4s ease-in-out infinite;
  }

  .cinematic-image-frames:hover .cinematic-frame-layer-1,
  .cinematic-image-frames:focus-within .cinematic-frame-layer-1,
  .group:hover .cinematic-image-frames .cinematic-frame-layer-1,
  .group:focus-within .cinematic-image-frames .cinematic-frame-layer-1 {
    animation: cinematicFlipTwo 5.4s ease-in-out infinite;
  }

  .cinematic-image-frames:hover .cinematic-frame-layer-2,
  .cinematic-image-frames:focus-within .cinematic-frame-layer-2,
  .group:hover .cinematic-image-frames .cinematic-frame-layer-2,
  .group:focus-within .cinematic-image-frames .cinematic-frame-layer-2 {
    animation: cinematicFlipThree 5.4s ease-in-out infinite;
  }

  .cinematic-sheen::before {
    content: none;
  }

  .cinematic-image:hover .cinematic-sheen::before,
  .cinematic-image:focus-within .cinematic-sheen::before {
    animation: none;
  }

  .cinematic-scan {
    display: none;
  }

  .review-orbit {
    animation: reviewFloat 8s ease-in-out infinite;
  }

  .review-orbit-delayed {
    animation: reviewFloat 9s ease-in-out 1.4s infinite;
  }

  .review-sheen::before {
    content: "";
    position: absolute;
    inset: 0;
    pointer-events: none;
    background: linear-gradient(110deg, transparent 0%, rgba(214,154,102, 0.16) 48%, transparent 62%);
    opacity: 0;
    transform: translateX(-120%);
  }

  .review-sheen:hover::before {
    opacity: 1;
    animation: reviewSweep 1.15s ease forwards;
  }
`;

export const GlassPanel = styled.div`
  border: 1px solid rgba(245, 242, 236, 0.14);
  background:
    linear-gradient(135deg, rgba(255, 255, 255, 0.075), rgba(255, 255, 255, 0.018)),
    linear-gradient(180deg, rgba(214,154,102, 0.055), transparent 54%);
  box-shadow: 0 18px 50px rgba(0, 0, 0, 0.24), inset 0 1px 0 rgba(255, 255, 255, 0.08);
`;

export const Noise = styled.div`
  display: none;
`;
