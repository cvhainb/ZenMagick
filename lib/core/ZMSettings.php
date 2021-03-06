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
 * Settings (ZenMagick's configuration).
 *
 * @author DerManoMann
 * @package org.zenmagick.core
 */
class ZMSettings {
    private static $settings_ = array();


    /**
     * Get the value for the given setting name.
     *
     * @param string name The setting to check.
     * @param mixed default Optional default value to be returned if setting not found; default is <code>null</code>.
     * @return mixed The setting value or <code>null</code>.
     */
    public static function get($name, $default=null) {
        if (!array_key_exists($name, ZMSettings::$settings_)) {
            return $default;
        }

        return ZMSettings::$settings_[$name];
    }

    /**
     * Set configuration value.
     *
     * <p>A value of <code>null</code> will remove the setting.</p>
     *
     * @param string name The setting to check.
     * @param mixed value (New) value.
     * @return mixed The old setting value or <code>null</code>.
     */
    public static function set($name, $value) {
        $oldValue = isset(ZMSettings::$settings_[$name]) ? ZMSettings::$settings_[$name] : null;
        if (null !== $value) {
            ZMSettings::$settings_[$name] = $value;
        } else {
            if (array_key_exists($name, ZMSettings::$settings_)) {
                unset(ZMSettings::$settings_[$name]);
            }
        }

        return $oldValue;
    }

    /**
     * Get a map of all settings.
     *
     * @return array Map of all settings.
     */
    public static function getAll() {
        return ZMSettings::$settings_;
    }

    /**
     * Add a map of settings.
     *
     * @param array settings Map of settings.
     * @param boolean replace If <code>true</code> existing settings will be replaced; default is <code>true</code>.
     */
    public static function addAll($settings, $replace=true) {
        if ($replace) {
            ZMSettings::$settings_ = array_merge(ZMSettings::$settings_, $settings);
        } else {
            foreach ($settings as $name => $value) {
                if (!isset(ZMSettings::$settings_[$name])) {
                    ZMSettings::$settings_[$name] = $value;
                }
            }
        }
    }

    /**
     * Check if a given setting exists.
     *
     * <p>This is useful in cases where <code>null</code> is a valid setting value. In that
     * case, the <code>get</code> method will be ambiguous and <code>exists</code> should
     * be used.</p>.
     *
     * @param string name The setting to check.
     * @return boolean <code>true</code> if a setting with the given name exists.
     */
    public static function exists($name) {
        return isset(ZMSettings::$settings_[$name]);
    }

    /**
     * Append configuration value.
     *
     * @param string name The setting to append to.
     * @param mixed value The value to append.
     * @param string delim Optional delimiter to be used if the value exists and is not empty; default is <em>','</em>.
     * @return mixed The old setting value or <code>null</code>.
     */
    public static function append($name, $value, $delim=',') {
        $oldValue = ZMSettings::get($name);
        if (isset(ZMSettings::$settings_[$name]) && !empty($oldValue)) {
            ZMSettings::$settings_[$name] = $oldValue.$delim.$value;
        } else {
            ZMSettings::$settings_[$name] = $value;
        }

        return $oldValue;
    }

    /**
     * Load mappings from a YAML style string.
     *
     * @param string yaml The yaml style mappings.
     * @param boolean override Optional flag to control whether to override existing mappings or to merge;
     *  default is <code>true</code> to override.
     */
    public static function load($yaml, $override=true) {
        ZMSettings::$settings_ = ZMRuntime::yamlParse($yaml, ZMSettings::$settings_, $override);
    }

}
