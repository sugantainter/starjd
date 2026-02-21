export const PLATFORM_NAMES = {
  instagram: 'Instagram',
  facebook: 'Facebook',
  youtube: 'YouTube',
  tiktok: 'TikTok',
  twitter: 'Twitter / X',
  linkedin: 'LinkedIn',
  snapchat: 'Snapchat',
  pinterest: 'Pinterest',
  other: 'Other',
};

export function platformDisplayName(platform) {
  if (!platform) return '';
  return PLATFORM_NAMES[platform.toLowerCase()] || platform.charAt(0).toUpperCase() + platform.slice(1);
}
