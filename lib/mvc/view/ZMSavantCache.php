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
 * Savant cache interface.
 *
 * <p>Implementations are free to cache individual Savant templates and their output.</p>
 *
 * @author DerManoMann
 * @package org.zenmagick.mvc.view
 */
interface ZMSavantCache {

    /**
     * Get cached template.
     *
     * @param string tpl The template name.
     * @return string The cache contents or <code>null</code>.
     */
    public function lookup($tpl);

    /**
     * Save template contents.
     *
     * @param string tpl The template name.
     * @param string contents The evaluated contents.
     */
    public function save($tpl, $contents);

}
