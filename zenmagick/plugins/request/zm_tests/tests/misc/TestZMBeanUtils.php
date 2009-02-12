<?php

/**
 * Test ZMBeanUtils.
 *
 * @package org.zenmagick.plugins.zm_tests.tests
 * @author DerManoMann
 * @version $Id$
 */
class TestZMBeanUtils extends ZMTestCase {

    /**
     * Test obj2map.
     */
    public function testObj2map() {
        // get all properties
        $expectAll = array('foo' => 'bar', 'doh' => 'nut', 'propertyNames' => array('foo', 'doh'), 'attachedMethods' => array());
        $obj = ZMLoader::make('ZMObject');
        $obj->set('foo', 'bar');
        $obj->set('doh', 'nut');
        $map = ZMBeanUtils::obj2map($obj);
        $this->assertEqual($expectAll, $map);

        // get subset of properties
        $expectSpecific = array('foo' => 'bar', 'doh' => 'nut');
        $map = ZMBeanUtils::obj2map($obj, array_keys($expectSpecific));
        $this->assertEqual($expectSpecific, $map);
    }

    /**
     * Test setAll.
     */
    public function testSetAll() {
        $data = array('foo' => 'bar', 'doh' => 'nut');

        // set all
        $expectAll = array('foo' => 'bar', 'doh' => 'nut', 'propertyNames' => array('foo', 'doh'), 'attachedMethods' => array());
        $obj = ZMLoader::make('ZMObject');
        $map = ZMBeanUtils::setAll($obj, $data);
        $this->assertEqual('bar',$obj->getFoo());
        $map = ZMBeanUtils::obj2map($obj);
        $this->assertEqual($expectAll, $map);

        // set some
        $expectSome = array('foo' => 'bar', 'propertyNames' => array('foo'), 'attachedMethods' => array());
        $obj = ZMLoader::make('ZMObject');
        $map = ZMBeanUtils::setAll($obj, $data, array('foo'));
        $this->assertEqual('bar',$obj->getFoo());
        $map = ZMBeanUtils::obj2map($obj);
        $this->assertEqual($expectSome, $map);
    }

    /**
     * Test map2obj.
     */
    public function testMap2obj() {
        $data = array('foo' => 'bar', 'doh' => 'nut');

        // test all
        $expectAll = array('foo' => 'bar', 'doh' => 'nut', 'propertyNames' => array('foo', 'doh'), 'attachedMethods' => array());
        $obj = ZMBeanUtils::map2obj('ZMObject', $data);
        $map = ZMBeanUtils::obj2map($obj);
        $this->assertEqual($expectAll, $map);
        $this->assertTrue($obj instanceof ZMObject);

        // test some
        $expectSome = array('foo' => 'bar', 'propertyNames' => array('foo'), 'attachedMethods' => array());
        $obj = ZMBeanUtils::map2obj('ZMObject', $data, array('foo'));
        $map = ZMBeanUtils::obj2map($obj);
        $this->assertEqual($expectSome, $map);
        $this->assertTrue($obj instanceof ZMObject);
    }

    /**
     * Test getBean.
     */
    public function testGetBean() {
        $expect = array('foo' => 'bar', 'doh' => 'nut', 'propertyNames' => array('foo', 'doh'), 'attachedMethods' => array());
        $definition = 'ZMObject#foo=bar&doh=nut';
        $obj = ZMBeanUtils::getBean($definition);
        $map = ZMBeanUtils::obj2map($obj);
        $this->assertEqual($expect, $map);
        $this->assertTrue($obj instanceof ZMObject);

        // test empty properties
        $expect = array('propertyNames' => array(), 'attachedMethods' => array());
        $definition = 'ZMObject';
        $obj = ZMBeanUtils::getBean($definition);
        $map = ZMBeanUtils::obj2map($obj);
        $this->assertEqual($expect, $map);
        $this->assertTrue($obj instanceof ZMObject);
    }

}

?>