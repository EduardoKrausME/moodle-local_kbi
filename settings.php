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
 * setting file
 *
 * @package   local_kbi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $settings = new admin_settingpage("kbi", get_string("pluginname", "local_kbi"));
    $ADMIN->add("localplugins", $settings);

    if (!$ADMIN->locate("integracaoroot")) {
        $ADMIN->add("root", new admin_category("integracaoroot", get_string("integracaoroot", "local_kbi")));
    }

    $ADMIN->add("integracaoroot",
        new admin_externalpage(
            "local_kbi",
            get_string("modulename", "local_kbi"),
            "{$CFG->wwwroot}/local/kopere_dashboard/open.php?classname=dashboard&method=start"
        )
    );
}

if ($ADMIN->fulltree) {
    if (method_exists($settings, "add")) {
        $removetitle = true;
        require(__DIR__ . "/settings_kopere.php");

        $settings->add(
            new admin_setting_heading("kbi_title", "", "")
        );
    }
}
