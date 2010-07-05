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
 * Locale driven by a single yaml file per language.
 *
 * @author DerManoMann
 * @package org.zenmagick.core.services.locale
 */
class ZMYamlLocale implements ZMLocale {
    private $translations_;


    /**
     * Create new instance.
     */
    function __construct() {
        $this->translations_ = array();
    }

    /**
     * Destroy instance.
     */
    function __destruct() {
    }


    /**
     * Add translations.
     *
     * @param array translations Map of translations.
     */
    public function addTanslations($translations) {
        $this->translations_ = array_merge($this->translations_, $translations);
    }

    /**
     * {@inheritDoc}
     */
    public function init($language) {
        $path = ZMFileUtils::mkPath(array(ZMRuntime::getApplicationPath(), 'locale', $language, 'LC_MESSAGES', 'messages.yaml'));
        $this->translations_ = ZMRuntime::yamlLoad(@file_get_contents($path));
    }

    /**
     * {@inheritDoc}
     */
    public function translate($text, $context=null, $domain=ZMLocale::DEFAULT_DOMAIN) {
        if (array_key_exists($text, $this->translations_)) {
            return $this->translations_[$text];
        }

        return $text;
    }

    /**
     * {@inheritDoc}
     */
    public function translatePlural($single, $number, $plural=null, $context=null, $domain=ZMLocale::DEFAULT_DOMAIN) {
        // not really supported
        return $this->translate($single);
    }

}