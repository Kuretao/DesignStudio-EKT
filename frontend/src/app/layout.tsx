import type { Metadata } from "next";
import StyledRegistry from "@/src/components/providers/StyledRegistry";
import ClientShell from "@/src/components/layout/ClientShell";
import Header from "@/src/components/layout/Header";
import Breadcrumbs from "@/src/components/layout/Breadcrumbs";
import Footer from "@/src/components/layout/Footer";
import Analytics from "@/src/components/layout/Analytics";
import SiteMetadata from "@/src/components/layout/SiteMetadata/SiteMetadata";
import { CmsProvider } from "@/src/cms";
import { SiteI18nProvider } from "@/src/i18n";
import "pannellum/build/pannellum.css";
import "./globals.css";

const googleSiteVerification =
  process.env.GOOGLE_SITE_VERIFICATION ??
  "HlopiXKhbxQ7ylgQsea3aHhSYGyqjgy6Xgq55kBJffc";

export const metadata: Metadata = {
  metadataBase: new URL(
    process.env.NEXT_PUBLIC_SITE_URL ?? "https://3dsmartdesign.ru",
  ),
  icons: {
    icon: "/logo.png",
    shortcut: "/logo.png",
    apple: "/logo.png",
  },
  title:
    "Студия дизайна интерьера, архитектуры и ландшафта в Самаре | 3D Smart Design Studio",
  description:
    "3D Smart Design Studio: дизайн интерьера, архитектурное проектирование, 3D-визуализация, ландшафтный дизайн, комплектация и авторский надзор в Самаре.",
  keywords: [
    "дизайн интерьера Самара",
    "архитектура Самара",
    "ландшафтный дизайн Самара",
    "3D-визуализация",
    "3D Smart Design Studio",
  ],
  verification: {
    google: googleSiteVerification,
    yandex: process.env.YANDEX_SITE_VERIFICATION,
  },
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="ru">
      <body>
        <StyledRegistry>
          <SiteI18nProvider>
            <CmsProvider>
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
        </StyledRegistry>
      </body>
    </html>
  );
}
