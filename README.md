# SCSVMV Technology Business Incubator (TBI)

Landing page plus a PHP incubatee application form. After submit, the site saves to MySQL and generates a **filled PDF** (same sections as `assets/docs/application-form.pdf`) for download.

Stack: **HTML/CSS + PHP 8 + MySQL + Dompdf**, for Hostinger or A2 shared hosting.

## Important

- Keep the homepage as **`index.html`**. Do not put the apply form in a root `index.php` (hosts prefer `index.php` and would hide the landing page).
- **Fill application** → [`apply/index.php`](apply/index.php)
- `npx serve` only previews the static site; it cannot run PHP or build PDFs.

## Preview locally

### Landing page only (static)

```bash
npx --yes serve -l 4173 .
```

Visit `http://localhost:4173`.

### Form + PDF (PHP + MySQL)

1. Install PHP 8+ and Composer on your machine (or use Hostinger to test).
2. Create DB and import schema:

```bash
cp config.sample.php config.php
# edit config.php — db credentials, admin_password, notify_email
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS tbi_apply CHARACTER SET utf8mb4;"
mysql -u root -p tbi_apply < sql/schema.sql
```

3. Install Dompdf:

```bash
composer install
```

4. Run PHP’s built-in server from the project root:

```bash
php -S localhost:4173
```

5. Open `http://localhost:4173` → **Fill application** → submit → **Download filled application PDF**.

Admin: `http://localhost:4173/admin/` (password from `config.php`).

## Hostinger / A2 deploy

1. Create a MySQL database and user in hPanel or cPanel.
2. Import `sql/schema.sql` (phpMyAdmin).
3. Upload project files to `public_html` (or a subdomain document root).
4. On the server (SSH or local then upload `vendor/`):

```bash
composer install --no-dev --optimize-autoloader
```

5. Copy `config.sample.php` → `config.php` with:
   - `db.host` (usually `localhost`)
   - `db.name`, `db.user`, `db.pass`
   - `admin_password`
   - `notify_email` / `from_email`
6. PHP 8.x; raise `upload_max_filesize` / `post_max_size` if needed.
7. Enable HTTPS.
8. Keep `config.php` and `uploads/` off public listing (`uploads/.htaccess` blocks direct access).

## Flow

1. Visitor fills `apply/index.php`.
2. `apply/submit.php` validates, saves MySQL, generates `uploads/{id}/application.pdf`.
3. `apply/success.php` offers **Download filled application PDF** (`apply/download-pdf.php`).
4. Blank official PDF remains available as **Download PDF**.

PAN and Aadhaar are **not** collected on the form or in the generated PDF.

## Structure

```
index.html                 # landing page (homepage)
apply/index.php            # web form
apply/submit.php           # save + generate PDF
apply/success.php
apply/download-pdf.php
admin/                     # password-protected submissions
includes/
  app.php
  generate_application_pdf.php
  pdf_application_template.php
sql/schema.sql
composer.json              # dompdf/dompdf
config.sample.php
uploads/                   # private uploads + filled PDFs
```

## Security notes

- External links use `rel="noopener noreferrer"` and `https://`.
- Form uses CSRF token, honeypot, prepared statements, file type/size checks, and IP rate limiting.
- PDF download after submit is limited to the current session’s latest application id.
- Prefer CSP / HSTS on the host; `.htaccess` sets `X-Content-Type-Options: nosniff`.
