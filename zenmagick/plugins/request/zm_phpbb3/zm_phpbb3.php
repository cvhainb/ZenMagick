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


define('ZM_PHPBB3_ROOT', ZMSettings::get('plugins.zm_pbpbb3.root', DIR_WS_PHPBB));


/**
 * Plugin to enable phpBB3 support in ZenMagick.
 *
 * @package org.zenmagick.plugins.zm_phpbb3
 * @author DerManoMann
 * @version $Id$
 */
class zm_phpbb3 extends ZMPlugin {
    private $page_;
    private $prePostAccount_;
    private $phpBB3_;


    /**
     * Create new instance.
     */
    function __construct() {
        parent::__construct('phpBB3', 'phpBB3 for ZenMagick');
        $this->setLoaderPolicy(ZMPlugin::LP_FOLDER);
        $this->page_ = '';
        $this->prePostAccount_ = null;
        $this->phpBB3_ = null;
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }

    /**
     * Install this plugin.
     */
    function install() {
        parent::install();

        // warning: screwed logic!
        $this->addConfigValue('Nickname policy', 'requireNickname', true, 'Leave nickname as optional (will skip automatic phpBB registration)', 
            "zen_cfg_select_drop_down(array(array('id'=>'1', 'text'=>'No'), array('id'=>'0', 'text'=>'Yes')), ");
    }


    /**
     * Get the phpBB3 adapter.
     */
    protected function getAdapter() {
        if (null == $this->phpBB3_) {
            $this->phpBB3_ = ZMLoader::make('ZMPhpBB3');
        }

        return $this->phpBB3_;
    }

    /**
     * Init this plugin.
     */
    function init() {
        parent::init();
        $this->page_ = ZMRequest::getPageName();
        $this->prePostAccount_ = ZMRequest::getAccount();

        // enable nickname field
        ZMSettings::set('isAccountNickname', true);

        if ('create_account' == $this->page_) {
            $phpBB = $this->getAdapter();
            // add custom validation rules
            $rules = array(
                array("WrapperRule", 'nick', 'The entered nickname is already taken (phpBB3).', array($phpBB, 'vDuplicateNickname')),
                array("WrapperRule", 'email_address', 'The entered email address is already taken (phpBB3).', array($phpBB, 'vDuplicateEmail'))
            );
            // optionally, make nickname required
            if ($this->get('requireNickname')) {
                $rules[] = array('RequiredRule', 'nick', 'Please enter a nickname.');
            }
            ZMValidator::instance()->addRules('create_account', $rules);
            $this->zcoSubscribe();
        } else if ('account_password' == $this->page_) {
            $this->zcoSubscribe();
        } else if ('account_edit' == $this->page_) {
            $phpBB = $this->getAdapter();
            $rules = array(
                array("WrapperRule", 'email_address', 'The entered email address is already taken (phpBB3).', array($phpBB, 'vDuplicateChangedEmail'))
            );
            ZMValidator::instance()->addRules('edit_account', $rules);
            $this->zcoSubscribe();
        }

        // register tests
        if (null != ($tests = ZMPlugins::instance()->getPluginForId('zm_tests'))) {
            // add class path only now to avoid errors due to missing ZMTestCase
            ZMLoader::instance()->addPath($this->getPluginDir().'tests/');
            $tests->addTest('TestZMPhpBB3');
        }
    }

    /**
     * Event callback for controller processing.
     *
     * <p>Here the additional processing is done by checking the result view id. As per convention,
     * ZenMagick controller will use the viewId 'success' if POST processing was successful.</p>
     *
     * @param array args Optional parameter ('view' => $view).
     */
    function onZMControllerProcessEnd($args) {
        if ('POST' == ZMRequest::getMethod()) {
            $view = $args['view'];

            if ('create_account' == $this->page_ && 'success' == $view->getMappingId()) {
                // account created
                $email = ZMRequest::getParameter('email_address');
                $password = ZMRequest::getParameter('password');
                $nickName = ZMRequest::getParameter('nick');
                //TODO: $phpBB->createAccount($nickName, $password, $email);
            }

            if ('account_password' == $this->page_ && 'success' == $view->getMappingId()) {
                $account = ZMRequest::getAccount();
                if (null != $account && !ZMTools::isEmpty($account->getNickName())) {
                    $newPassword = ZMRequest::getParameter('password_new');
                    // TODO: $phpBB->changePassword($account->getNickName(), $newPassword);
                }
            }
        }
    }

}


?>
