<?php
/**
 * Copyright © The Garden Health & Beauty Store, Inc All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Garden\SlideShow\Block;

use Garden\SlideShow\Model\ResourceModel\SlideShow\CollectionFactory;
use Garden\SlideShow\Model\SlideShow;
use Garden\SlideShow\Model\Config;
use Garden\SlideShow\Ui\Component\Form\DisplayPosition\Options;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;

class CmsPage extends Template
{
    /**
     * @var Collection
     */
    protected $sliderCollection;

    /**
     * @var Collection
     */
    protected $collectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var File
     */
    protected $fileDriver;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param StoreManagerInterface $storeManager
     * @param File $fileDriver
     * @param DirectoryList $directoryList
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        File $fileDriver,
        DirectoryList $directoryList
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->fileDriver = $fileDriver;
        $this->directoryList = $directoryList;
    }

    /**
     * get slideshow data
     *
     * @param string $displayPosition
     * @return Collection
     * @throws NoSuchEntityException
     */
    private function getSlideShow($displayPosition = '')
    {
        $this->sliderCollection = $this->collectionFactory->create()
            ->addFieldToFilter(
                ['store_id', 'store_id'],
                [
                    ['finset' => $this->storeManager->getStore()->getId()],
                    ['eq' => 0]
                ]
            )
            ->addFieldToFilter('is_active', [SlideShow::STATUS_ENABLED], 'eq')
            ->addFieldToFilter('display_position', [$displayPosition], 'eq')
            ->setOrder('sort_order', 'ASC');
        return $this->sliderCollection;
    }

    /**
     * get full image url
     *
     * @param string $fileName
     * @return string
     * @throws NoSuchEntityException
     */
    public function getFullImagePath($fileName)
    {
        return $this->storeManager->getStore()->getBaseUrl(
            UrlInterface::URL_TYPE_MEDIA
        ) . Config::SLIDESHOW_MEDIA_PATH . '/' . $fileName;
    }

    /**
     * check image exists
     *
     * @param string $fileName
     * @return bool
     * @throws FileSystemException
     */
    public function IsImageExits($fileName)
    {
        if ($fileName == '') {
            return false;
        }
        $absolutePath = (string)$this->directoryList->getPath('media') . Config::SLIDESHOW_MEDIA_PATH . '/' . $fileName;
        return $this->fileDriver->isExists($absolutePath);
    }

    /**
     * get full url
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getStoreUrlLink()
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_LINK);
    }

    public function getSliderData()
    {
        return $this->getSlideShow(Options::SLIDER_KEY);
    }

    public function getFavouriteBrand()
    {
        return $this->getSlideShow(Options::BRAND_KEY);
    }

    public function getBannerData()
    {
        return $this->getSlideShow(Options::BANNER_KEY);
    }
}
