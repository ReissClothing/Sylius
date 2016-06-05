<?php
/**
 * @author    Pete Ward <peter.ward@reiss.com>
 * @date      27/05/2016
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Metadata\Accessor;

use Psr\Log\LoggerInterface;
use Sylius\Component\Metadata\Model\MetadataSubjectInterface;

/**
 * @author Pete Ward <peter.ward@reiss.com>
 */
class CautiousMetadataAccessor implements MetadataAccessorInterface
{
    /**
     * @var MetadataAccessorInterface
     */
    private $metadataAccessor;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param MetadataAccessorInterface $metadataAccessor
     * @param LoggerInterface $logger
     */
    public function __construct(MetadataAccessorInterface $metadataAccessor, LoggerInterface $logger)
    {
        $this->metadataAccessor = $metadataAccessor;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getProperty(MetadataSubjectInterface $metadataSubject, $type, $propertyPath = null)
    {
        try {
            return $this->metadataAccessor->getProperty($metadataSubject, $type, $propertyPath);
        } catch (\Twig_Error $e) {
            // I'm not sure if the CachedAccessor is the best place for this to go, probably not but time is tight...
            $this->logger->error('Failed to render metadata for: ' . $metadataSubject->getMetadataIdentifier() . ': ' . $e->getRawMessage());

            return null;
        }
    }
}
