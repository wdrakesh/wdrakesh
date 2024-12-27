<?php
/**
 * Copyright © The Garden Health & Beauty Store, Inc All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Garden\SlideShow\Model;

use Garden\SlideShow\Model\ResourceModel\SlideShow\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Garden\Service\Api\ImageInfoInterface;
use Garden\SlideShow\Model\Config;

class SlideShowDataProvider extends AbstractDataProvider
{
    public const IMAGE_FIELD_NAME = 'fld_image';

    /**
     * @var Collection
     */
    protected $collection;
    /**
     * @var array
     */
    protected $loadedData;
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    /**
     * @var ImageInfoInterface
     */
    protected $imageInfo;

    /**
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param ImageInfoInterface $imageInfo
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        ImageInfoInterface $imageInfo,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->collectionFactory = $collectionFactory;
        $this->dataPersistor = $dataPersistor;
        $this->imageInfo = $imageInfo;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData(): ?array
    {
        if (isset($this->_loadedData)) {
            return $this->_loadedData;
        }

        $this->collection = $this->collectionFactory->create();
        $items = $this->collection->getItems();
        /** @var SlideShow $slideshow */
        foreach ($items as $slideshow) {
            $this->convertValues($slideshow);
            $this->loadedData[$slideshow->getId()] = $slideshow->getData();
        }

        //case when run time get error need to persist data on form
        $data = $this->dataPersistor->get('slideshow_item');
        if (!empty($data)) {
            $slideshow = $this->collection->getNewEmptyItem();
            $slideshow->setData($data);
            $this->convertValues($slideshow);
            $this->loadedData[$slideshow->getId()] = $slideshow->getData();
            $this->dataPersistor->clear('slideshow_item');
        }

        return $this->loadedData;
    }

    /**
     * Converts slideshow image data to acceptable for rendering format
     *
     * @param SlideShow $slideshow
     * @return void
     */
    private function convertValues($slideshow): void
    {
        //for image
        $img = [];
        $this->imageInfo->setImageDirPath(Config::SLIDESHOW_MEDIA_PATH);
        $fileName = $slideshow->getData(self::IMAGE_FIELD_NAME);
        if ($this->imageInfo->isExist($fileName)) {

            $img[0] = $this->imageInfo->getFileInfo($fileName);
        }
        //$resultData['fld_image'] = $img;

        //for category IDs
        $storeIds = $slideshow->getData('store_id');
        $store_ids = !is_array($storeIds) ? explode(',', $storeIds) : $storeIds;
        $slideshow->setData('fld_image', $img);
        $slideshow->setData('storeviews', $store_ids);
    }
}
