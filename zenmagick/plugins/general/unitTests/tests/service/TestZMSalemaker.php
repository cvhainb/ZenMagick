<?php

/**
 * Test salemaker service.
 *
 * @package org.zenmagick.plugins.unitTests.tests
 * @author DerManoMann
 * @version $Id: TestZMSalemaker.php 2610 2009-11-20 02:45:25Z dermanomann $
 */
class TestZMSalemaker extends ZMTestCase {

    /**
     * Test getSaleDiscountTypeInfo.
     */
    public function testGetSaleDiscountTypeInfo() {
        foreach (ZMProducts::instance()->getAllProducts(false) as $product) {
            $productId = $product->getId();
            $info = ZMSalemaker::instance()->getSaleDiscountTypeInfo($productId);
            $type = zen_get_products_sale_discount_type($productId);
            $amount = zen_get_products_sale_discount_type($productId, false, 'amount');
            if (!$this->assertEqual(array('type'=>$type, 'amount'=>$amount), $info)) {
                echo $productId . $product->getName();
                break;
            }
        }
    }

}

?>