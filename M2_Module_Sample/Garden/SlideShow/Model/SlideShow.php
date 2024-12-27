<?php
/**
 * Copyright © The Garden Health & Beauty Store, Inc All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Garden\SlideShow\Model;

use Garden\SlideShow\Model\ResourceModel\SlideShow as SlideShowResourceModel;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class SlideShow extends AbstractModel implements IdentityInterface
{
    public const CACHE_TAG = 'garden_slideshow';
    public const STATUS_ENABLED     = '1';
    public const STATUS_DISABLED     = '0';

    protected $_cacheTag = self::CACHE_TAG;

    protected $_eventPrefix = self::CACHE_TAG;

    protected function _construct()
    {
        $this->_init(SlideShowResourceModel::class);
    }

    /**
     * @return array|string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return array
     */
    public function getDefaultValues(): array
    {
        return [];
    }

    public function getSlideShowId(): ?int
    {
        return (int)$this->getData(SlideShowResourceModel::PRIMARY_KEY);
    }
}
