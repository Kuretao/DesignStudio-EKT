import { ImageResponse } from "next/og";
import { absoluteSiteUrl, loadSiteMetadataSettings } from "../siteMetadata";

export const dynamic = "force-dynamic";

const size = {
  width: 1200,
  height: 630,
};

export async function GET() {
  const settings = await loadSiteMetadataSettings();
  const siteName = settings?.siteName?.trim() || "3D Smart Design Studio";
  const title = settings?.seoTitle?.trim() || siteName;
  const description =
    settings?.seoDescription?.trim() ||
    "Студия концептуального дизайна. Интерьеры, архитектура, ландшафт.";
  const image =
    absoluteSiteUrl(settings?.socialPreviewImage || settings?.logo) ||
    absoluteSiteUrl("/logo.png") ||
    "";

  return new ImageResponse(
    (
      <div
        style={{
          position: "relative",
          display: "flex",
          width: "100%",
          height: "100%",
          overflow: "hidden",
          background: "#0c0b09",
          color: "#f5f2ec",
          fontFamily: "Arial, sans-serif",
        }}
      >
        {image ? (
          <img
            src={image}
            alt=""
            style={{
              position: "absolute",
              inset: 0,
              width: "100%",
              height: "100%",
              objectFit: "cover",
            }}
          />
        ) : null}
        <div
          style={{
            position: "absolute",
            inset: 0,
            background:
              "linear-gradient(90deg, rgba(5,5,5,.9), rgba(5,5,5,.58) 52%, rgba(5,5,5,.18)), linear-gradient(0deg, rgba(5,5,5,.78), rgba(5,5,5,.12) 58%)",
          }}
        />
        <div
          style={{
            position: "relative",
            display: "flex",
            flexDirection: "column",
            justifyContent: "flex-end",
            width: "100%",
            padding: "72px",
          }}
        >
          <div
            style={{
              display: "flex",
              color: "#d69a66",
              fontSize: 22,
              letterSpacing: 8,
              textTransform: "uppercase",
            }}
          >
            {siteName}
          </div>
          <div
            style={{
              display: "flex",
              maxWidth: 820,
              marginTop: 28,
              fontSize: 74,
              lineHeight: 0.98,
              fontWeight: 400,
              letterSpacing: -2,
            }}
          >
            {title}
          </div>
          <div
            style={{
              display: "flex",
              maxWidth: 720,
              marginTop: 26,
              color: "#d6d1ca",
              fontSize: 30,
              lineHeight: 1.35,
            }}
          >
            {description}
          </div>
        </div>
      </div>
    ),
    {
      ...size,
      headers: {
        "Cache-Control": "public, max-age=0, s-maxage=3600",
      },
    },
  );
}
