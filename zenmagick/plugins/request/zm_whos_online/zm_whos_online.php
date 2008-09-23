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
 * Provide information about current site users.
 *
 * @package org.zenmagick.plugins.zm_whos_online
 * @author DerManoMann
 * @version $Id$
 */
class zm_whos_online extends ZMPlugin {

    /**
     * Create new instance.
     */
    function __construct() {
        parent::__construct('Who\'s online', 'Provide inormation about current site users', '${plugin.version}');
        $this->setLoaderSupport('ALL');
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }

    /**
     * Init this plugin.
     */
    function init() {
        parent::init();

        ZMDbTableMapper::instance()->setMappingForTable('whos_online', array(
            'accountId' => 'column=customer_id;type=integer',
            'fullName' => 'column=full_name;type=string',
            'sessionId' => 'column=session_id;type=string',
            'ipAddress' => 'column=ip_address;type=string',
            'sessionStartTime' => 'column=time_entry;type=string',
            'lastRequestTime' => 'column=time_last_click;type=string',
            'lastUrl' => 'column=last_page_url;type=string',
            'hostAddress' => 'column=host_address;type=string',
            'userAgent' => 'column=user_agent;type=string',
        ));

        // register tests
        if (null != ($tests = ZMPlugins::instance()->getPluginForId('zm_tests'))) {
            // add class path only now to avoid errors due to missing ZMTestCase
            ZMLoader::instance()->addPath($this->getPluginDir().'tests/');
            $tests->addTest('TestWhosOnline');
        }
    }

    /**
     * Get stats about currently online users.
     *
     * <p>The returned array has three elements. The first contains the  count of anonymous users,
     the second the number of registered users online and the third the total number of online users.</p>
     *
     * @param boolean expire Optional expire flag to delete old entries; default is <code>true</code>.
     * @return array Online user stats.
     */
    public function getStats($expire=true) {
        if ($expire) {
            // expire old entries
            $timeAgo = (time() - 1200);
            $sql = "DELETE FROM " . TABLE_WHOS_ONLINE . "
                    WHERE time_last_click < :lastRequestTime";
            ZMRuntime::getDatabase()->update($sql, array('lastRequestTime' => $timeAgo), TABLE_WHOS_ONLINE);
        }

        $sql = "SELECT customer_id FROM " . TABLE_WHOS_ONLINE;
        $results = ZMRuntime::getDatabase()->query($sql, array(), TABLE_WHOS_ONLINE, ZM_DB_MODEL_RAW);
        $anonymous = 0;
        $registered = 0;
        foreach ($results as $result) {
            if (0 != $result['customer_id']) {
                ++$registered;
            } else {
                ++$anonymous;
            }
        }

        return array($anonymous, $registered, ($anonymous+$registered));
    }

}

?>