<?php

namespace Entities\Cart\Classes;

use Entities\Packages\Models\PackageLineModel;
use Entities\Payments\Models\TransactionModel;
use Entities\Products\Models\ProductModel;
use Module\Orders\Models\OrderLineModel;

class CartProductCapsule
{
    public $cartItem;
    public $transaction;
    public $orderLine;
    public $parentEntityType = "card";
    public $parentEntityTypeId;
    public $product;
    public $packageLine;
    public $processed = false;
    public $instantiation;

    public function setTransaction(TransactionModel $transaction) : self
    {
        $this->transaction = $transaction;
        return $this;
    }

    public function setOrderLine(OrderLineModel $orderLine) : self
    {
        $this->orderLine = $orderLine;
        return $this;
    }

    public function setProduct(ProductModel $product) : self
    {
        $this->product = $product;
        return $this;
    }

    public function setPackageLine(PackageLineModel $packageLine) : self
    {
        $this->packageLine = $packageLine;
        return $this;
    }

    public function setProductInstantiation($instantiation) : self
    {
        $this->instantiation = $instantiation;
        return $this;
    }

    public function getTransaction() : TransactionModel
    {
        return $this->transaction;
    }

    public function getOrderLine() : OrderLineModel
    {
        return $this->orderLine;
    }

    public function getProduct() : ProductModel
    {
        return $this->product;
    }

    public function getPackageLine() : PackageLineModel
    {
        return $this->packageLine;
    }

    public function getProductInstantiation()
    {
        return $this->instantiation;
    }
    public function setProcessed(bool $logical ) : self
    {
        $this->processed = $logical;
        return $this;
    }

    public function setParentEntity($type, $id) : self
    {
        $this->parentEntityType = $type;
        $this->parentEntityTypeId = $id;
        return $this;
    }
}