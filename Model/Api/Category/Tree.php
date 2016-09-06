<?php
namespace Styla\Connect2\Model\Api\Category;

class Tree extends \Magento\Catalog\Model\Category\Tree
{
    //this is a list of additional attributes that will be available in the generated category tree
    //this could be refactored to a more extensible implementation, like the product data converters...
    protected $_extraAttributes = ['image'];

    /**
     *
     * @var \Styla\Connect2\Api\Data\StylaCategoryTreeInterfaceFactory
     */
    protected $stylaTreeFactory;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\Tree $categoryTree,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection,
        \Magento\Catalog\Api\Data\CategoryTreeInterfaceFactory $treeFactory,
        \Styla\Connect2\Api\Data\StylaCategoryTreeInterfaceFactory $stylaTreeFactory
    )
    {
        $this->stylaTreeFactory = $stylaTreeFactory;

        return parent::__construct($categoryTree, $storeManager, $categoryCollection, $treeFactory);
    }

    /**
     * @param \Magento\Framework\Data\Tree\Node $node
     * @param int                               $depth
     * @param int                               $currentLevel
     * @return \Magento\Catalog\Api\Data\CategoryTreeInterface
     */
    public function getTree($node, $depth = null, $currentLevel = 0)
    {
        /** @var \Magento\Catalog\Api\Data\CategoryTreeInterface[] $children */
        $children = $this->getChildren($node, $depth, $currentLevel);
        /** @var \Magento\Catalog\Api\Data\CategoryTreeInterface $tree */
        $tree = $this->stylaTreeFactory->create();
        $tree->setId($node->getId())
            ->setParentId($node->getParentId())
            ->setName($node->getName())
            ->setPosition($node->getPosition())
            ->setLevel($node->getLevel())
            ->setIsActive($node->getIsActive())
            ->setProductCount($node->getProductCount())
            ->setChildrenData($children);

        $this->_setExtraAttributes($tree, $node);

        return $tree;
    }

    /**
     * Copy the extra attributes from the node (the category data) to the tree
     *
     */
    protected function _setExtraAttributes($tree, $node)
    {
        foreach ($this->_extraAttributes as $attribute) {
            $tree->setData($attribute, $node->getData($attribute));
        }
    }

    /**
     * Load any additional attributes into the category collection
     *
     */
    protected function _addExtraAttributes($collection)
    {
        foreach ($this->_extraAttributes as $attribute) {
            $collection->addAttributeToSelect($attribute);
        }
    }

    /**
     * @return void
     */
    protected function prepareCollection()
    {
        $storeId = $this->storeManager->getStore()->getId();
        $this->categoryCollection->addAttributeToSelect('name')
            ->addAttributeToSelect('is_active')
            ->setProductStoreId($storeId)
            ->setLoadProductCount(true)
            ->setStoreId($storeId);

        $this->_addExtraAttributes($this->categoryCollection);
    }
}
