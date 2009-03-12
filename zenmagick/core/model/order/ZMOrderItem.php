<?php
/*
 * ZenMagick - Extensions for zen-cart
 * Copyright (C) 2006-2009 ZenMagick
 *
 * Portions Copyright (c) 2003 The zen-cart developers
 * Portions Copyright (c) 2003 osCommerce
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */
?>
<?php


/**
 * A single order item
 *
 * @author DerManoMann
 * @package org.zenmagick.model.order
 * @version $Id$
 */
class ZMOrderItem extends ZMObject {
    var $productId_;
    var $qty_;
    var $name_;
    var $model_;
    var $taxRate_;
    var $calculatedPrice_;
    var $attributes_;


    /**
     * Create new instance.
     */
    function __construct() {
        parent::__construct();
        $this->taxRate_ = null;
        $this->attributes_ = array();
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Get the order item id.
     *
     * @return int The order item id.
     */
    public function getId() { return $this->get('orderItemId'); }

    /**
     * Get the order item product id.
     *
     * @return int The order item product id.
     */
    public function getProductId() { return $this->productId_; }

    /**
     * Get the product this item is associated to.
     *
     * @return ZMProduct The product.
     */
    public public function getProduct() {
        return ZMProducts::instance()->getProductForId($this->getProductId());
    }

    /**
     * Get the quantity.
     *
     * @return int The quantity for this item.
     */
    public function getQty() { return $this->qty_; }

    /**
     * Get the item name.
     *
     * @return string The item name.
     */
    public function getName() { return $this->name_; }

    /**
     * Get the model.
     *
     * @return string The item model.
     */
    public function getModel() { return $this->model_; }

    /**
     * Get the tax rate.
     *
     * @return float The tax rate.
     */
    public function getTaxRate() { 
        if (null == $this->taxRate_) {
            $this->taxRate_ = ZMLoader::make('TaxRate');
            $this->taxRate_->setRate($this->get('taxValue'));
        }

        return $this->taxRate_;
    }

    /**
     * Get the calculated price.
     *
     * @return float The calculated price.
     */
    public function getCalculatedPrice() { return $this->calculatedPrice_; }

    /**
     * Checks if the item has associated attributes.
     *
     * @return boolean </code>true</code> if attributes exist, <code>false</code> if not.
     */
    public function hasAttributes() { return 0 < count($this->attributes_); }

    /**
     * Get the item attributes.
     *
     * @return array A list of <code>ZMAttribute</code> instances.
     */
    public function getAttributes() { return $this->attributes_; }

    /**
     * Set the order item id.
     *
     * @param int id The order item id.
     */
    public function setId($id) { $this->set('orderItemId', $id); }

    /**
     * Set the order item product id.
     *
     * @param int productId The order item product id.
     */
    public function setProductId($productId) { $this->productId_ = $productId; }

    /**
     * Set the quantity.
     *
     * @param int qty The quantity for this item.
     */
    public function setQty($qty) { $this->qty_ = $qty; }

    /**
     * Set the item name.
     *
     * @param string name The item name.
     */
    public function setName($name) { $this->name_ = $name; }

    /**
     * Set the model.
     *
     * @param string model The item model.
     */
    public function setModel($model) { $this->model_ = $model; }

    /**
     * Set the tax rate.
     *
     * @param float taxRate The tax rate.
     */
    public function setTaxRate($taxRate) { $this->taxRate_ = $taxRate; }

    /**
     * Set the calculated price.
     *
     * @param float price The calculated price.
     */
    public function setCalculatedPrice($price) { $this->calculatedPrice_ = $price; }

    /**
     * Add an item attribute.
     *
     * @param ZMAttribute attribute A <code>ZMAttribute</code>.
     */
    public function addAttribute($attribute) { $this->attributes_[] = $attribute; }

    /**
     * Set item attributes.
     *
     * @param array attributes A list of <code>ZMAttribute</code> instances.
     */
    public function setAttributes($attributes) { $this->attributes_ = $attributes; }

}

?>
