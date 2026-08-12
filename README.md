# SCSVMV Technology Business Incubator (TBI)

Static landing page styled to match [kanchiuniv.ac.in](https://kanchiuniv.ac.in/) (Astra + Elementor brand: navy `#28245B`, red `#990102`, yellow `#FFF900`).

## Preview locally

```bash
# Option A — open the file
open index.html

# Option B — static server
npx --yes serve -l 4173 .
```

Then visit `http://localhost:4173`.

## Structure

```
index.html
styles/main.css
scripts/main.js
assets/logos/scsvmv-logo.png   # official university seal
assets/logos/                  # MSME/EDII/StartupTN/Startup India still placeholders
README.md
```

## WordPress handoff (kanchiuniv.ac.in)

1. Create or update a page under Incubation (e.g. **Technology Business Incubator**).
2. Upload `assets/` to the media library (or a `/tbi/` folder on the server).
3. Paste sections into Elementor **HTML** widgets, updating image paths to the uploaded URLs **or** host the whole folder as a subdirectory and link from the menu.
4. Point **Apply** to the live application form URL.
5. Replace placeholder logos with official MSME, EDII, StartupTN, and Startup India artwork.

## Asset checklist

- [ ] Official MSME logo
- [ ] Official EDII logo
- [ ] Official StartupTN logo
- [ ] Official Startup India logo
- [ ] Optional campus / TBI photos for the hero or projects section
- [ ] Confirm EDII grant programme name (e.g. TIDE / other) for final copy

## Security notes (v1)

- Static HTML only — no backend, login, or data collection on this page.
- External links use `rel="noopener noreferrer"` and `https://`.
- JS does not use `innerHTML` and makes no network requests.
- Sensitive documents (PAN/Aadhaar) are listed as checklist items only; visitors are told to use official channels.
- Prefer university CSP / HSTS already on kanchiuniv; if hosting as files, set `X-Content-Type-Options: nosniff`.
- Optional later: self-host Noto Serif + Poppins instead of Google Fonts.
