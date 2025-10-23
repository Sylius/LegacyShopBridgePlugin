<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\LegacyBridgePlugin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class MakeServicesPublicCompilerPass implements CompilerPassInterface
{
    /**
     * List of service IDs that need to be public for legacy compatibility
     */
    private const SERVICE_IDS = [
        'sylius.modifier.order_item_quantity',
        'sylius.factory.add_to_cart_command',
        'sylius.modifier.order',
    ];

    public function process(ContainerBuilder $container): void
    {
        foreach (self::SERVICE_IDS as $serviceId) {
            if (!$container->hasDefinition($serviceId) && !$container->hasAlias($serviceId)) {
                continue;
            }

            if ($container->hasDefinition($serviceId)) {
                $container->getDefinition($serviceId)->setPublic(true);
            }

            if ($container->hasAlias($serviceId)) {
                $container->getAlias($serviceId)->setPublic(true);
            }
        }
    }
}
