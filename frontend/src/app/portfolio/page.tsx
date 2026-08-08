"use client";

import { useEffect, useState } from "react";
import { useCms } from "@/src/cms";
import PortfolioPage from "@/src/modules/pages/PortfolioPage";

export default function Page() {
  const { projects } = useCms();
  const selectedProject = projects.find((project) => project.isSelected) ?? projects[0];
  const [activeProject, setActiveProject] = useState(selectedProject);

  useEffect(() => {
    const selected = projects.find((project) => project.isSelected) ?? projects[0];
    if (!selected) return;

    setActiveProject((current) => {
      if (!current) return selected;
      const currentProject = projects.find((project) => project.slug === current.slug);

      if (!currentProject) return selected;

      return currentProject;
    });
  }, [projects]);

  return (
    <PortfolioPage
      activeProject={activeProject}
      setActiveProject={setActiveProject}
    />
  );
}
