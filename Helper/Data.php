<?php

namespace Codilar\GraphQlDemo\Helper;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper {
    protected $context;
    public function __construct(Context $context)
    {
        $this->context = $context;
        parent::__construct($context);
    }

    public function getMaximumPrice()
    {
        return $this->scopeConfig->getValue('graphql/general/maximum_price', ScopeInterface::SCOPE_STORE);
    }
    public function getMinimumPrice()
    {
        return $this->scopeConfig->getValue('graphql/general/minimum_price', ScopeInterface::SCOPE_STORE);
    }
}
