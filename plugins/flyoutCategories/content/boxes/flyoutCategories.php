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
<?php if (isset($flyoutCategories)) { ?>
<?php $resources->cssFile('stylesheet_categories_menu.css'); ?>
<div class="box flyoutCategories" style="overflow:visible;"> <!-- re-enable overflow as disabled in default theme on .box -->
    <div id="nav-cat">
    <?php
        $generator = new ZMFlyoutCategoriesGenerator($request);
        echo $generator->buildTree(true);
    ?>
    <?php
        /*** this is not really supported by ZenMagick but will work for now ***/
        $content = '';
        if (SHOW_CATEGORIES_BOX_SPECIALS == 'true' || SHOW_CATEGORIES_BOX_PRODUCTS_ALL == 'true') {
          $content .= '';  // insert a blank line/box in the menu
          if (SHOW_CATEGORIES_BOX_SPECIALS == 'true') {
            $content .= '<ul class="level1"><li><a href="' . $net->url(FILENAME_SPECIALS) . '">' . _zm('Specials...') . '</a></li></ul>';
          }
          if (SHOW_CATEGORIES_BOX_PRODUCTS_NEW == 'true') {
            $content .= '<ul class="level1"><li><a href="' . $net->url(FILENAME_PRODUCTS_NEW) . '">' . _zm('New Products...') . '</a></li></ul>';
          }
          if (SHOW_CATEGORIES_BOX_FEATURED_PRODUCTS == 'true') {
              $products = ZMProducts::instance()->getFeaturedProducts(0, 1, false, $session->getLanguageId());
            if (0 < count($products)) {
              $content .= '<ul class="level1"><li><a href="' . $net->url(FILENAME_FEATURED_PRODUCTS) . '">' . _zm('Featured...') . '</a></li></ul>';
            }
          }
          if (SHOW_CATEGORIES_BOX_PRODUCTS_ALL == 'true') {
            $content .= '<ul class="level1"><li><a href="' . $net->url(FILENAME_PRODUCTS_ALL) . '">' . _zm('All Products...') . '</a></li></ul>';
          }
        }

        echo $content;
        // May want to add ............onfocus="this.blur()"...... to each A HREF to get rid of the dotted-box around links when they're clicked.
        // just parse the $content string and insert it into each A HREF tag
    ?>
    </div>
</div>
<?php } ?>
