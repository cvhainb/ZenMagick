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
 * Store request wrapper.
 *
 * <p><strong>NOTE:</strong</strong> For the time of transition between static and instance
 * usage of request methods this will have a temp. name of <code>ZMRequest</code>.</p>
 *
 * @author DerManoMann
 * @package zenmagick.store.sf.mvc
 */
class Request extends ZMRequest {
    private $categoryPathArray_;
    private $shoppingCart_;
    private $metaTags_;


    /**
     * Create new instance.
     *
     * @param array parameter Optional request parameter; if <code>null</code>,
     *  <code>$_GET</code> and <code>$_POST</code> will be used.
     */
    function __construct($parameter=null) {
        parent::__construct($parameter);
        $this->categoryPathArray_ = null;
        $this->shoppingCart_ = null;
        $this->shoppingCart_ = null;
        $this->metaTags_ = null;
    }


    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * {@inheritDoc}
     */
    public function url($requestId=null, $params='', $secure=false) {
        // custom params handling
        if (null == $requestId || null === $params) {
            // if requestId null, keep current and also current params
            $query = $this->getParameterMap();
            unset($query[ZMSettings::get('zenmagick.mvc.request.idName', ZMRequest::DEFAULT_REQUEST_ID)]);
            unset($query[$this->getSession()->getName()]);
            if (null != $params) {
                parse_str($params, $arr);
                $query = array_merge($query, $arr);
            }
            // rebuild
            $params = array();
            foreach ($query as $name => $value) {
                if (is_array($value)) {
                    foreach ($value as $subValue) {
                        $params[] = $name.'[]='.$subValue;
                    }
                } else {
                    $params[] = $name.'='.$value;
                }
            }
            $params = implode('&', $params);
        }

        // default to current requestId
        $requestId = $requestId === null ? $this->getRequestId() : $requestId;

        // delegate generation to SEO rewriters
        $args = array('requestId' => $requestId, 'params' => $params, 'secure' => $secure);
        foreach ($this->getSeoRewriter() as $rewriter) {
            if (null != ($rewrittenUrl = $rewriter->rewrite($this, $args))) {
                return $rewrittenUrl;
            }
        }

        ZMLogging::instance()->trace('unresolved URL: '.$requestId);
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getRequestId() {
        return $this->getParameter(ZM_PAGE_KEY);
    }

    /**
     * {@inheritDoc}
     */
    public function setRequestId($requestId) {
        parent::setRequestId($requestId);
        $this->setParameter(ZM_PAGE_KEY, $requestId);
    }

    /**
     * Get the current shopping cart.
     *
     * @return ZMShoppingCart The current shopping cart (may be empty).
     */
    public function getShoppingCart() { 
        if (null == $this->shoppingCart_) {
        	// TODO: enable
        	if ($this->isAnonymous() || true) {
              $this->shoppingCart_ = ZMBeanUtils::getBean('ShoppingCart');
        	} else {
        		  $this->shoppingCart_ = ZMShoppingCarts::instance()->loadCartForAccountId($this->getAccountId());
        	}
        }

        return $this->shoppingCart_;
    }

    /**
     * Get the current page index (if available).
     *
     * @return int The current page index (default is 1).
     */
    public function getPageIndex() {  return $this->getParameter('page', 1); }

    /**
     * Get the current sort id.
     *
     * @return string The current sort id.
     */
    public function getSortId() {  return $this->getParameter('sort_id'); }

    /** 
     * Get the sub page name; this is the contents name for static pages.
     *
     * @return strin The static page contents id.
     */
    public function getSubPageName() { return $this->getParameter('cat'); }

    /**
     * Get the product id.
     *
     * @return int The request product id or <code>0</code>.
     */
    public function getProductId() { return (int)$this->getParameter('products_id', $this->getParameter('productId', 0)); }

    /**
     * Get the language code.
     *
     * <p><strong>NOTE:</strong> This will return only the language code as found in the request. If you 
     * want to find out the session language (ie the language for the current request), use <code>Runtime::getLanguageId()</code>
     * or <code>Runtime::getLanguage()</code>.</p>
     *
     * @return string The language code or <code>null</code>.
     */
    public function getLanguageCode() { return (int)$this->getParameter('language'); }

    /**
     * Get the currency code.
     *
     * <p><strong>NOTE:</strong> This will return the currency code as found in the request. If not set,
     * the session currency code will be returned instead. To access the session currency code directly, use 
     * <code>$request->getSession()->getCurrencyCode()</code>.</p>
     *
     * @return string The currency code or <code>null</code>.
     */
    public function getCurrencyCode() { return $this->getParameter('currency', $this->getSession()->getCurrencyCode()); }

    /**
     * Get the request model number.
     *
     * @return string The model numner or <code>null</code>.
     */
    public function getModel() { return $this->getParameter('model'); }

    /**
     * Get the current category path.
     *
     * @return string The category path value (<code>cPath</code>) or <code>null</code>.
     */
    public function getCategoryPath() { return $this->getParameter('cPath', null); }

    /**
     * Get the category path arry.
     *
     * @return array The current category path broken into an array of category ids.
     */
    public function getCategoryPathArray() {
        $path = $this->getParameter('cPath');
        $cPath = array();
        if (null !== $path) {
            $path = explode('_', $path);
            foreach ($path as $categoryId) {
                $categoryId = (int)$categoryId;
                if (!in_array($categoryId, $cPath)) {
                    $cPath[] = $categoryId;
                }
            }
        }
        return $cPath;
    }

    /**
     * Get the current category id.
     *
     * @return int The current category id or <code>0</code>.
     */
    public function getCategoryId() {
        $cPath = $this->getCategoryPathArray();

        if (0 < count($cPath)) {
            return end($cPath);
        }

        return 0;
    }

    /**
     * Set the category path arry.
     *
     * @param array cPath The category path as array.
     */
    public function setCategoryPathArray($cPath) {
        if (is_array($cPath)) {
            $this->setParameter('cPath', implode('_', $cPath));
            $this->setParameter('cPath', implode('_', $cPath));
        } else {
            ZMLogging::instance()->log('invalid cPath: ' . $cPath, ZMLogging::ERROR);
        }
    }

    /**
     * Get the manufacturer id.
     *
     * @return int The manufacturer id or <code>0</code>.
     */
    public function getManufacturerId() { return $this->getParameter('manufacturers_id', 0); }

    /**
     * Get the account id.
     *
     * @return int The account id for the currently logged in user or <code>0</code>.
     */
    public function getAccountId() { return $this->getSession()->getAccountId(); }

    /**
     * {@inheritDoc}
     */
    public function getUser() {
        return $this->getAccount();
    }

    /**
     * Get the account.
     *
     * @return ZMAccount The account or <code>null</code>.
     */
    public function getAccount() {
        $accountId = $this->getAccountId();
        if (0 == $accountId) {
            return null;
        }

        return ZMAccounts::instance()->getAccountForId($accountId);
    }

    /**
     * Get the current review id.
     *
     * @return int The current review id or <code>0</code>.
     */
    public function getReviewId() { return $this->getParameter('reviews_id', 0); }

    /**
     * Get the current order id.
     *
     * @return int The current order id or <code>0</code>.
     */
    public function getOrderId() { return $this->getParameter('order_id',  $this->getParameter('orderId', 0)); }

    /**
     * Returns <code>true</code> if the user is not logged in at all.
     *
     * @return boolean <code>true</code> if the current user is guest, <code>false</code> if not.
     */
    public function isAnonymous() { return $this->getSession()->isAnonymous(); }

    /**
     * Returns <code>true</code> if the user is fully registered and logged in.
     *
     * @return boolean <code>true</code> if the current user is fully registered and logged in, <code>false</code> if not.
     */
    public function isRegistered() { return $this->getSession()->isRegistered(); }

    /**
     * Returns <code>true</code> if the user is a guest user.
     *
     * @return boolean <code>true</code> if the current user is guest, <code>false</code> if not.
     */
    public function isGuest() { return $this->getSession()->isGuest(); }

    /**
     * Get the page base url.
     *
     * @return string A base URL for the current request.
     */
    public function getPageBase() {
        return $this->absoluteUrl('', true);
    }

    /**
     * Checks, if the current page is a checkout page.
     * 
     * @package org.zenmagick.misc
     * @param boolean includeCart If <code>true</code>, the shopping cart is considered a checkout page, too; (defaults to <code>true</code>)
     * @return boolean <code>true</code> if the current page is a checkout page.
     */
    public function isCheckout($includeCart=true) {
        $page = $this->getRequestId();
        return ($includeCart && 'shopping_cart' == $page) || !(false === strpos($page, 'checkout_'));
    }

    /**
     * Check if we are running as admin.
     *
     * @return boolean <code>true</code> if code execution is in the context of an admin page,
     *  <code>false</code> if not.
     */
    public function isAdmin() {
        return defined('IS_ADMIN_FLAG') && constant('IS_ADMIN_FLAG');
    }

    /**
     * {@inheritDoc}
     */
    public function getTemplatePath() {
        // for storefront both templates and resources are relative to the installation folder
        return dirname(ZMRuntime::getInstallationPath()).DIRECTORY_SEPARATOR;
    }

    /**
     * {@inheritDoc}
     */
    public function getWebPath() {
        // for storefront both templates and resources are relative to the installation folder
        return dirname(ZMRuntime::getInstallationPath()).DIRECTORY_SEPARATOR;
    }

    /**
     * Set the last URL.
     */
    public function setLastUrl() {
        // save url to be used as redirect in some cases
        if ('GET' == $this->getMethod()) {
            $this->getSession()->setValue('lastUrl', $this->url());
        } else {
            $this->getSession()->setValue('lastUrl', null);
        }
    }

    /**
     * Get the last URL.
     *
     * @return string The last URL or <code>null</code>.
     */
    public function getLastUrl() {
        return $this->getSession()->getValue('lastUrl');
    }

}
