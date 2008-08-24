<?php
/*
 * ZenMagick - Extensions for zen-cart
 * Copyright (C) 2006-2008 ZenMagick
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
 * Search criteria.
 *
 * @author DerManoMann
 * @package org.zenmagick.model
 * @version $Id$
 */
class ZMSearchCriteria extends ZMModel {

    /**
     * Create new instance.
     */
    function __construct() {
        parent::__construct();

        $this->set('includeTax', ZMSettings::get('isTaxInclusive'));
        $this->set('countryId', ZMSettings::get('storeCountry'));
        $this->set('zoneId', ZMSettings::get('storeCountry'));
        $this->set('languageId', ZMSettings::get('storeDefaultLanguageId'));
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Populate all available fields from the given request.
     *
     * @param array req A request; if <code>null</code>, use the current <code>ZMRequest</code> instead.
     */
    public function populate($req=null) {
        $this->set('keywords', ZMRequest::getParameter('keyword'));
        $this->set('priceFrom', ZMRequest::getParameter('pfrom'));
        $this->set('priceTo', ZMRequest::getParameter('pto'));
        $this->set('dateFrom', ZMRequest::getParameter('dfrom'));
        $this->set('dateTo', ZMRequest::getParameter('dto'));
        $this->set('categoryId', ZMRequest::getParameter('categories_id'), 0);
        $this->set('includeSubcategories', ZMRequest::getParameter('inc_subcat') == '1');
        $this->set('manufacturerId', ZMRequest::getParameter('manufacturers_id'), 0);
        $this->set('includeDescription', ZMRequest::getParameter('search_in_description') == '1');
    }

    /**
     * Get the keyword(s).
     *
     * @param string default A default value.
     * @return string The search term.
     */
    public function getKeywords($default='') { return $this->get('keywords', $default); }

    /**
     * Get the include description flag.
     *
     * @param string default A default value.
     * @return boolean <code>true</code> if descriptions should be searched too.
     */
    public function isIncludeDescription($default=true) { return $this->get('includeDescription', $default); }

    /**
     * Get the category.
     *
     * @param string default A default value.
     * @return integer The category id.
     */
    public function getCategoryId($default=0) { return $this->get('categoryId', $default); }

    /**
     * Get the include subcategories flag.
     *
     * @param string default A default value.
     * @return boolean <code>true</code> if subcategories should be searched too.
     */
    public function isIncludeSubcategories($default=true) { return $this->get('includeSubcategories', $default); }

    /**
     * Get the manufacturer.
     *
     * @param string default A default value.
     * @return integer The manufacturer id.
     */
    public function getManufacturerId($default='') { return $this->get('manufacturerId', $default); }

    /**
     * Get the from date.
     *
     * @param string default A default value.
     * @return string The from date.
     */
    public function getDateFrom($default='') { return $this->get('dateFrom', $default); }

    /**
     * Get the to date.
     *
     * @param string default A default value.
     * @return string The to date.
     */
    public function getDateTo($default='') { return $this->get('dateTo', $default); }

    /**
     * Get the price from.
     *
     * @param string default A default value.
     * @return string The price from.
     */
    public function getPriceFrom($default='') { return $this->get('priceFrom', $default); }

    /**
     * Get the price to.
     *
     * @param string default A default value.
     * @return string The price to.
     */
    public function getPriceTo($default='') { return $this->get('priceTo', $default); }

    /**
     * Check if prices are tax inclusive.
     *
     * @return boolean <code>true</code> if included, <code>false</code> if not.
     */
    public function isIncludeTax() { return $this->get('includeTax'); }

    /**
     * Get the country for tax calculations (if required).
     *
     * @return integer The country id.
     */
    public function getCountryId() { return $this->get('countryId'); }

    /**
     * Get the zone for tax calculations (if required).
     *
     * @return integer The zone id.
     */
    public function getZoneId() { return $this->get('zoneId', 0); }

    /**
     * Get the language id.
     *
     * @return integer The language id.
     */
    public function getLanguageId() { return $this->get('languageId'); }

}

?>