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


/**
 * Plugin adding master password functionality.
 *
 * @author mano
 * @package org.zenmagick.plugins.zm_master_password
 * @version $Id$
 */
class zm_master_password extends ZMPlugin {

    /**
     * Default c'tor.
     */
    function zm_master_password() {
        parent::__construct('Master Password', 'Master password for all accounts.', '${plugin.version}');
        $this->setLoaderSupport('ALL');
        $this->setTraditional(false);
    }

    /**
     * Default c'tor.
     */
    function __construct() {
        $this->zm_master_password();
    }

    /**
     * Default d'tor.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Install this plugin.
     */
    function install() {
        parent::install();

        $this->addConfigValue('Master Password', 'masterPassword', '', 'The master password (will be encrypted in the database)');
    }

    /**
     * Init this plugin.
     */
    function init() {
        parent::init();

        $this->addMenuItem('master_password', zm_l10n_get('Master Password'), 'zm_master_password_admin');
    }

}

?>
