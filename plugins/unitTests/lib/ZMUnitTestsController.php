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
 * Unit testing controller.
 *
 * @author DerManoMann
 * @package org.zenmagick.plugins.unitTests
 */
class ZMUnitTestsController extends ZMController {
    private $plugin_;


    /**
     * Create new instance.
     */
    function __construct() {
        parent::__construct();
        $this->plugin_ = ZMPlugins::instance()->getPluginForId('unitTests');
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

        // XXX: for adminusers testcase...
        ZMLoader::instance()->addPath(ZMFileUtils::mkPath(array(ZMRuntime::getInstallationPath(), 'apps', 'admin', 'lib', 'services')));
        ZMLoader::instance()->addPath(ZMFileUtils::mkPath(array(ZMRuntime::getInstallationPath(), 'apps', 'admin', 'lib', 'model')));

        // add tests folder to class path
        $testsLoader = new ZMLoader();
        $testBaseDir = $this->plugin_->getPluginDirectory().'tests';
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
                $dir = dirname($dir);
                if ($dir != $testBaseDir) {
                    $group = basename($dir);
                }
            } while ($dir != $testBaseDir);
            if (!array_key_exists($group, $allTests)) {
                $allTests[$group] = array();
            }
            $allTests[$group][] = $class;
        }

        // add plugins/tests folder of all available plugins to loader
        $pluginLoader = new ZMLoader();
        foreach (ZMPlugins::instance()->getAllPlugins() as $plugin) {
            if ($plugin instanceof ZMUnitTestsPlugin) {
                continue;
            }
            $ptests = $plugin->getPluginDirectory().'tests' . DIRECTORY_SEPARATOR;
            if (is_dir($ptests)) {
                $pluginLoader->addPath($ptests);
                // scan for tests
                foreach (ZMFileUtils::findIncludes($ptests) as $file) {
                    $name = basename($file);
                    if (ZMLangUtils::startsWith($name, 'Test')) {
                        $name = str_replace('.php', '', $name);
                        $this->plugin_->addTest($name);
                    }
                }
            }
        }
        ZMLoader::instance()->setParent($pluginLoader);

        // merge in all custom registered tests
        $allTests = array_merge($allTests, $this->plugin_->getTests());
        ksort($allTests);

        // create instances rather than just class names
        foreach ($allTests as $group => $tests) {
            foreach ($tests as $key => $clazz) {
                if (null != ($test = ZMBeanUtils::getBean($clazz))) {
                    $allTests[$group][$key] = $test;
                } else {
                    ZMMessages::instance()->warn('could not create instance of '.$clazz);
                    unset($allTests[$group][$key]);
                }
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

        $hideErrors = ZMLangUtils::asBoolean($request->getParameter('hideErrors', false));
        $context['hideErrors'] = $hideErrors;

        $context['all_selected_testCases'] = array_flip($testCases);
        $context['all_selected_tests'] = array_flip($tests);
        $context['all_results'] = array();
        if (0 < count($testCases)) {
            // prepare selected tests
            $suite = new TestSuite('ZenMagick Tests');
            foreach ($testCases as $name) {
                $testCase = ZMBeanUtils::getBean($name);
                if ($testCase instanceof SimpleTestCase) {
                    $suite->add($name);
                }
            }

            // allow for more time to run tests
            set_time_limit(300);

            // run tests
            $reporter = new ZMHtmlReporter($hideErrors);
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

        return $this->findView(null, $context);
    }

}
