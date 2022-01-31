<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\GraphQlDemo\Model\Resolver;

use Magento\CatalogGraphQl\Model\Resolver\Products\Query\ProductQueryInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\CatalogGraphQl\DataProvider\Product\SearchCriteriaBuilder;
use Magento\Framework\Pricing\PriceInfo\Factory as PriceInfoFactory;

/**
 * Products field resolver, used for GraphQL request processing.
 */
class ProductPriceResolver implements ResolverInterface
{
    protected $priceInfoFactory;
    protected $helperData;
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Codilar\GraphQlDemo\Helper\Data $helperData,
        \Magento\Framework\App\Action\Context $context,
        PriceInfoFactory $priceInfoFactory
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->helperData = $helperData;
        $this->priceInfoFactory = $priceInfoFactory;

    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
              $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $this->validateInput($args);

        $maxPrice= $this->getMaxValue();
        $minPrice= $this->getMinValue();

        $searchCriteria = $this->searchCriteriaBuilder->addFilter('entity_id', 1,'gteq')->create();
        $products = $this->productRepository->getList($searchCriteria)->getItems();


        $productRecord['items'] = [];
        foreach($products as $product) {
            $finalPrice = $product->getFinalPrice();
            $productId = $product->getId();
            if ($minPrice < $finalPrice && $finalPrice < $maxPrice){

                $productRecord['items'][$productId] = $product->getData();
                $productRecord['items'][$productId] ['model'] = $product;
            }
        }

        return $productRecord;
    }

    /**
     * Validate input arguments
     *
     * @param array $args
     * @throws GraphQlAuthorizationException
     * @throws GraphQlInputException
     */
    private function validateInput(array $args)
    {
        if ($args['price']['from']==="maxprice" && $args['price']['to']==="minprice") {
            return $args['price'];
        }
        else{
             throw new GraphQlAuthorizationException(__('arguments should be maxprice and minprice'));

        }
    }

    private function getMaxValue()
    {
        return $this->helperData->getMaximumPrice();
    }
    private function getMinValue()
    {
        return $this->helperData->getMinimumPrice();
    }
}
