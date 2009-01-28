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
 */
?>
<?php


/**
 * Admin controller.
 *
 * @author DerManoMann
 * @package org.zenmagick.plugins.zm_settings
 * @version $Id$
 */
class ZMSettingsAdminController extends ZMPluginPageController {

    /**
     * Create new instance.
     */
    function __construct() {
        parent::__construct('settings_admin', zm_l10n_get('Settings'), 'zm_settings');
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
    public function processGet() {
        $page = parent::processGet();

        $context = array();

        //$context['zm_resultList'] = $resultList;

        $page->setContents($this->getPageContents($context));
        return $page;
    }

    /**
     * {@inheritDoc}
     */
    public function processPost() {
        $page = self::processGet();

        return $page;
    }

}

?>