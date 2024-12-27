<?php
/**
 * Copyright © The Garden Health & Beauty Store, Inc All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Garden\SlideShow\Controller\Adminhtml\SlideShow;

use Garden\Service\Api\ImageInfoInterface;
use Garden\SlideShow\Model\Config;
use Garden\SlideShow\Model\ResourceModel\SlideShow as ResourceSlideShow;
use Garden\SlideShow\Model\SlideShowFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\View\Result\PageFactory;

class Delete extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Garden_SlideShow::manage';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var SlideShowFactory
     */
    protected $slideshowFactory;

    /**
     * @var ResourceSlideShow
     */
    private $resourceSlideShow;

    /**
     * @var ImageInfoInterface
     */
    protected $imageInfo;

    /**
     * Delete constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param SlideShowFactory $slideshowFactory
     * @param ResourceSlideShow $resourceSlideShow
     * @param ImageInfoInterface $imageInfo
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        SlideShowFactory $slideshowFactory,
        ResourceSlideShow $resourceSlideShow,
        ImageInfoInterface $imageInfo
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->slideshowFactory = $slideshowFactory;
        $this->resourceSlideShow = $resourceSlideShow;
        $this->imageInfo = $imageInfo;
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('slideshow_id');
        $model = $this->slideshowFactory->create();
        $this->imageInfo->setImageDirPath(Config::SLIDESHOW_MEDIA_PATH);

        if ($id) {
            try {
                // init model and delete
                $this->resourceSlideShow->load($model, $id);
                //unlink image
                $this->imageInfo->removeImage($model->getFldImage());
                $this->resourceSlideShow->delete($model);
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the item.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['slideshow_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find the item to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
