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
 * Packer for the <em>Savant3</em> library.
 *
 * @author DerManoMann
 * @package org.zenmagick.core.build
 */
class ZMSavant3Packer extends ZMPhpPackagePacker implements ZMLibraryPacker {

    /**
     * {@inheritDoc}
     */
    public function process($sourceDir, $targetDir, $version, $strip) {
        $this->rootFolder_ = $sourceDir;
        $this->outputFilename_ = $targetDir.'savant-'.$version.'.packed.php';

        // run the parent package packer; strip/leave references
        $this->packFiles($strip, true);
    }

    /**
     * {@inheritDoc}
     */
    public function dropInclude($line) {
        return false !== strpos($line, 'Savant3') || false !== strpos($line, 'dirname(__FILE__)');
    }

    /**
     * {@inheritDoc}
     */
    protected function getFileList() {
        // just Savant3.php in the rootFolder and the classes in the Savant3 folder, but not resources and not tests
        return array_merge(
            ZMFileUtils::findIncludes($this->rootFolder_, '.php', false),
            ZMFileUtils::findIncludes($this->rootFolder_.'Savant3'.DIRECTORY_SEPARATOR, '.php', false)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function patchFile($filename, $lines) {
        if ('Savant3.php' == basename($filename)) {
            foreach ($lines as $ii => $line) {
                if (false !== strpos($line, '$arg1 = @func_get_arg(1);')) {
                    $lines[$ii] = '$arg1 = 1 < func_num_args() ? @func_get_arg(1) : null;';
                    break;
                }
            }
            return $lines;
        }
        return null;
    }

}
