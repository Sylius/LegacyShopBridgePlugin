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

namespace Sylius\LegacyShopBridgePlugin\Twig;

use Sylius\TwigExtra\Twig\Extension\SortByExtension as BaseSortByExtension;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SortByExtension extends AbstractExtension
{
    public function __construct(private readonly BaseSortByExtension $baseSortByExtension)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('sort_by', [$this, 'sortBy']),
        ];
    }

    /**
     * @throws NoSuchPropertyException
     */
    public function sortBy(iterable $iterable, string $field, string $order = 'ASC'): array
    {
        trigger_deprecation(
            'sylius/sylius-legacy-shop-bridge-plugin',
            '1.0',
            'The "sort_by" filter is deprecated. Use "sylius_sort_by" instead.',
        );

        return $this->baseSortByExtension->sortBy($iterable, $field, $order);
    }
}
