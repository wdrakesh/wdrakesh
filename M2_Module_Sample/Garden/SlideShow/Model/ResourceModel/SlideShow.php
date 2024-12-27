<?php
/**
 * Copyright © The Garden Health & Beauty Store, Inc All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Garden\SlideShow\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class SlideShow extends AbstractDb
{
    public const PRIMARY_KEY = 'slideshow_id';
    public const TABLE_NAME = 'garden_slideshow';

    /**
     * SlideShow constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, self::PRIMARY_KEY);
    }
}
