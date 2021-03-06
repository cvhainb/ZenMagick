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

    $currentLanguage = Runtime::getLanguage();
    $selectedLanguageId = $request->getParameter('languageId', $currentLanguage->getId());

    $category = ZMCategories::instance()->getCategoryForId($request->getCategoryId(), $selectedLanguageId);
    if (null === $category) {
        $category = ZMBeanUtils::getBean("Category");
        $category->setName('** new category **');

        // set a few defaults from the default language category
        $defaultLanguage = ZMLanguages::instance()->getLanguageForCode(ZMSettings::get('defaultLanguageCode'));
        $defaultCategory = ZMCategories::instance()->getCategoryForId($request->getCategoryId(), $defaultLanguage->getId());
        if (null != $defaultCategory) {
            // only if exist (might not be the case if category is all new)
            $category->setName($defaultCategory->getName());
            $category->setSortOrder($defaultCategory->getSortOrder());
            $category->setImage($defaultCategory->getImage());
        }
    }

?>

  <form action="<?php echo $admin->url(null, $defaultUrlParams) ?>" method="GET">
    <div>
      <input type="hidden" name="fkt" value="CategoryAdminTab">
    </div>
    <h2><?php echo $category->getName() ?> ( <select id="languageId" name="languageId" onchange="this.form.submit();">
      <?php foreach (ZMLanguages::instance()->getLanguages() as $language) { ?>
        <?php $selected = $selectedLanguageId == $language->getId() ? ' selected="selected"' : ''; ?>
        <option value="<?php echo $language->getId() ?>"<?php echo $selected ?>><?php echo $language->getName() ?></option>
      <?php } ?>
    </select> )</h2>
  </form>

  <form action="<?php echo $admin->url(null, $defaultUrlParams) ?>" method="POST">
    <fieldset>
        <legend>General</legend>
        <input type="checkbox" id="status" name="status" value="1" <?php $form->checked($category->isActive()) ?>> <label for="status">Status</label>
        <br><br>
        <label for="categoryName">Name</label>
        <input type="text" id="categoryName" name="categoryName" value="<?php echo $html->encode($category->getName()) ?>" size="30">
        <br>
        <label for="categoryDescription" style="display:block;">Description</label>
        <textarea id="categoryDescription" name="categoryDescription" rows="5" cols="80"><?php echo $html->encode($category->getDescription()) ?></textarea>
    </fieldset>

    <fieldset style="position:relative;">
        <legend>Image Options</legend>
        <div><input type="hidden" name="currentImage" value="<?php echo $category->getImage() ?>"></div>
        <?php echo $html->image($category->getImageInfo(), ZMProducts::IMAGE_SMALL, 'style=position:absolute;top:6px;right:30px;') ?>
        <p class="opt"><label for="categoryImage">Upload Image</label><input type="file" id="categoryImage" name="categoryImage"></p>
        <p class="opt">
          <label for="imgDir">... to directory</label><select id="imgDir" name="imgDir">
            <option value="">Main Directory</option>
            <option value="attributes/">attributes</option>
            <option value="uploads/">uploads</option>
          </select>
        </p>
        <p class="or">Or</p>
        <p class="opt"><label for="imageName">Select image on server</label><input type="text" id="imageName" name="imageName"></p>
        <p class="or">Or</p>
        <p class="opt"><input type="checkbox" id="imageDelete" name="imageDelete" value="1"> <label for="imageDelete">Clear image association</label></p>
    </fieldset>

    <fieldset>
        <legend>Other Options</legend>
        <p class="opt">
            <label for="sortOrder">Sort Order</label><input type="text" id="sortOrder" name="sortOrder" value="<?php echo $category->getSortOrder() ?>" size="4">
        </p>
        <hr>
        <p class="opt">
            <label for="restrictType">Restrict Product Type</label><select id="restrictType" name="restrictType">
              <option value="">-- No Restriction --</option>
              <option value="1">Product - General</option>
              <option value="2">Product - Music</option>
              <option value="3">Document - General</option>
              <option value="4">Document - Product</option>
              <option value="5">Product - Free Shipping</option>
            </select>
        </p>
        <p class="opt">
            <input type="radio" id="restrictTypeSingle" name="restrictTypeLevel" value="r"> <label for="restrictTypeAll">Category only</label>
            <input type="radio" id="restrictTypeAll" name="restrictTypeLevel" value="r"> <label for="restrictTypeAll">Include Subcategories</label>
        </p>
    </fieldset>

    <h3>Full update, move, delete, create coming ...</h3>
    <div class="btn">
        <input type="hidden" name="fkt" value="CategoryAdminTab">
        <input type="hidden" name="languageId" value="<?php echo $selectedLanguageId ?>">
        <?php if (0 < $category->getId()) { ?>
            <input type="submit" class="btn" name="update" value="Update">
        <?php } ?>
<!--
        <input type="submit" class="btn mod" value="Move">
        <input type="submit" class="btn del" value="Delete" onclick="return zm_user_confirm('Delete category \'<?php echo $category->getName() ?>\'?');">
-->
    </div>
</form>
