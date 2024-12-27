<?php
/**
 * Copyright © The Garden Health & Beauty Store, Inc All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Garden\SlideShow\Controller\Adminhtml\SlideShow;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
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

    private $dataPersistor;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param DataPersistorInterface|null $dataPersistor
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        DataPersistorInterface $dataPersistor = null
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->dataPersistor = $dataPersistor ?: ObjectManager::getInstance()->get(DataPersistorInterface::class);
    }

    /**
     * Index action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Garden_SlideShow::manage');
        $resultPage->addBreadcrumb(__('Garden'), __('Garden'));
        $resultPage->addBreadcrumb(__('Manage Items'), __('Manage Items'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Items'));

        $this->dataPersistor->clear('slideshow_item');

        return $resultPage;
    }
}
