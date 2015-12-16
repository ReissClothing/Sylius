<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Filter;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\OrderItemInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaxonFilter extends AbstractFilter
{
    const OPTION_TAXON = 'taxon';

    /**
     * {@inheritdoc}
     */
    protected function filter(Collection $collection)
    {
        $returnedCollection = new ArrayCollection();

        /** @var OrderItemInterface $item */
        foreach ($collection as $item)
        {
            foreach ($item->getProduct()->getTaxons() as $taxon) {
                if ($taxon->getId() == $this->configuration[self::OPTION_TAXON]) {
                    $returnedCollection->add($item);
                }
            }
        }

        return $returnedCollection;
    }

    protected function resolveConfiguration(array $configuration)
    {
        $resolver = new OptionsResolver();

        $resolver->setRequired(self::OPTION_TAXON);
        $resolver->setAllowedTypes(self::OPTION_TAXON, 'int');

        return $resolver->resolve($configuration);
    }
}