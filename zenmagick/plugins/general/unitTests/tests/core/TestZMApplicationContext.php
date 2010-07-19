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
 * Test ZMApplicationContext.
 *
 * @package org.zenmagick.plugins.unitTests.tests
 * @author DerManoMann
 */
class TestZMApplicationContext extends ZMTestCase {

    /**
     * Test getContext.
     */
    public function testGetContext() {
        $context = ZMRuntime::getContext();
        $this->assertNotNull($context);
    }

    /**
     * Test load.
     */
    public function testLoad() {
        $context = ZMRuntime::getContext();
        $context->load(file_get_contents(ZMFileUtils::mkPath($this->getTestsBaseDirectory(), 'core', 'data', 'context.yaml')));
        $this->assertEqual('ZMObject#name=yoo', $context->getDefinition('Yoo'));
        $obj = ZMBeanUtils::getBean('Yoo');
        if ($this->assertNotNull($obj)) {
            $this->assertEqual('yoo', $obj->getName());
        }
    }

}