# Portfolio CMS API Documentation

Dokumen ini menjelaskan API publik untuk konsumsi website portfolio, termasuk integrasi Next.js. API ini read-only dan tidak memakai authentication, role, atau permission middleware karena konten portfolio memang ditujukan untuk publik.

## Ringkasan

| Item | Nilai |
| --- | --- |
| Base URL lokal | `http://127.0.0.1:8000` |
| Prefix API | `/api/v1` |
| Format response | JSON |
| Authentication | Tidak perlu |
| Permission | Tidak perlu |
| Write endpoint | Tidak tersedia |
| Cache HTTP | `Cache-Control: public, max-age=300` |
| Cache server | 15 menit, auto flush saat konten CMS berubah |

Middleware route API yang aktif hanya:

- `throttle:60,1`
- `SubstituteBindings`

Tidak ada middleware berikut pada API portfolio:

- `auth`
- `can`
- `role`
- `permission`
- middleware Spatie permission

## Endpoint

### Get Portfolio Content

Mengambil seluruh data publik portfolio dalam satu response. Endpoint ini dibuat single payload agar frontend Next.js bisa melakukan satu kali fetch untuk membangun halaman portfolio.

```http
GET /api/v1/portfolio
```

#### Headers

Tidak ada header wajib.

```http
Accept: application/json
```

#### Response 200

```json
{
  "data": {
    "global": {
      "site_settings": {},
      "social_media_links": [],
      "tech_stacks": [],
      "media_assets": []
    },
    "landing_page": {
      "hero": {},
      "about_sections": []
    },
    "about_page": {
      "hero": {},
      "sections": [],
      "expertise_sections": [],
      "tools": [],
      "hobby_sections": []
    },
    "projects_page": {
      "hero": {},
      "categories": [],
      "featured_projects": []
    },
    "notes_page": {
      "hero": {},
      "categories": [],
      "featured_notes": []
    },
    "contact_page": {
      "information": {}
    }
  }
}
```

Field object yang belum diisi dari CMS akan bernilai `null`. Collection yang kosong akan bernilai array kosong `[]`.

## Struktur Data

### `global.site_settings`

Berisi konfigurasi global website.

Field penting:

- `site_title`
- `logo_light`
- `logo_light_url`
- `logo_dark`
- `logo_dark_url`
- `favicon`
- `favicon_url`
- `copyright_text`
- `meta_title`
- `meta_description`
- `og_image`
- `og_image_url`
- `created_at`
- `updated_at`

### `global.social_media_links`

Berisi social media yang aktif, terurut berdasarkan `order_no`.

Field penting:

- `title`
- `url`
- `is_active`
- `order_no`
- `created_at`
- `updated_at`

### `global.tech_stacks`

Berisi tech stack yang aktif, terurut berdasarkan `order_no`.

Field penting:

- `name`
- `icon`
- `icon_url`
- `is_active`
- `order_no`
- `created_at`
- `updated_at`

### `global.media_assets`

Berisi media library CMS yang aktif.

Field penting:

- `title`
- `file_path`
- `file_path_url`
- `alt_text`
- `disk`
- `mime_type`
- `file_size`
- `formatted_size`
- `width`
- `height`
- `is_active`
- `created_at`
- `updated_at`

### `landing_page`

Berisi konten landing page.

Field penting:

- `hero.title`
- `hero.subtitle`
- `hero.meta_title`
- `hero.meta_description`
- `hero.og_image_url`
- `about_sections[].title`
- `about_sections[].subtitle`
- `about_sections[].order_no`
- `about_sections[].items[]`

Item pada `about_sections[].items[]`:

- `title`
- `subtitle`
- `icon`
- `icon_url`
- `order_no`

### `about_page`

Berisi konten halaman About.

Field penting:

- `hero.title`
- `hero.subtitle`
- `hero.photo`
- `hero.photo_url`
- `hero.photo_alt`
- `hero.meta_title`
- `hero.meta_description`
- `hero.og_image_url`
- `sections[]`
- `expertise_sections[]`
- `tools[]`
- `hobby_sections[]`

Item pada `sections[]`:

- `title`
- `subtitle_rich`
- `order_no`
- `items[]`

Item pada `sections[].items[]`:

- `title`
- `subtitle`
- `icon`
- `icon_url`
- `order_no`

Item pada `tools[]`:

- `name`
- `logo`
- `logo_url`
- `is_active`
- `order_no`

Item pada `hobby_sections[].items[]`:

- `photo`
- `photo_url`
- `photo_alt`
- `order_no`

### `projects_page`

Berisi hero, kategori project, project published, dan featured project.

Field penting:

- `hero.title`
- `hero.subtitle`
- `hero.meta_title`
- `hero.meta_description`
- `hero.og_image_url`
- `categories[]`
- `featured_projects[]`

Kategori hanya berisi kategori aktif. Project hanya muncul jika:

- `is_active = true`
- `published_at` sudah terisi dan tidak berada di masa depan

Item pada `categories[]`:

- `title`
- `slug`
- `is_active`
- `order_no`
- `projects[]`

Item project:

- `project_category_id`
- `title`
- `subtitle`
- `slug`
- `description_rich`
- `thumbnail`
- `thumbnail_url`
- `thumbnail_alt`
- `is_active`
- `is_featured`
- `published_at`
- `github_url`
- `live_url`
- `meta_title`
- `meta_description`
- `og_image`
- `og_image_url`
- `category`
- `tech_stacks[]`
- `core_features[]`
- `design_highlights[]`

Item pada `core_features[]`:

- `description`
- `order_no`

Item pada `design_highlights[]`:

- `title`
- `subtitle`
- `photo`
- `photo_url`
- `photo_alt`
- `order_no`

### `notes_page`

Berisi hero, kategori notes, notes published, dan featured notes.

Field penting:

- `hero.title`
- `hero.subtitle`
- `hero.meta_title`
- `hero.meta_description`
- `hero.og_image_url`
- `categories[]`
- `featured_notes[]`

Notes hanya muncul jika:

- `is_active = true`
- `published_at` sudah terisi dan tidak berada di masa depan

Item pada `categories[]`:

- `title`
- `slug`
- `is_active`
- `order_no`
- `notes[]`

Item note:

- `notes_category_id`
- `title`
- `subtitle`
- `slug`
- `is_active`
- `is_featured`
- `published_at`
- `meta_title`
- `meta_description`
- `og_image`
- `og_image_url`
- `category`
- `created_at`
- `updated_at`

### `contact_page.information`

Berisi informasi kontak publik.

Field penting:

- `location`
- `image`
- `image_url`
- `image_alt`
- `email`
- `meta_title`
- `meta_description`
- `og_image`
- `og_image_url`
- `created_at`
- `updated_at`

Catatan: `contact_messages` tidak diekspos di API publik karena tabel tersebut menyimpan submission/contact inbox admin.

## Contoh Integrasi Next.js

### Server Component atau Route Handler

Gunakan environment variable server-side agar URL CMS tidak perlu dibundle ke client.

```ts
type PortfolioContentResponse = {
  data: Record<string, unknown>;
};

export async function getPortfolioContent() {
  const baseUrl = process.env.CMS_API_URL;

  if (!baseUrl) {
    throw new Error('CMS_API_URL is not configured');
  }

  const response = await fetch(`${baseUrl}/api/v1/portfolio`, {
    headers: {
      Accept: 'application/json',
    },
    next: {
      revalidate: 300,
    },
  });

  if (!response.ok) {
    throw new Error(`Failed to fetch portfolio content: ${response.status}`);
  }

  const payload = (await response.json()) as PortfolioContentResponse;

  return payload.data;
}
```

Contoh `.env.local` Next.js:

```env
CMS_API_URL=https://cms.example.com
```

### Client-side Fetch

Jika fetch dilakukan langsung dari browser, gunakan public environment variable dan pastikan CORS Laravel mengizinkan domain frontend.

```env
NEXT_PUBLIC_CMS_API_URL=https://cms.example.com
```

```ts
export async function getPortfolioContentFromBrowser() {
  const response = await fetch(`${process.env.NEXT_PUBLIC_CMS_API_URL}/api/v1/portfolio`, {
    headers: {
      Accept: 'application/json',
    },
  });

  if (!response.ok) {
    throw new Error('Failed to fetch portfolio content');
  }

  return (await response.json()).data;
}
```

## Error Response

| Status | Penyebab |
| --- | --- |
| `200` | Data berhasil diambil |
| `404` | Endpoint tidak ditemukan |
| `405` | Method selain `GET` atau `HEAD` digunakan |
| `429` | Terkena rate limit `60 request / menit` |
| `500` | Error server |

## Media URL

Setiap field media penting memiliki pasangan URL absolut dengan suffix `_url`, misalnya:

- `logo_light` dan `logo_light_url`
- `thumbnail` dan `thumbnail_url`
- `photo` dan `photo_url`
- `og_image` dan `og_image_url`

Pastikan production sudah menjalankan:

```bash
php artisan storage:link
```

Pastikan juga `APP_URL` pada Laravel mengarah ke domain CMS yang benar agar URL media yang dihasilkan sesuai.

## Production Checklist

- `GET /api/v1/portfolio` tetap public dan tidak diberi middleware `auth`, `can`, `role`, atau `permission`.
- `POST`, `PUT`, `PATCH`, dan `DELETE` untuk `/api/v1/portfolio` tetap tidak tersedia.
- `APP_URL` sudah sesuai domain production CMS.
- `php artisan storage:link` sudah dijalankan.
- CORS hanya dibuka untuk domain frontend portfolio jika fetch dilakukan dari browser.
- Data yang belum siap publish tetap `is_active = false` atau `published_at = null`.
- Contact message/inbox admin tidak diekspos lewat API publik.

## Verifikasi Developer

Command untuk mengecek route API:

```bash
php artisan route:list --path=api -vv --no-interaction
```

Expected middleware untuk endpoint portfolio:

```text
GET|HEAD api/v1/portfolio
Illuminate\Routing\Middleware\ThrottleRequests:60,1
Illuminate\Routing\Middleware\SubstituteBindings
```

Command test yang menjaga API tetap public read-only:

```bash
php artisan test --compact tests/Feature/PortfolioApiTest.php
```
