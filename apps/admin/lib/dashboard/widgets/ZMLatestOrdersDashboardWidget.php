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
 * Latest orders dashboard widget.
 *
 * @author DerManoMann
 * @package zenmagick.store.admin.dashbord.widgets
 */
class ZMLatestOrdersDashboardWidget extends ZMDashboardWidget {

    /**
     * Create new user.
     * 
     */
    function __construct() {
        parent::__construct(_zm('Latest Orders'));
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
    public function getContents($request) {
        $admin2 = $request->getToolbox()->admin2;
        $utils = $request->getToolbox()->utils;
        $language = $request->getSelectedLanguage();
        $contents = '';
        $contents .= '<table class="grid" cellspacing="0">';
        $contents .= '<tr><th>'._zm('Order').'</th><th>'._zm('Account').'</th><th>'._zm('Placed').'</th><th>'._zm('Total').'</th></tr>';
        foreach (ZMOrders::instance()->getAllOrders($language->getId(), 5) as $order) {
            $contents .= '<tr>';
            $actualAccount = ZMAccounts::instance()->getAccountForId($order->getAccountId());
            $name = $actualAccount->getType() == ZMAccount::REGISTERED ? $order->getAccount()->getFullName() : _zm('** Guest **');
            $contents .= '    <td><a href="'.$admin2->url('order', 'orderId='.$order->getId()).'">'.$order->getId().'</a></td>';
            $contents .= '    <td><a href="'.$admin2->url('account', 'accountId='.$order->getAccountId()).'">'.$name.'</a></td>';
            $contents .= '    <td>'.ZMLocaleUtils::dateShort($order->getOrderDate()).'</td>';
            $contents .= '    <td>'.$utils->formatMoney($order->getTotal()).'</td>';
            $contents .= '  </tr>';
        }
        $contents .= '</table>';
        return $contents;
    }

}
