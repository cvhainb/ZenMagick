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
 * <p>A select form widget.</p>
 *
 * @author DerManoMann
 * @package org.zenmagick.mvc.widgets.form
 * @version $Id$
 */
class ZMSelectFormWidget extends ZMFormWidget {
    private $options_;


    /**
     * Create new instance.
     */
    function __construct() {
        parent::__construct();
        $this->setAttributeNames(array('id', 'class', 'size', 'multiple'));
        $this->options_ = array();
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Get the options map.
     *
     * @return array Map of value/name pairs.
     */
    public function getOptions() {
        return $this->options_;
    }

    /**
     * Set the options map.
     *
     * @param mixed options Map of value/name pairs.
     */
    public function setOptions($options) {
        $this->options_ = ZMLangUtils::toArray($options);
    }

    /**
     * {@inheritDoc}
     */
    public function render() {
        $html = ZMToolbox::instance()->html;
        $output = '<select'.$this->getAttributeString(false).'>';
        foreach ($this->getOptions() as $value => $name) {
            $selected = '';
            if ($value == $this->getValue()) {
              if (ZMSettings::get('zenmagick.mvc.html.xhtml')) {
                  $selected = ' selected="selected"';
              } else {
                  $selected = ' selected';
              }
              
            }
            $output .= '<option'.$selected.' value="'.$html->encode($value, false).'">'.$html->encode($name, false).'</option>';
        }
        $output .= '</select>';
        return $output;
    }

}

?>