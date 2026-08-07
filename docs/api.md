# Portfolio CMS API Documentation

Dokumen ini menjelaskan API publik untuk website portfolio Next.js. API bersifat read-only, tidak memakai authentication, tidak memakai role/permission middleware, dan hanya menampilkan konten yang aktif/published.

## Ringkasan

| Item | Nilai |
| --- | --- |
| Base URL lokal | `http://127.0.0.1:8000` |
| Base URL production | `https://cms-portfolio.ryucode.site` |
| Prefix API | `/api/v1` |
| Format response | JSON |
| Authentication | Tidak perlu |
| Permission | Tidak perlu |
| Write endpoint | Tidak tersedia |
| Cache HTTP | `Cache-Control: public, max-age=300` |
| Cache server | 15 menit, invalidasi saat data CMS berubah |

Middleware route API yang aktif hanya:

- `throttle:60,1`
- `SubstituteBindings`

Tidak ada middleware berikut pada route API publik:

- `auth`
- `can`
- `role`
- `permission`
- middleware Spatie permission

## Prinsip Desain API

Endpoint tidak lagi dibuat sebagai satu payload besar untuk seluruh database portfolio. `/api/v1/portfolio` hanya dipakai sebagai bootstrap ringan untuk render awal. Data yang berpotensi besar seperti projects, notes, dan media assets dipisah ke endpoint list/detail dengan pagination dan filter.

## Endpoint Summary

| Method | Endpoint | Fungsi |
| --- | --- | --- |
| `GET` | `/api/v1/portfolio` | Bootstrap ringan homepage |
| `GET` | `/api/v1/global` | Site settings, social links, tech stacks |
| `GET` | `/api/v1/landing-page` | Konten lengkap landing page |
| `GET` | `/api/v1/about-page` | Konten lengkap about page |
| `GET` | `/api/v1/projects-page` | Hero project, kategori, featured projects |
| `GET` | `/api/v1/project-categories` | List kategori project aktif |
| `GET` | `/api/v1/projects` | List project published dengan pagination/filter |
| `GET` | `/api/v1/projects/{slug}` | Detail project published |
| `GET` | `/api/v1/notes-page` | Hero notes, kategori, featured notes |
| `GET` | `/api/v1/notes-categories` | List kategori notes aktif |
| `GET` | `/api/v1/notes` | List notes published dengan pagination/filter |
| `GET` | `/api/v1/notes/{slug}` | Detail note published |
| `GET` | `/api/v1/contact-information` | Informasi kontak publik |
| `GET` | `/api/v1/media-assets` | Media assets aktif dengan pagination/filter |

## Standard Response

Endpoint non-paginated menggunakan envelope `data`.

```json
{
  "data": {}
}
```

Endpoint paginated menggunakan struktur berikut.

```json
{
  "data": [],
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 3,
    "per_page": 9,
    "to": 9,
    "total": 27
  },
  "links": {
    "first": "http://localhost:8000/api/v1/projects?page=1",
    "last": "http://localhost:8000/api/v1/projects?page=3",
    "prev": null,
    "next": "http://localhost:8000/api/v1/projects?page=2"
  }
}
```

Object yang belum diisi dari CMS bernilai `null`. Collection kosong bernilai `[]`.

## Bootstrap

### Get Bootstrap Portfolio Content

```http
GET /api/v1/portfolio
```

Endpoint ini hanya untuk render awal. Payload sengaja ringan.

Response berisi:

- `global`
- `landing_page.hero`
- `projects_page.hero`
- `projects_page.featured_projects`, maksimal 6 data
- `notes_page.hero`
- `notes_page.featured_notes`, maksimal 6 data
- `contact_page.information`

Endpoint ini tidak memuat:

- semua project
- semua notes
- detail project
- detail notes
- about sections lengkap
- media library lengkap

## Global

### Get Global Content

```http
GET /api/v1/global
```

Response berisi:

- `site_settings`
- `social_media_links`
- `tech_stacks`

Field media penting memiliki URL absolut, misalnya `logo_light_url`, `logo_dark_url`, `favicon_url`, dan `og_image_url`.

## Landing Page

### Get Landing Page

```http
GET /api/v1/landing-page
```

Response berisi:

- `hero`
- `about_sections[]`
- `about_sections[].items[]`

## About Page

### Get About Page

```http
GET /api/v1/about-page
```

Response berisi:

- `hero`
- `sections[]`
- `sections[].items[]`
- `expertise_sections[]`
- `tools[]`
- `hobby_sections[]`
- `hobby_sections[].items[]`

## Projects

### Get Projects Page Metadata

```http
GET /api/v1/projects-page
```

Response berisi:

- `hero`
- `categories[]`
- `featured_projects[]`, maksimal 6 data

### Get Project Categories

```http
GET /api/v1/project-categories
```

Hanya kategori dengan `is_active = true` yang ditampilkan.

### Get Projects List

```http
GET /api/v1/projects
```

Query parameter:

| Parameter | Type | Default | Keterangan |
| --- | --- | --- | --- |
| `category` | string | null | Filter berdasarkan slug kategori |
| `featured` | boolean | null | `true` hanya featured, `false` hanya non-featured |
| `page` | integer | `1` | Halaman pagination |
| `per_page` | integer | `9` | Jumlah data per halaman, maksimal `24` |

Contoh:

```http
GET /api/v1/projects?category=web-apps&featured=true&page=1&per_page=9
```

List project berisi data ringkas untuk card/list dan tidak membawa `description_rich`, `core_features`, atau `design_highlights`.

Project ditampilkan jika:

- `is_active = true`
- `published_at = null` atau `published_at <= now()`

### Get Project Detail

```http
GET /api/v1/projects/{slug}
```

Contoh:

```http
GET /api/v1/projects/portfolio-cms
```

Detail project memuat:

- field project lengkap
- `category`
- `tech_stacks[]`
- `core_features[]`
- `design_highlights[]`

Jika slug tidak ditemukan atau project belum published, response `404`.

## Notes

### Get Notes Page Metadata

```http
GET /api/v1/notes-page
```

Response berisi:

- `hero`
- `categories[]`
- `featured_notes[]`, maksimal 6 data

### Get Notes Categories

```http
GET /api/v1/notes-categories
```

Hanya kategori dengan `is_active = true` yang ditampilkan.

### Get Notes List

```http
GET /api/v1/notes
```

Query parameter:

| Parameter | Type | Default | Keterangan |
| --- | --- | --- | --- |
| `category` | string | null | Filter berdasarkan slug kategori |
| `featured` | boolean | null | `true` hanya featured, `false` hanya non-featured |
| `page` | integer | `1` | Halaman pagination |
| `per_page` | integer | `10` | Jumlah data per halaman, maksimal `24` |

Contoh:

```http
GET /api/v1/notes?category=laravel&page=1&per_page=10
```

Notes ditampilkan jika:

- `is_active = true`
- `published_at = null` atau `published_at <= now()`

### Get Note Detail

```http
GET /api/v1/notes/{slug}
```

Jika slug tidak ditemukan atau note belum published, response `404`.

## Contact

### Get Contact Information

```http
GET /api/v1/contact-information
```

Response berisi:

- `location`
- `image`
- `image_url`
- `image_alt`
- `email`
- SEO fields

`contact_messages` tidak diekspos karena itu inbox/submission admin.

## Media Assets

### Get Media Assets

```http
GET /api/v1/media-assets
```

Query parameter:

| Parameter | Type | Default | Keterangan |
| --- | --- | --- | --- |
| `collection` | string | null | Filter berdasarkan collection media |
| `page` | integer | `1` | Halaman pagination |
| `per_page` | integer | `24` | Jumlah data per halaman, maksimal `48` |

Contoh:

```http
GET /api/v1/media-assets?collection=seo&page=1&per_page=24
```

Hanya media asset dengan `is_active = true` yang ditampilkan.

## Media URL

Setiap field media penting memiliki pasangan URL absolut dengan suffix `_url`, misalnya:

- `logo_light` dan `logo_light_url`
- `thumbnail` dan `thumbnail_url`
- `photo` dan `photo_url`
- `og_image` dan `og_image_url`
- `file_path` dan `file_path_url`

Media URL dihasilkan dari disk media Laravel. Untuk production project ini disk media harus mengarah ke Cloudflare R2, bukan storage local.

Konfigurasi minimum production:

```env
FILESYSTEM_DISK=r2
MEDIA_DISK=r2
R2_ACCESS_KEY_ID=...
R2_SECRET_ACCESS_KEY=...
R2_DEFAULT_REGION=auto
R2_BUCKET=...
R2_ENDPOINT=https://<account-id>.r2.cloudflarestorage.com
R2_URL=https://<public-r2-domain>
R2_USE_PATH_STYLE_ENDPOINT=true
```

Gunakan `R2_URL` untuk domain publik media. Jika memakai custom domain Cloudflare, isi `R2_URL` dengan custom domain HTTPS tersebut.

## Contoh Integrasi Next.js

Gunakan server-side fetch untuk data CMS.

```ts
type ApiResponse<T> = {
  data: T;
};

async function cmsFetch<T>(path: string): Promise<T> {
  const baseUrl = process.env.CMS_API_URL;

  if (!baseUrl) {
    throw new Error('CMS_API_URL is not configured');
  }

  const response = await fetch(`${baseUrl}${path}`, {
    headers: {
      Accept: 'application/json',
    },
    next: {
      revalidate: 300,
    },
  });

  if (!response.ok) {
    throw new Error(`CMS request failed: ${response.status}`);
  }

  const payload = (await response.json()) as ApiResponse<T>;

  return payload.data;
}

export function getBootstrap() {
  return cmsFetch('/api/v1/portfolio');
}

export function getProjects(page = 1) {
  return fetch(`${process.env.CMS_API_URL}/api/v1/projects?page=${page}`, {
    headers: {
      Accept: 'application/json',
    },
    next: {
      revalidate: 300,
    },
  }).then((response) => response.json());
}

export function getProject(slug: string) {
  return cmsFetch(`/api/v1/projects/${slug}`);
}
```

Contoh `.env.local` Next.js:

```env
CMS_API_URL=https://cms-portfolio.ryucode.site
```

Jika fetch dilakukan dari browser, gunakan `NEXT_PUBLIC_CMS_API_URL` dan pastikan CORS Laravel hanya membuka domain frontend.

## Error Response

| Status | Penyebab |
| --- | --- |
| `200` | Data berhasil diambil |
| `404` | Endpoint tidak ditemukan, slug tidak ditemukan, atau konten belum published |
| `405` | Method selain `GET` atau `HEAD` digunakan |
| `422` | Query parameter tidak valid |
| `429` | Terkena rate limit `60 request / menit` |
| `500` | Error server |

## Production Checklist

- Semua endpoint `/api/v1/*` tetap public dan tidak diberi middleware `auth`, `can`, `role`, atau `permission`.
- Method write seperti `POST`, `PUT`, `PATCH`, dan `DELETE` tetap tidak tersedia untuk API publik.
- `APP_URL` sudah memakai domain HTTPS production.
- `ASSET_URL` sudah memakai domain HTTPS production jika deploy di balik proxy seperti Heroku.
- `FILESYSTEM_DISK` dan `MEDIA_DISK` sudah memakai `r2`.
- `R2_ENDPOINT`, `R2_BUCKET`, `R2_ACCESS_KEY_ID`, dan `R2_SECRET_ACCESS_KEY` sudah sesuai konfigurasi bucket.
- `R2_URL` sudah memakai domain HTTPS publik agar field media `_url` bisa langsung dipakai frontend.
- CORS hanya dibuka untuk domain frontend portfolio jika fetch dilakukan dari browser.
- Data yang belum siap publish tetap `is_active = false` atau `published_at` dibuat di masa depan.
- Contact message/inbox admin tidak diekspos lewat API publik.

## Verifikasi Developer

Command untuk mengecek route API:

```bash
php artisan route:list --path=api -vv --no-interaction
```

Command test API:

```bash
php artisan test --compact tests/Feature/PortfolioApiTest.php
```
