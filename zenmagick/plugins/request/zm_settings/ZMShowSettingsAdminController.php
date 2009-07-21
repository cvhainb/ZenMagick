<?php
/*
 * ZenMagick - Extensions for zen-cart
 * Copyright (C) 2006-2009 ZenMagick
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
 * Show settings controlller.
 *
 * @author DerManoMann
 * @package org.zenmagick.plugins.zm_settings
 * @version $Id$
 */
class ZMShowSettingsAdminController extends ZMPluginPageController {

    /**
     * Create new instance.
     */
    function __construct() {
        parent::__construct('show_settings', zm_l10n_get('Display Settings'), 'zm_settings');
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Get value for the given key and type.
     *
     * @param string key The key.
     * @param string type The type.
     * @return string The value as string.
     */
    protected function getStringValue($key, $type) {
        if (null === ($value = ZMSettings::get($key))) {
            return '-- NOT SET --';
        }

        switch ($type) {
        case 'int':
        case 'string':
            break;
        case 'array':
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            break;
        case 'octal':
            $value = '0'.decoct($value);
            break;
        case 'boolean':
            $value = ZMLangUtils::asBoolean($value) ? 'true' : 'false';
            break;
        default:
            echo $details['type']."<BR>";
            break;
        }

        return (string)$value;
    }

    /**
     * {@inheritDoc}
     */
    public function processGet($request) {
        $page = parent::processGet($request);
        $settingDetails = array();
        // prepare values
        foreach (zm_get_settings_details() as $group => $groupDetails) { 
            foreach ($groupDetails as $sub => $subDetails) {
                foreach ($subDetails as $subKey => $details) {
                    $key = $group.'.'.$sub.'.'.$details['key'];
                    $type = array_pop(explode(':', $details['type']));
                    if (false === strpos($details['type'], 'dynamic:')) {
                        $settingDetails[$group][$sub][$subKey]['fullkey'] = $key;
                        $settingDetails[$group][$sub][$subKey]['key'] = $details['key'];
                        $settingDetails[$group][$sub][$subKey]['desc'] = $details['desc'];
                        $settingDetails[$group][$sub][$subKey]['value'] = $this->getStringValue($key, $type);
                    } else {
                        // dynamic
                        $dt = explode(':', $details['type']);
                        $dynVar = '@'.$dt[1].'@';
                        $bits = explode($dynVar, $key);
                        $prefix = $bits[0];
                        $suffix = $bits[1];
                        foreach (ZMSettings::getAll() as $akey => $avalue) {
                            if (ZMLangUtils::startsWith($akey, $prefix) && ZMLangUtils::endsWith($akey, $suffix)) {
                                // potential match
                                $dynVal = substr($akey, strlen($prefix), -strlen($suffix));
                                if (!ZMLangUtils::isEmpty($dynVal)) {
                                    // yep
                                    $details['key'] = str_replace($dynVar, $dynVal, $details['key']);

                                    // build real key
                                    $key = $group.'.'.$sub.'.'.$details['key'];
                                    $settingDetails[$group][$sub][$subKey]['fullkey'] = $key;
                                    $settingDetails[$group][$sub][$subKey]['key'] = $details['key'];
                                    $settingDetails[$group][$sub][$subKey]['desc'] = '* '.str_replace($dynVar, $dynVal, $details['desc']);
                                    $settingDetails[$group][$sub][$subKey]['value'] = $this->getStringValue($key, $type);
                                }
                            }
                        }
                    }
                }
            }
        }
        // check for settings without details
        foreach (ZMSettings::getAll() as $key => $value) {
            foreach ($settingDetails as $group => $groupDetails) { 
                if (ZMLangUtils::startsWith($key, $group.'.')) {
                    $found = false;
                    foreach ($groupDetails as $subDetails) {
                        foreach ($subDetails as $details) {
                            if ($key == $details['fullkey']) {
                                $found = true;
                                break;
                            }
                        }
                    }
                    if (!$found) {
                        ZMMessages::instance()->warn('missing details for "'.$key.'"');
                    }
                }
            }
        }

        $context = array('settingDetails' => $settingDetails);
        $page->setContents($this->getPageContents($context));
        return $page;
    }

}

?>