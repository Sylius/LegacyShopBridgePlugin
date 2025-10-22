<?php

declare(strict_types=1);

namespace Sylius\LegacyBridgePlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Sylius\LegacyBridgePlugin\DependencyInjection\Compiler\LegacySonataBlockPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SyliusLegacyBridgePlugin extends Bundle
{
    use SyliusPluginTrait;

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new LegacySonataBlockPass());
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
