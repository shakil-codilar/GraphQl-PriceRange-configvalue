<?php

namespace Codilar\GraphQlDemo\Model\Resolver;

use Magento\CatalogGraphQl\Model\Resolver\Product\ProductFieldsSelector;
use Magento\CatalogGraphQl\Model\Resolver\Products\DataProvider\Deferred\Product as ProductDataProvider;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ValueFactory;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Exception\LocalizedException;

class BrandResolver implements ResolverInterface
{
    protected $brandCollectionFactory;
    /**
     * @var ValueFactory
     */
    private $valueFactory;

    /**
     * @param ValueFactory $valueFactory
     */

    public function __construct(
        \Codilar\Attribute\Block\ProductList $brandCollectionFactory,
        valueFactory $valueFactory
    )
    {
        $this->brandCollectionFactory = $brandCollectionFactory;
        $this->valueFactory = $valueFactory;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $items = $this->brandCollectionFactory->getText();
        $result=[];
        $x=0;
        foreach ($items as $item){
            $result[$x]['id']= $item->getBrandId();
            $result[$x]['is_active']= $item->getIsActive();
            $result[$x]['name']= $item->getName();
            $result[$x]['information']= $item->getInfo();
            $x++;
        }

        return $result;
    }
}
