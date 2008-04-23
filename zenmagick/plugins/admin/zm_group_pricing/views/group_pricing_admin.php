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
 *
 * $Id$
 */
?>
<?php
    $toolbox = ZMToolbox::instance();
?>
  <?php $toolbox->form->open('', $zm_nav_params, false, array('method'=>'get')) ?>
    <h2>Group Pricing ( <?php zm_idp_select('groupId', $priceGroups, 1, ZMRequest::getParameter('groupId'), 'this.form.submit()') ?> )</h2>
  </form>

  <?php $toolbox->form->open('', $zm_nav_params) ?>
    <fieldset>
      <?php $groupId = ZMRequest::getParameter('groupId', $priceGroups[0]->getId()); ?>
      <input type="hidden" name="groupId" value="<?php echo $groupId ?>">
      <input type="hidden" name="groupPricingId" value="<?php echo ZMRequest::getParameter('groupPricingId') ?>">
      <legend>Discount</legend>
      <p>
        <label for="discount">Discount</label> 
        <input type="text" id="discount" name="discount" value="<?php echo ZMRequest::getParameter('discount') ?>">

        <?php $type = ZMRequest::getParameter('type'); ?>
        <label for="type">Type</label> 
        <select id="type" name="type">
          <option value="%"<?php if ('%' == $type) { echo ' selected'; } ?>>Percent</option>
          <option value="$"<?php if ('$' == $type) { echo ' selected'; } ?>>Amount</option>
        </select>
      </p>
      <p>
        <input type="checkbox" id="regularPriceOnly" name="regularPriceOnly" value="1"<?php $toolbox->form->checked(ZMRequest::getParameter('regularPriceOnly')) ?>>
        <label for="regularPriceOnly">Do not allow discount on sale/special</label>
      </p>
      <p>
        <label for="startDate">Start Date</label> 
        <input type="text" id="startDate" name="startDate" value="<?php zm_date_short(ZMRequest::getParameter('startDate')) ?>">
        <label for="endDate">End Date</label> 
        <input type="text" id="endDate" name="endDate" value="<?php zm_date_short(ZMRequest::getParameter('endDate')) ?>">
        <?php echo UI_DATE_FORMAT ?>, for example: <?php echo UI_DATE_FORMAT_SAMPLE ?>
      </p>
    </fieldset>
    <p>
      <input type="hidden" name="fkt" value="zm_group_pricing_admin">
      <?php if (0 < ZMRequest::getParameter('groupPricingId')) { ?>
          <input type="submit" name="update" value="Update">
          <a href="<?php $toolbox->net->url('', $zm_nav_params.'&groupPricingId='.ZMRequest::getParameter('groupPricingId').'&delete=true') ?>">Delete</a>
      <?php } else { ?>
          <input type="submit" name="create" value="Create">
      <?php } ?>
    </p>
  </form>
