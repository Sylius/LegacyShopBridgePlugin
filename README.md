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

2. Enable the plugin and required bundles in `config/bundles.php`:

```php
return [
    // ...
    Sonata\BlockBundle\SonataBlockBundle::class => ['all' => true],
    FOS\RestBundle\FOSRestBundle::class => ['all' => true],
    Sylius\LegacyBridgePlugin\SyliusLegacyBridgePlugin::class => ['all' => true],
];
```

## Configuration

### 1. Import Plugin Configuration

Add the plugin configuration import to your `config/packages/_sylius.yaml` (or main configuration file):

```yaml
imports:
    - { resource: "@SyliusLegacyBridgePlugin/config/config.yaml" }
```

### 2. Configure FOSRestBundle

If you don't have FOSRestBundle configured yet, add the following to `config/packages/fos_rest.yaml`:

```yaml
fos_rest:
    exception: true
    view:
        formats:
            json: true
            xml:  true
        empty_content: 204
    format_listener:
        rules:
            - { path: '^/api/.*', priorities: ['json', 'xml'], fallback_format: json, prefer_extension: true }
            - { path: '^/', stop: true }
```

### 3. Configure Controllers

Configure the Sylius controllers to use the legacy bridge functionality:

**Option A: If you have NOT overridden the following controllers in your project**

Add the following to your `config/packages/_sylius.yaml`:

```yaml
sylius_order:
    resources:
        order:
            classes:
                controller: Sylius\LegacyBridgePlugin\Controller\OrderController
        order_item:
            classes:
                controller: Sylius\LegacyBridgePlugin\Controller\OrderItemController

sylius_addressing:
    resources:
        province:
            classes:
                controller: Sylius\LegacyBridgePlugin\Controller\ProvinceController
```

**Option B: If you HAVE already any of these controllers in your project**

Add the appropriate traits to your existing controllers:

```php
// src/Controller/OrderController.php
namespace App\Controller;

use Sylius\LegacyBridgePlugin\Controller\Trait\OrderTrait;

class OrderController extends \Sylius\Bundle\CoreBundle\Controller\OrderController
{
    use OrderTrait; // Adds: widgetAction(), clearAction()

    // ... your existing custom methods
}
```

```php
// src/Controller/OrderItemController.php
namespace App\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\LegacyBridgePlugin\Controller\Trait\OrderItemTrait;

class OrderItemController extends ResourceController
{
    use OrderItemTrait; // Adds: addAction(), removeAction() and helper methods

    // ... your existing custom methods
}
```

```php
// src/Controller/ProvinceController.php
namespace App\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\LegacyBridgePlugin\Controller\Trait\ProvinceTrait;

class ProvinceController extends ResourceController
{
    use ProvinceTrait; // Adds: choiceOrTextFieldFormAction() and helper methods

    // ... your existing custom methods
}
```

### 4. Update UI Configuration

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

### 5. Configure Twig Paths

Add the following Twig paths configuration to your `config/packages/twig.yaml`:

```yaml
twig:
    paths:
        # Add these lines ONLY if you have overridden bundle templates in your project
        '%kernel.project_dir%/templates/bundles/SyliusShopBundle': 'SyliusShop'
        '%kernel.project_dir%/templates/bundles/SyliusUiBundle': 'SyliusUi'

        # These two lines are REQUIRED
        '%kernel.project_dir%/vendor/sylius/legacy-bridge-plugin/templates/bundles/SyliusShopBundle': 'SyliusShop'
        '%kernel.project_dir%/vendor/sylius/legacy-bridge-plugin/templates/bundles/SyliusUiBundle': 'SyliusUi'
```

**Note:** The first two lines are only needed if you have customized `SyliusShopBundle` or `SyliusUiBundle` templates in your `templates/bundles/` directory. The last two lines pointing to the plugin's templates are always required.

### 6. Add Routes

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

### 7. Update Encore Entry Points (Shop)

Update your shop template base file to use legacy Encore entries:

```twig
{# Before #}
{{ encore_entry_link_tags('shop-entry', null, 'shop') }}
{{ encore_entry_script_tags('shop-entry', null, 'shop') }}

{# After #}
{{ encore_entry_link_tags('legacy-shop-entry', null, 'legacy.shop') }}
{{ encore_entry_script_tags('legacy-shop-entry', null, 'legacy.shop') }}
```

### 8. Update Asset Paths

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

### 9. Configure Webpack

Add the legacy bridge configuration to your `webpack.config.js`:

```javascript
const path = require('path');

// ... your existing Encore configurations ...

// Legacy Shop Configuration
Encore.reset();

Encore
    .setOutputPath('public/build/legacy/shop')
    .setPublicPath('/build/legacy/shop')
    .addEntry('legacy-shop-entry', path.resolve(__dirname, './vendor/sylius/legacy-bridge-plugin/assets/shop/entrypoint.js'))
    .addAliases({
        'semantic-ui-css': path.resolve(__dirname, 'node_modules/semantic-ui-css'),
        'jquery': path.resolve(__dirname, 'node_modules/jquery'),
        'lightbox2': path.resolve(__dirname, 'node_modules/lightbox2'),
        'slick-carousel': path.resolve(__dirname, 'node_modules/slick-carousel'),
    })
    .disableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableSassLoader()
;

const legacyShopConfig = Encore.getWebpackConfig();

legacyShopConfig.externals = Object.assign({}, legacyShopConfig.externals, { window: 'window', document: 'document' });
legacyShopConfig.name = 'legacy.shop';

// Export all configs
module.exports = [
    // ... your existing configs ...
    legacyShopConfig,
];
```

### 10. Install Frontend Dependencies

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

### 11. Build Assets

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
