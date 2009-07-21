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
 <?php $zm_theme->staticPageContent("mainpagedivstack") ?>
<?php $zm_theme->staticPageContent("main_page") ?>
<?php $featured = ZMProducts::instance()->getFeaturedProducts(null, 4, false, $session->getLanguageId()); ?>
 <div id="featured">
<h3>Featured Products</h3>

  <?php foreach ($featured as $product) { ?>
    <div>
      <p><?php $html->productImageLink($product) ?></p>
      <p><a href="<?php $net->product($product->getId()) ?>"><?php $html->encode($product->getName()) ?></a></p>
      <?php $offers = $product->getOffers(); ?>
      <p><?php $utils->formatMoney($offers->getCalculatedPrice()) ?></p>
    </div>
  <?php } ?>
</div>