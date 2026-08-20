# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

An [Omeka S](https://omeka.org/s/) theme (`uc-caan`, package `uc-caan-theme`). Themes in Omeka S are the presentation layer only — no models/controllers, just Laminas view scripts (`.phtml`), view helpers, Sass/CSS, JS, and a `config/theme.ini` that declares theme settings/regions/templates. This repo is its own git repo (remote: `uc-caan-theme`), nested inside a larger Omeka S application checkout; the parent app is gitignored from this repo's perspective and vice versa.

## Commands

Run from the theme root (requires Node.js, `npm install` first):

- `npm run start` — runs default gulp task (`css:watch`): watches `asset/sass/**/*.scss` and recompiles on change.
- `npm run build` / `gulp css` — one-off Sass → CSS compile (compressed output, autoprefixed) from `asset/sass/*.scss` into `asset/css/`.
- `gulp css:watch` — same watch behavior as `npm run start`.

There is no test suite, linter, or PHP build step in this theme — it's compiled Sass + static PHP view scripts.

## Architecture

**Sass structure** (`asset/sass/style.scss` is the entrypoint, imports in this order): `abstracts` (variables/mixins, no output) → `generic` (normalize/box-sizing/collapse) → `base` (element and layout defaults) → `components` (one folder per UI feature, e.g. `components/blocks/*` for Omeka block-layout styles, `components/navigation`, `components/facets`) → `utilities` (alignment/accessibility/clearfix helper classes). When adding a new visual component, create a folder under `components/`, add its `_*.scss` partial, and import it from `components/_components.scss`.

**View scripts** (`view/`) override/extend Omeka S core templates by matching the same path Omeka core uses:
- `view/omeka/site/...` — resource browse/show pages (item, item-set, media, page).
- `view/common/block-layout/...` and `view/common/block-template/...` — renderers for Omeka page-builder blocks (Asset, Browse Preview, Item Carousel, etc.) and named block templates declared in `theme.ini` (`block_templates.*`).
- `view/common/page-template/...` — the theme's custom page templates, declared in `theme.ini` (`page_templates.*`): default, no-page-nav, page-with-hero(-no-page-nav), full-width(-no-page-nav).
- `view/common/resource-page-block-layout/...` — blocks placeable in the configurable resource-page regions (left/main/right) defined in `theme.ini` (`resource_page_regions.*`, `resource_page_blocks.*`).
- `view/layout/layout.phtml` — the HTML shell; registers `asset/css/style.css` (compiled output, not source) and `asset/css/iconfonts.css` via `headLink()`.

**View helpers** (`helper/*.php`, namespace `OmekaTheme\Helper`, registered in `theme.ini` under `helpers[]`) are plain Laminas `AbstractHelper` classes invoked in `.phtml` as `$this->HelperName(...)`:
- `ContrastColor` / `ShadeColor` — programmatic hex color math backing the theme's color-scheme settings (primary/secondary/accent/complementary, set via `theme.ini` color pickers).
- `GetSVG` — inlines an SVG asset by name.
- `ResourceTags` — renders the Resource Type/Class tag shown on resource pages (toggled via the `resource_tags` theme setting).
- `ResourceClassLayout` — parses the `resource_class_layouts` theme setting (pipe-delimited `<class>|<left%>|<center%>|<right%>` rows) into per-resource sidebar widths.

**Theme settings** are entirely defined in `config/theme.ini` under `[config]` (`element_groups.*` / `elements.*`) — this is the single source of truth for what's configurable from the Omeka S admin theme-settings UI (colors, header/banner/footer content, social links, resource tags, resource-class layouts, browse layout/truncation). Page templates, resource-page regions/blocks, and named block templates are declared at the bottom of the same file.

**Content-authoring conventions** implemented via CSS/JS rather than PHP (documented in README.md, relevant when styling or debugging page-builder content):
- Group blocks: class `image-hover-text` (+ optional `secondary`/`complementary`/`primary`) for hover-caption assets; `has-background` + a color class (+ optional `fullwidth`) for solid-color group backgrounds.
- Asset/Item Carousel/Media Embed blocks: `caption-complementary` / `caption-primary` class overrides the default caption background color.
- Utility classes (`asset/sass/utilities/_utilities.scss`): `alignleft/right/center/full/wide/narrow`, `textleft/right/center`, `inline`, `clearfix`, `screen-reader-text`.
