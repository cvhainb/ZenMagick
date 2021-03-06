<?php
/*
 * ZenMagick - Smart e-commerce
 * Copyright (C) 2006-2010 zenmagick.org
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
 * A feature value.
 *
 * @author DerManoMann
 * @package org.zenmagick.model.catalog
 */
class ZMFeatureValue extends ZMObject {
    var $id_;
    var $index_;
    var $value_;


    /**
     * Create new feature value.
     *
     * @param int id The feature id.
     * @param int index The value index.
     * @param mixed The actual value.
     */
    function __construct($id, $index, $value) {
        parent::__construct();
        $this->id_ = $id;
        $this->index_ = $index;
        $this->value_ = $value;
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Get the feature value id.
     *
     * @return int The feature value id.
     */
    function getId() { return $this->id_; }

    /**
     * Get the feature value (sort) index.
     *
     * @return int The feature value index.
     */
    function getIndex() { return $this->index_; }

    /**
     * Get the value.
     *
     * @return string The value.
     */
    function getValue() { return $this->value_; }

}
