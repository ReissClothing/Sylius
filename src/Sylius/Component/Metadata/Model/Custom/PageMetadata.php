<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Metadata\Model\Custom;

use Sylius\Component\Metadata\Model\AbstractMetadata;
use Sylius\Component\Metadata\Model\Twitter\CardInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class PageMetadata extends AbstractMetadata implements PageMetadataInterface
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string[]
     */
    protected $keywords = [];

    /**
     * @var string
     */
    protected $charset = 'UTF-8';

    /**
     * @var CardInterface
     */
    protected $twitter;

    /**
     * We don't want these merging up because Facebook defaults to using the page title if it can't find
     * anything specific. This is more likely to be correct than something higher up the hierarchy.
     *
     * @see https://blog.kissmetrics.com/open-graph-meta-tags/
     *
     * @var string
     */
    protected $ogTitle;

    /**
     * @var string
     */
    protected $ogDescription;
    
    /**
     * {@inheritdoc}
     */
    public function getNonMergeProperties()
    {
        return [
            'ogTitle',
            'ogDescription',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * {@inheritdoc}
     */
    public function setKeywords(array $keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * {@inheritdoc}
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * {@inheritdoc}
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * {@inheritdoc}
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * {@inheritdoc}
     */
    public function setTwitter(CardInterface $twitter = null)
    {
        $this->twitter = $twitter;
    }

    /**
     * {@inheritdoc}
     */
    public function getOgDescription()
    {
        return $this->ogDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function setOgDescription($ogDescription)
    {
        $this->ogDescription = $ogDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function getOgTitle()
    {
        return $this->ogTitle;
    }

    /**
     * {@inheritdoc}
     */
    public function setOgTitle($ogTitle)
    {
        $this->ogTitle = $ogTitle;
    }
}
