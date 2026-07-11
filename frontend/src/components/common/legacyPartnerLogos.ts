const LEGACY_PARTNER_LOGOS: Record<string, string> = {
  capital_group_companies: "/images/partners/capital-group.webp",
  new_don: "/images/partners/new-don.webp",
  donstroy: "/images/partners/donstroy.svg",
  new_time: "/images/partners/new-time.svg",
  stroy_stil: "/images/partners/fsk.svg",
  parkoviy: "/images/partners/parkovy.webp",
  vesna: "/images/partners/vesna.webp",
  bereg: "/images/partners/grad.svg",
};

export function resolvePartnerLogo(logo?: string | null) {
  if (!logo) return logo;

  const normalized = logo.toLowerCase();
  const legacyKey = Object.keys(LEGACY_PARTNER_LOGOS).find((key) =>
    normalized.includes(`/d/${key}.`),
  );

  return legacyKey ? LEGACY_PARTNER_LOGOS[legacyKey] : logo;
}
