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
 * Advanced search result controller.
 *
 * @author DerManoMann
 * @package org.zenmagick.rp.uip.controller
 * @version $Id$
 * @deprecated Use the new SearchController instead
 */
class ZMAdvancedSearchResultController extends ZMController {

    /**
     * Create new instance.
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Process a HTTP GET request.
     * 
     * @return ZMView A <code>ZMView</code> that handles presentation or <code>null</code>
     * if the controller generates the contents itself.
     */
    function processGet($request) {
    global $listing_sql;

        ZMLogging::instance()->log('search SQL: '.$listing_sql, ZMLogging::TRACE);

        // zc search sql
        $request->getCrumbtrail()->addCrumb("Advanced Search", $request->getToolbox()->net->url(FILENAME_ADVANCED_SEARCH, null, false, false));
        $request->getCrumbtrail()->addCrumb("Results");

        $resultList = ZMLoader::make("ResultList");
        $resultSource = ZMLoader::make("ObjectResultSource", 'Product', ZMProducts::instance(), "getProductsForSQL", array($listing_sql));
        $resultList->setResultSource($resultSource);
        $sorter = ZMLoader::make("ProductSorter");
        $resultList->addSorter($sorter);
        $resultList->setPageNumber($request->getPageIndex());

        return $this->findView(null, array(('zm_resultList' => $resultList));
    }

}

?>
