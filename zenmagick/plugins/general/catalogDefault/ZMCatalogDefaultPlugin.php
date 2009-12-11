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
 * Default catalog manager plugin.
 *
 * @package org.zenmagick.plugins.catalogDefault
 * @author DerManoMann
 * @version $Id: zm_catalog_default.php 2419 2009-07-10 03:27:10Z dermanomann $
 */
class ZMCatalogDefaultPlugin extends Plugin {

    /**
     * Create new instance.
     */
    function __construct() {
        parent::__construct('Catalog Manager', 'Default Catalog Manager page', '${plugin.version}');
        $this->setLoaderPolicy(ZMPlugin::LP_FOLDER);
        $this->setPreferredSortOrder(0);
        $this->setContext(Plugin::CONTEXT_ADMIN);
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
    public function init() {
        parent::init();
        $this->addMenuItem('catalogDefaultTab', zm_l10n_get('Catalog Manager'), 'CatalogDefaultTab', ZMAdminMenu::MENU_CATALOG_MANAGER_TAB);
    }

}

?>