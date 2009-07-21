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

<?php if (0 == $request->getProductId() && 'specials' != $request->getPageName()) { ?>
    <?php $products = ZMProducts::instance()->getSpecials(1, $session->getLanguageId()); ?>
    <?php if (0 != count($products)) { $product = $products[0]; ?>
        <h2><a href="<?php $net->url(FILENAME_SPECIALS) ?>"><?php zm_l10n("[More]") ?></a><?php zm_l10n("Specials") ?></h2>
        <div id="sb_specials" class="box">
            <p><?php $html->productImageLink($product) ?></p>
            <p><a href="<?php $net->product($product->getId()) ?>"><?php $html->encode($product->getName()) ?></a></p>
            <?php $offers = $product->getOffers(); ?>
            <p><?php $utils->formatMoney($offers->getCalculatedPrice()) ?></p>
        </div>
    <?php } ?>
<?php } ?>