<?php

declare(strict_types=1);

namespace Codilar\GraphQlDemo\Model\Resolver\Products\SearchCriteria\CollectionProcessor\FilterProcessor;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Api\Filter;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Framework\Registry;

/**
 * Category filter allows to filter products collection using custom defined filters from search criteria.
 */
class ProductAttributeFilter implements CustomFilterInterface
{
    protected $configurable;
    protected $collectionFactory;
    protected $registry;

    public function __construct(
        Configurable $configurable,
        CollectionFactory $collectionFactory,
        \Psr\Log\LoggerInterface $logger,
        Registry $registry
    ) {
        $this->registry = $registry;
        $this->configurable = $configurable;
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
    }

    public function apply(Filter $filter, AbstractDb $collection)
    {
        $conditionType = $filter->getConditionType();
        $attributeName = $filter->getField();
        $attributeValue = $filter->getValue();
        $category = $this->registry->registry('current_category');


        if($attributeName == 'brand'){
            $conditions = [];
            foreach ($attributeValue as $value){
                $conditions[] = ['attribute'=>$attributeName, 'finset'=>$value];
            }
            $simpleSelect = $this->collectionFactory->create()
                ->addAttributeToFilter($conditions);

        }else{
            $simpleSelect = $this->collectionFactory->create()
                ->addAttributeToFilter($attributeName, [$conditionType => $attributeValue]);
        }

        $simpleSelect->addAttributeToFilter('status', Status::STATUS_ENABLED);
        if ($category) {
            $simpleSelect->addCategoriesFilter(['in' => (int)$category->getId()]);
        }


        $arr =  $simpleSelect;
        $entity_ids = [];
        foreach ($arr->getData() as $a){
            $entity_ids[] = $a['brand_id'];
        }

        $collection->getSelect()->where($collection->getConnection()->prepareSqlCondition(
            'e.brand_id', ['in' => $entity_ids]
        ));

        return true;
    }
}
