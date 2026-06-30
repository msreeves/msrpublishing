# Legacy demo CPTs (admin only)

These post types predate the Atlas Briefing **resource** IA. They remain in the database and admin for backward compatibility but are **not** used on the public publishing home (`front-page.php`).

| CPT | Public | Admin location |
|-----|--------|----------------|
| `event` | No | **Legacy demos** |
| `publication` | No | **Legacy demos** |
| `advert` | No | **Legacy demos** |
| `partner` | No | Top-level (legacy partnerships demo) |
| `member` | No | **Legacy demos** (was public; Phase III admin-only) |

**Public publishing routes:** `/` (home), `/resources/` (archive), `/resources/{format}/` (format hubs), single resources at `/resources/{slug}/`.

Legacy listing partials were archived to `scripts/archive/msrsandbox-p59-legacy/` (P59). Home sections live in `template-parts/sections/`.
