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
 * Admin controller for cache admin.
 *
 * @author DerManoMann
 * @package zenmagick.store.admin.mvc.controller
 * @todo move hash calculation into controller
 */
class ZMCacheAdminController extends ZMController {

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
    public function getViewData($request) {
        return array('providers' => ZMCaches::instance()->getProviders());
    }

    /**
     * {@inheritDoc}
     */
    public function processGet($request) {
        return $this->findView();
    }

    /**
     * {@inheritDoc}
     */
    public function processPost($request) {
        if ($request->handleDemo()) {
            return $this->findView('success-demo');
        }

        foreach (ZMCaches::instance()->getProviders() as $type => $provider) {
            $stats = $provider->getStats();
            foreach ($stats['system']['groups'] as $group => $config) {
                $hash = md5($type.$group.implode($config));
                if ('x' == $request->getParameter('cache_'.$hash)) {
                    $cache = ZMCaches::instance()->getCache($group, $config, $type);
                    $result = $cache->clear();
                    $msg = 'Clear cache \'%s\' ' . ($result ? 'successful' : 'failed');
                    ZMMessages::instance()->add(sprintf(_zm($msg), $type.'/'.$group), ($result ? 'success' : 'error'));
                }
            }
        }

        return $this->findView('success');
    }

}
