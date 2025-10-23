<p align="center">
    <a href="https://sylius.com" target="_blank">
        <picture>
          <source media="(prefers-color-scheme: dark)" srcset="https://media.sylius.com/sylius-logo-800-dark.png">
          <source media="(prefers-color-scheme: light)" srcset="https://media.sylius.com/sylius-logo-800.png">
          <img alt="Sylius Logo." src="https://media.sylius.com/sylius-logo-800.png">
        </picture>
    </a>
</p>

<h1 align="center">Sylius Legacy Bridge Plugin</h1>

<p align="center">A plugin for bridging legacy Sylius functionality with modern Sylius applications.</p>

## Installation

1. Install the plugin via Composer:

```bash
composer require sylius/legacy-bridge-plugin
```

2. Enable the plugin in `config/bundles.php`:

```php
return [
    // ...
    Sylius\LegacyBridgePlugin\SyliusLegacyBridgePlugin::class => ['all' => true],
];
```

## Configuration

### 1. Update UI Configuration

Replace `sylius_ui` configuration with `sylius_legacy_bridge` in your `config/packages/sylius_ui.yaml` (or wherever your UI events are configured):

```yaml
# Before
sylius_ui:
    events:
        # ...

# After
sylius_legacy_bridge:
    events:
        # ...
```

### 2. Add Routes

Add the plugin routes to your `config/routes.yaml` file. **Important:** These routes must be loaded after the shop routes:

```yaml
# Your shop routes
sylius_shop:
    resource: "@SyliusShopBundle/Resources/config/routing.yml"
    prefix: /{_locale}
    requirements:
        _locale: ^[a-z]{2}(?:_[A-Z]{2})?$

# Legacy bridge routes - must be loaded AFTER shop routes
sylius_legacy_bridge:
    resource: "@SyliusLegacyBridgePlugin/config/routes.yaml"
```

### 3. Update Encore Entry Points (Shop)

Update your shop template base file to use legacy Encore entries:

```twig
{# Before #}
{{ encore_entry_link_tags('shop-entry', null, 'shop') }}
{{ encore_entry_script_tags('shop-entry', null, 'shop') }}

{# After #}
{{ encore_entry_link_tags('legacy-shop-entry', null, 'legacy.shop') }}
{{ encore_entry_script_tags('legacy-shop-entry', null, 'legacy.shop') }}
```

### 4. Update Asset Paths

Replace asset references to use the legacy paths:

```twig
{# Example 1 #}
{# Before: #}
{{ asset('build/shop/images/logo.png', 'shop') }}
{# After: #}
{{ asset('build/legacy/shop/images/logo.png', 'legacy.shop') }}

{# Example 2 #}
{# Before: #}
{{ asset('build/shop/images/sylius-plus-banner.png', 'shop') }}
{# After: #}
{{ asset('build/legacy/shop/images/sylius-plus-banner.png', 'legacy.shop') }}
```

**Regex for bulk replacement:**

Find:
```regex
\{\{\s*asset\(\s*(['"]))build\/shop\/([^'"]+)\1\s*,\s*(['"])shop\3\s*\)\s*\}\}
```

Replace:
```
{{ asset($1build/legacy/shop/$2$1, $3legacy.shop$3) }}
```

### 5. Configure Webpack

Add the legacy bridge configuration to your `webpack.config.js`. Refer to the plugin's webpack configuration for the exact setup needed.

### 6. Install Frontend Dependencies

Add the following legacy dependencies to your `package.json`:

```json
{
    "dependencies": {
        "jquery": "^3.5.0",
        "lightbox2": "^2.9.0",
        "semantic-ui-css": "^2.2.0",
        "slick-carousel": "^1.8.1"
    }
}
```

Then install the dependencies:

```bash
npm install
# or
yarn install
```

### 7. Build Assets

Build your frontend assets:

```bash
npm run build
# or
yarn build
```

## Usage

Once installed and configured, the plugin will provide legacy compatibility for older Sylius templates and functionality, allowing you to gradually migrate to modern Sylius features.

## License

This plugin is under the MIT license. See the complete license in the LICENSE file.
