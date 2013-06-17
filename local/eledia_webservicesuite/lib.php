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
 * Get a moodle record by a unique idnumber
 *
 * @param string $table The table to search in
 * @param string $idnumber The idnumber to search for
 * @param string $notfound_errorcode The errorcode for the exception string when the record is not found
 * @param string $multiple_errorcode The errorcode for the exception string when the idnumber is not unique
 * @param string $errorcode_module The module name to get the exception strings
 * @return table obj, throws exceptions if idnumber is not unique or is not found
 */
function get_record_by_idnumber ($table,
        $idnumber,
        $notfound_exception = false,
        $multiple_exception = false,
        $notfound_errorcode = 'wsidnumbernotfound',
        $multiple_errorcode = 'wsmultipleidnumbersfound',
        $module = 'local_eledia_webservicesuite'){

    global $DB;
    if ($table == 'user') {
        $count = $DB->count_records($table, array('idnumber' => $idnumber, 'deleted' => 0));
    }else{
        $count = $DB->count_records($table, array('idnumber' => $idnumber));
    }

    switch ($count) {
        case 0:
            if ($notfound_exception) {
                $errorparams = new stdClass();
                $errorparams->idnumber = $idnumber;
                throw new moodle_exception($notfound_errorcode, $module, '', $errorparams);
            } else {
                return false;
            }
        case 1:
            if ($table == 'user') {
                return $DB->get_record($table, array('idnumber' => $idnumber, 'deleted' => 0));
            } else{
                return $DB->get_record($table, array('idnumber' => $idnumber));
            }
        default:
            if ($multiple_exception) {
                $errorparams = new stdClass();
                $errorparams->idnumber = $idnumber;
                throw new moodle_exception($multiple_errorcode, $module, '', $errorparams);
            }  else {
                return false;
            }
            break;
    }
}
