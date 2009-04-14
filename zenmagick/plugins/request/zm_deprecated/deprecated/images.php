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
 *
 * $Id$
 */
?>
<?php

    /**
     * Create a HTML <code>&lt;a&gt;</code> tag with the product image of the
     * given product.
     *
     * <p>In constrast to the <code>..._href</code> functions, this one will
     * return a full HTML <code>&lt;img&gt;</code> tag.</p>
     *
     * @package org.zenmagick.deprecated
     * @param ZMProduct product A product.
     * @param int categoryId Optional category id.
     * @param string format Can be either of <code>ZMProducts::IMAGE_SMALL</code>, <code>ZMProducts::IMAGE_MEDIUM</code> 
     *  or <code>ZMProducts::IMAGE_LARGE</code>; default is <code>ZMProducts::IMAGE_SMALL</code>.
     * @param boolean echo If <code>true</code>, the URI will be echo'ed as well as returned.
     * @return string A fully formated HTML <code>&lt;a&gt;</code> tag.
     * @deprecated use the new toolbox instead!
     */
    function zm_product_image_link($product, $categoryId=null, $format=ZMProducts::IMAGE_SMALL, $echo=ZM_ECHO_DEFAULT) {
        return ZMToolbox::instance()->html->productImageLink($product, $categoryId, null, $format, $echo);
    }


?>