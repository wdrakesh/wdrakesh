<?php
/**
 * Copyright © The Garden Health & Beauty Store, Inc All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Garden\Slideshow\Ui\Component\Listing\Column;

use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Garden\SlideShow\Model\Config;

class FldImage extends Column
{
    /**
         * @var UrlInterface
         */
    public const IMAGE_WIDTH = '150';
    public const IMAGE_HEIGHT = '90';
    public const IMAGE_STYLE = 'display: block;';
    public const IMAGE_KEY = 'fld_image';

    /**
     * Store manager
     *
     * @var StoreManager
     */
    private $_storeManager;

    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var string
     */
    protected $imageKey;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Escaper $escaper
     * @param StoreManagerInterface $storemanager
     * @param array $components
     * @param array $data
     * @param string $imageKey
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Escaper $escaper,
        StoreManagerInterface $storemanager,
        array $components = [],
        array $data = [],
        $imageKey = self::IMAGE_KEY
    ) {
        $this->_storeManager = $storemanager;
        $this->escaper = $escaper;
        $this->imageKey = $imageKey;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }

    /**
     * Get data
     *
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        if (!isset($item[$this->imageKey])) {
            return '';
        }
        $imageKey = $item[$this->imageKey];
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
            UrlInterface::URL_TYPE_MEDIA
        );

        $srcImage = $mediaDirectory . Config::SLIDESHOW_MEDIA_PATH . '/' . $imageKey;

        return sprintf(
            '<img src="%s"  width="%s"  style="%s" />',
            $srcImage,
            self::IMAGE_WIDTH,
            self::IMAGE_STYLE
        );
    }
}
