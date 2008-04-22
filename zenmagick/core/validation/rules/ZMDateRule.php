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
 * Date validation rule.
 *
 * @author mano
 * @package org.zenmagick.validation.rules
 * @version $Id$
 */
class ZMDateRule extends ZMRule {
    var $format_;


    /**
     * Create new date rule.
     *
     * @param string name The field name.
     * @param string format The date format (eg: DD/MM/YYYY); if <code>null</code>, <em>UI_DATE_FORMAT</em> will be used.
     * @param string msg Optional message.
     */
    function __construct($name, $format=null, $msg=null) {
        parent::__construct($name, "Please enter a valid date (%s).", $msg);
        $this->format_ = $format;
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Get the format string.
     */
    function getFormat() {
        if (null == $this->format_) {
            return constant('UI_DATE_FORMAT');
        } else {
            return $this->format_;
        }
    }

    /**
     * Validate the given request data.
     *
     * @param array req The request data.
     * @return boolean <code>true</code> if the value for <code>$name</code> is valid, <code>false</code> if not.
     */
    function validate($req) {
        $da = ZMToolbox::instance()->date->parseDate($req[$this->getName()], $this->getFormat());

        return empty($req[$this->getName()]) || @checkdate($da[1], $da[0], $da[2].$da[3]);
    }


    /**
     * Return an appropriate error message.
     *
     * @return string Localized error message.
     */
    function getErrorMsg() {
        return zm_l10n_get((null != $this->getMsg() ? $this->getMsg() : $this->getDefaultMsg()), $this->getName(), $this->getFormat());
    }


    /**
     * Create JS validation call.
     *
     * @return string Formatted JavaScript .
     */
    function toJSString() {
        $js = "    new Array('date'";
        $js .= ",'".$this->getName()."'";
        $js .= ",'".addslashes($this->getErrorMsg())."'";
        $js .= ",".'"'.$this->getFormat().'"';
        $js .= ")";
        return $js;
    }

}

?>
