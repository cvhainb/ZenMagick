<?php
/*
 * ZenMagick Core - Another PHP framework.
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
 */
?>
<?php


/**
 * Empty validation rules that can be used to wrap custom logic.
 *
 * @author DerManoMann
 * @package org.zenmagick.services.mvc.rules
 * @version $Id: ZMWrapperRule.php 2121 2009-03-31 01:56:56Z dermanomann $
 */
class ZMWrapperRule extends ZMRule {
    private $function_;
    private $javascript_;


    /**
     * Create new rule.
     *
     * @param string name The field name.
     * @param string msg Optional message.
     * @param mixed function The function name or array.
     */
    function __construct($name, $msg=null, $function) {
        parent::__construct($name, "Please enter a value for %s.", $msg);

        $this->function_ = null;
        $this->javascript_ = '';
        $this->function_ = $function;
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Set the validation function.
     *
     * <p>The function must implement the same siganture as <code>validate($req)</code>.</p>
     *
     * @param string function The function name.
     */
    public function setFunction($function) {
        $this->function_ = $function;
    }

    /**
     * Set the JavaScript validation code.
     *
     * @param string javascript The javascript.
     */
    public function setJavaScript($javascript) {
        $this->javascript = $javascript;
    }

    /**
     * Validate the given request data.
     *
     * @param array req The request data.
     * @return boolean <code>true</code> if the value for <code>$name</code> is valid, <code>false</code> if not.
     */
    public function validate($req) {
        if (is_array($this->function_) && 2 == count($this->function_) && is_object($this->function_[0]) && is_string($this->function_[1])) {
            // expect object, method name
            $obj = $this->function_[0];
            $method = $this->function_[1];
            return $obj->$method($req);
        } else if (function_exists($this->function_)) {
            return call_user_func($this->function_, $req);
        }

        return true;
    }


    /**
     * Create JS validation call.
     *
     * @return string Formatted JavaScript .
     */
    public function toJSString() {
        return $this->javascript_;
    }

}

?>