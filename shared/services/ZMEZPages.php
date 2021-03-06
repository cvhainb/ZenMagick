<?php
/*
 * ZenMagick - Smart e-commerce
 * Copyright (C) 2006-2010 zenmagick.org
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
 * EZ-pages.
 *
 * @author DerManoMann
 * @package zenmagick.store.shared.services
 */
class ZMEZPages extends ZMObject {

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
     * Get instance.
     */
    public static function instance() {
        return ZMRuntime::singleton('EZPages');
    }


    /**
     * Get all pages for the given language.
     *
     * @param int languageId The languageId.
     * @return array List of <code>ZMEZPage</code> instances.
     */
    public function getAllPages($languageId) {
        $sql = "SELECT * 
                FROM " . TABLE_EZPAGES;
        if (ZMSettings::get('isEZPagesLangSupport')) {
            $sql .= " WHERE languages_id = :languageId";
        }
        $sql .= " ORDER BY toc_sort_order, pages_title";
        return ZMRuntime::getDatabase()->query($sql, array('languageId' => $languageId), TABLE_EZPAGES, 'EZPage');
    }

    /**
     * Get page for id.
     *
     * @param int pageId The page id.
     * @param int languageId The languageId.
     * @return ZMEZPage A new instance or <code>null</code>.
     */
    public function getPageForId($pageId, $languageId) {
        $sql = "SELECT * 
                FROM " . TABLE_EZPAGES . "
                WHERE pages_id = :id";
        if (ZMSettings::get('isEZPagesLangSupport')) {
            $sql .= " AND languages_id = :languageId";
        }
        return ZMRuntime::getDatabase()->querySingle($sql, array('id' => $pageId, 'languageId' => $languageId), TABLE_EZPAGES, 'EZPage');
    }

    /**
     * Get all pages for for a given chapter.
     *
     * @param int chapterId The chapter id.
     * @param int languageId The languageId.
     * @return array List of <code>ZMEZPage</code> instances.
     */
    public function getPagesForChapterId($chapterId, $languageId) {
        $sql = "SELECT *
                FROM " . TABLE_EZPAGES . " 
                WHERE ((status_toc = 1 AND toc_sort_order <> 0) AND toc_chapter= :tocChapter)
                AND alt_url_external = '' AND alt_url = ''";
        if (ZMSettings::get('isEZPagesLangSupport')) {
            $sql .= " AND languages_id = :languageId";
        }
        $sql .= " ORDER BY toc_sort_order, pages_title";
        return ZMRuntime::getDatabase()->query($sql, array('tocChapter' => $chapterId, 'languageId' => $languageId), TABLE_EZPAGES, 'EZPage');
    }

    /**
     * Get all header pages.
     *
     * @param int languageId The languageId.
     * @return array List of <code>ZMEZPage</code> instances.
     */
    public function getPagesForHeader($languageId) {
        $sql = "SELECT * 
                FROM " . TABLE_EZPAGES . "
                WHERE status_header = 1
                  AND header_sort_order > 0";
        if (ZMSettings::get('isEZPagesLangSupport')) {
            $sql .= " AND languages_id = :languageId";
        }
        $sql .= " ORDER BY header_sort_order, pages_title";
        return ZMRuntime::getDatabase()->query($sql, array('languageId' => $languageId), TABLE_EZPAGES, 'EZPage');
    }

    /**
     * Get all sidebar pages.
     *
     * @param int languageId The languageId.
     * @return array List of <code>ZMEZPage</code> instances.
     */
    public function getPagesForSidebar($languageId) {
        $sql = "SELECT * 
                FROM " . TABLE_EZPAGES . "
                WHERE status_sidebox = 1
                  AND sidebox_sort_order > 0";
        if (ZMSettings::get('isEZPagesLangSupport')) {
            $sql .= " AND languages_id = :languageId";
        }
        $sql .= " ORDER BY sidebox_sort_order, pages_title";
        return ZMRuntime::getDatabase()->query($sql, array('languageId' => $languageId), TABLE_EZPAGES, 'EZPage');
    }

    /**
     * Get all footer pages.
     *
     * @param int languageId The languageId.
     * @return array List of <code>ZMEZPage</code> instances.
     */
    public function getPagesForFooter($languageId) {
        $sql = "SELECT * 
                FROM " . TABLE_EZPAGES . "
                WHERE status_footer = 1
                  AND footer_sort_order > 0";
        if (ZMSettings::get('isEZPagesLangSupport')) {
            $sql .= " AND languages_id = :languageId";
        }
        $sql .= " ORDER BY footer_sort_order, pages_title";
        return ZMRuntime::getDatabase()->query($sql, array('languageId' => $languageId), TABLE_EZPAGES, 'EZPage');
    }

    /**
     * Create a new page.
     *
     * @param ZMEZPage page The page to create.
     * @return ZMEZPage The updated (keys, etc) instance.
     */
    public function createPage($page) {
        return ZMRuntime::getDatabase()->createModel(TABLE_EZPAGES, $page);
    }

    /**
     * Update an existing page.
     *
     * @param ZMEZPage page The page to update.
     * @return boolean <code>true</code> for success.
     */
    public function updatePage($page) {
        ZMRuntime::getDatabase()->updateModel(TABLE_EZPAGES, $page);
        return true;
    }

    /**
     * Delete an existing page.
     *
     * @param ZMEZPage page The page to delete.
     * @return boolean <code>true</code> for success.
     */
    public function removePage($page) {
        ZMRuntime::getDatabase()->removeModel(TABLE_EZPAGES, $page);
        return true;
    }

}
