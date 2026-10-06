# Spec: Move Radicle projects off ObjectPress

Status: Draft · 2026-10-06

## Context

ObjectPress was built as a toolkit for every kind of WordPress project (barebone, Bedrock, Radicle).
Most projects have since moved to Laravel + Filament, and Radicle (Acorn) now natively covers what
ObjectPress provided: a Laravel container, service providers, Blade, Eloquent models, config, CLI.

Running both in the same request means two Laravel containers side by side. That has already caused
bugs: ObjectPress's `OP\Core\Container` used to call `Facade::setFacadeApplication()` with its own bare
container, taking every Laravel facade away from Acorn (eg. `Vite::withEntryPoints()` crashed the
block editor with "Cannot access offset of type Asset on array"). That was patched by only claiming
the facades when no host framework owns them, but the duplication remains.

ObjectPress is no longer actively maintained. It stays supported for barebone WordPress projects only.

## Goal

Radicle projects use Acorn's native mechanisms and do not depend on `tgeorgel/objectpress`.

## Non-goals

- Changing ObjectPress for barebone WordPress projects.
- Removing dead code from ObjectPress itself (can be done separately).

## Inventory (from the Alliance Française Radicle project)

| Consumer | ObjectPress usage | Radicle-native replacement |
|---|---|---|
| `mu-plugins/01-object-press-boot.php` | Boots ObjectPress on `after_setup_theme`, `wp_die`s if missing | Delete |
| Theme models (`Page`, `Term`, `Taxonomy`, `User`, `Attachment`, `Product`) | Extend `OP\Framework\Models\*` | Plain Eloquent models (Radicle's `App\Models\Post` pattern) with WP-specific scopes as traits |
| `AppHelper::currentLang()` | `ObjectPress::app()->make(LanguageDriver::class)` | Direct Polylang call (`pll_current_language()`) |
| `AppHelper::getBreadcrumb()` | `ModelFactory::currentPost()` | Resolve the current post via `get_queried_object()` + model lookup |
| `AcfServiceProvider` | `OP\Support\Facades\Theme::on()` / `addFilter()` | `add_action` / `add_filter`; register it in `composer.json` → `extra.acorn.providers` (it is currently not registered at all) |
| `wp-grogu/acf-manager` casts (`config/acf.php`) | `EloquentPost(s)` / `EloquentTerm(s)` transformers return `OP\Framework\Models\*` | New transformers in acf-manager (or the theme) that take a configurable model class, defaulting to the app's models |
| stack-tools `ContactForm` | Post types, taxonomy, API routes, validator factory, `ServiceProvider`, models, `Theme`/`Config`/`ObjectPress` facades, ships its own `vendor/` copy | Biggest piece; see below |
| stack-tools `Images` | `GqlType` / `GqlField` (only with `WP_ECODESIGN` + WPGraphQL), unregistered WP-CLI commands | Inline the small base classes or drop the GraphQL integration; register commands via Acorn if still needed |

### ContactForm

- Post types / taxonomy: register with `register_post_type()` / `register_taxonomy()` (or `config/post-types.php`, already used by Radicle).
- API routes: Acorn routes or plain `register_rest_route()`.
- Validation: `Illuminate\Validation\Factory` from the Acorn container (`app('validator')`).
- Service provider: an Acorn `ServiceProvider`.
- Remove its bundled `vendor/tgeorgel/objectpress` and rely on the host app.

## Laravel 13 compatibility

`amphibee/wordpress-eloquent-models` (which `OP\Framework\Models` builds on) was incompatible with
Laravel 13 up to v2.2.3: `Connection::select()` / `cursor()` lacked the new `array $fetchUsing = []`
parameter, making any ObjectPress model query a fatal error on Acorn 6. Fixed in v2.2.4, which
ObjectPress now requires. The fact that a Laravel major can break the models through a third-party
connection class is one more reason to move to plain Eloquent models on Acorn's connection.

## Plan

1. **Models first**: rewrite theme models as plain Eloquent models; add the WP scopes/relations actually used.
2. **acf-manager casts**: add model-agnostic Eloquent transformers and switch `config/acf.php`.
3. **Theme glue**: rewrite `AppHelper` language/breadcrumb methods; port and register `AcfServiceProvider`.
4. **ContactForm**: port to Acorn (post types, routes, validation, provider); drop its vendored ObjectPress.
5. **Images**: inline or drop the GraphQL/CLI base classes.
6. **Remove**: delete `01-object-press-boot.php`, `config/object-press.php`, and `tgeorgel/objectpress` from `composer.json`.

Each step should leave the site working. Check by browsing the front end, editing a post in the block
editor, submitting a contact form, and exporting contacts.

## Acceptance

- `composer why tgeorgel/objectpress` returns nothing in the Radicle project, including stack-tools.
- No `OP\` references remain in `app/`, `config/`, `resources/` or `mu-plugins/`.
- Block editor, front end, contact form submission/export and ACF relationship fields work.
