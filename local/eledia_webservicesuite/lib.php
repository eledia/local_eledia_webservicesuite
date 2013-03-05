<?php

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
