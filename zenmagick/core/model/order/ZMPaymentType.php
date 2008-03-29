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
 * A single payment type including all required information and settings.
 *
 * @author mano
 * @package org.zenmagick.model.order
 * @version $Id$
 */
class ZMPaymentType extends ZMModel {
    var $id_;
    var $name_;
    var $instructions_;
    var $error_;
    var $fields_;


    /**
     * Create a new payment type.
     *
     * @param int id The id.
     * @param string name The name.
     * @param string instructions Optional instructions.
     */
    function __construct($id, $name, $instructions='') {
        parent::__construct();
        $this->id_ = $id;
        $this->name_ = $name;
        $this->instructions_ = $instructions;
        $this->error_ = null;
        $this->fields_ = array();
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Get the payment type id.
     *
     * @return int The payment type id.
     */
    function getId() { return $this->id_; }

    /**
     * Get the payment name.
     *
     * @return string The payment name.
     */
    function getName() { return $this->name_; }

    /**
     * Get the optional payment instructions.
     *
     * @return string Payment instructions.
     */
    function getInstructions() { return $this->instructions_; }

    /**
     * Get the payment error (if any).
     *
     * @return string The payment error message.
     */
    function getError() { return $this->error_; }

    /**
     * Get the payment form fields.
     *
     * @return array A list of <code>ZMPaymentField</code> instances.
     */
    function getFields() { return $this->fields_; }

    /**
     * Add a form field to this payment type.
     *
     * @param ZMPaymentField field The new form field.
     */
    function addField($field) { array_push($this->fields_, $field); }

}

?>
