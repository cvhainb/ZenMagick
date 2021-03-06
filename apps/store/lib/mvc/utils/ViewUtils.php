<?php
/*
 * ZenMagick - Another PHP framework.
 * Copyright (C) 2006,2010 zenmagick.org
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
 * View utils.
 *
 * @author DerManoMann
 * @package zenmagick.store.sf.mvc.utils
 */
class ViewUtils extends ZMViewUtils {

    /**
     * Create new instance.
     *
     * @param ZMView view The current view.
     */
    function __construct($view) {
        parent::__construct($view);
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
    public function resolveResource($filename) {
        $request = $this->getView()->getVar('request');
        // look in template path, not resource!
        return $this->getView()->asUrl($request, $filename, ZMView::TEMPLATE);
    }

}
