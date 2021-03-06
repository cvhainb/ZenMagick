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
 * Plugin to enable vBulletin support in ZenMagick.
 *
 * @package org.zenmagick.plugins.vbulletin
 * @author DerManoMann
 */
class ZMVBulletinPlugin extends Plugin implements ZMRequestHandler {
    private $page_;
    private $prePostAccount_;
    private $adapter_;


    /**
     * Create new instance.
     */
    function __construct() {
        parent::__construct('vBulletin', 'vBulletin for ZenMagick');
        $this->page_ = '';
        $this->prePostAccount_ = null;
        $this->adapter_ = null;
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }

    /**
     * {@inheritDoc}
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
        if (null == $this->adapter_) {
            $this->adapter_ = ZMBeanUtils::getBean('VBulletinAdapter');
        }

        return $this->adapter_;
    }

    /**
     * {@inheritDoc}
     */
    public function initRequest($request) {
        $this->page_ = $request->getRequestId();
        $this->prePostAccount_ = $request->getAccount();

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
     * Event handler for login.
     *
     * @param array args Optional parameter.
     */
    public function onZMLoginSuccess($args=array()) {
        $request = $args['request'];
        $account = $args['account'];
        // check if nickname set and no matching forum user 
        if (!ZMLangUtils::isEmpty($account->getNickName())) {
            if (null == $this->getAdapter()->getAccountForNickName($account->getNickName())) {
                // no vBulletin user found, so create one now!
                $password = $request->getParameter('password');
                $this->getAdapter()->createAccount($account, $password);
            }
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
    public function onZMControllerProcessEnd($args) {
        $request = $args['request'];
        if ('POST' == $request->getMethod()) {
            $view = $args['view'];
            if ('account_edit' == $this->page_ && 'success' == $view->getMappingId()) {
                $account = ZMAccounts::instance()->getAccountForId($request->getAccountId());
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
