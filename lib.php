<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * lib file
 *
 * @package   local_kbi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_kbi\core_hook_output;
use local_kbi\local\util\filter;
use local_kbi\local\util\string_util;
use local_kbi\local\vo\local_kbi_block;
use local_kbi\local\vo\local_kbi_page;

/**
 * Function local_kbi_before_footer
 *
 * @throws coding_exception
 */
function local_kbi_before_footer() {
    core_hook_output::before_footer_html_generation();
}

/**
 * Function getremoteaddr
 *
 * @return string
 */
function local_kbi_getremoteaddr() {
    if (isset($_SERVER["HTTP_X_REAL_IP"])) {
        return $_SERVER["HTTP_X_REAL_IP"];
    } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
        return $_SERVER["HTTP_CLIENT_IP"];
    }

    return getremoteaddr();
}

/**
 * Function local_kbi_iplookup_find_location
 *
 * @param $ip
 *
 * @return object
 */
function local_kbi_iplookup_find_location($ip) {
    global $CFG;

    require_once("{$CFG->dirroot}/iplookup/lib.php");

    $cache = \cache::make("local_kbi", "ip_user_location");

    if ($cache->has($ip)) {
        $dataip = $cache->get($ip);
    } else {
        $dataip = (object)iplookup_find_location($ip);
        $cache->set($ip, $dataip);
    }

    return (object)$dataip;
}

/**
 * Function load_kbi_assets
 *
 * @return string
 */
function load_kbi_assets() {
    static $koperebiloaded = false;

    if (!$koperebiloaded) {
        $koperebiloaded = true;

        get_kopere_lang();

        return "";
    }

    return "";
}

/**
 *
 * @param $pageid
 *
 * @return string
 * @throws \ScssPhp\ScssPhp\Exception\SassException
 * @throws coding_exception
 * @throws dml_exception
 */
function load_kbi($pageid) {
    global $DB, $CFG;

    require_once("{$CFG->dirroot}/local/kopere_dashboard/autoload.php");

    $text = load_kbi_assets();

    $text .= "<div class='kopere_dashboard_div'>";
    $text .= "<div class='content-w'>";
    $text .= "<div class='content-i'>";
    $text .= "<div class='content-box'>";

    /** @var local_kbi_page $koperebipage */
    $koperebipage = $DB->get_record("local_kbi_page", ["id" => $pageid]);
    if ($koperebipage) {
        if ($koperebipage->description) {
            $text .= "<h2>" . string_util::get_string($koperebipage->description) . "</h2>";
        }

        $koperebiblocks = $DB->get_records("local_kbi_block", ["page_id" => $koperebipage->id], "sequence ASC");

        $text .= filter::create_filter($koperebipage);

        /** @var local_kbi_block $koperebiblock */
        foreach ($koperebiblocks as $koperebiblock) {
            $text .= (new \local_kbi\local\util\preview_util())->details_block($koperebiblock);
        }
    }

    $text .= "</div>";
    $text .= "</div>";
    $text .= "</div>";
    $text .= "</div>";

    return $text;
}

/**
 *
 * @return string
 * @throws Exception
 */
function load_kbi_ajax($coursemoduleid, $pageid) {
    global $CFG;

    require_once("{$CFG->dirroot}/local/kopere_dashboard/autoload.php");

    $text = load_kbi_assets();
    $text .= "<div class='kopere_dashboard_div-ajax'
                   id='kopere_dashboard_div-coursemodule_{$coursemoduleid}'
                   data-koperebi='{$pageid}'>" . get_string("loading", "local_kbi") . "</div>";

    return $text;
}
