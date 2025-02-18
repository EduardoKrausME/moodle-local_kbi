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

namespace local_kbi\local\block;

use local_kbi\local\block\util\cache_util;
use local_kbi\local\block\util\code_util;
use local_kbi\local\block\util\database_util;
use local_kbi\local\block\util\reload_util;
use local_kbi\output\renderer_bi_mustache;
use local_kbi\local\util\sql_util;
use local_kdashboard\html\form;
use local_kdashboard\html\inputs\input_textarea;
use local_kdashboard\util\message;

/**
 * Class html
 *
 * @package   local_kbi
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class html implements i_type {

    /**
     * Function get_name
     *
     * @return string
     * @throws \coding_exception
     */
    public static function get_name() {
        return get_string("html_name", "local_kbi");
    }

    /**
     * Function get_description
     *
     * @return string
     * @throws \coding_exception
     */
    public static function get_description() {
        return get_string("html_desc", "local_kbi");
    }

    /**
     * Function title_extra
     *
     * @param $koperebielement
     *
     * @return string
     */
    public function title_extra($koperebielement) {
        return "";
    }

    /**
     * Function edit
     *
     * @param form $form
     * @param $koperebielement
     *
     * @throws \Exception
     */
    public function edit(form $form, $koperebielement) {
        global $PAGE;

        message::print_info(get_string("html_block_desc", "local_kbi"));

        $form->add_input(
            input_textarea::new_instance()
                ->set_title(get_string("html_block", "local_kbi"))
                ->set_style("width:100%;font-family:monospace;white-space:nowrap;")
                ->set_name("infohtml")
                ->set_value(@$koperebielement->info_obj["html"]));
        $PAGE->requires->js_call_amd("local_kbi/load_ace", "getScript", ["infohtml", "html"]);

        code_util::input_commandsql($form, $koperebielement);
    }

    /**
     * Function is_edit_columns
     *
     * @return bool
     */
    public function is_edit_columns() {
        return false;
    }

    /**
     * Function edit_columns
     *
     * @param form $form
     * @param $koperebielement
     */
    public function edit_columns(form $form, $koperebielement) {
    }

    /**
     * Function preview
     *
     * @param $koperebielement
     *
     * @return mixed
     * @throws \coding_exception
     */
    public function preview($koperebielement) {
        global $OUTPUT;

        return $OUTPUT->render_from_template("local_kbi/block_html_preview", [
            "ajax_url" => local_kdashboard_makeurl("bi-chart_data", "load_data",
                ["item_id" => $koperebielement->id], "view-ajax"),
            "local_kbi_id" => $koperebielement->id,
            "error_data_loader" => get_string("error_data_loader", "local_kbi"),
            "reload_time" => reload_util::convert($koperebielement->reload),
        ]);
    }

    /**
     * Function get_chart_data
     *
     * @param $koperebielement
     *
     * @throws \Exception
     */
    public function get_chart_data($koperebielement) {

        ob_clean();
        header('Content-Type: application/json; charset: utf-8');

        $cache = cache_util::get_cache_make($koperebielement->cache);

        if (false && $cache->has($koperebielement->id)) {
            $returnhtml = $cache->get($koperebielement->id);
        } else {
            $comand = sql_util::prepare_sql($koperebielement->commandsql);

            $mustache = new renderer_bi_mustache();
            $html = $koperebielement->info_obj["html"];
            if (strpos($html, "{{#lines}}") === false) {
                try {
                    $line = (new database_util())->get_record_sql_block($comand->sql, $comand->params);
                } catch (\Exception $e) {
                    echo json_encode([
                        "sql" => $comand->sql,
                        "html" => message::danger($e->getMessage()),
                        "trace" => $e->getTrace(),
                    ]);
                    die();
                }
                $returnhtml = $mustache->render_from_string($html, $line);
            } else {
                try {
                    $lines = (new database_util())->get_records_sql_block($comand->sql, $comand->params);
                } catch (\Exception $e) {
                    echo json_encode([
                        "sql" => $comand->sql,
                        "html" => message::danger($e->getMessage()),
                        "trace" => $e->getTrace(),
                    ]);
                    die();
                }
                $returnhtml = $mustache->render_from_string($html, ["lines" => json_decode(json_encode($lines))]);
            }

            $cache->set($koperebielement->id, $returnhtml);
        }

        echo json_encode(["html" => $returnhtml]);
        die();
    }

    /**
     * https://developers.google.com/chart/interactive/docs/gallery/table?hl=pt_br
     *
     * @param $koperebielement
     *
     * @return string
     * @throws \Exception
     */
    public function preview_google($koperebielement) {
        global $OUTPUT;

        $addcolumn = [];
        $formatter = [];

        $comand = sql_util::prepare_sql($koperebielement->commandsql);
        try {
            $lines = (new database_util())->get_records_sql_block($comand->sql, $comand->params);
        } catch (\Exception $e) {
            if (AJAX_SCRIPT) {
                echo json_encode([
                    "sql" => $comand->sql,
                    "error" => $e->getMessage(),
                    "trace" => $e->getTraceAsString(),
                ]);
                die;
            } else {
                message::print_danger($e->getMessage());
                return "";
            }
        }

        $columns = array_keys((array)$lines[0]);

        foreach ($columns as $column) {
            $addcolumn[] = "data.addColumn('string', '{$column}');";
        }

        $linechart = [];
        foreach ($lines as $line) {
            $linereturn = [];
            foreach ($columns as $column) {

                $valor = $line->{$column};
                $linereturn[] = $valor;
            }
            $linechart[] = $linereturn;
        }

        return $OUTPUT->render_from_template("local_kbi/block_html_preview_google", [
            "koperebiitem_id" => $koperebielement->id,
            "column" => implode("\n\t\t\t\t", $addcolumn),
            "linechart" => json_encode($linechart, JSON_PRETTY_PRINT),
            "formatter" => implode("\n\t\t\t\t", $formatter),
        ]);
    }
}
