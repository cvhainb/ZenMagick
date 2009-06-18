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

if (!function_exists('__autoload')) {
    /**
     * __autoload.
     */
    function __autoload($className) {
        ZMLoader::resolve($className, true);
    }
}


/**
 * ZenMagick code/class loader.
 *
 * <p>Loader might be chained to allow delegation of theme loading. All loader
 * implement a <em>parent first</em> strategy.</p>
 *
 * <p>Classes in ZenMagick have to adhere to the following conventions:</p>
 * <ul>
 *  <li>ZenMagick classes start always with the prefix <em>ZM</em></li>
 *  <li>Filenames have to reflect the contained class; this is <strong>case sensitive</strong>
 *  </li>
 *  <li>There is always one class per file</li>
 *  <li>Custom classes use the name of the parent class without the <em>ZM</em> prefix;
 *    For example a custom index controller would extend <code>ZMIndexController</code> and
 *    be named <code>IndexController</code>
 *  </li>
 *  <li>Classes are created using the class loader's <code>makeClass(..)</code> method</li>
 *  <li>Parent classes following the above conventions will be automatically resolved</li>
 * </ul>
 *
 * <p><strong>Note:</strong> This is not as scalable as Java code and does not handle more than on
 * level of inheritance.</p>
 *
 * <p><strong>Static methods operate all on the root loader.</strong></p>
 *
 * @author DerManoMann
 * @package org.zenmagick.core
 * @version $Id: ZMLoader.php 2172 2009-04-22 00:19:36Z dermanomann $
 */
class ZMLoader {
    private static $root_ = null;
    private $parent_;
    private $path_;
    private $classPrefix_ = 'ZM';
    private $global_;
    private $cache_;
    private $stats_;


    /**
     * Create a new loader.
     * 
     * @param string prefix Optional prefix to be used for class resolving; default is <em>ZM</em>.
     */
    public function __construct($prefix='ZM') {
        $this->classPrefix_ = $prefix;
        $this->parent_ = null;
        $this->path_ = array();
        $this->global_ = array();
        $this->cache_ = array();
        $this->stats_ = array('static' => 0, 'class' => 0);
    }


    /**
     * Get the root loader.
     *
     * @param string prefix Optional prefix to be used for class resolving; default is <em>ZM</em>.
     * @return ZMLoader The root loader.
     */
    public static function instance($prefix='ZM') {
        if (null == ZMLoader::$root_) {
            ZMLoader::$root_ = new ZMLoader($prefix);
        }
        return ZMLoader::$root_;
    }


    /**
     * Get the class path.
     *
     * @param boolean includeParent If <code>true</code> include the parent loader path: default is <code>true</code>.
     * @return array Class path array.
     */
    public function getClassPath($includeParent=true) {
        $classPath = array_merge($this->path_);
        if ($includeParent && null != $this->parent_) {
            $classPath = array_merge($this->parent_->getClassPath(), $classPath);
        }

        return $classPath;
    }

    /**
     * Get the class file for the given class name.
     *
     * @param string name The class name without the <em>ZM</em> prefix.
     * @return string The class filename that or <code>null</code>.
     */
    protected function getClassFile($name) {
        $filename = null;
        if (null != $this->parent_) {
            $filename = $this->parent_->getClassFile($name);
        }

        return null != $filename ? $filename : (isset($this->path_[$name]) ? $this->path_[$name] : null);
    }

    /**
     * Add a given path to the loaders path.
     *
     * @param string path The path to add.
     * @param boolean recursive Flag to indicate if the path should be scanned recursively.
     */
    public function addPath($path, $recursive=true) {
        $this->path_ = array_merge($this->path_, $this->scan($path, $recursive));
    }

    /**
     * Manually register a class.
     *
     * @param string clazz The class name.
     * @param string filename The filename.
     */
    public function registerClass($clazz, $filename) {
        $this->path_[$clazz] = $filename;
    }

    /**
     * Set the parent loader.
     */
    public function setParent($parent) {
        $last = $this;
        while (null != $last->parent_) {
            $last = $last->parent_;
        }
        $last->parent_ = $parent;
    }

    /**
     * Add a file to be loaded in global context.
     *
     * @param string filename The file to load.
     */
    public function addGlobal($filename) {
        $this->global_[] = $filename;
    }

    /**
     * Get files to be loaded in global context.
     *
     * @param array List of filenames.
     */
    public function getGlobal() {
        $list = $this->global_;
        if (null != $this->parent_) {
            $list = array_merge($list, $this->parent_->getGlobal());
        }
        return $list;
    }

    /**
     * Load all available static code.
     *
     * <p><strong>Note:</strong> Using this is intended to load functions, defines, etc. As this
     * is loaded inside a method, variables inside static files will not be real globals.</p>
     */
    public function loadStatic() {
        foreach ($this->getStatic() as $static) {
            ++$this->stats_['static'];
            require_once $static;
        }
    }

    /**
     * Returns a list of all the static code in this loaders path. Code is identified by a filename starting with
     * a lower case character.
     *
     * <p>Note: This is an instance specific method. There is no delegation to a parent loader.</p>
     *
     * @return array Static files with local.php being the first (if it exists).
     */
    public function getStatic() {
        $static = array();
        // get full list
        foreach ($this->path_ as $name => $file) {
            if ($name == $file) {
                $static[$name] = $file;
            }
        }

        if (array_key_exists('local', $static)) {
            // get local to top 
            $tmp = array();
            $tmp[] = $static['local'];
            unset($static['local']);
            foreach ($static as $name => $file) {
                $tmp[] = $file;
            }
            $static = $tmp; 
        }

        return $static;
    }

    /**
     * Shortcut version of <code>ZMLoader::instance()->resolveClass($name)</code>.
     *
     * @param string name The class name (without the <em>ZM</em> prefix).
     * @param boolean isAutoload Optional boolean to indicate that this is an autoload call; default is <code>false</code>.
     * @return string The resolved class name; this is either the given name, the ZenMagick default
     *  implementation or <code>null</code>.
     */
    public static function resolve($name, $isAutoLoad=false) {
        return ZMLoader::instance()->resolveClass($name);
    }

    /**
     * Resolve and load the class given.
     *
     * @param string name The class name (without the <em>ZM</em> prefix).
     * @param boolean isAutoload Optional boolean to indicate that this is an autoload call; default is <code>false</code>.
     * @return string The resolved class name or <code>null</code>.
     */
    private function resolveFromClassPath($name, $isAutoLoad=false) {
        $classfile = $this->getClassFile($name);
        if (null != $classfile) {
            // we know about the class
            if ($isAutoLoad || (!class_exists($name) && !interface_exists($name))) {
                ++$this->stats_['class'];
                require_once $classfile;
            }
            return $name;
        }

        // not in path
        return null;
    }

    /**
     * Resolve and load the class code for the given class name.
     *
     * @param string name The class name (without the <em>ZM</em> prefix).
     * @param boolean isAutoload Optional boolean to indicate that this is an autoload call; default is <code>false</code>.
     * @return string The resolved class name; this is either the given name, the ZenMagick default
     *  implementation or <code>null</code>.
     */
    public function resolveClass($name, $isAutoLoad=false) {
        if (isset($this->cache_[$name])) {
            return $this->cache_[$name];
        }
        if (0 === strpos($name, $this->classPrefix_)) {
            if (!$isAutoLoad && (class_exists($name) || interface_exists($name))) {
                $this->cache_[$name] = $name;
                return $name;
            }
            return $this->resolveFromClassPath($name);
        }

        $classname = $this->resolveFromClassPath($name);

        if (null != $classname || (!$isAutoLoad && (class_exists($name) || interface_exists($name)))) {
            // XXX: get rid of
            // non prefix class exists, now make sure it's a ZenMagick class 
            // to avoid conflicts with zen cart class names
            $parent = $classname;
            while (false !== ($parent = get_parent_class($parent))) {
                if (0 === strpos($parent, $this->classPrefix_)) {
                    $this->cache_[$name] = $classname;
                    return $classname;
                }
            }
        }

        // default to prefixed name
        return $this->resolveClass($this->classPrefix_.$name);
    }

    /**
     * Shortcut for creating new class instances.
     *
     * <p>Please note that is it also possible to pass just a single parameter (array) that contains the class name
     * (first element) and optionally constructor arguments (second, third,..).</p>
     *
     * @param string name The class name (without the <em>ZM</em> prefix).
     * @param var arg Optional constructor arguments.
     * @return mixed A new instance of the given class.
     */
    public static function make($args) {
        if (!is_array($args)) {
            $args = func_get_args();
        }
        return ZMLoader::instance()->makeClass($args);
    }

    /**
     * Resolve, load and instantiate a new instance of the given class.
     *
     * @param string name The class name (without the <em>ZM</em> prefix).
     * @param var arg Optional constructor arguments.
     * @return mixed A new instance of the given class.
     */
    protected function makeClass($name) {
        if (is_array($name)) {
            $tmp = $name;
            $name = array_shift($tmp);
            $args = $tmp;
        } else {
            $args = func_get_args();
            array_shift($args);
        }
        $clazz = $this->resolveClass($name);
        if (null != $clazz) {
            if (!class_exists($clazz)) {
                throw new ZMException('class not found ' . $clazz);
            }
            $obj = null;
            switch (count($args)) {
            case 0:
                $obj = new $clazz();
                break;
            case 1:
                $obj = new $clazz($args[0]);
                break;
            case 2:
                $obj = new $clazz($args[0], $args[1]);
                break;
            case 3:
                $obj = new $clazz($args[0], $args[1], $args[2]);
                break;
            case 4:
                $obj = new $clazz($args[0], $args[1], $args[2], $args[3]);
                break;
            case 5:
                $obj = new $clazz($args[0], $args[1], $args[2], $args[3], $args[4]);
                break;
            default:
                throw new ZMException('unsupported number of constructor arguments ' . $clazz);
            }
            return $obj;

        }
        return null;
    }


    /**
     * Scan (recursively) for <code>.php</code> files.
     *
     * <p>It is worth mentioning that directories will always be processed only after
     * all plain files in a directory are done.</p>
     *
     * @param string dir The name of the root directory to scan.
     * @param string ext Optional file suffix/extension; default is <em>.php</em>.
     * @param boolean recursive If <code>true</code>, scan recursively.
     * @return array List of full filenames of <code>.php</code> files.
     */
    public static function findIncludes($dir, $ext='.php', $recursive=false, $level=0) {
        // sanity check
        if (!is_dir($dir) || false !== strpos($dir, '.svn')) {
            return $includes;
        }

        $includes = array();

        $handle = @opendir($dir);
        while (false !== ($name = readdir($handle))) { 
            if ("." == $name || ".." == $name || ".svn" == $name) {
                continue;
            }
            $file = $dir.$name;
            if (is_dir($file) && $recursive) {
                $includes = array_merge($includes, self::findIncludes($file.DIRECTORY_SEPARATOR, $ext, $recursive, $level+1));
            } else if ($ext == substr($name, -strlen($ext))) {
                $includes[] = $file;
            }
        }
        @closedir($handle);

        return $includes;
    }


    /**
     * Scan the given path for PHP files.
     *
     * @param string path The path to scan.
     * @param boolean recursive Flag to indicate if the path should be scanned recursively.
     * @return array A file map for the given path.
     */
    protected function scan($path, $recursive=true) {
        $files = ZMLoader::findIncludes($path, '.php', $recursive);
        $map = array();
        foreach ($files as $file) {
            $name = str_replace('.php', '', basename($file));
            // support for Name.class.php style
            $name = str_replace('.class', '', $name);
            if ($name == strtolower($name)) {
                // static, so make it unique
                $name = $file;
            }
            $map[$name] = $file;
        }

        return $map;
    }

    /**
     * Normalize class names based on the filename
     *
     * <p>This is pretty much following Java conventions.</p>
     *
     * @param string filename The filename.
     * @return string A corresponding class name.
     */
    public static function makeClassname($filename) {
        // strip potential file extension and dir
        $classname = str_replace('.php', '', basename($filename));
        // '_' == word boundary
        $classname = str_replace(array('_', '-'), ' ', $classname);
        // capitalise words
        $classname = ucwords($classname);
        // cuddle together :)
        $classname = str_replace(' ', '', $classname);
        return $classname;
    }

    /**
     * Get loader stats.
     *
     * @param all Optional parameter to indicate that stats of all loaders should be returned.
     */
    public function getStats($all=true) {
        $list = array('static' => $this->stats_['static'], 'class' => $this->stats_['class']);
        if ($all && null != $this->parent_) {
            $plist = $this->parent_->getStats(true);
            $list['static'] += $plist['static'];
            $list['class'] += $plist['class'];
        }
        return $list;
    }

}

?>