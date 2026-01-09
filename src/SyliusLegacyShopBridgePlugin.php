<?php

declare(strict_types=1);

namespace Sylius\LegacyShopBridgePlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Sylius\LegacyShopBridgePlugin\DependencyInjection\Compiler\LegacySonataBlockPass;
use Sylius\LegacyShopBridgePlugin\DependencyInjection\Compiler\MakeServicesPublicCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SyliusLegacyShopBridgePlugin extends Bundle
{
    use SyliusPluginTrait;

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new LegacySonataBlockPass());
        $container->addCompilerPass(new MakeServicesPublicCompilerPass());
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
