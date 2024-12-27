<?php
/**
 * Copyright © The Garden Health & Beauty Store, Inc All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Garden\SlideShow\Block\Adminhtml\SlideShow\Edit;

use Garden\SlideShow\Model\SlideShowFactory;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class GenericButton
{
    /**
     * @var Context
     */
    protected $context;
    /**
     * @var SlideShowFactory
     */
    private $slideshowFactory;

    /**
     * GenericButton constructor.
     * @param Context $context
     * @param SlideShowFactory $slideshowFactory
     */
    public function __construct(
        Context $context,
        SlideShowFactory $slideshowFactory
    ) {
        $this->context = $context;
        $this->slideshowFactory = $slideshowFactory;
    }

    /**
     * Return SlideShow ID
     *
     * @return int
     * @throws LocalizedException
     */
    public function getSlideShowId(): ?int
    {
        try {
            $id = (int)$this->context->getRequest()->getParam('slideshow_id');
            return $id ? $this->slideshowFactory->create()->load($id)->getSlideShowId() : null;
        } catch (NoSuchEntityException $e) {
            throw new LocalizedException(
                __("The item does not exist. Please try again."),
                $e
            );
        }
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return  string
     */
    public function getUrl($route = '', $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
