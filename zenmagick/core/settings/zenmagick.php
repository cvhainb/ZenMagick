<?php
/*
 * ZenMagick - Extensions for zen-cart
 * Copyright (C) 2006,2007 ZenMagick
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

    // ZenMagick setup
    define('ZM_ROOT', 'zenmagick/');
    define('ZM_DEFAULT_THEME', 'default');
    define('ZM_THEMES_DIR', ZM_ROOT.'themes/');
    define('ZM_PLUGINS_DIR', ZM_ROOT.'plugins/');
    define('ZM_THEME_CONTENT_DIR', 'content/');
    define('ZM_THEME_EXTRA_DIR', 'extra/');
    define('ZM_THEME_BOXES_DIR', 'content/boxes/');
    define('ZM_THEME_LANG_DIR', 'lang/');

    // events
    define('ZM_EVENT_INIT_DONE', 'init_done');

    // db
    define('ZM_DB_PREFIX', DB_PREFIX);
    define('ZM_TABLE_FEATURE_TYPES', ZM_DB_PREFIX . 'zm_feature_types');
    define('ZM_TABLE_PRODUCT_FEATURES', ZM_DB_PREFIX . 'zm_product_features');
    define('ZM_TABLE_FEATURES', ZM_DB_PREFIX . 'zm_features');

    // files
    define ('ZM_FILENAME_COMPARE_PRODUCTS', 'product_comparison');
    define ('ZM_FILENAME_SOURCE_VIEW', 'source_view');
    define ('ZM_FILENAME_RSS', 'rss');

    // admin
    define('ZM_ADMINFN_INSTALLATION', 'zmInstallation.php');
    define('ZM_ADMINFN_CATALOG_MANAGER', 'zmCatalogManager.php');
    define('ZM_ADMINFN_FEATURES', 'zmFeatures.php');
    define('ZM_ADMINFN_L10N', 'zmL10n.php');
    define('ZM_ADMINFN_CACHE', 'zmCacheManager.php');
    define('ZM_ADMINFN_ABOUT', 'zmAbout.php');
    define('ZM_ADMINFN_CONSOLE', 'zmConsole.php');
    define('ZM_ADMINFN_PLUGINS', 'zmPlugins.php');

    // plugins/modules
    define('ZM_PLUGIN_PREFIX', 'PLUGIN_');
    define('ZM_PLUGIN_ENABLED_SUFFIX', 'ENABLED');
    define('ZM_PLUGIN_SORT_ORDER_SUFFIX', 'SORT_ORDER');

    // others
    define('PRODUCTS_OPTIONS_TYPE_SELECT', 0);

?>
