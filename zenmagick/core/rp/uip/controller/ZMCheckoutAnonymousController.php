<?php
/*
 * ZenMagick - Extensions for zen-cart
 * Copyright (C) 2006,2007 ZenMagick
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
 * Request controller for anonymous checkout.
 *
 * @author mano
 * @package net.radebatz.zenmagick.rp.uip.controller
 * @version $Id$
 */
class ZMCheckoutAnonymousController extends ZMController {

    /**
     * Default c'tor.
     */
    function ZMCheckoutAnonymousController() {
        parent::__construct();
    }

    /**
     * Default c'tor.
     */
    function __construct() {
        $this->ZMCheckoutAnonymousController();
    }

    /**
     * Default d'tor.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Process a HTTP POST request.
     * 
     * @return ZMView A <code>ZMView</code> that handles presentation or <code>null</code>
     * if the controller generates the contents itself.
     */
    function processPost() {
    global $zm_request, $zm_accounts, $zm_messages;

        // our session
        $session = new ZMSession();

        if (!$session->isValid()) {
            return $this->findView('cookie_usage');
        }

        // create anonymous account
        $account = $this->create("Account");
        if (!$session->isGuest()) {
            // already logged in
            return $this->findView('account');
        }

        if (!$this->validate('login')) {
            return $this->findView();
        }

        $emailAddress = $zm_request->getParameter('email_address');
        $account = $zm_accounts->getAccountForEmailAddress($emailAddress);
        if (null === $account) {
            $zm_messages->error('Sorry, there is no match for that email address and/or password.');
            return $this->findView();
        }

        $password = $zm_request->getParameter('password');
        if (!zm_validate_password($password, $account->getPassword())) {
            $zm_messages->error('Sorry, there is no match for that email address and/or password.');
            return $this->findView();
        }

        // update session with valid account
        $session->recreate();
        $session->setAccount($account);

        // update login stats
        $zm_accounts->updateAccountLoginStats($account->getId());

        // restore cart contents
        $session->restoreCart();

        $followUpUrl = $session->getLoginFollowUp();
        if (null != $followUpUrl) {
            zm_redirect($followUpUrl);
        }

        return $this->findView('success');
    }

}

?>
