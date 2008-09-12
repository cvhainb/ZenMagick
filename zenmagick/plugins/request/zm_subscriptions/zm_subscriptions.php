<?php
/*
 * ZenMagick - Extensions for zen-cart
 * Copyright (C) 2006-2008 ZenMagick
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

define('ZM_FILENAME_SUBSCRIPTION_CANCEL', 'cancel_subscription');


/**
 * Subscriptions.
 *
 * @package org.zenmagick.plugins.zm_subscriptions
 * @author DerManoMann
 * @version $Id: zm_token.php 1460 2008-08-26 01:37:31Z DerManoMann $
 */
class zm_subscriptions extends ZMPlugin {

    /**
     * Create new instance.
     */
    function __construct() {
        parent::__construct('Subscriptions', 'Allow users to subscribe products/orders', '${plugin.version}');
        $this->setLoaderSupport('FOLDER');

        // the new prices and customer flag
        $customFields = array(
            'orders' => array(
                'is_subscription;integer;subscription',
                'subscription_next_order;datetime;nextOrder',
                'subscription_schedule;string;schedule',
                'subscription_order_id;integer;subscriptionOrderId'
            )
        );
        foreach ($customFields as $table => $fields) {
            foreach ($fields as $field) {
                ZMSettings::append('sql.'.$table.'.customFields', $field, ',');
            }
        }
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
    function install() {
        parent::install();
        ZMDbUtils::executePatch(file($this->getPluginDir()."sql/install.sql"), $this->messages_);

        $this->addConfigValue('Qualifying amount', 'minAmount', '0', 'The minimum amoout to qualify for a subscription');
        $this->addConfigValue('Minimum orders', 'minOrders', '0', 'The minimum number of orders before the subscription can be canceled');
        $this->addConfigValue('Cancel dealline', 'cancelDeadline', '0', 'Days before the next order the user can cancel the subscription');
        $this->addConfigValue('Notification email template name', 'emailTemplate', '',
            'Name of an email template to notify customers of new subscription orders; leave empty for none');
        $this->addConfigValue('Order history', 'orderHistory', true, 'Create subscription order history on schedule',
            "zen_cfg_select_drop_down(array(array('id'=>'1', 'text'=>'Yes'), array('id'=>'0', 'text'=>'No')), ");
        $this->addConfigValue('Shipping Address', 'addressPolicy', 'order', 'use either the original shipping addres, or the current default address',
            "zen_cfg_select_drop_down(array(array('id'=>'order', 'text'=>'Order Address'), array('id'=>'account', 'text'=>'Account Address')), ");
        $this->addConfigValue('Order status', 'orderStatus', '2', 'Order status for subscription orders', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name');
    }

    /**
     * {@inheritDoc}
     */
    function remove($keepSettings=false) {
        parent::remove($keepSettings);
        ZMDbUtils::executePatch(file($this->getPluginDir()."sql/uninstall.sql"), $this->messages_);
    }

    /**
     * {@inheritDoc}
     */
    public function init() {
        parent::init();

        $this->zcoSubscribe();

        // register tests
        if (null != ($tests = ZMPlugins::instance()->getPluginForId('zm_tests'))) {
            // add class path only now to avoid errors due to missing UnitTestCase
            ZMLoader::instance()->addPath($this->getPluginDir().'tests/');
            $tests->addTest('TestSubscriptions');
        }

        // if zm_cron available, load cron job
        if (null != ZMPlugins::instance()->getPluginForId('zm_cron')) {
            // add class path only now to avoid errors due to missing ZMCronJob
            ZMLoader::instance()->addPath($this->getPluginDir().'cron/');
        }

        ZMUrlMapper::instance()->setMapping(null, 'cancel_subscription', 'account', 'RedirectView', 'secure=true');
    }

    /**
     * Event handler to pick up subscription cehckout options.
     */
    public function onZMInitDone($args=array()) {
        if ('checkout_shipping' == ZMRequest::getPageName() && 'POST' == ZMRequest::getMethod()) {
            if (ZMTools::asBoolean(ZMRequest::getParameter('subscription'))) {
                ZMRequest::getSession()->setValue('subscription_schedule', ZMRequest::getParameter('schedule'));
            } else {
                ZMRequest::getSession()->removeValue('subscription_schedule');
            }
        }
        if ('checkout_success' == ZMRequest::getPageName()) {
            ZMRequest::getSession()->removeValue('subscription_schedule');
        }
    }

    /**
     * Check if currently subscription is selected.
     *
     * @return string The subscription schedule key or <code>null</code>.
     */
    public function getSelectedSchedule() {
        $schedule = ZMRequest::getSession()->getValue('subscription_schedule');
        return empty($schedule) ? null : $schedule;
    }

    /**
     * Get all available schedules as map.
     * 
     * <p>This can be configured/changed via the setting <em>plugins.zm_subscriptions.schedules</em>.</p>
     *
     * @return array Hash map of schedule key => name.
     */
    public function getSchedules() {
        $defaults = array(
            '1w' => 'Weekly',
            '10d' => 'Every 10 days',
            '4w' => 'Every four weeks',
            '1m' => 'Once a month'
        );
        return ZMSettings::get('plugins.zm_subscriptions.schedules', $defaults);
    }

    /**
     * Does the current shopping cart qualify for subscription.
     * 
     * @return boolean <code>true</code> if a subscription is allowed.
     */
    public function canSubscribe() {
        $cart = ZMRequest::getShoppingCart();
        return $this->get('minAmount') <= $cart->getTotal();
    }

    /**
     * Order created event handler.
     */
    public function onZMCreateOrder($args=array()) {
        $orderId = $args['orderId'];
        if (null != ($schedule = $this->getSelectedSchedule())) {
            $sql = "UPDATE " . TABLE_ORDERS . "
                    SET subscription_next_order = DATE_ADD(date_purchased, INTERVAL " . self::schedule2SQL($schedule) . "),
                      is_subscription = :subscription, subscription_schedule = :schedule
                    WHERE orders_id = :orderId";
            $args = array('orderId' => $orderId, 'subscription' => true, 'schedule' => $schedule);
            ZMRuntime::getDatabase()->update($sql, $args, TABLE_ORDERS);
        }
    }

    /**
     * Convert UI schedule value into something useful for SQL.
     *
     * @param string schedule The schedule value.
     * @return string A string that can be used in SQL <em>DATE_ADD</em>.
     */
    public static function schedule2SQL($schedule) {
        $schedule = preg_replace('/[^0-9dwm]/', '', $schedule); 
        return str_replace(array('d', 'w', 'm'), array(' DAY', ' WEEK', ' MONTH'), $schedule);
    }

}

?>
