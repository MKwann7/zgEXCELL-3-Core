<?php

namespace Entities\Cart\Classes;

use Entities\Packages\Models\PackageLineModel;
use Entities\Payments\Models\TransactionModel;
use Entities\Products\Models\ProductModel;
use Entities\Orders\Models\OrderLineModel;

class CartProductCapsule
{
    public $cartItem;
    public TransactionModel $transaction;
    public OrderLineModel $orderLine;
    public string $parentEntityType = "card";
    public $parentEntityTypeId;
    public ProductModel $product;
    public PackageLineModel $packageLine;
    public bool $processed = false;
    public $instantiation;

    /**
     * @param TransactionModel $transaction
     * @return $this
     */
    public function setTransaction(TransactionModel $transaction) : self
    {
        $this->transaction = $transaction;
        return $this;
    }

    /**
     * @param OrderLineModel $orderLine
     * @return $this
     */
    public function setOrderLine(OrderLineModel $orderLine) : self
    {
        $this->orderLine = $orderLine;
        return $this;
    }

    /**
     * @param ProductModel $product
     * @return $this
     */
    public function setProduct(ProductModel $product) : self
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @param PackageLineModel $packageLine
     * @return $this
     */
    public function setPackageLine(PackageLineModel $packageLine) : self
    {
        $this->packageLine = $packageLine;
        return $this;
    }

    /**
     * @param $instantiation
     * @return $this
     */
    public function setProductInstantiation($instantiation) : self
    {
        $this->instantiation = $instantiation;
        return $this;
    }

    /**
     * @return TransactionModel
     */
    public function getTransaction() : TransactionModel
    {
        return $this->transaction;
    }

    /**
     * @return OrderLineModel
     */
    public function getOrderLine() : OrderLineModel
    {
        return $this->orderLine;
    }

    /**
     * @return ProductModel
     */
    public function getProduct() : ProductModel
    {
        return $this->product;
    }

    /**
     * @return PackageLineModel
     */
    public function getPackageLine() : PackageLineModel
    {
        return $this->packageLine;
    }

    /**
     * @return mixed
     */
    public function getProductInstantiation()
    {
        return $this->instantiation;
    }

    /**
     * @param bool $logical
     * @return $this
     */
    public function setProcessed(bool $logical ) : self
    {
        $this->processed = $logical;
        return $this;
    }

    /**
     * @param string $type
     * @param $id
     * @return $this
     */
    public function setParentEntity(string $type, $id) : self
    {
        $this->parentEntityType = $type;
        $this->parentEntityTypeId = $id;
        return $this;
    }
}