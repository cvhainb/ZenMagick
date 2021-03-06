<?php
/*
 * ZenMagick - Smart e-commerce
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
 * FirePHP ZenMagick logging service.
 *
 * <p>Since FirePHP does only know four level of logging, <em>DEBUG</em> <strong>and</strong> <em>TRACE</em>
 * are mapped to FirePHP's <em>LOG</em> level.</p>
 *
 * @author DerManoMann
 * @package org.zenmagick.plugins.firePHP
 */
class Logging extends ZMLogging {
    protected static $LEVEL_MAP = array(
        ZMLogging::ERROR => FirePHP::ERROR,
        ZMLogging::WARN => FirePHP::WARN,
        ZMLogging::INFO => FirePHP::INFO,
        ZMLogging::DEBUG => FirePHP::LOG,
        ZMLogging::TRACE => FirePHP::LOG
    );

    /**
     * Create new instance.
     */
    function __construct() {
        parent::__construct();
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
    public function log($msg, $level=ZMLogging::INFO) {
        if (ZMSettings::get('zenmagick.core.logging.enabled') && $level <= ZMSettings::get('zenmagick.core.logging.level')) {
            if (!headers_sent()) {
                FirePHP::getInstance(true)->fb($msg, self::$LEVEL_MAP[$level]);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function dump($obj, $msg=null, $level=ZMLogging::DEBUG) {
        if (ZMSettings::get('zenmagick.core.logging.enabled') && $level <= ZMSettings::get('zenmagick.core.logging.level')) {
            if (!headers_sent()) {
                if ($obj instanceof Exception) {
                    FirePHP::getInstance(true)->fb($obj);
                } else {
                    FirePHP::getInstance(true)->fb($obj, $msg, FirePHP::DUMP);
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function trace($msg=null, $level=ZMLogging::DEBUG) {
        if (ZMSettings::get('zenmagick.core.logging.enabled') && $level <= ZMSettings::get('zenmagick.core.logging.level')) {
            if (!headers_sent()) {
                FirePHP::getInstance(true)->fb($msg, FirePHP::TRACE);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function logError($line, $info) {
        // map error type to log level
        $errTypes = array (
            1 => FirePHP::ERROR,
            2 => FirePHP::WARN,
            4 => FirePHP::ERROR,
            8 => FirePHP::INFO,
            16 => FirePHP::ERROR,
            32 => FirePHP::WARN,
            64 => FirePHP::ERROR,
            128 => FirePHP::WARN,
            256 => FirePHP::ERROR,
            512 => FirePHP::WARN,
            1024 => FirePHP::LOG,
            2048 => FirePHP::LOG,
            4096 => FirePHP::ERROR,
            8192 => FirePHP::LOG,
            16384 => FirePHP::LOG,
        ); 

        if (!headers_sent()) {
            FirePHP::getInstance(true)->fb($line, $errTypes[$info['errno']]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function exceptionHandler($e) { 
        if (!headers_sent()) {
            FirePHP::getInstance(true)->fb($e);
        }
    } 

}
