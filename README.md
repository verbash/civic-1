# Civic-1 — WordPress Plugin

Portable Civic-1 (cv1) UI kit for coalition / community-org child themes.

## Naming

| Use | Form | Example |
|-----|------|---------|
| Product / inserter / docs | **Civic-1** | Block category label “Civic-1” |
| UI kit prefix | **cv1** | `.cv1-section-subnav`, `cv1.css` |
| WordPress slugs | **civic-1** | Plugin folder, patterns `civic-1/callout`, text domain, style handle |

Brand, slugs, and UI prefix all use **1** (not “one”). PHP functions use `civic_1_*` / `cv1_*`; constants use `CIVIC_1_*`.

## What this plugin provides

| Capability | Detail |
|---|---|
| **Components** | Header bar, drawer nav, header CTA, section subnav/title, embed wrappers, hub anchors |
| **Block patterns** | 12 content patterns under `civic-1/*` |
| **Heading styles** | `cv1-page-title`, `cv1-section`, `cv1-accent` (editor style picker + CSS classes) |
| **Styles** | `cv1.css`, drawer, mobile, header CTA/critical |
| **Section registry** | Logic in `inc/section-registry.php`; data via `civic_1_section_configs` filter |

## What stays in the child theme

| File | Reason |
|---|---|
| `inc/d4c-section-config.php` (per client) | Section tabs/hubs, nav post ID, CTA copy — filters only |
| `inc/blog.php` (optional, D4C) | Client-specific blog markup |
| `patterns/header-bar.php` | Pattern slug references theme; calls `cv1_header_bar_markup()` |
| `parts/header.html`, templates, `theme.json` | FSE shell + brand tokens |

## Client integration filters

```php
add_filter( 'civic_1_primary_nav_id', fn (): int => 791 );
add_filter( 'civic_1_section_configs', 'my_client_section_configs' );
add_filter( 'cv1_header_cta_button_label', fn () => __( 'Donate', 'my-theme' ) );
```

## Navigation

Menus are `wp_navigation` posts (Site Editor). Default primary nav ID **791** — override with `civic_1_primary_nav_id`.

## Requirements

- WordPress 6.5+
- PHP 8.1+
- FSE block child theme (Twenty Twenty-Two or compatible)
