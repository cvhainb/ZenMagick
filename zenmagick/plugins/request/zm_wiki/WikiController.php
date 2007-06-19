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
 */
?>
<?php

define ('ZM_FILENAME_WIKI', 'wiki');


/**
 * Request controller for wiki pages.
 *
 * @author mano
 * @package net.radebatz.zenmagick.plugins.zm_wiki
 * @version $Id$
 */
class WikiController extends ZMController {

    /**
     * Default c'tor.
     */
    function WikiController() {
    global $zm_request, $zm_wiki, $pawfaliki_config;

        parent::__construct();

        $pawfaliki_config['GENERAL']['TITLE'] = zm_setting('storeName');
        $pawfaliki_config['GENERAL']['HOMEPAGE'] = zm_l10n_get("WikiRoot");
        $pawfaliki_config['LOCALE']['HOMEPAGE_LINK'] = "[[WikiRoot]]"; // link to the homepage
        $pawfaliki_config['GENERAL']['ADMIN'] = zm_setting('storeEmail');
        $pawfaliki_config['GENERAL']['CSS'] = '';
        if ($zm_request->isAdmin()) {
            $pawfaliki_config['GENERAL']['PAGES_DIRECTORY'] = "../wiki/files/";
            $pawfaliki_config['GENERAL']['TEMP_DIRECTORY'] = "../wiki/tmp/";
        } else {
            $pawfaliki_config['GENERAL']['PAGES_DIRECTORY'] = "wiki/files/";
            $pawfaliki_config['GENERAL']['TEMP_DIRECTORY'] = "wiki/tmp/";
        }

        // SYNTAX: Wiki editing syntax
        $pawfaliki_config['SYNTAX']['WIKIWORDS'] = false; // Auto-generation of links from WikiWords
        $pawfaliki_config['SYNTAX']['AUTOCREATE'] = true; // Display ? next to wiki pages that don't exist yet.
        $pawfaliki_config['SYNTAX']['HTMLCODE'] = true; // Allows raw html using %% tags

        // BACKUP: Backup & Restore settings
        $pawfaliki_config['BACKUP']['ENABLE'] = $zm_request->isAdmin(); // Enable backup & restore

        // RSS: RSS feed
        $pawfaliki_config['RSS']['ENABLE'] = false; // Enable rss support (http://mywiki.example?format=rss)

        // CHANGES: email page changes
        $pawfaliki_config['EMAIL']['ENABLE'] = false; // do we email page changes?

        // LICENSES: pages with special licenses
        $pawfaliki_config['LICENSE']['DEFAULT'] = "noLicense";
    }

    /**
     * Default c'tor.
     */
    function __construct() {
        $this->WikiController();
    }

    /**
     * Default d'tor.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Process a HTTP GET request.
     * 
     * @return ZMView A <code>ZMView</code> that handles presentation or <code>null</code>
     * if the controller generates the contents itself.
     */
    function processGet() {
    global $zm_request, $zm_crumbtrail, $zm_wiki;

        $zm_crumbtrail->clear();
        $zm_crumbtrail->addCrumb("Wiki", zm_href(ZM_FILENAME_WIKI, '', false));
        $page = $zm_request->getParameter('page', 'WikiRoot');
        $zm_crumbtrail->addCrumb(zm_format_title($page));

        return $this->create("PluginView", zm_view_wiki, $zm_wiki);
    }


    /**
     * Process a HTTP POST request.
     * 
     * @return ZMView A <code>ZMView</code> that handles presentation or <code>null</code>
     * if the controller generates the contents itself.
     */
    function processPost() {
    global $zm_request, $zm_crumbtrail;

        $zm_crumbtrail->clear();
        $zm_crumbtrail->addCrumb("Wiki", zm_href(ZM_FILENAME_WIKI, '', false));
        $page = $zm_request->getParameter('page', 'WikiRoot');
        $zm_crumbtrail->addCrumb(zm_format_title($page));

        return $this->create("PluginView", zm_view_wiki_edit, $zm_wiki);
    }

}

?>
