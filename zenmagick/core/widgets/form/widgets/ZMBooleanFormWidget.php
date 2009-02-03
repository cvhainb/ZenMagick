<?php
/*
 * ZenMagick - Extensions for zen-cart
 * Copyright (C) 2006-2008 ZenMagick
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
 * <p>A widget to make a boolean selection (true/false).</p>
 *
 * <p>Style can be: <em>radio</em>, <em>select</em> or <em>checkbox</em>. Default is <em>radio</em>.</p>
 *
 * <p>If style is <em>checkbox</em>, the custom property <code>label</code> might be set to override the use
 * of the title as label text.</p>
 *
 * <p>Radiobox and select label for <code>true</code> and <code>false</code> may be set via <em>label.true</em> and
 * <em>label.false</em>, respectively.</p>
 *
 * @author DerManoMann
 * @package org.zenmagick.widgets.form.widget
 * @version $Id$
 */
class ZMBooleanFormWidget extends ZMFormWidget {

    /**
     * Create new instance.
     */
    function __construct() {
        parent::__construct();
        // defaults
        $this->set('style', 'checkbox');
        $this->set('label.true', 'True');
        $this->set('label.false', 'False');
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Overload to evaluate as boolean.
     *
     * @return boolean The value.
     */
    public function getValue() {
        return ZMTools::asBoolean(parent::getValue());
    }

    /**
     * Build the hidden value element name used for checkbox rendering.
     *
     * @param string name The name.
     * @return string The generated name for the hidden element.
     */
    protected function getCheckboxHiddenValueName($name) {
        if (false === strpos($name, '[')) {
            return '_'.$name;
        }
        // XXX: does this work with multi value parameters; eg. name = foo[] ??
        return preg_replace('/\[/', '[_', $name, 1);
    }

    /**
     * Render as checkbox.
     *
     * @return The rendered HTML.
     */
    protected function renderCheckbox() {
        $html = ZMToolbox::instance()->html;
        $slash = ZMSettings::get('isXHTML') ? '/' : '';
        $checked = ZMSettings::get('isXHTML') ? ' checked="checked"' : ' checked';
        $idBase = $html->encode($this->get('id'), false);
        $name = $this->getName();
        if (empty($idBase)) {
            // default to name; we need this to make label work
            $idBase = $name;
        }
        $value = $this->getValue();
        $label = $this->get('label');
        if (empty($label)) {
            $label = $this->getTitle();
        }

        ob_start();
        echo '<input type="hidden" name="'.$this->getCheckboxHiddenValueName($name).'" value="'.($value ? 'true' : 'false').'"'.$slash.'>';
        echo '<input type="checkbox" id="'.$idBase.'" name="'.$name.'" value="true"'.($value ? $checked : '').$slash.'>';
        echo ' <label for="'.$idBase.'">'.$html->encode(zm_l10n_get($label), false).'</label>';
        return ob_get_clean();
    }

    /**
     * Render as radiobox group.
     *
     * @return The rendered HTML.
     */
    protected function renderRadio() {
        $html = ZMToolbox::instance()->html;
        $slash = ZMSettings::get('isXHTML') ? '/' : '';
        $checked = ZMSettings::get('isXHTML') ? ' checked="checked"' : ' checked';
        $idBase = $html->encode($this->get('id'), false);
        $name = $this->getName();
        if (empty($idBase)) {
            // default to name; we need this to make label work
            $idBase = $name;
        }
        $value = $this->getValue();

        ob_start();
        echo '<input type="radio" id="'.$idBase.'_true" name="'.$name.'" value="true"'.($value ? $checked : '').$slash.'>';
        echo ' <label for="'.$idBase.'_true">'.$html->encode(zm_l10n_get($this->get('label.true')), false).'</label>';
        echo '<input type="radio" id="'.$idBase.'_false" name="'.$name.'" value="false"'.(!$value ? $checked : '').$slash.'>';
        echo ' <label for="'.$idBase.'_false">'.$html->encode(zm_l10n_get($this->get('label.false')), false).'</label>';
        return ob_get_clean();
    }

    /**
     * Render as select box.
     *
     * @return The rendered HTML.
     */
    protected function renderSelect() {
        $html = ZMToolbox::instance()->html;
        $slash = ZMSettings::get('isXHTML') ? '/' : '';
        $selected = ZMSettings::get('isXHTML') ? ' selected="selected"' : ' selected';
        $id = $html->encode($this->get('id'), false);
        $name = $this->getName();
        $value = $this->getValue();

        ob_start();
        echo '<select '.(!empty($id) ? ' id="'.$id.'"' : '').' name="'.$name.'">';
        echo '  <option value="true"'.(!$value ? $selected : '').'>'.$html->encode(zm_l10n_get($this->get('label.true')), false).'</option>';
        echo '  <option value="false"'.(!$value ? $selected : '').'>'.$html->encode(zm_l10n_get($this->get('label.false')), false).'</option>';
        echo '</select>';
        return ob_get_clean();
    }

    /**
     * {@inheritDoc}
     */
    public function render() {
        switch ($this->get('style')) {
            default:
                ZMLogging::instance()->log('invalid style "'.$this->get('style').'" - using default', ZMLogging::DEBUG);
            case 'radio':
                return $this->renderRadio();
                break;
            case 'select':
                return $this->renderSelect();
                break;
            case 'checkbox':
                return $this->renderCheckbox();
                break;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function handleFormData($data) {
        if ('checkbox' != $this->get('style')) {
            return parent::handleFormData($data);
        }

        $name = $this->getName();
        $_name = $this->getCheckboxHiddenValueName($name);
        echo 'name:'.$name;
        echo '_name:'.$_name;
        if (!array_key_exists($name, $data) && array_key_exists($_name, $data)) {
            $data[$name] = false;
            unset($data[$_name]);
        }

        return $data;
    }

}

?>