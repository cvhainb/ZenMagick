<?php

/**
 * Test product service.
 *
 * @package org.zenmagick.plugins.zm_tests.tests
 * @author DerManoMann
 * @version $Id$
 */
class TestZMProducts extends UnitTestCase {

    /**
     * Set up.
     */
    public function setUp() {
        ZMSettings::set('dbProvider', 'ZMCreoleDatabase');
        //ZMSettings::set('dbProvider', 'ZMZenCartDatabase');
    }


    /**
     * Test create product.
     */
    public function testCreateProduct() {
        //TODO
    }

    /**
     * Test update product.
     */
    public function testUpdateProduct() {
        $product = ZMProducts::instance()->getProductForId(2);
        $this->assertNotNull($product);
        $product->setName($product->getName().'@@@');
        ZMProducts::instance()->updateProduct($product);
        $reloaded = ZMProducts::instance()->getProductForId($product->getId());
        foreach (array_keys(ZMDbTableMapper::instance()->getMapping(array(TABLE_PRODUCTS, TABLE_PRODUCTS_DESCRIPTION))) as $key) {
            $prefixList = array('get', 'is', 'has');
            $done = false;
            foreach ($prefixList as $prefix) {
                $getter = $prefix . ucwords($key);
                if (method_exists($product, $getter)) {
                    $this->assertEqual($product->$getter(), $reloaded->$getter(), '%s getter='.$getter);
                    $done = true;
                    break;
                }
            }
            if (!$done) {
                $this->assertEqual($product->get($key), $reloaded->get($key), '%s key='.$key);
            }
        }
        // revert name change
        $product->setName(str_replace('@@@', '', $product->getName()));
        ZMProducts::instance()->updateProduct($product);
    }

    /**
     * Test product type settings.
     */
    public function testProductTypeSettings() {
        $fieldData = array(
            'starting_at' => true,
            'reviews' => true,
            'tell_a_friend' => true
        );
        $product = ZMProducts::instance()->getProductForId(2);
        foreach ($fieldData as $field => $value) {
            $this->assertEqual($value, $product->getTypeSetting($field), '%s field='.$field);
        }
    }

    /**
     * Test featured products on homepage.
     */
    public function testFeaturedProductsHome() {
        $featuredIds = array_flip(array(34, 40, 12, 27, 26, 168, 169, 171, 172));
        $products = ZMProducts::instance()->getFeaturedProducts();
        $this->assertEqual(9, count($products));
        foreach ($products as $product) {
            $this->assertTrue(array_key_exists($product->getId(), $featuredIds));
        }
    }

    /**
     * Test featured products on category page.
     */
    public function testFeaturedProductsCategory() {
        $featuredIds = array_flip(array(12));
        $products = ZMProducts::instance()->getFeaturedProducts(3, 4, true);
        $this->assertEqual(1, count($products));
        foreach ($products as $product) {
            $this->assertTrue(array_key_exists($product->getId(), $featuredIds));
        }
    }

    /**
     * Test new products on home page.
     */
    public function testNewProductsHome() {
        $products = ZMProducts::instance()->getNewProducts();
        $this->assertEqual(125, count($products));
    }

    /**
     * Test new products on category page.
     */
    public function testNewProductsCategory() {
        $featuredIds = array_flip(array(1, 2));
        $products = ZMProducts::instance()->getNewProducts(4, 0, '0');
        $this->assertEqual(2, count($products));
        foreach ($products as $product) {
            $this->assertTrue(array_key_exists($product->getId(), $featuredIds));
        }
    }

    /**
     * Test bestseller products on home page.
     */
    public function testBestsellerProductsHome() {
        $products = ZMProducts::instance()->getBestSellers(null, 999);
        $this->assertEqual(51, count($products));
    }

    /**
     * Test bestseller products on category page.
     */
    public function testBestsellerProductsCategory() {
        $featuredIds = array_flip(array(1, 2));
        $products = ZMProducts::instance()->getBestSellers(4, 999);
        $this->assertEqual(2, count($products));
        foreach ($products as $product) {
            $this->assertTrue(array_key_exists($product->getId(), $featuredIds));
        }
    }

}

?>