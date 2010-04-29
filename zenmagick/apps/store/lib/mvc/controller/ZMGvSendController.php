<?php
/*
 * ZenMagick - Extensions for zen-cart
 * Copyright (C) 2006-2010 zenmagick.org
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
 * Request controller for gv send page.
 *
 * @author DerManoMann
 * @package org.zenmagick.store.mvc.controller
 * @version $Id$
 */
class ZMGvSendController extends ZMController {

    /**
     * Create new instance.
     */
    function __construct() {
        parent::__construct();
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
    public function preProcess($request) {
        $request->getToolbox()->crumbtrail->addCrumb("Account", $request->url(FILENAME_ACCOUNT, '', true));
        $request->getToolbox()->crumbtrail->addCrumb($request->getToolbox()->utils->getTitle());
    }

    /**
     * {@inheritDoc}
     */
    public function processGet($request) {
        return $this->findView(null, array('currentAccount' => $request->getAccount()));
    }

    /**
     * {@inheritDoc}
     */
    public function processPost($request) {
        $gvReceiver = $this->getFormData($request);

        // back from confirmation to edit or not valid
        if (null != $request->getParameter('edit')) {
            return $this->findView();
        }

        $data = array();
        $data['currentAccount'] = $request->getAccount();
        // to fake the email content display
        $data['currentCoupon'] = ZMLoader::make("Coupon", 0, zm_l10n_get('THE_COUPON_CODE'));

        return $this->findView('success', $data);
    }

}