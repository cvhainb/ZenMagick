<?php

/**
 * Test url comparison.
 *
 * @package org.zenmagick.plugins.zm_tests.tests
 * @author DerManoMann
 * @version $Id$
 */
class TestCompareUrls extends UnitTestCase {

    /**
     * Test current.
     */
    public function testCurrent() {
        $this->assertTrue(ZMTools::compareStoreUrl('index.php?main_page=tests&abc=def'));
    }

    /**
     * Test two.
     */
    public function testTwo() {
        $this->assertTrue(ZMTools::compareStoreUrl('index.php?main_page=tests&abc=def', 'index.php?main_page=tests'));
        $this->assertFalse(ZMTools::compareStoreUrl('index.php?main_page=page&id=1', 'index.php?main_page=page'));
        $this->assertTrue(ZMTools::compareStoreUrl('index.php?main_page=static&cat=foo', 'http://localhost/index.php?main_page=static&cat=foo'));
    }

    /**
     * Test incomplete.
     */
    public function testIncomplete() {
        $this->assertTrue(ZMTools::compareStoreUrl('index.php', 'index.php?main_page=index'));
        $this->assertTrue(ZMTools::compareStoreUrl('index.php?main_page=', 'index.php?main_page=index'));
    }

}

?>