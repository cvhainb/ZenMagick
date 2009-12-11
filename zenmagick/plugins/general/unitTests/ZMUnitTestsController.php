<?php
/*
 * ZenMagick - Extensions for zen-cart
 * Copyright (C) 2006-2009 zenmagick.org
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
 * Unit testing controller.
 *
 * @author DerManoMann
 * @package org.zenmagick.plugins.unitTests
 * @version $Id: TestsController.php 2458 2009-07-20 05:09:51Z dermanomann $
 */
class ZMUnitTestsController extends ZMController {
    private $plugin;


    /**
     * Create new instance.
     */
    function __construct() {
        $this->plugin = ZMPlugins::instance()->getPluginForId('unitTests');
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
    public function processGet($request) {
        // show test view only
        Runtime::getTheme()->getThemeInfo()->setLayout('tests', null);

        $testsLoader = ZMLoader::make("Loader");
        $testBaseDir = $this->plugin->getPluginDirectory().'tests/';
        $testsLoader->addPath($testBaseDir);
        // test data  is lower case
        $testsLoader->loadStatic();

        ZMLoader::instance()->setParent($testsLoader);

        $tests = array();
        foreach ($testsLoader->getClassPath() as $class => $file) {
            if (ZMLangUtils::startsWith($class, 'Test')) {
                $tests[$class] = $file;
            }
        }

        // group tests
        $allTests = array();
        foreach ($tests as $class => $file) {
            $dir = $file;
            $group = UNIT_TESTS_GROUP_DEFAULT;
            do {
                $dir = dirname($dir).'/';
                if ($dir != $testBaseDir) {
                    $group = basename($dir);
                }
            } while ($dir != $testBaseDir);
            if (!array_key_exists($group, $allTests)) {
                $allTests[$group] = array();
            }
            $allTests[$group][] = $class;
        }

        // merge in all custom registered tests
        $allTests = array_merge($allTests, $this->plugin->getTests());
        ksort($allTests);

        // make available
        ZMLoader::resolve('ZMTestCase');
        ZMLoader::resolve('ZMWebTestCase');

        // create instances rather than just class names
        foreach ($allTests as $group => $tests) {
            foreach ($tests as $key => $clazz) {
                $allTests[$group][$key] = ZMLoader::make($clazz);
            }
        }

        $context = array();

        $context['all_tests'] = $allTests;

        $testCases = $request->getParameter('testCases', array());
        $tests = $request->getParameter('tests', array());
        // build testCases from tests as there might be tests selected, but not the testCase
        $testCaseMap = array();
        foreach ($tests as $id) {
            // XXX: this should not be handled by the reporter
            list($testCase, $test) = explode('-', $id);
            $testCaseMap[$testCase] = $testCase;
        }
        $testCases = array();
        foreach ($testCaseMap as $testCase) {
            $testCases[] = $testCase;
        }
        
        $context['all_selected_testCases'] = array_flip($testCases);
        $context['all_selected_tests'] = array_flip($tests);
        if (0 < count($testCases)) {
            // prepare selected tests
            $suite = new TestSuite('ZenMagick Tests');
            foreach ($testCases as $name) {
                $testCase = ZMLoader::make($name);
                if ($testCase instanceof SimpleTestCase) {
                    $suite->addTestClass($name);
                }
            }

            // allow for more time to run tests
            set_time_limit(300);

            // run tests
            $reporter = new ZMHtmlReporter();
            // enable all selected tests
            foreach ($tests as $id) {
                // XXX: this should not be handled by the reporter
                list($testCase, $test) = explode('-', $id);
                $reporter->enableTest($testCase, $test);
            }
            ob_start();
            $suite->run($reporter);
            $report = ob_get_clean();
            $context['all_results'] = $reporter->getResults();
        } else {
            $report = '';
        }

        $context['html_report'] = $report;

        return $this->findView('tests', $context);
    }

}

?>