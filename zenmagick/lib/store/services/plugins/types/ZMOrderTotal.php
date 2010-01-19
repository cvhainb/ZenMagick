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
 * Order total interface.
 *
 * @author DerManoMann
 * @package org.zenmagick.store.services.plugins.types
 * @version $Id$
 */
interface ZMOrderTotal {

    /**
     * Evaluate the given cart and return resulting order totals.
     *
     * @param ZMRequest request The current request.
     * @param ZMShoppingCart shoppingCart The current shopping cart.
     * @return mixed Either a single <code>ZMOrderTotalDetails</code>, a list of order total details
     *  (<code>ZMOrderTotalDetails</code>) or <code>null</code>.
     */
    public function calculate($request, $shoppingCart);

}

?>