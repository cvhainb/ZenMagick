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
 * A RSS feed.
 *
 * @author DerManoMann
 * @package org.zenmagick.mvc.rss
 */
class ZMRssFeed extends ZMObject {
    private $channel_;
    private $items_;


    /**
     * Create new RSS feed.
     */
    function __construct() {
        parent::__construct();
        $this->channel_ = null;
        $this->items_ = array();
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Get the channel.
     *
     * @return ZMRssChannel The channel.
     */
    public function getChannel() { return $this->channel_; }

    /**
     * Get the feed items.
     *
     * @return array A list of <code>ZMRssItem</code> instances.
     */
    public function getItems() { return $this->items_; }

    /**
     * Set the channel.
     *
     * @param ZMRssChannel channel The channel.
     */
    public function setChannel($channel) { $this->channel_ = $channel; }

    /**
     * Set the feed items.
     *
     * @param array items A list of <code>ZMRssItem</code> instances.
     */
    public function setItems($items) { $this->items_ = $items; }

}
