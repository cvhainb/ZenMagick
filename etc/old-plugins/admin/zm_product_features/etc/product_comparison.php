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
    $toolbox = ZMRequest::instance()->getToolbox();
    // create list of features per product in same order
    $featureList = array();
    // get unique list of all feature names
    $featureNames = array(); 

    foreach ($zm_productList as $product) {
        $features = ZMFeatures::instance()->getFeaturesforProductIdAndStatus($product->getId());
        array_push($featureList, $features);
        $featureNames = array_merge($featureNames, array_flip(array_keys($features)));
    } 
    ksort($featureNames);
?>

<table cellspacing="0" cellpadding="0" id="pcompare">
    <thead>
        <tr>
            <th>&nbsp;</th>
            <?php foreach ($zm_productList as $product) { ?>
                <th class="pl">
                  <a href="<?php zm_product_href($product->getId()) ?>"><?php echo $product->getName() ?></a>
                  <?php zm_product_image_link($product) ?>
                </th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($featureNames as $featureName => $foo) { ?>
            <tr>
                <td><?php echo $featureName ?></td>
                <?php foreach ($featureList as $features) { ?>
                    <?php if (array_key_exists($featureName, $features)) { $feature = $features[$featureName]; ?>
                        <td><?php  zm_list_values($feature->getValues()) ?></td>
                    <?php } else { ?>
                        <td><?php _vzm("N/A") ?></td>
                    <?php } ?>
                <?php } ?>
            </tr>
        <?php } ?>
    </tbody>
</table>
<?php echo $toolbox->form->open('category', '', false, array('method'=>'get')) ?>
    <?php zm_hidden_list('compareId[]', ZMRequest::instance()->getParameter("compareId")) ?>
    <div class="btn"><input type="submit" class="btn" value="<?php _vzm("Change Selection") ?>" /></div>
</form>
