<?php
/*
 * ZenMagick - Another PHP framework.
 * Copyright (C) 2006-2010 zenmagick.org
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

    // allow seo rewriters to fiddle with the request
    $_zm_request->seoDecode();

    // make sure we use the appropriate protocol (HTTPS, for example) if required
    ZMSacsManager::instance()->ensureAccessMethod($_zm_request);

    // form validation
    ZMValidator::instance()->load(file_get_contents(ZMFileUtils::mkPath(array(ZMRuntime::getApplicationPath(), 'config', 'validation.yaml'))));

    // reset as other global code migth fiddle with it...
    $request = $_zm_request;
    ZMEvents::instance()->fireEvent(null, ZMEvents::INIT_DONE, array('request' => $_zm_request));

    ZMDispatcher::dispatch($_zm_request);
    $_zm_request->closeSession();
    exit;
