<?php
/**
 * Copyright © The Garden Health & Beauty Store, Inc All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Garden\SlideShow\Controller\Adminhtml\SlideShow;

use Garden\SlideShow\Model\ResourceModel\SlideShow as ResourceSlideShow;
use Garden\SlideShow\Model\SlideShowFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'Garden_SlideShow::manage';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var ResourceSlideShow
     */
    private $resourceSlideShow;
    /**
     * @var SlideShowFactory
     */
    private $slideshowFactory;

    /**
     * Edit constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param SlideShowFactory $slideshowFactory
     * @param ResourceSlideShow $resourceSlideShow
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        SlideShowFactory $slideshowFactory,
        ResourceSlideShow $resourceSlideShow
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->slideshowFactory = $slideshowFactory;
        $this->resourceSlideShow = $resourceSlideShow;
        parent::__construct($context);
    }

    /**
     * Edit Item
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('slideshow_id');
        $model = $this->slideshowFactory->create();

        // 2. Initial checking
        if ($id) {
            $this->resourceSlideShow->load($model, $id);
            if (!$model->getSlideShowId()) {
                $this->messageManager->addErrorMessage(__('This item no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        //$this->_coreRegistry->register('slideshow_item', $model);

        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Garden_SlideShow::manage');
        $resultPage->getConfig()->getTitle()->prepend(__('SlideShow'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getSlideShowId() ? 'Edit Item ID: ' . $model->getSlideShowId() : __('New Item'));

        return $resultPage;
    }
}
