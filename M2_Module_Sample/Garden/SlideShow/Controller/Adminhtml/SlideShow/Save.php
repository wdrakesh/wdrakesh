<?php

declare(strict_types=1);
/**
 * Copyright © The Garden Health & Beauty Store, Inc All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Garden\SlideShow\Controller\Adminhtml\SlideShow;

use Garden\SlideShow\Model\ResourceModel\SlideShow as ResourceSlideShow;
use Garden\SlideShow\Model\SlideShow;
use Garden\SlideShow\Model\SlideShowFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Result\PageFactory;
use Garden\Service\Api\ImageUploaderInterface;
use Garden\SlideShow\Model\Config;

class Save extends Action implements HttpPostActionInterface
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var TimezoneInterface
     */
    private $timezone;
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;
    /**
     * @var Date
     */
    protected $_dateFilter;
    /**
     * @var SlideShowFactory
     */
    private $slideshowFactory;
    /**
     * @var ResourceSlideShow
     */
    private $resourceSlideShow;
    /**
     * @var mixed
     */
    private $imageUploader;

    /**
     * Save constructor.
     * @param Context $context
     * @param Date $dateFilter
     * @param SlideShowFactory $slideshowFactory
     * @param ResourceSlideShow $resourceSlideShow
     * @param ImageUploaderInterface $imageUploader
     * @param TimezoneInterface|null $timezone
     * @param DataPersistorInterface|null $dataPersistor
     */
    public function __construct(
        Context $context,
        Date $dateFilter,
        SlideShowFactory $slideshowFactory,
        ResourceSlideShow $resourceSlideShow,
        ImageUploaderInterface $imageUploader,
        TimezoneInterface $timezone = null,
        DataPersistorInterface $dataPersistor = null
    ) {
        parent::__construct($context);
        $this->_dateFilter = $dateFilter;
        $this->timezone =  $timezone ?? \Magento\Framework\App\ObjectManager::getInstance()->get(
            TimezoneInterface::class
        );
        $this->dataPersistor = $dataPersistor ?? \Magento\Framework\App\ObjectManager::getInstance()->get(
            DataPersistorInterface::class
        );
        $this->slideshowFactory = $slideshowFactory;
        $this->resourceSlideShow = $resourceSlideShow;
        $this->imageUploader = $imageUploader;
    }

    protected function _filterImageProcessor(array $rawData)
    {
        $data = $rawData;

        //set temp path and original path
        $this->imageUploader->setBasePath(Config::IMAGE_BASE_PATH);
        $this->imageUploader->setBaseTmpPath(Config::IMAGE_TMP_PATH);

        if (isset($data['fld_image'][0]['name']) && isset($data['fld_image'][0]['tmp_name'])) {
            $data['fld_image'] = $data['fld_image'][0]['name'];
            $this->imageUploader->moveFileFromTmp($data['fld_image']);
        } elseif (isset($data['fld_image'][0]['name']) && !isset($data['fld_image'][0]['tmp_name'])) {
            $data['fld_image'] = $data['fld_image'][0]['name'];
        } else {
            $data['fld_image'] = '';
        }
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($this->getRequest()->getPostValue()) {
            $model = $this->slideshowFactory->create();
            try {
                /** @var  $data */
                $data = $this->getRequest()->getPostValue();
                $this->_filterImageProcessor($data);

                $id = (int)$this->getRequest()->getParam('slideshow_id');
                if ($id) {
                    $this->resourceSlideShow->load($model, $id);
                }

                $storeIds = $data['store_id'];
                $data['store_id'] = isset($storeIds) ? implode(',', $storeIds) : 0;
                $data['fld_image'] = $data['fld_image'][0]['name'] ?? null;
                unset($data['storeviews']);

                $model->setData($data);

                $this->dataPersistor->set('slideshow_item', $data);
                $this->resourceSlideShow->save($model);

                $this->messageManager->addSuccessMessage(__('You saved the item.'));
                return $this->processResultRedirect($model, $resultRedirect);
            } catch (\Throwable $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while saving the item.' . $e->getMessage()));
            }
            $this->dataPersistor->set('slideshow_item', $data);
            return $resultRedirect->setPath('*/*/edit', ['slideshow_id' => $this->getRequest()->getParam('slideshow_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param $model
     * @param $resultRedirect
     * @return mixed
     */
    private function processResultRedirect($model, $resultRedirect)
    {
        $this->dataPersistor->clear('slideshow_item');
        if ($this->getRequest()->getParam('back') === 'continue') {
            return $resultRedirect->setPath('*/*/edit', ['slideshow_id' => $model->getSlideShowId(), '_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
