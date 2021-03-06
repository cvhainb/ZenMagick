<?php
/*
 * ZenMagick - Smart e-commerce
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


/**
 * Check for unique openID.
 *
 * @author mano
 * @package org.zenmagick.plugins.openID
 */
class ZMUniqueOpenIDRule extends ZMRule {

    /**
     * Create new required rule.
     *
     * @param string name The field name.
     * @param string msg Optional message.
     */
    function __construct($name, $msg=null) {
        parent::__construct($name, "OpenID already in use.", $msg);
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
    public function validate($request, $data) {
        $plugin = ZMPlugins::instance()->getPluginForId('openID');
        $openid = $data[$this->getName()];
        $idExists = null != $plugin->getAccountForOpenID($openid);
        // empty or doesn't exist or exists but same as current account (account update)
        return empty($openid) || !$idExists || $openid == ZMRequest::instance()->getAccount()->get('openid');
    }


    /**
     * {@inheritDoc}
     */
    public function toJSString() {
        return '';
    }

}
