<?php
namespace Styla\Connect2\Controller\Adminhtml\Connector;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory as PageFactory;
use Magento\Framework\App\Action\Context;

class Connect extends Action
{
    protected $pageFactory;
    
    public function __construct(Context $context, PageFactory $pageFactory) {
        $this->pageFactory = $pageFactory;
        
        return parent::__construct($context);
    }
    
    public function execute() {
        $page = $this->pageFactory->create();
        
        return $page;
    }
}
