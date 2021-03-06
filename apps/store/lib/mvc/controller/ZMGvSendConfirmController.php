<?php
/*
 * ZenMagick - Smart e-commerce
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
 * Request controller for gv confirmation page.
 *
 * @author DerManoMann
 * @package zenmagick.store.sf.mvc.controller
 */
class ZMGvSendConfirmController extends ZMController {

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
        $request->getToolbox()->crumbtrail->addCrumb("Account", $request->url('account', '', true));
        $request->getToolbox()->crumbtrail->addCrumb($request->getToolbox()->utils->getTitle());
    }

    /**
     * {@inheritDoc}
     */
    public function processGet($request) {
        $data = array();
        $data['currentAccount'] = $request->getAccount();
        $data['currentCoupon'] = ZMLoader::make("Coupon", 0, _zm('THE_COUPON_CODE'));
        return $this->findView(null, $data);
    }

    /**
     * {@inheritDoc}
     */
    protected function validateFormData($request, $formBean) {
        // need specific view to go back to in case of validation errors
        $result = parent::validateFormData($request, $formBean);
        if (null != $result) {
            return $this->findView('edit');
        }
        return null;
    }

    /**
     * Process a HTTP POST request.
     * 
     * @return ZMView A <code>ZMView</code> that handles presentation or <code>null</code>
     * if the controller generates the contents itself.
     */
    public function processPost($request) {
        if (null != $request->getParameter('edit')) {
            return $this->findView('edit');
        }

        // the form data
        $gvReceiver = $this->getFormData($request);
        // the sender account
        $account = $request->getAccount();
        // current balance
        $balance = $account->getVoucherBalance();
        // coupon amount
        $amount = $gvReceiver->getAmount(); 

        $currentCurrencyCode = $request->getCurrencyCode();
        if (ZMSettings::get('defaultCurrency') != $currentCurrencyCode) {
            // need to convert amount to default currency as GV values are in default currency
            $currency = ZMCurrencies::instance()->getCurrencyForCode($currentCurrencyCode);
            $amount = $currency->convertFrom($amount);
        }

        // update balance
        $newBalance = $balance - $amount;
        $coupons = ZMCoupons::instance();
        $coupons->setVoucherBalanceForAccountId($account->getId(), $newBalance);

        // create the new voucher
        $couponCode = $coupons->createCouponCode($account->getEmail());
        $coupon = $coupons->createCoupon($couponCode, $amount, ZMCoupons::TYPPE_GV);

        // create coupon tracker
        $coupons->createCouponTracker($coupon, $account, $gvReceiver);

        // create gv_send email
        $context = array('currentAccount' => $account, 'gvReceiver' => $gvReceiver, 'currentCoupon' => $coupon, 'office_only_html' => '', 'office_only_text' => '');
        zm_mail(sprintf(_zm("A gift from %s"), $account->getFullName()), 'gv_send', $context, $gvReceiver->getEmail());
        if (ZMSettings::get('isEmailAdminGvSend')) {
            // store copy
            $session = $request->getSession();
            $context = $request->getToolbox()->macro->officeOnlyEmailFooter($account->getFullName(), $account->getEmail(), $session);
            $context['currentAccount'] = $account;
            $context['gvReceiver'] = $gvReceiver;
            $context['currentCoupon'] = $coupon;
            zm_mail(sprintf(_zm("[GIFT CERTIFICATE] A gift from %s"), $account->getFullName()), 'gv_send', $context, ZMSettings::get('emailAdminGvSend'));
        }

        ZMMessages::instance()->success(_zm("Gift Certificate successfully send!"));

        return $this->findView('success');
    }

}
