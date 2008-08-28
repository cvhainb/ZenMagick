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
 * Token service.
 *
 * @author DerManoMann
 * @package org.zenmagick.plugins.zm_token
 * @version $Id$
 */
class ZMTokens extends ZMObject {

    /**
     * Create new instance.
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }

    /**
     * Get instance.
     */
    public static function instance() {
        return ZMObject::singleton('Tokens');
    }


    /**
     * Generate a random token.
     *
     * @param int length Optional length; default is <em>32</em>.
     * @return string The token.
     */
    protected function createToken($length=32) {
        static $chars	=	'0123456789abcdef';
        $max=	strlen($chars) - 1;
        $token = '';
        $name = session_name();
        for ($ii=0; $ii < $length; ++$ii) {
            $token .=	$chars[(rand(0, $max))];
        }

        return md5($token.$name);
    }

    /**
     * Get a new token for the given resource.
     *
     * @param string resource The resource.
     * @param int lifetime The lifetime of the new token (in seconds).
     * @return ZMToken A token.
     */
    public function getNewToken($resource, $lifetime) {
        $token = ZMLoader::make('Token');
        $token->setHash($this->createToken());
        $token->setResource($resource);
        $now = mktime();
        //TODO: where do we put this format
        $token->setIssued(date('Y-m-d H:i:s', $now));
        $token->setExpires(date('Y-m-d H:i:s', $now+$lifetime));
        return ZMRuntime::getDatabase()->createModel(ZM_TABLE_TOKEN, $token);
    }

    /**
     * Check if <em>hash</em> is valid in context of the <em>resource</em>.
     *
     * @param string resource The resource.
     * @param string hash The hash code.
     * @param boolean expire Optional flag to invalidate a matching token; default is <code>true</code>.
     * @return ZMToken A valid token or <code>null</code>.
     */
    public function validateHash($resource, $hash, $expire=true) {
        $sql = "SELECT * FROM " . ZM_TABLE_TOKEN . "
                WHERE hash = :hash AND resource = :resource AND expires >= now()";
        $token = ZMRuntime::getDatabase()->querySingle($sql, array('hash'=>$hash, 'resource'=>$resource), ZM_TABLE_TOKEN, 'Token');
        if ($expire) {
            $sql = "DELETE FROM " . ZM_TABLE_TOKEN . "
                    WHERE hash = :hash AND resource = :resource";
            ZMRuntime::getDatabase()->update($sql, array('hash'=>$hash, 'resource'=>$resource), ZM_TABLE_TOKEN);
        }
        return $token;
    }

    /**
     * Clear all expired token.
     */
    public function clearExpired() {
        $sql = "DELETE FROM " . ZM_TABLE_TOKEN . "
                WHERE expires < now()";
        ZMRuntime::getDatabase()->update($sql);
    }

}

?>