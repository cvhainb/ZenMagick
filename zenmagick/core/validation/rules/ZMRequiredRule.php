<?php
/*
 * ZenMagick - Extensions for zen-cart
 * Copyright (C) 2006-2009 ZenMagick
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
 * Required validation rules.
 *
 * @author DerManoMann
 * @package org.zenmagick.validation.rules
 * @version $Id$
 */
class ZMRequiredRule extends ZMRule {

    /**
     * Create new required rule.
     *
     * <p>If a list of names is specified, validation is considered teh existence of at least one.
     *
     * @param mixed name The field name or a list (either an array or comma separated string) of names.
     * @param string msg Optional message.
     */
    function __construct($name, $msg=null) {
        if (is_array($name)) {
            $name = implode(',', $name);
        }
        parent::__construct($name, "Please enter a value for %s.", $msg);
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Validate the given request data.
     *
     * @param array req The request data.
     * @return boolean <code>true</code> if the value for <code>$name</code> is valid, <code>false</code> if not.
     */
    public function validate($req) {
        foreach (explode(',', $this->getName()) as $name) {
            // remove trailing '[]' used for checkbox groups
            $name = str_replace(array('[', ']'), '', $name);
            if (array_key_exists($name, $req) && !empty($req[$name])) {
                return true;
            }
        }
        return false;
    }


    /**
     * Create JS validation call.
     *
     * @return string Formatted JavaScript .
     */
    public function toJSString() {
        $js = "    new Array('required'";
        $js .= ",'".$this->getName()."'";
        $js .= ",'".addslashes($this->getErrorMsg())."'";
        $js .= ")";
        return $js;
    }

}

?>
