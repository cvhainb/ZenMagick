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

<?php if ($zm_resultList->hasFilters() || $zm_resultList->hasSorters()) { ?>
    <?php $form->open(null, null, false, array('method'=>'get','class'=>'ropt','onsubmit'=>null)) ?>
        <?php if ($zm_resultList->hasFilters()) { ?>
            <div class="rlf">
                <?php foreach($zm_resultList->getFilters() as $filter) { if (!$filter->isAvailable()) continue; ?>
                    <?php /* if multi select do not auto submit */ ?>
                    <?php $opts = $filter->isMultiSelection() ? ' size="3" multiple="multiple"' : ' onchange="this.form.submit()"'; ?>
                    <select id="<?php echo str_replace('[]', '', $filter->getId()) ?>" name="<?php echo $filter->getId() ?>"<?php echo $opts ?>>
                        <option value=""><?php zm_l10n("Filter by '%s' ...", $filter->getName()) ?></option>
                        <?php foreach($filter->getOptions() as $option) { ?>
                            <?php $selected = $option->isActive() ? ' selected="selected"' : ''; ?>
                            <option value="<?php echo $option->getId() ?>"<?php echo $selected ?>><?php echo $option->getName() ?></option>
                        <?php } ?>
                    </select>
                <?php } ?>
            </div>
        <?php } ?>
        <?php if ($zm_resultList->hasSorters()) { ?>
            <div class="rls">
                <?php if (ZMRequest::getParameter('keyword')) { ?>
                    <input type="hidden" name="keyword" value="<?php echo ZMRequest::getParameter('keyword') ?>" />
                <?php } ?>
                <input type="hidden" name="page" value="<?php echo $zm_resultList->getPageNumber() ?>" />
                <?php if (ZMRequest::getCategoryPath()) { ?>
                    <input type="hidden" name="cPath" value="<?php echo ZMRequest::getCategoryPath() ?>" />
                <?php } else if (ZMRequest::getManufacturerId()) { ?>
                    <input type="hidden" name="manufacturers_id" value="<?php echo ZMRequest::getManufacturerId() ?>" />
                <?php } else if (null != ZMRequest::getParameter("compareId")) { ?>
                    <?php $form->hiddenList('compareId[]', ZMRequest::getParameter("compareId")) ?>
                <?php } ?>

                <select id="sort" name="sort_id" onchange="this.form.submit()">
                    <option value=""><?php zm_l10n("Sort by ...") ?></option>
                    <?php foreach($zm_resultList->getSorters() as $sorter) { ?>
                        <?php foreach($sorter->getOptions() as $option) { ?>
                            <?php $selected = $option->isActive() ? ' selected="selected"' : ''; ?>
                            <?php $indicator = $option->isActive() ? ($option->isDecending() ? ' (-)' : ' (+)') : ''; ?>
                            <?php $id = $option->isActive() ? $option->getReverseId() : $option->getId(); ?>
                            <option value="<?php echo $id ?>"<?php echo $selected ?>><?php echo $option->getName().$indicator ?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
        <?php } ?>
        <div><input type="submit" class="btn" value="<?php zm_l10n("Sort / Reverse / Filter") ?>" /></div>
    </form>
<?php } ?>
