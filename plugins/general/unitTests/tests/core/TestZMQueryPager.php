<?php
/*
 * ZenMagick - Smart e-commerce
 * Copyright (C) 2006-2010 zenmagick.org
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
 * Test query pager.
 *
 * @package org.zenmagick.plugins.unitTests.tests
 * @author DerManoMann
 */
class TestZMQueryPager extends ZMTestCase {

    /**
     * Test simple.
     */
    public function testSimple() {
        $queryPager = new ZMQueryPager(new ZMQueryDetails(Runtime::getDatabase(), 'select * from '.TABLE_PRODUCTS, array(), TABLE_PRODUCTS, 'Product'));
        $queryPager->getResults(2, 13);
    }

    /**
     * Test sql aware.
     *
     * @todo create reliable test data
     */
    public function testSQLAware() {
            $resultList = new ZMResultList();
            $resultSource = ZMLoader::make('ObjectResultSource', 'ZMOrder', ZMOrders::instance(), 'getAllOrders', array(1));
            $resultList->setResultSource($resultSource);
            $sorter = ZMLoader::make('OrderSorter');
            $sorter->setSortId('date');
            $sorter->setDescending(true);
            $queryDetails = $sorter->getQueryDetails();
            $resultList->addSorter($sorter);
            $filter = ZMLoader::make('OrderStatusIdFilter');
            $resultList->addFilter($filter);
            $resultList->setPageNumber(3);
            $orders = $resultList->getResults();
            echo 'is final source: ' . $resultSource->isFinal()."<BR>";
            echo "# of pages: " . $resultList->getNumberOfPages()."<BR>";
            foreach ($orders as $order) {
                echo $order->getId() . ' ' . $order->getOrderDate()."<BR>";
            }
    }

}