import type { Metadata } from "next";
import StyledRegistry from "@/src/components/providers/StyledRegistry";
import ClientShell from "@/src/components/layout/ClientShell";
import Header from "@/src/components/layout/Header";
import Breadcrumbs from "@/src/components/layout/Breadcrumbs";
import Footer from "@/src/components/layout/Footer";
import Analytics from "@/src/components/layout/Analytics";
import MaintenanceGate from "@/src/components/layout/MaintenanceGate";
import SiteMetadata from "@/src/components/layout/SiteMetadata/SiteMetadata";
import { CmsProvider } from "@/src/cms";
import { SiteI18nProvider } from "@/src/i18n";
import { getSiteMetadata, loadCmsPayload } from "./siteMetadata";
import "pannellum/build/pannellum.css";
import "./globals.css";

export async function generateMetadata(): Promise<Metadata> {
  return getSiteMetadata();
}

export default async function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const initialCmsPayload = await loadCmsPayload();

  return (
    <html lang="ru">
      <body>
        <StyledRegistry>
          <MaintenanceGate enabled={initialCmsPayload?.settings?.maintenanceEnabled ?? true}>
            <SiteI18nProvider>
              <CmsProvider initialPayload={initialCmsPayload}>
                <SiteMetadata />
                <Analytics />
                <ClientShell>
                  <Header />
                  <Breadcrumbs />
                  {children}
                  <Footer />
                </ClientShell>
              </CmsProvider>
            </SiteI18nProvider>
          </MaintenanceGate>
        </StyledRegistry>
      </body>
    </html>
  );
}
