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
 * service file
 *
 * @package   local_kbi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$functions = [
    "local_kbi_cat_sortorder" => [
        "classpath" => "local/kbi/classes/external/categorie.php",
        "classname" => "\\local_kbi\\external\\categorie",
        "methodname" => "sortorder",
        "description" => "Saves the sortorder of the categories",
        "type" => "write",
        "ajax" => true,
        "capabilities" => "local/kopere_dashboard:view",
    ],
    "local_kbi_block_sequence" => [
        "classpath" => "local/kbi/classes/external/block.php",
        "classname" => "\\local_kbi\\external\\block",
        "methodname" => "sequence",
        "description" => "Saves the sequence of blocks on the page",
        "type" => "write",
        "ajax" => true,
        "capabilities" => "local/kopere_dashboard:view",
    ],
    "local_kbi_block_delete" => [
        "classpath" => "local/kbi/classes/external/block.php",
        "classname" => "\\local_kbi\\external\\block",
        "methodname" => "delete",
        "description" => "Deletes a block from the page",
        "type" => "write",
        "ajax" => true,
        "capabilities" => "local/kopere_dashboard:view",
    ],
    "local_kbi_block_add" => [
        "classpath" => "local/kbi/classes/external/block.php",
        "classname" => "\\local_kbi\\external\\block",
        "methodname" => "add",
        "description" => "Adds a new block to the page",
        "type" => "write",
        "ajax" => true,
        "capabilities" => "local/kopere_dashboard:view",
    ],
    "local_kbi_online_update" => [
        "classpath" => "local/kbi/classes/external/online_update.php",
        "classname" => '\local_kbi\external\online_update',
        "methodname" => "api",
        "description" => 'Records the time spent by a user for the dashboard',
        "type" => "write",
        "ajax" => true,
    ],
    "local_kbi_page_html" => [
        "classpath" => "local/kbi/classes/external/page_html.php",
        "classname" => '\local_kbi\external\page_html',
        "methodname" => "api",
        "description" => 'Records the time spent by a user for the dashboard',
        "type" => "write",
        "ajax" => true,
    ],
];
