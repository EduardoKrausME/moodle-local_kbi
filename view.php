<?php

require_once("../../config.php");

//require_once("db/db-config.php");
//reset_bi_reports();

$month = 12;

if ($CFG->dbtype == "mysqli" || $CFG->dbtype == "mariadb") {
    $where = 'currenttime < DATE_SUB(NOW(), INTERVAL :month MONTH)';
    $DB->delete_records_select("local_kbi_online", $where, ["month" => $month]);
} else if ($CFG->dbtype == "pgsql") {
    $where = "currenttime < :month";
    $time = strtotime("-{$month} months", time());
    $DB->delete_records_select("local_kbi_online", $where, ["month" => $time]);
} else {
    mtrace("only mysqli and pgsql");
    return;
}
