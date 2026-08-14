# SCSVMV Technology Business Incubator (TBI)

Landing page plus a PHP incubatee application form (same fields as `assets/docs/application-form.pdf`). Stack: **HTML/CSS + PHP 8 + MySQL**, for Hostinger or A2 shared hosting.

## Preview locally

The home page is static. The application form needs PHP and MySQL.

```bash
# Static preview of the landing page
npx --yes serve -l 4173 .

# Form (requires PHP + MySQL)
cp config.sample.php config.php
# edit config.php with local DB credentials
mysql -u root -p tbi_apply < sql/schema.sql
php -S localhost:4173
```

Then visit `http://localhost:4173` and `http://localhost:4173/apply/`.

## Hostinger / A2 deploy

1. Create a MySQL database and user in hPanel or cPanel.
2. Import `sql/schema.sql` (phpMyAdmin).
3. Upload all files to `public_html` (or a subdomain document root).
4. Copy `config.sample.php` to `config.php` on the server. Set:
   - `db.host` (usually `localhost`)
   - `db.name`, `db.user`, `db.pass`
   - `admin_password` (for `/admin/`)
   - `notify_email` / `from_email`
5. Confirm PHP 8.x and raise `upload_max_filesize` / `post_max_size` if needed (photo 2 MB, write-up 5 MB).
6. Enable HTTPS.
7. Keep `config.php` off git. `uploads/` is blocked from direct web access.

Admin list: `https://your-domain/admin/` (password from `config.php`).

PAN and Aadhaar are **not** collected on the form.

## Structure

```
index.html
styles/main.css
scripts/main.js
assets/logos/scsvmv-logo.png   # official university seal
assets/logos/msme.png            # Ministry of MSME
assets/logos/edii.png            # EDII Tamil Nadu (editn.in)
assets/logos/startuptn.png       # StartupTN (official logo, transparent background)
assets/logos/startup-india.png   # Startup India (uxdt.nic.in)
README.md
```

## WordPress handoff (kanchiuniv.ac.in)

1. Create or update a page under Incubation (e.g. **Technology Business Incubator**).
2. Upload `assets/` to the media library (or a `/tbi/` folder on the server).
3. Paste sections into Elementor **HTML** widgets, updating image paths to the uploaded URLs **or** host the whole folder as a subdirectory and link from the menu.
4. Point **Apply** to the live application form URL.
5. Affiliation logos are included under `assets/logos/` (MSME, EDII, StartupTN, Startup India).

## Asset checklist

- [x] Official MSME logo
- [x] Official EDII logo
- [x] Official StartupTN logo
- [x] Official Startup India logo
- [ ] Optional campus / TBI photos for the hero or projects section
- [ ] Confirm EDII grant programme name (e.g. TIDE / other) for final copy

## Security notes

- External links use `rel="noopener noreferrer"` and `https://`.
- Form uses CSRF token, honeypot, prepared statements, file type/size checks, and IP rate limiting.
- PAN/Aadhaar are not accepted on the website.
- Prefer CSP / HSTS on the host; this repo sets `X-Content-Type-Options: nosniff` via `.htaccess`.
