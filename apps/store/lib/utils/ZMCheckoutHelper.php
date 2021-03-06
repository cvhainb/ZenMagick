<?php
/*
 * ZenMagick - Smart e-commerce
 * Copyright (C) 2006-2010 zenmagick.org
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
 * Checkout helper.
 *
 * @author DerManoMann
 * @package zenmagick.store.sf.utils
 */
class ZMCheckoutHelper extends ZMObject {
    const CART_PRODUCT_STATUS = 'status';
    const CART_PRODUCT_QUANTITY = 'quantity';
    const CART_PRODUCT_UNITS = 'units';
    private $shoppingCart_;


    /**
     * Create new instance.
     *
     * @param ZMShoppingCart shoppingCart The cart.
     */
    function __construct($shoppingCart) {
        parent::__construct();
        $this->shoppingCart_ = $shoppingCart;
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Check if the given shopping cart qualifies for free shipping (as per free shipping ot).
     *
     * @return boolean <code>true</code> if the cart qualifies for free shipping.
     */
    public function isFreeShipping() {
        if (ZMSettings::get('isOrderTotalFreeShipping')) {
            $pass = false;
            $shippingAddress = $this->shoppingCart_->getShippingAddress();
            switch (ZMSettings::get('freeShippingDestination')) {
            case 'national':
                if ($shippingAddress->getCountryId() == ZMSettings::get('storeCountry')) {
                    $pass = true;
                }
                break;
            case 'international':
                if ($shippingAddress->getCountryId() != ZMSettings::get('storeCountry')) {
                    $pass = true;
                }
                break;
            case 'both':
                $pass = true;
                break;
            }

            if (($pass == true) && ($this->shoppingCart_->getTotal() >= ZMSettings::get('freeShippingOrderThreshold'))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if there are only gift vouchers in the cart.
     *
     * @return boolean <code>true</code> if only vouchers are in the cart.
     */
    public function isGVOnly() { 
        foreach ($this->shoppingCart_->getItems() as $item) {
            $product = $item->getProduct();
            if (!preg_match('/^GIFT/', $product->getModel())) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks for free products in the cart.
     *
     * @return int The number of free products in the cart.
     */
    public function freeProductsCount() {
        $count = 0;
        foreach ($this->shoppingCart_->getItems() as $item) {
            $product = $item->getProduct();
            if ($product->isFree()) {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * Checks for virtual products in the cart.
     *
     * @return int The number of virtual products in the cart.
     */
    public function virtualProductsCount() {
        $count = 0;
        foreach ($this->shoppingCart_->getItems() as $item) {
            $product = $item->getProduct();
            if ($product->isVirtual()) {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * Checks for free shipping.
     *
     * @return int The number of free shipping products in the cart.
     */
    public function freeShippingCount() {
        $count = 0;
        foreach ($this->shoppingCart_->getItems() as $item) {
            $product = $item->getProduct();
            if ($product->isAlwaysFreeShipping()) {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * Check for virtual cart.
     *
     * <p><strong>NOTE:</strong> In contrast to Zen Cart, we treat the <em>always free shipping</em>
     * product attribute as <code>boolean</code>. That means currently there is no support for
     * the special case where virtual products <strong>do</strong> require a shipping address.</p>
     * 
     * @return boolean <code>true</code> if the cart is purely virtual.
     */
    public function isVirtual() {
        return 'virtual' == $this->getType();
    }

    /**
     * Get the cart type.
     * 
     * @return string The cart type; one of <em>physical</em>, <em>mixed</em>, <em>virtual</em>.
     */
    public function getType() {
        return $this->shoppingCart_->cart_->get_content_type();
    }

    /**
     * Check whether the cart is ready for checkout or not.
     *
     * <p>Possible return values:</p>
     * <ul>
     *  <li>status - one or more products are not availalable (product status)</li>
     *  <li>quantity - one or more products have unsatisfied quantity restrictions</li>
     *  <li>units - one or more products have unsatisfied unit restrictions</li>
     * </ul>
     *
     * @return array A map of errorCode =&gt; item pairs.
     */
    public function checkCartStatus() {
        $map = array();
        foreach ($this->shoppingCart_->getItems() as $item) {
            $product = $item->getProduct();

            // check product status
            if (!$product->getStatus()) {
                if (!isset($map[self::CART_PRODUCT_STATUS])) {
                    $map[self::CART_PRODUCT_STATUS] = array();
                }
                $map[self::CART_PRODUCT_STATUS][] = $item;
            }

            // check min qty
            $minQty = $product->getMinOrderQty();
            $qty = $item->getQuantity();
            if ($product->isQtyMixed()) {
                $tqty = 0;
                // make $qty the total over all attribute combinations (SKUs) in the cart
                foreach ($this->shoppingCart_->getItems() as $titem) {
                    if ($product->getId() == $titem->getProduct()->getId()) {
                        $tqty += $titem->getQuantity();
                    }
                }
                $qty = $tqty;
            }
            if ($qty < $minQty) {
                if (!isset($map[self::CART_PRODUCT_QUANTITY])) {
                    $map[self::CART_PRODUCT_QUANTITY] = array();
                }
                $map[self::CART_PRODUCT_QUANTITY][] = $item;
            }

            // check quantity units
            $units = $product->getQtyOrderUnits();
            if (ZMTools::fmod_round($qty, $units)) {
                if (!isset($map[self::CART_PRODUCT_UNITS])) {
                    $map[self::CART_PRODUCT_UNITS] = array();
                }
                $map[self::CART_PRODUCT_UNITS][] = $item;
            }
        }

        return $map;
    }

    /**
     * Check whether the cart is ready for checkout or not.
     *
     * <p><strong>NOTE:</strong> The main difference to the Zen Cart implementation of this method is that 
     * no error messages are generated. This is left to the controller to handle.</p>
     *
     * @return boolean <code>true</code> if the cart is ready or checkout, <code>false</code> if not.
     */
    public function readyForCheckout() {
        return 0 == count($this->checkCartStatus());
    }

    /**
     * Check stock.
     *
     * @param boolean messages Optional flag to enable/hide messages related to stock checking; default is <code>true</code>.
     * @return boolean <code>true</code> if the stock check was sucessful (or disabled).
     */
    public function checkStock($messages=true) {
        if (ZMSettings::get('isEnableStock') && $this->shoppingCart_->hasOutOfStockItems()) {
            if (ZMSettings::get('isAllowLowStockCheckout')) {
                if ($messages) {
                    ZMMessages::instance()->warn('Products marked as "Out Of Stock" will be placed on backorder.');
                }
                return true;
            } else {
                if ($messages) {
                    ZMMessages::instance()->error('The shopping cart contains products currently out of stock. To checkout you may either lower the quantity or remove those products from the cart.');
                }
                return false;
            }
        }
        return true;
    }

    /**
     * Validate the current checkout request.
     *
     * @param ZMRequest request The current request.
     * @param boolean showMessages Optional flag to enable/hide messages related to validation issues; default is <code>true</code>.
     * @return string Either a <em>viewId</em>, which would indicate an error/issue, or <code>null</code>
     *  if everything is ok.
     */
    public function validateCheckout($request, $showMessages=true) {
        if ($this->shoppingCart_->isEmpty()) {
            return "empty_cart";
        }
        $session = $request->getSession();
        if (null == ZMAccounts::instance()->getAccountForId($session->getAccountId())) {
            $session->clear();
            $request->saveFollowUpUrl();
            return "login";
        }

        if (!$this->readyForCheckout()) {
            if ($showMessages) {
                ZMMessages::instance()->error(_zm('Please update your order ...'));
            }
            return "cart_not_ready";
        }

        if (!$this->checkStock($showMessages)) {
            return "low_stock";
        }

        if (!$this->isVirtual() && null == $this->shoppingCart_->getSelectedShippingMethod()) {
            return 'require_shipping';
        }

        // TODO: check for free shipping and return back to shipping page if so?
        // or just add message about qualifying and leave it to the user?

        return null;
    }

    /**
     * Validate checkout addresses.
     *
     * @param ZMRequest request The current request.
     * @param boolean showMessages Optional flag to enable/hide messages related to validation issues; default is <code>true</code>.
     * @return string Either a <em>viewId</em>, which would indicate an error/issue, or <code>null</code>
     *  if everything is ok.
     */
    public function validateAddresses($request, $showMessages=true) {
        // validate addresses
        $session = $request->getSession();
        $account = $request->getAccount();
        if (!$session->isAnonymous() && !$this->shoppingCart_->hasShippingAddress() && !$this->shoppingCart_->isVirtual()) {
            if (0 < $account->getDefaultAddressId()) {
                $this->shoppingCart_->setShippingAddressId($account->getDefaultAddressId());
                // TODO: reset selected shipping method as address changed (if addressId set in session is invalid)
            } else {
                if ($showMessages) {
                    ZMMessages::instance()->error(_zm('Please provide a shipping address'));
                }
                return "require_shipping_address";
            }
        }
        if (!$this->shoppingCart_->hasBillingAddress()) {
            if (0 < $account->getDefaultAddressId()) {
                $this->shoppingCart_->setBillingAddressId($account->getDefaultAddressId());
            } else {
                if ($showMessages) {
                    ZMMessages::instance()->error(_zm('Please provide a billing address'));
                }
            }
            return "require_payment_address";
        }

        return null;
    }

    /**
     * Save the cart hash as reference against tampering.
     *
     * @param ZMRequest request The current request.
     */
    public function saveHash($request) {
        $request->getSession()->setValue('shoppingCartHash', $this->shoppingCart_->getHash());
    }

    /**
     * Validate the cart hash.
     *
     * @param ZMRequest request The current request.
     * @return boolean <code>true</code> if, and only if, the session cart hash and the current hash are the same.
     */
    public function verifyHash($request) {
        return $request->getSession()->getValue('shoppingCartHash') == $this->shoppingCart_->getHash();
    }

    /**
     * Mark cart as free shipping.
     */
    public function markCartFreeShipping() {
        // TODO: 
        $_SESSION['shipping'] = 'free_free';
        // not sure there are other cases where we want to mark the cart, but just in case...
        if ($this->isVirtual()) {
            $_SESSION['sendto'] = false;
        }
    }

    /**
     * Get available payment types for the cart.
     *
     * <p>Includes logic to handle the <em>freecharger</code> payment type.</p>
     *
     * @return array List of <code>ZMPaymentType</code> instances.
     */
    public function getPaymentTypes() {
        $cartTotal = $this->shoppingCart_->getTotal();

        ZMTools::prepareWrapperEnv($this->shoppingCart_, $this->shoppingCart_->getBillingAddress());

        //TODO: fix
        $shippingCost = $_SESSION['shipping']['cost'];

        if (defined('MODULE_PAYMENT_FREECHARGER_STATUS') && MODULE_PAYMENT_FREECHARGER_STATUS && 0 == $cartTotal && 0 == $shippingCost) {
            return array(ZMPaymentTypes::instance()->getPaymentTypeForId('freecharger'));
        }

        // all available except freecharger
        $paymentTypes = ZMPaymentTypes::instance()->getPaymentTypes();
        if (array_key_exists('freecharger', $paymentTypes)) {
            unset($paymentTypes['freecharger']);
        }

        ZMTools::cleanupWrapperEnv();

        return $paymentTypes;
    }

}
