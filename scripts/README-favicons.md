# Favicons

Square PNGs in `public/` (`favicon-16x16.png` … `favicon-512x512.png`) are generated from `public/logo.png` (center crop).

After you replace `logo.png`, regenerate:

```bash
php scripts/generate-favicons.php
cp public/favicon-32x32.png public/favicon.ico
```

`layouts/app.blade.php` references these files plus `site.webmanifest` and `browserconfig.xml`.
