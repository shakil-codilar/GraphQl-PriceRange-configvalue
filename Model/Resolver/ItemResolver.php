<?php
declare(strict_types=1);

namespace Codilar\GraphQlDemo\Model\Resolver;

use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Product collection resolver
 */
class ItemResolver implements ResolverInterface
{
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
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
        $id = $this->getId($args);
        $searchCriteriaFilter = $this->searchCriteriaBuilder->addFilter('brand', $id, 'eq')->create();
        $productCollection = $this->productRepository->getList($searchCriteriaFilter);
        $products=$productCollection->getItems();
        $x=0;
        $productRecord['total_count']= $productCollection->getTotalCount();
        $productRecord['items'] = [];
        foreach($products as $product) {
            $productId = $product->getId();
            $productRecord['items'][$productId] = $product->getData();
            $productRecord['items'][$productId] ['model']= $product;
        }
        return $productRecord;
    }
    private function getId(array $args)
    {
        if (!isset($args['brand_id'])) {
            throw new GraphQlInputException(__('"id should be specified'));
        }
        return $args['brand_id'];
    }
}
