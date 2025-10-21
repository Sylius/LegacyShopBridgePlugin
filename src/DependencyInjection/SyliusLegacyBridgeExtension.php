<?php

declare(strict_types=1);

namespace Sylius\LegacyBridgePlugin\DependencyInjection;

use Sylius\Bundle\CoreBundle\DependencyInjection\PrependDoctrineMigrationsTrait;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Bundle\UiBundle\DependencyInjection\SyliusUiExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SyliusLegacyBridgeExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    use PrependDoctrineMigrationsTrait;

    /** @psalm-suppress UnusedVariable */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));

        $loader->load('services.xml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependDoctrineMigrations($container);
        $this->replaceSyliusUiExtension($container);
    }

    private function replaceSyliusUiExtension(ContainerBuilder $container): void
    {
        // Get the original Sylius UI extension
        $originalExtension = $container->getExtension('sylius_ui');

        // Create a wrapper extension that uses our extended configuration
        $wrappedExtension = new class($originalExtension) extends \Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension implements \Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface {
            public function __construct(private readonly object $originalExtension)
            {
            }

            public function getAlias(): string
            {
                return 'sylius_ui';
            }

            public function getConfiguration(array $config, ContainerBuilder $container): \Sylius\LegacyBridgePlugin\DependencyInjection\ExtendedSyliusUiConfiguration
            {
                return new \Sylius\LegacyBridgePlugin\DependencyInjection\ExtendedSyliusUiConfiguration();
            }

            public function load(array $configs, ContainerBuilder $container): void
            {
                // Process configuration with our extended configuration
                $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

                // Store events separately if they exist
                if (isset($config['events'])) {
                    $container->setParameter('sylius_ui.events', $config['events']);
                    unset($config['events']);
                }

                // Reload the original extension with the modified config
                $this->originalExtension->load([$config], $container);
            }

            public function prepend(ContainerBuilder $container): void
            {
                if ($this->originalExtension instanceof \Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface) {
                    $this->originalExtension->prepend($container);
                }
            }
        };

        // Replace the extension
        $container->registerExtension($wrappedExtension);
    }

    protected function getMigrationsNamespace(): string
    {
        return 'DoctrineMigrations';
    }

    protected function getMigrationsDirectory(): string
    {
        return '@SyliusLegacyBridgePlugin/src/Migrations';
    }

    protected function getNamespacesOfMigrationsExecutedBefore(): array
    {
        return [
            'Sylius\Bundle\CoreBundle\Migrations',
        ];
    }
}
