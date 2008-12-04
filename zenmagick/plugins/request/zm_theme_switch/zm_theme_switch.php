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
 * Allow users to switch between themes.
 *
 * @package org.zenmagick.plugins
 * @author DerManoMann
 * @version $Id$
 */
class zm_theme_switch extends ZMPlugin {
    const SESS_THEME_KEY = 'themeId';


    /**
     * Create new instance.
     */
    function __construct() {
        parent::__construct('Theme Switch', 'Allow users to select a theme');
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }

    /**
     * Init this plugin.
     */
    function init() {
        parent::init();

        $session = ZMRequest::getSession();
        if (null != ($themeId = ZMRequest::getParameter('themeId'))) {
            $session->setValue(self::SESS_THEME_KEY, $themeId);
        }

        if (null != ($themeId = $session->getValue(self::SESS_THEME_KEY))) {
            ZMRuntime::setThemeId($themeId);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function filterResponse($contents) {
        $themes = explode(',', ZMSettings::get('plugins.zm_theme_switch.themes'));
        $links = '';
        foreach ($themes as $themeInfo) {
            if (!ZMTools::isEmpty(trim($themeInfo))) {
                // themeId:name
                $details = explode(':', $themeInfo);
                if (2> count($details)) {
                    // default
                    $details[1] = $details[0];
                }
                if (!empty($links)) {
                    $links .= '&nbsp;|&nbsp;';
                }
                $links .= '<a href="'.ZMToolbox::instance()->net->url(null, 'themeId='.$details[0], ZMRequest::isSecure(), false).'">'.$details[1].'</a>';
            }
        }
        if (!ZMTools::isEmpty($links)) {
            $switch =  '<div id="style-switcher" style="text-align:right;padding:2px 8px;">' . zm_l10n_get('Switch theme: ') . $links . '</div>';
            $contents =  preg_replace('/(<body[^>]*>)/', '\1'.$switch, $contents, 1);
        }
        return $contents;
    }

}

?>