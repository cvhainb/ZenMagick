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


/**
 * A select form widget.
 *
 * <p>Style can be: <em>select</em> (default) or <em>radio</em>.</p>
 *
 * @author DerManoMann
 * @package org.zenmagick.mvc.widgets.form
 */
class ZMSelectFormWidget extends ZMFormWidget {
    private $options_;


    /**
     * Create new instance.
     */
    function __construct() {
        parent::__construct();
        $this->setAttributeNames(array('id', 'class', 'size', 'multiple', 'title'));
        $this->options_ = array();
        // defaults
        $this->set('style', 'select');
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
    public function setValue($value) {
        $arr = @unserialize($value);
        if (is_array($arr)) {
            $value = $arr;
        }
        parent::setValue($value);
    }

    /**
     * Set the multiple flag.
     *
     * @param boolean multiple New value.
     */
    public function setMultiple($multiple) {
        $this->set('multiple', ZMLangUtils::asBoolean($multiple));
    }

    /**
     * {@inheritDoc}
     */
    public function isMultiValue() {
        return ZMLangUtils::asBoolean($this->get('multiple'));
    }

    /**
     * Get the options map.
     *
     * @param ZMRequest request The current request.
     * @return array Map of value/name pairs.
     */
    public function getOptions($request) {
        return $this->options_;
    }

    /**
     * Add a single option.
     *
     * @param string name The option name.
     * @param string value The value; default is <code>null</code> to use the name.
     */
    public function addOption($name, $value=null) {
        $value = null === $value ? $name : $value;
        $this->options_[$value] = $name;
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
    public function getStringValue() {
        if ($this->isMultiValue()) {
            // only for multi values, to avoid serializing int values, etc...
            return serialize($this->getValue());
        }

        return parent::getStringValue();
    }

    /**
     * {@inheritDoc}
     */
    public function render($request, $view) {
        if ($this->isMultiValue()) {
            ZMLogging::instance()->log('multi-value: defaulting style to select', ZMLogging::TRACE);
            $this->set('style', 'select');
        }
        switch ($this->get('style')) {
            default:
                ZMLogging::instance()->log('invalid style "'.$this->get('style').'" - using default', ZMLogging::DEBUG);
            case 'select':
                return $this->renderSelect($request);
            case 'radio':
                return $this->renderRadio($request);
        }
    }

    /**
     * Render as seclect drop down.
     *
     * @param ZMRequest request The current request.
     */
    public function renderSelect($request) {
        $values = $this->getValue();
        if (!is_array($values)) {
            $values = array($values);
        }
        $output = '<select'.$this->getAttributeString($request, false).'>';
        foreach ($this->getOptions($request) as $oval => $name) {
            $selected = '';
            if (in_array($oval, $values)) {
                if (ZMSettings::get('zenmagick.mvc.html.xhtml')) {
                    $selected = ' selected="selected"';
                } else {
                    $selected = ' selected';
                }
            }
            $output .= '<option'.$selected.' value="'.ZMHtmlUtils::encode($oval).'">'.ZMHtmlUtils::encode($name).'</option>';
        }
        $output .= '</select>';
        return $output;
    }

    /**
     * Render as group of radio buttons.
     *
     * @param ZMRequest request The current request.
     */
    public function renderRadio($request) {
        $slash = ZMSettings::get('zenmagick.mvc.html.xhtml') ? '/' : '';
        $checked = ZMSettings::get('zenmagick.mvc.html.xhtml') ? ' checked="checked"' : ' checked';

        $values = $this->getValue();
        if (!is_array($values)) {
            $values = array($values);
        }

        $idBase = ZMHtmlUtils::encode($this->get('id'));
        if (empty($idBase)) {
            // default to name; we need this to make label work
            $idBase = $this->getName();
        }

        $value = $this->getValue();

        ob_start();
        $index = 0;
        foreach ($this->getOptions($request) as $oval => $name) {
            echo '<input type="radio" id="'.$idBase.'-'.$index.'" class="'.$this->get('class').'" name="'.$this->getName().'" value="'.ZMHtmlUtils::encode($oval).'"'.($oval==$value ? $checked : '').$slash.'>';
            echo ' <label for="'.$idBase.'-'.$index.'">'.ZMHtmlUtils::encode(_zm($name)).'</label>';
            ++$index;
        }
        return ob_get_clean();
    }

}