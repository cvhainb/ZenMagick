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
 * A central place for all runtime stuff.
 *
 * <p>This is kind of the <em>application context</em>.</p>
 * @author mano
 * @package org.zenmagick
 * @version $Id$
 */
class ZMRuntime extends ZMObject {
    private static $themeId_;
    private static $theme_;
    private static $db_;


    /**
     * Get instance.
     */
    public static function instance() {
        return ZMObject::singleton('Runtime');
    }

    /**
     * Get the application scope.
     *
     * @return string Either <code>ZM_SCOPE_STORE</code> or <code>ZM_SCOPE_ADMIN</code>.
     */
    public static function getScope() {
        return ZMSettings::get('isAdmin') ? ZM_SCOPE_ADMIN : ZM_SCOPE_STORE;
    }

    /**
     * Get the database dao.
     *
     * @return queryFactory *The* zen-cart <code>queryFactory</code> instance.
     */
    public static function getDB() { if (null == ZMRuntime::$db_) { global $db; ZMRuntime::$db_ = $db; } return ZMRuntime::$db_; }

    /**
     * Get the database (provider).
     *
     * @return ZMDatabase A <code>ZMDatabase</code> implementation.
     */
    public static function getDatabase() { return ZMObject::singleton(ZMSettings::get('dbProvider')); }

    /**
     * Return the directory containing all themes.
     *
     * @return string The base directory for themes.
     */
    public static function getThemesDir() { return DIR_FS_CATALOG.ZM_THEMES_DIR; }

    /**
     * Return the directory containing all plugins.
     *
     * @return string The base directory for plugins.
     */
    public static function getPluginsDir() { return DIR_FS_CATALOG.ZM_PLUGINS_DIR; }

    /**
     * Return the base path for theme URIs.
     *
     * @return string The URL path prefix for all themes.
     */
    public static function getThemesPathPrefix() { return ZMRuntime::getContext().ZM_THEMES_DIR; }

    /**
     * Get the full ZenMagick installation path.
     *
     * @return string The ZenMagick installation folder.
     */
    public static function getZMRootPath() { return DIR_FS_CATALOG.ZM_ROOT; }

    /**
     * The application context.
     *
     * @return string The application context.
     */
    public static function getContext() { return DIR_WS_CATALOG; }

    /**
     * Get the effective theme id.
     *
     * @return string The currently effective theme id.
     */
    public static function getThemeId() {
        if (null != ZMRuntime::$themeId_) {
            return ZMRuntime::$themeId_;
        }

        if (null != ZMRuntime::$theme_) {
            return ZMRuntime::$theme_->getThemeId();
        }

        ZMRuntime::$themeId_ = ZMThemes::instance()->getZCThemeId();
        $path = ZMRuntime::getThemesDir().ZMRuntime::$themeId_;
        if (!@file_exists($path) || !@is_dir($path)) {
            ZMObject::log("invalid theme id: '".ZMRuntime::$themeId_.'"');
            ZMRuntime::$themeId_ = null;
            return ZM_DEFAULT_THEME;
        }

        return ZMRuntime::$themeId_;
    }

    /**
     * Set the theme id.
     *
     * <p>This will overwrite the configured theme id.</p>
     *
     * <p>Calling this method is quite expensive, as all theme specific stuff needs
     * to be updated - <strong>this is not completely implemented yet</strong>.</p>
     *
     * @param string themeId The new theme id.
     */
    public static function setThemeId($themeId) { 
        ZMRuntime::$themeId_ = $themeId; 
        ZMRuntime::$theme_ = null;
    }

    /**
     * Get the current theme.
     *
     * @return ZMTheme The current theme.
     */
    public static function getTheme() {
        if (null == ZMRuntime::$theme_) {
            ZMRuntime::$theme_ = ZMLoader::make("Theme", ZMRuntime::getThemeId());
        }

        return ZMRuntime::$theme_;
    }

    /**
     * Set the current theme.
     *
     * @param ZMTheme theme The theme.
     */
    public static function setTheme($theme) {
        ZMRuntime::$theme_ = $theme;
    }

    /**
     * Get the language.
     *
     * @return ZMLanguage The current language.
     */
    public static function getLanguage() {
        return ZMObject::singleton('Session')->getLanguage();
    }

    /**
     * Get the default language.
     *
     * @return ZMLanguage The default language.
     */
    public static function getDefaultLanguage() {
        $language = ZMLanguages::instance()->getLanguageForId(ZMSettings::get('storeDefaultLanguageId'));
        if (null == $language) {
            ZMObject::log('no default language found - using en as fallback', ZM_LOG_WARN);
            $language = ZMLoader::make("Language");
            $language->setId(1);
            $language->setDirectory('english');
            $language->setCode('en');
        }
        return $language;
    }

    /**
     * Get the current currency.
     *
     * @return ZMCurrency The current currency.
     */
    public static function getCurrency() {
        $session = ZMObject::singleton('Session');
        $currency = ZMCurrencies::instance()->getCurrencyForCode($session->getCurrencyCode());
        return $currency;
    }

    /**
     * Get the currently elapsed page execution time.
     *
     * @param string time Optional execution timestamp to be used instead of the current time.
     * @return long The execution time in milliseconds.
     */
    public static function getExecutionTime($time=null) {
        $startTime = explode (' ', PAGE_PARSE_START_TIME);
        $endTime = explode (' ', (null!=$time?$time:microtime()));
        $executionTime = $endTime[1]+$endTime[0]-$startTime[1]-$startTime[0];
        return round($executionTime, 4);
    }

    /**
     * Finish execution.
     *
     * <p>Calling this function will end all request handling in an ordered manner.</p>
     */
    public static function finish() {
        zen_session_close();
        exit();
    }

    /**
     * Get the store base URL.
     *
     * @param boolean secure If set, return a secure URL (if configured); default is <code>false</code>.
     * @return string The store base url.
     */
    public static function getBaseURL($secure=false) {
        if ($secure && ZMSettings::get('isEnableSSL')) {
            return HTTP_SERVER . DIR_WS_CATALOG;
        } else {
            return HTTPS_SERVER . DIR_WS_HTTPS_CATALOG;
        }
    }

}

?>
