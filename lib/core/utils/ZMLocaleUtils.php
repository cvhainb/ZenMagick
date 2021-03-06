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
 * Locale utils.
 *
 * @author DerManoMann
 * @package org.zenmagick.core.utils
 */
class ZMlocaleUtils {
    /** Locale patterns. */
    const LOCALE_PATTERNS = 'zm_l10n_get,zm_l10n,_zmn,_zm,_vzm';


    /**
     * Get all parameter token for the function call pointed to by index.
     *
     * @param array tokens All tokens
     * @param int index The index of the function to examine.
     * @return array List of parameter token.
     */
    private static function getParameterToken($tokens, $index) {
        $parameters = array();
        for ($ii=$index+1; $ii<count($tokens); ++$ii) {
            $token = $tokens[$ii];
            if (is_string($token) && ')' == $token) {
                break;
            }
            if (is_array($token) && T_CONSTANT_ENCAPSED_STRING == $token[0]) {
                $parameters[] = $token;
            }
        }
        return $parameters;
    }

    /**
     * Build a language map for all found l10n strings in the given directory tree.
     *
     * @param string baseDir The base folder of the directory tree to scan.
     * @param string ext File extension to look for; default is <em>.php</em>.
     * @return array A map of l10n strings for each file.
     */
    public static function buildL10nMap($baseDir, $ext='.php') {
        $relBase = ZMFileUtils::normalizeFilename(dirname($baseDir));

        $lnPatterns = explode(',', self::LOCALE_PATTERNS);
        $map = array();
        foreach (ZMFileUtils::findIncludes($baseDir.DIRECTORY_SEPARATOR, $ext, true) as $filename) {
            $strings = array();
            $contents = file_get_contents($filename);
            // try to convert into relative path
            $filename = ZMFileUtils::normalizeFilename($filename);
            $filename = str_replace($relBase, '', $filename);

            // use PHP tokenizer to analyze...
            $tokens = token_get_all($contents);
            foreach ($tokens as $ii => $token) {
                // need string token to start with..
                if (is_array($token) && T_STRING == $token[0] && in_array($token[1], $lnPatterns) && ($ii+2) <= count($tokens)) {
                    $parameters = self::getParameterToken($tokens, $ii);
                    if (0 < count($parameters)) {
                        $text = substr($parameters[0][1], 1, -1);
                        $line = $parameters[0][2];
                        $context = null;
                        $plural = null;

                        // check for context / plural
                        if ('_zm' == $token[1] && 1 < count($parameters)) {
                            $context = substr($parameters[1][1], 1, -1);
                        } else if ('_zmn' == $token[1]) {
                            // default to single text
                            $plural = $text;
                            if (2 < count($parameters)) {
                                $plural = substr($parameters[1][1], 1, -1);
                                $context = substr($parameters[2][1], 1, -1);
                            } else  if (1 < count($parameters)) {
                                $plural = substr($parameters[1][1], 1, -1);
                            }
                        }
                        $strings[$text] = array('msg' => $text, 'plural' => $plural, 'context' => $context, 'filename' => $filename, 'line' => $line);
                    }
                }
            }

            if (0 < count($strings)) {
                $map[$filename] = $strings;
            }
        }

        return $map;
    }

    /**
     * Create a yaml file from a l10n map.
     *
     * <p>The created YAML will include comments with the filename and warnings about duplicate mappings, etc.</p>
     *
     * @param array map The map.
     * @return string The formatted YAML.
     */
    public static function map2yaml($map) {
        $lines = array();
        $lines[] = '# language mapping generated by ZenMagick Admin v'.ZMSettings::get('zenmagick.version');
        $globalMap = array();
        foreach ($map as $filename => $strings) {
            if (null === $strings) {
                continue;
            }

            $lines[] = '#: '.$filename;
            foreach ($strings as $key => $info) {
                $quote = '"';
                // either we have escaped single quotes or double quotes that are not escaped
                if (false !== strpos($key, '\\\'') || (false !== strpos($key, '"') && false === strpos($key, '\\"'))) {
                    $quote = "'";
                }

                $line = '';
                if (array_key_exists($key, $globalMap)) {
                    // key exists!
                    if ($globalMap[$key] != $info['msg']) {
                        // same key different value!
                        $line = '#. ** WARNING: key exists with different translation : ';
                    } else {
                        $line = '#. ** DUPLICATE: ';
                    }
                }
                $globalMap[$key] = $info;

                // format the actual line
                $line .= $quote.$key.$quote.': '.$quote.$info['msg'].$quote;
                $lines[] = $line;
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Create a po(t) file from a l10n map.
     *
     * <p>This method operates only on the untranslated string. Translation itself happens further down the tool chain.</p>
     *
     * @param array map The map.
     * @param boolean pot Optional flag to indicate pot format (empty translations); default is <code>false</code>.
     * @return string The formatted po(t) content.
     */
    public static function map2po($map, $pot=false) {
        $lines = array();
        if (!$pot) {
            $lines[] = 'msgid ""';
            $lines[] = 'msgstr ""';
            $lines[] = '"Project-Id-Version: '.ZMSettings::get('zenmagick.version').'\n"';
            $lines[] = '"POT-Creation-Date: '.date().'\n"';
            $lines[] = '"PO-Revision-Date: \n"';
            $lines[] = '"Last-Translator: \n"';
            $lines[] = '"Language-Team: \n"';
            $lines[] = '"MIME-Version: 1.0\n"';
            $lines[] = '"Content-Type: text/plain; charset=UTF-8\n"';
            $lines[] = '"Content-Transfer-Encoding: 8bit\n"';
            $lines[] = '';
        }

        // build a unique list of strings

        $globalMap = array();
        foreach ($map as $filename => $strings) {
            if (null === $strings) {
                continue;
            }

            foreach ($strings as $key => $info) {
                if (!array_key_exists($key, $globalMap)) {
                    $globalMap[$key] = array();
                }
                $info['filename'] = $filename;
                $globalMap[$key][] = $info;
            }
        }

        // process
        $quote = '"';
        foreach ($globalMap as $string => $infos) {
            $location = '#:';
            foreach ($infos as $info) {
                $location .= ' '.$info['filename'].':'.$info['line'];
            }
            $lines[] = $location;

            // preformat string
            $string = stripslashes($string);
            $string = str_replace('"', '\"', $string);

            // newline in string?
            $nl = 0 < substr_count($string, "\n");
            if (!$nl) {
                $string = '"'.trim($string).'"';
            } else {
                $tmp = '""'."\n";
                foreach (explode("\n", $string) as $sl) {
                    $tmp .= '"'.trim($sl).'"'."\n";
                }
                $string = $tmp;
            }

            // format the actual line(s)
            if (null != $info['context']) {
                $lines[] = 'msgctxt '.$info['context'];
            }
            $lines[] = 'msgid '.$string;
            if (null != $info['plural']) {
                $lines[] = 'msgid_plural '.$info['plural'];
                if ($pot) {
                    $lines[] = 'msgstr[0] ""';
                    $lines[] = 'msgstr[1] ""';
                }
            } else {
                $lines[] = $pot ? 'msgstr ""' : 'msgstr '.$string;
            }

            $lines[] = '';
        }

        return implode("\n", $lines);
    }

    /**
     * Resolve a locale path.
     *
     * <p>The path given is assumed to contain the full locale as specified in the <code>$locale</code> parameter.</p>
     * <p>The function will validate the path and if not valid will default to using just the language.</p>
     *  
     * @param string path The full path.
     * @param string locale The locale.
     * @return string A valid path or <code>null</code>.
     *
     */
    public static function resolvePath($path, $locale) {
        if (file_exists($path)) {
            return $path;
        }

        $lt = explode('_', $locale);
        if (2 > count($lt)) {
            return null;
        }

        // try language
        $path = str_replace($locale, $lt[0], $path);
        if (file_exists($path)) {
            return $path;
        }

        return null;
    }

    /**
     * Format a date as short.
     *
     * @param Date date A date.
     * @param string format Optional format string to override the format provided by the active <code>ZMLocale</code>; default is <code>null</code>.
     * @return string A short version.
     */
    public static function dateShort($date, $format=null) {
        if ($date instanceof DateTime) {
            $format = null != $format ? $format : ZMLocales::instance()->getLocale()->getFormat('date', 'short');
            return $date->format($format);
        }

        return $date;
    }

    /**
     * Format a date as long.
     *
     * @param Date date A date.
     * @param string format Optional format string to override the format provided by the active <code>ZMLocale</code>; default is <code>null</code>.
     * @return string A long version.
     */
    public static function dateLong($date, $format=null) {
        if ($date instanceof DateTime) {
            $format = null != $format ? $format : ZMLocales::instance()->getLocale()->getFormat('date', 'long');
            return $date->format($format);
        }

        return $date;
    }

    /**
     * Convenience method to lookup a locale format.
     *
     * @param string group The group.
     * @param string type Optional type.
     * @return string The format or <code>null</code>
     * @see ZMLocale::getFormat(string,string)
     */
    public static function getFormat($group, $type=null) {
        return ZMLocales::instance()->getLocale()->getFormat($group, $type);
    }

}
