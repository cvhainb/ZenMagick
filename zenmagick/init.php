<?php
/*
 * ZenMagick - Another PHP framework.
 * Copyright (C) 2006,2009 ZenMagick
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

    // app location relative to zenmagick installation (ZM_BASE_PATH)
    define('ZM_APP_PATH', 'lib'.DIRECTORY_SEPARATOR.'store'.DIRECTORY_SEPARATOR);

    include 'bootstrap.php';

    // load mvc mappings
    ZMUrlManager::instance()->load(file_get_contents(ZMFileUtils::mkPath(array(ZMRuntime::getApplicationPath(), 'config', 'url_mappings.yaml'))), false);
    ZMSacsManager::instance()->load(file_get_contents(ZMFileUtils::mkPath(array(ZMRuntime::getApplicationPath(), 'config', 'sacs_mappings.yaml'))), false);

    // make sure we use the appropriate protocol (HTTPS, for example) if required
    ZMSacsManager::instance()->ensureAccessMethod($_zm_request);

    // load stuff that really needs to be global!
    foreach (ZMLoader::instance()->getGlobal() as $_zm_global) {
        include_once $_zm_global;
    }

    $request = $_zm_request;
    ZMEvents::instance()->fireEvent(null, ZMEvents::INIT_DONE, array('request' => $_zm_request));

?>
