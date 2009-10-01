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
 * Plugin to enable vBulletin support in ZenMagick.
 *
 * @package org.zenmagick.plugins.zm_vbulletin
 * @author DerManoMann
 * @version $Id$
 */
class zm_vbulletin extends Plugin {
    private $page_;
    private $prePostAccount_;
    private $vBulletin_;


    /**
     * Create new instance.
     */
    function __construct() {
        parent::__construct('vBulletin', 'vBulletin for ZenMagick');
        $this->setLoaderPolicy(ZMPlugin::LP_FOLDER);
        $this->page_ = '';
        $this->prePostAccount_ = null;
        $this->vBulletin_ = null;
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
    public function install() {
        parent::install();

        $this->addConfigValue('vBulletin Installation Folder', 'vBulletinDir', '', 'Path to your vBulletin installation',
              'widget@TextFormWidget#name=vBulletinDir&default=&size=24&maxlength=255');
        $this->addConfigValue('Nickname policy', 'requireNickname', true, 'Make nickname mandatory (If disabled, automatic vBulletin registration will be skipped)', 
            'widget@BooleanFormWidget#name=requireNickname&default=true&label=Require nickname');
    }


    /**
     * Get the vBulletin adapter.
     */
    protected function getAdapter() {
        if (null == $this->vBulletin_) {
            $this->vBulletin_ = ZMLoader::make('VBulletinAdapter');
        }

        return $this->vBulletin_;
    }

    /**
     * Init this plugin.
     */
    public function init() {
        parent::init();
        $this->page_ = ZMRequest::instance()->getRequestId();
        $this->prePostAccount_ = ZMRequest::instance()->getAccount();

        // main define to get at things
        $vBulletinDir = $this->get('vBulletinDir');
        if (empty($vBulletinDir)) {
            $vBulletinDir = ZMSettings::get('plugins.zm_vbulletin.root');
        }
        define('ZM_VBULLETIN_ROOT', $vBulletinDir);

        // enable nick name field
        ZMSettings::set('isAccountNickname', true);

        // using events
        ZMEvents::instance()->attach($this);

        // register tests
        if (null != ($tests = ZMPlugins::instance()->getPluginForId('zm_tests'))) {
            // add class path only now to avoid errors due to missing ZMTestCase
            ZMLoader::instance()->addPath($this->getPluginDirectory().'tests/');
            $tests->addTest('TestZMVBulletin');
        }
    }

    /**
     * Init done callback.
     *
     * <p>Setup additional validation rules; this is done here to avoid getting in the way of
     * custom global/theme validation rule setups.</p>
     *
     * @param array args Optional parameter.
     */
    public function onZMInitDone($args=null) {
        if ('create_account' == $this->page_) {
            $vBulletin = $this->getAdapter();
            // add custom validation rules
            $rules = array(
                array("WrapperRule", 'nickName', 'The entered nick name is already taken (vBulletin).', array($vBulletin, 'vDuplicateNickname')),
                array("WrapperRule", 'email', 'The entered email address is already taken (vBulletin).', array($vBulletin, 'vDuplicateEmail'))
            );
            // optionally, make nick name required
            if (ZMLangUtils::asBoolean($this->get('requireNickname'))) {
                $rules[] = array('RequiredRule', 'nickName', 'Please enter a nick name.');
            }
            ZMValidator::instance()->addRules('registration', $rules);
        } else if ('account_password' == $this->page_) {
            ZMEvents::instance()->attach($this);
        } else if ('account_edit' == $this->page_) {
            $vBulletin = $this->getAdapter();
            $rules = array(
                array("WrapperRule", 'nickName', 'The entered nick name is already taken (vBulletin).', array($vBulletin, 'vDuplicateChangedNickname')),
                array("WrapperRule", 'email', 'The entered email address is already taken (vBulletin).', array($vBulletin, 'vDuplicateChangedEmail'))
            );
            // optionally, make nick name required
            if (ZMLangUtils::asBoolean($this->get('requireNickname'))) {
                $rules[] = array('RequiredRule', 'nickName', 'Please enter a nick name.');
            }
            ZMValidator::instance()->addRules('account', $rules);
            ZMEvents::instance()->attach($this);
        }
    }

    /**
     * Account created event callback.
     *
     * <p>Here the additional processing is done by checking the result view id. As per convention,
     * ZenMagick controller will use the viewId 'success' if POST processing was successful.</p>
     *
     * @param array args Optional parameter.
     */
    public function onZMCreateAccount($args) {
        $account = $args['account'];
        if (!ZMLangUtils::isEmpty($account->getNickName())) {
            $password = $args['clearPassword'];
            $this->getAdapter()->createAccount($account, $password);
        }
    }

    /**
     * Event callback for controller processing.
     *
     * <p>Here the additional processing is done by checking the result view id. As per convention,
     * ZenMagick controller will use the viewId 'success' if POST processing was successful.</p>
     *
     * @param array args Optional parameter.
     */
    public function onZMPasswordChanged($args) {
        $account = $args['account'];
        if (!ZMLangUtils::isEmpty($account->getNickName())) {
            $password = $args['clearPassword'];
            $this->getAdapter()->updateAccount($account->getNickName(), $password, $account->getEmail());
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
        if ('POST' == ZMRequest::instance()->getMethod()) {
            $view = $args['view'];
            if ('account_edit' == $this->page_ && 'success' == $view->getMappingId()) {
                $account = ZMAccounts::instance()->getAccountForId(ZMRequest::instance()->getAccountId());
                $vbAccount = $this->getAdapter()->getAccountForNickName($account->getNickName());
                if (null != $account && !ZMLangUtils::isEmpty($account->getNickName())) {
                    if (null != $vbAccount) {
                        $this->getAdapter()->updateAccount($account->getNickName(), null, $account->getEmail());
                    } else {
                        // TODO: create
                    }
                }
            }
        }
    }

}


?>
