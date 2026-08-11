"use client";

import Link from "next/link";
import { useState } from "react";
import { useCms, useCmsText } from "@/src/cms";
import type { Project } from "@/src/types";
import { GlassPanel } from "@/src/ui";
import FAQ from "@/src/components/common/FAQ";
import BrandStrip from "@/src/components/common/BrandStrip";
import CinematicImage from "@/src/components/common/CinematicImage";
import ProjectQuiz from "@/src/components/common/ProjectQuiz";
import StyleLab from "@/src/components/common/StyleLab";
import AwardsSection from "@/src/components/common/AwardsSection";
import HeroBackdropSlider from "@/src/components/common/HeroBackdropSlider";
import AboutPage from "@/src/modules/pages/AboutPage";
import { ContactSection } from "@/src/modules/pages/ContactPage";
import { PortfolioGrid, ProjectShowcase } from "@/src/modules/pages/PortfolioPage";
import { ServicePages, ServicesSummary, Workflow } from "@/src/modules/pages/ServicesPage";
import ContactModal from "@/src/modals/ContactModal";
import { optimizeImageUrl } from "@/src/utils/images";

const assetPrefix = process.env.NEXT_PUBLIC_BASE_PATH ?? "";

type HomePageProps = {
  activeProject?: Project;
  setActiveProject: (project: Project) => void;
};

function FeatureProject({
  project,
  fallbackLabel,
  labels,
  reverse = false,
}: {
  project: Project;
  fallbackLabel: string;
  labels: { type: string; location: string; year: string };
  reverse?: boolean;
}) {
  const image = optimizeImageUrl(project.featuredImage || project.image, 1600, 76);
  const title = project.featuredTitle || project.title;
  const description = project.featuredDescription || project.description;
  const label = project.featuredLabel || fallbackLabel;

  return (
    <section className="snap-section feature-project-section relative flex min-h-[100svh] items-end overflow-hidden px-5 py-16 md:px-10 lg:min-h-screen lg:px-16">
      <CinematicImage
        frames={[image, ...(project.featuredGalleryImages ?? []), project.afterImage, project.beforeImage]}
        alt={title}
        fill
        className="project-bg"
        hint="frames"
        mode="frames"
        showHint={false}
      />
      <div className="feature-project-light absolute inset-0" />
      <div className="absolute inset-0 bg-gradient-to-t from-[#080705] via-[#080705]/30 to-transparent" />
      <div className="absolute inset-0 bg-gradient-to-r from-[#080705]/72 via-[#324238]/12 to-[#E8DDCE]/18" />

      <div className={`relative z-10 grid w-full gap-8 md:grid-cols-2 ${reverse ? "md:[&>*:first-child]:col-start-2" : ""}`}>
        <GlassPanel data-reveal-card className="section-in magnetic-card rounded-[2rem] p-7 md:p-10">
          <p className="mb-6 text-xs uppercase tracking-[0.42em] text-[#D69A66]">{label}</p>
          <h2 className="text-[clamp(2.7rem,5vw,4.8rem)] font-light leading-tight tracking-normal [overflow-wrap:anywhere]">{title}</h2>
          <p className="mt-5 max-w-xl text-base leading-relaxed text-[#D6D1CA] md:text-lg">{description}</p>
          <div className="mt-9 grid grid-cols-3 gap-4 border-t border-white/15 pt-6 text-sm text-[#D6D1CA]">
            <div><span className="block text-white">{project.category}</span>{labels.type}</div>
            <div><span className="block text-white">{project.location}</span>{labels.location}</div>
            <div><span className="block text-white">{project.year}</span>{labels.year}</div>
          </div>
        </GlassPanel>
      </div>
    </section>
  );
}

function HomePage({ activeProject, setActiveProject }: HomePageProps) {
  const { homeHero, homeStory, projects, ready } = useCms();
  const text = useCmsText();
  const [contactModalOpen, setContactModalOpen] = useState(false);
  const storyText = homeStory.text;
  const heroLinkOpensModal = homeHero.linkHref === "/kontakty" || homeHero.linkLabel.toLowerCase().includes("обсудить");
  const projectLabels = {
    type: text("home.projectTypeLabel", "Тип"),
    location: text("home.projectLocationLabel", "Локация"),
    year: text("home.projectYearLabel", "Год"),
  };
  const featuredProjects = projects.filter((project) => project.isFeatured);
  const primaryFeatured = featuredProjects[0] ?? projects[0];
  const secondaryFeatured = featuredProjects[1] ?? projects[2] ?? projects[1] ?? projects[0];
  const heroMedia = homeHero.images.length ? homeHero.images : [`${assetPrefix}/background112233.mp4`];
  const heroVideo = heroMedia.find((source) => /\.(?:mp4|webm|ogg)(?:$|\?)/iu.test(source));

  return (
    <>
      <div className="slides-wrap relative isolate overflow-x-clip bg-[#0f0d0a]">
        <section className="hero-section snap-section relative z-[1] flex min-h-[100svh] overflow-hidden px-5 py-28 md:px-10 lg:min-h-screen lg:px-16">
          {heroVideo ? (
            <video
              className="hero-video absolute inset-0 h-full w-full object-cover opacity-75"
              src={heroVideo}
              autoPlay
              muted
              loop
              playsInline
              preload="metadata"
            />
          ) : (
            <HeroBackdropSlider
              slides={heroMedia.map((image, index) => ({
                image,
                alt: index === 0 ? homeHero.title : "",
              }))}
              className="hero-video opacity-75"
              controlsClassName="hidden"
            />
          )}
          <div className="hero-light-field absolute inset-0" />
          <div className="absolute inset-0 bg-[radial-gradient(circle_at_72%_18%,rgba(232,221,206,.22),transparent_33%),radial-gradient(circle_at_18%_78%,rgba(111,128,106,.16),transparent_36%),linear-gradient(90deg,rgba(8,7,5,.88),rgba(8,7,5,.58)_44%,rgba(15,13,10,.2))]" />
          <div className="hero-copper-line absolute bottom-0 left-0 z-[1] h-px w-full bg-gradient-to-r from-[#D69A66] via-white/30 to-transparent" />

          <div className="relative z-10 flex w-full items-end">
            <div className="max-w-6xl">
              {homeHero.eyebrow ? (
                <p className="hero-reveal mb-5 text-xs uppercase tracking-[0.45em] text-[#D69A66]">
                  {homeHero.eyebrow}
                </p>
              ) : null}
              <h1 className="hero-reveal max-w-5xl text-[clamp(2.9rem,5.6vw,5.5rem)] font-light leading-[0.96] tracking-normal md:tracking-[-0.035em]">
                {homeHero.title}
              </h1>
              <div className="hero-reveal mt-8 grid max-w-4xl gap-6 md:grid-cols-[1fr_auto] md:items-end">
                {homeHero.text ? (
                  <p className="text-lg leading-relaxed text-[#D6D1CA] md:text-xl">
                    {homeHero.text}
                  </p>
                ) : null}
                {homeHero.linkHref && homeHero.linkLabel ? (
                  heroLinkOpensModal ? (
                    <button
                      type="button"
                      onClick={() => setContactModalOpen(true)}
                      className="group inline-flex h-14 items-center justify-center rounded-full border border-[#D69A66]/50 px-7 text-sm uppercase tracking-[0.22em] text-[#F5F2EC] transition hover:bg-[#D69A66] hover:text-[#0c0b09]"
                    >
                      {homeHero.linkLabel}
                      <span className="ml-3 transition group-hover:translate-x-1">→</span>
                    </button>
                  ) : (
                    <Link
                      href={homeHero.linkHref}
                      className="group inline-flex h-14 items-center justify-center rounded-full border border-[#D69A66]/50 px-7 text-sm uppercase tracking-[0.22em] text-[#F5F2EC] transition hover:bg-[#D69A66] hover:text-[#0c0b09]"
                    >
                      {homeHero.linkLabel}
                      <span className="ml-3 transition group-hover:translate-x-1">→</span>
                    </Link>
                  )
                ) : null}
              </div>
            </div>
          </div>
        </section>

        {ready && primaryFeatured ? (
          <FeatureProject project={primaryFeatured} fallbackLabel={text("home.featureProject01", "Избранный проект 01")} labels={projectLabels} />
        ) : null}

        <section className="snap-section story-section relative z-[1] flex min-h-[100svh] items-start overflow-hidden px-5 pb-20 pt-28 md:px-10 md:pt-32 lg:min-h-screen lg:items-center lg:px-16 lg:py-28">
          <div className="story-backdrop absolute inset-0" aria-hidden="true" />
          
          <div className="relative z-10 mx-auto max-w-6xl">
            <p className="section-in mb-10 text-xs uppercase tracking-[0.45em] text-[#D69A66]">{homeStory.eyebrow}</p>
            <h2 className="text-[clamp(2.3rem,4.8vw,4.7rem)] font-light leading-tight tracking-normal text-[#F5F2EC] md:tracking-[-0.035em]">
              {storyText.split(" ").map((word, index) => (
                <span key={`${word}-${index}`} className="story-word inline-block pr-3">
                  {word}
                </span>
              ))}
            </h2>
          </div>
        </section>

        {ready && secondaryFeatured ? (
          <FeatureProject project={secondaryFeatured} fallbackLabel={text("home.featureProject02", "Избранный проект 02")} labels={projectLabels} reverse />
        ) : null}

        <section className="snap-section portfolio-grid-section relative z-[1] min-h-[100svh] px-5 py-28 md:px-10 lg:min-h-screen lg:px-16">
          <PortfolioGrid onSelectProject={setActiveProject} />
        </section>
      </div>

      <StyleLab />

      <div className="home-continuation relative overflow-hidden">
        <div className="relative z-10">
          {activeProject ? (
            <div className="home-flow-reveal"><ProjectShowcase project={activeProject} /></div>
          ) : null}
          <div className="home-flow-reveal"><AboutPage /></div>
          <div className="home-flow-reveal"><AwardsSection /></div>
          <div className="home-flow-reveal"><BrandStrip /></div>
          <div className="home-flow-reveal"><ServicesSummary /></div>
          <div className="home-flow-reveal"><ServicePages /></div>
          <div className="home-flow-reveal"><ProjectQuiz /></div>
          <div className="home-flow-reveal"><Workflow /></div>
          <div className="home-flow-reveal"><FAQ /></div>
          <div className="home-flow-reveal"><ContactSection /></div>
        </div>
      </div>
      <ContactModal open={contactModalOpen} onClose={() => setContactModalOpen(false)} />
    </>
  );
}

export default HomePage;
