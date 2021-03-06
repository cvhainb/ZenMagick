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
 * Compress lib/core into a single file 'core.php'.
 *
 * @author DerManoMann
 * @package zenmagick.store.admin.utils
 */
class ZMCoreCompressor extends ZMPhpPackagePacker {

    /**
     * Create new instance.
     */
    function __construct() {
        parent::__construct(null, ZMRuntime::getInstallationPath().'core.php', ZMRuntime::getInstallationPath().'core.tmp');
        $this->setResolveInheritance(true);
        $this->setDebug(false);
    }


    /**
     * Disable / remove the core.php file, effectively disabling the use of it.
     */
    public function disable() {
        @unlink(ZMRuntime::getInstallationPath().'core.php');
    }

    /**
     * check if enabled.
     *
     * @return boolean <code>true</code> if core.php exists, <code>false</code> if not.
     */
    public function isEnabled() {
        return file_exists(ZMRuntime::getInstallationPath().'core.php');
    }

    /**
     * Get errors.
     *
     * @return array List of text messages.
     */
    public function getErrors() {
        return array();
    }

    /**
     * Check for errors.
     *
     * @return boolean <code>true</code> if errors exist.
     */
    public function hasErrors() {
        return 0 != count($this->getErrors());
    }

    /**
     * {@inheritDoc}
     */
    protected function getFileList() {
        $pathList = array(
            ZMFileUtils::mkPath(array(ZMRuntime::getInstallationPath(), 'lib', 'core')),
            ZMFileUtils::mkPath(array(ZMRuntime::getInstallationPath(), 'lib', 'mvc')),
            ZMFileUtils::mkPath(array(ZMRuntime::getInstallationPath(), ZM_SHARED)),
            ZMFileUtils::mkPath(array(ZMRuntime::getInstallationPath(), 'apps', 'store', 'lib'))
        );

        $files = array();
        foreach ($pathList as $path) {
            $pathFiles = ZMFileUtils::findIncludes($path, '.php', true);
            $files = array_merge($files, $pathFiles);
        }

        $pluginFiles = $this->getPluginFiles();
        return array_merge($files, $pluginFiles);
    }

    /**
     * Build list of all plugin files to be included.
     *
     * @param array File list.
     */
    private function getPluginFiles() {
        $pluginFiles = array();
        foreach (ZMPlugins::instance()->getAllPlugins() as $plugin) {
            if (!$plugin->isEnabled()) {
                continue;
            }
            $flag = $plugin->getLoaderPolicy();
            $pluginDir = $plugin->getPluginDirectory();
            if (ZMPlugin::LP_NONE == $flag) {
                // plugin class only
                $pluginFiles[] = $pluginDir.get_class($plugin).'.php';
            } else {
                if (ZMPlugin::LP_LIB == $flag) {
                    // plugin class as well
                    $pluginFiles[] = $pluginDir.get_class($plugin).'.php';
                    $files = ZMFileUtils::findIncludes($pluginDir.'lib'.DIRECTORY_SEPARATOR, '.php', true);
                } else {
                    $files = ZMFileUtils::findIncludes($pluginDir, '.php', ZMPlugin::LP_FOLDER != $flag);
                }
                foreach ($files as $file) {
                    $fileBase = str_replace($pluginDir, '', $file);
                    $relDir = dirname($fileBase).DIRECTORY_SEPARATOR;
                    if ('.'.DIRECTORY_SEPARATOR == $relDir) {
                        $relDir = '';
                    }
                    if (false === ($source = file_get_contents($file))) {
                        ZMLogging::instance()->log('unable to read plugin source: '.$file, ZMLogging::WARN);
                        continue;
                    }
                    $pluginFiles[] = $file;
                }
            }
        }

        return $pluginFiles;
    }

}
