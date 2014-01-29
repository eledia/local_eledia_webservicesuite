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
 * Second formular with list of users to confirm the deletion
 *
 * @package    local
 * @subpackage eledia_webservicesuite
 * @author     Benjamin Wolf <support@eledia.de>
 * @copyright  2013 eLeDia GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    // It must be included from a Moodle page.
}

require_once($CFG->libdir.'/formslib.php');

class test_services_form extends moodleform {

    public function definition() {
        global $CFG, $DB;

        $mform =& $this->_form;
        $service_choose = optional_param('service_choose', '0', PARAM_ALPHANUM);

        $config = get_config('local_eledia_webservicesuite');
        if (empty($config->test_token)) {
            $mform->addElement('static', 'missing_token', '', get_string('missing_token', 'local_eledia_webservicesuite'));
            return;
        }

        $mform->addElement('header', '', get_string('eledia_desc_header', 'local_eledia_webservicesuite'), 'config_test_services');
        $mform->addElement('static', 'desc', '', get_string('eledia_desc', 'local_eledia_webservicesuite'));

        $options = array('choose',
            'getCoursesByIdnumber' => 'elediaservice_get_courses_by_idnumber',
            'updateCoursesByIdnumber' => 'elediaservice_update_courses_by_idnumber',
            'enrolUsersByIdnumber' => 'elediaservice_enrol_users_by_idnumber',
            'unenrolUsersByIdnumber' => 'elediaservice_unenrol_users_by_idnumber',
            'courseCompletion' => 'elediaservice_course_completion',
//            'getUserByMail' => 'elediaservice_get_user_by_mail',
            'getUserByIdnumber' => 'elediaservice_get_user_by_idnumber',
            'updateUsersByIdnumber' => 'elediaservice_update_users_by_idnumber',);
        $attributes = 'onChange="M.core_formchangechecker.set_form_submitted(); this.form.submit();"';
        $mform->addElement('select', 'service_choose',
                get_string('service_choose', 'local_eledia_webservicesuite'),
                $options, $attributes);
        $mform->setType('service_choose', PARAM_NOTAGS);
        $mform->setDefault('service_choose', 'choose');

        if (!empty($service_choose)) {
            $mform->addElement('header', '', 'function params', 'config_test_services');
        }

        switch ($service_choose) {
            case 'getCoursesByIdnumber':
                $mform->addElement('textarea', 'cidnumbers', get_string('idnumber'), 'wrap="virtual" rows="6" cols="100"');
                break;
            case 'updateCoursesByIdnumber':
                $mform->addElement('text', 'cidnumber', get_string('idnumbercourse', 'local_eledia_webservicesuite'),  'maxlength="100" size="50" ');
                $mform->setType('cidnumber', PARAM_RAW);
                $mform->addElement('text', 'fullname', 'fullname',  'maxlength="100" size="50" ');
                $mform->setType('fullname', PARAM_RAW);
                $mform->addElement('text', 'shortname', 'shortname',  'maxlength="100" size="50" ');
                $mform->setType('shortname', PARAM_RAW);
                $mform->addElement('text', 'categoryid', 'categoryid',  'maxlength="100" size="50" ');
                $mform->setType('categoryid', PARAM_RAW);
                $mform->addElement('editor', 'summary', 'summary');
                $mform->addElement('text', 'summaryformat', 'summaryformat',  'maxlength="100" size="50" ');
                $mform->setType('summaryformat', PARAM_RAW);
                $mform->addElement('text', 'format', 'format',  'maxlength="100" size="50" ');
                $mform->setType('format', PARAM_RAW);
                $mform->addElement('text', 'showgrades', 'showgrades',  'maxlength="100" size="50" ');
                $mform->setType('showgrades', PARAM_RAW);
                $mform->addElement('text', 'newsitems', 'newsitems',  'maxlength="100" size="50" ');
                $mform->setType('newsitems', PARAM_RAW);
                $mform->addElement('text', 'startdate', 'startdate',  'maxlength="100" size="50" ');
                $mform->setType('startdate', PARAM_RAW);
                $mform->addElement('text', 'numsections', 'numsections',  'maxlength="100" size="50" ');
                $mform->setType('numsections', PARAM_RAW);
                $mform->addElement('text', 'maxbytes', 'maxbytes',  'maxlength="100" size="50" ');
                $mform->setType('maxbytes', PARAM_RAW);
                $mform->addElement('text', 'showreports', 'showreports',  'maxlength="100" size="50" ');
                $mform->setType('showreports', PARAM_RAW);
                $mform->addElement('text', 'visible', 'visible',  'maxlength="100" size="50" ');
                $mform->setType('visible', PARAM_RAW);
                $mform->addElement('text', 'hiddensections', 'hiddensections',  'maxlength="100" size="50" ');
                $mform->setType('hiddensections', PARAM_RAW);
                $mform->addElement('text', 'groupmode', 'groupmode',  'maxlength="100" size="50" ');
                $mform->setType('groupmode', PARAM_RAW);
                $mform->addElement('text', 'groupmodeforce', 'groupmodeforce',  'maxlength="100" size="50" ');
                $mform->setType('groupmodeforce', PARAM_RAW);
                $mform->addElement('text', 'defaultgroupingid', 'defaultgroupingid',  'maxlength="100" size="50" ');
                $mform->setType('defaultgroupingid', PARAM_RAW);
                $mform->addElement('text', 'enablecompletion', 'enablecompletion',  'maxlength="100" size="50" ');
                $mform->setType('enablecompletion', PARAM_RAW);
                $mform->addElement('text', 'completionstartonenrol', 'completionstartonenrol',  'maxlength="100" size="50" ');
                $mform->setType('completionstartonenrol', PARAM_RAW);
                $mform->addElement('text', 'completionnotify', 'completionnotify',  'maxlength="100" size="50" ');
                $mform->setType('completionnotify', PARAM_RAW);
                $mform->addElement('text', 'lang', 'lang',  'maxlength="100" size="50" ');
                $mform->setType('lang', PARAM_RAW);
                $mform->addElement('text', 'forcetheme', 'forcetheme',  'maxlength="100" size="50" ');
                $mform->setType('forcetheme', PARAM_RAW);
                $mform->addElement('text', 'courseformatoption_name', 'courseformatoption_name',  'maxlength="100" size="50" ');
                $mform->setType('courseformatoption_name', PARAM_RAW);
                $mform->addElement('text', 'courseformatoption_value', 'courseformatoption_value',  'maxlength="100" size="50" ');
                $mform->setType('courseformatoption_value', PARAM_RAW);
                break;
            case 'enrolUsersByIdnumber':
                $mform->addElement('text', 'roleid', 'roleid',  'maxlength="100" size="50" ');
                $mform->setType('roleid', PARAM_INT);
                $mform->addElement('text', 'uidnumber', get_string('idnumberuser', 'local_eledia_webservicesuite'),  'maxlength="100" size="50" ');
                $mform->setType('uidnumber', PARAM_RAW);
                $mform->addElement('text', 'cidnumber', get_string('idnumbercourse', 'local_eledia_webservicesuite'),  'maxlength="100" size="50" ');
                $mform->setType('cidnumber', PARAM_RAW);
                $mform->addElement('text', 'timestart', 'timestart',  'maxlength="10" size="11" ');
                $mform->setType('timestart', PARAM_INT);
                $mform->addElement('text', 'timeend', 'timeend',  'maxlength="10" size="11" ');
                $mform->setType('timeend', PARAM_INT);
                $mform->addElement('checkbox', 'suspend', 'suspend');
                $mform->setDefault('suspend', false);
                break;
            case 'unenrolUsersByIdnumber':
                $mform->addElement('text', 'uidnumber_u', get_string('idnumberuser', 'local_eledia_webservicesuite'), 'maxlength="100" size="50" ');
                $mform->setType('uidnumber_u', PARAM_RAW);
                $mform->addElement('text', 'cidnumber', get_string('idnumbercourse', 'local_eledia_webservicesuite'), 'maxlength="100" size="50" ');
                $mform->setType('cidnumber', PARAM_RAW);
                $mform->addElement('text', 'enrolname', 'enrolment methode',  'maxlength="10" size="11" ');
                $mform->setType('enrolname', PARAM_RAW);
                $mform->setDefault('enrolname', 'manual');
                break;
            case 'courseCompletion':
                $mform->addElement('text', 'uidnumber', get_string('idnumberuser', 'local_eledia_webservicesuite'), 'maxlength="100" size="50" ');
                $mform->setType('uidnumber', PARAM_RAW);
                $mform->addElement('text', 'cidnumber', get_string('idnumbercourse', 'local_eledia_webservicesuite'), 'maxlength="100" size="50" ');
                $mform->setType('cidnumber', PARAM_RAW);
                break;
//            case 'getUserByMail':
//                $mform->addElement('text', 'email', 'email',  'maxlength="100" size="50" ');
//                $mform->setType('email', PARAM_EMAIL);
//                break;
            case 'getUserByIdnumber':
                $mform->addElement('text', 'uidnumber', get_string('idnumberuser', 'local_eledia_webservicesuite'), 'maxlength="100" size="50" ');
                $mform->setType('uidnumber', PARAM_RAW);
                break;
            case 'updateUsersByIdnumber':
                $mform->addElement('text', 'uidnumber', get_string('idnumberuser', 'local_eledia_webservicesuite'), 'maxlength="100" size="50" ');
                $mform->setType('uidnumber', PARAM_RAW);
                $mform->addElement('text', 'username', get_string('username'), 'maxlength="100" size="20"');
                $mform->setType('username', PARAM_NOTAGS);
                $mform->addElement('text', 'password', get_string('password'), 'maxlength="100" size="50" ');
                $mform->setType('password', PARAM_RAW);
                $mform->addElement('text', 'firstname', get_string('firstname'), 'maxlength="100" size="50" ');
                $mform->setType('firstname', PARAM_RAW);
                $mform->addElement('text', 'lastname', get_string('lastname'), 'maxlength="100" size="50" ');
                $mform->setType('lastname', PARAM_RAW);
                $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="25"');
                $mform->setType('email', PARAM_NOTAGS);
                $mform->addElement('text', 'auth', get_string('authentication', 'admin'), 'maxlength="100" size="20" ');
                $mform->setType('auth', PARAM_RAW);
                $mform->addElement('text', 'lang', get_string('lang', 'admin'), 'maxlength="2" size="3" ');
                $mform->setType('lang', PARAM_RAW);
                $mform->addElement('text', 'theme', get_string('theme'), 'maxlength="100" size="20" ');
                $mform->setType('theme', PARAM_RAW);
                $mform->addElement('text', 'timezone', get_string('timezone'), 'maxlength="100" size="20" ');
                $mform->setType('timezone', PARAM_RAW);
                $mform->addElement('text', 'mailformat', get_string('emailformat'), 'maxlength="10" size="10" ');
                $mform->setType('mailformat', PARAM_RAW);
                $mform->addElement('editor', 'description', get_string('description'));
                $mform->addElement('text', 'city', get_string('city'), 'maxlength="120" size="20"');
                $mform->setType('city', PARAM_TEXT);
                $country = get_string_manager()->get_list_of_countries();
                $default_country[''] = get_string('selectacountry');
                $country = array_merge($default_country, $country);
                $mform->addElement('select', 'country', get_string('country'), $country);
                break;
            default:
//$mform->addElement('static', 'desc', '', $service_choose);
                break;
        }

        $mform->addElement('submit', 'submitbutton', get_string('start', 'local_eledia_webservicesuite'));
        $mform->addElement('cancel', 'cancelbutton', get_string('back', 'local_eledia_webservicesuite'));
    }

    public function definition_after_data() {
        global $CFG;
        $mform =& $this->_form;

        if($mform->isSubmitted()){
            $service_choose = optional_param('service_choose', 0, PARAM_ALPHANUM);
            if (empty($service_choose)) {
                return;
            }
            $client = $this->prepare_client();
            if (empty($client)) {
                return;
            }

            $mform->addElement('header', '', 'Result:', 'config_test_services');

            switch ($service_choose) {
                case 'getCoursesByIdnumber':
                    $this->get_courses_by_idnumber_test($client);
                    break;
                case 'updateCoursesByIdnumber':
                    $this->update_courses_by_idnumber_test($client);
                    break;
                case 'enrolUsersByIdnumber':
                    $this->enrol_users_by_idnumber_test($client);
                    break;
                case 'unenrolUsersByIdnumber':
                    $this->unenrol_users_by_idnumber_test($client);
                    break;
                case 'courseCompletion':
                    $this->course_completion_test($client);
                    break;
                case 'getUserByIdnumber':
                    $this->get_user_by_idnumber($client);
                    break;
//                case 'getUserByMail':
//                    $this->get_user_by_mail($client);
//                    break;
                case 'updateUsersByIdnumber':
                    $this->update_users_by_idnumber($client);
                    break;
                default:
                    break;
            }
        }
    }

    public function prepare_client() {
        global $CFG;
        $config = get_config('local_eledia_webservicesuite');
        $token = $config->test_token;

        try {
            $client = new SoapClient(
                    NULL,
                    array(
                            "location" => $CFG->wwwroot.'/webservice/soap/server.php?wstoken='.$token,
                            "uri" => "urn:xmethods-delayed-quotes",
                            "style" => SOAP_RPC,
                            "use" => SOAP_ENCODED,
                            'trace' => 1
                    )
            );
        } catch (exception $e){
            $mform =& $this->_form;
            $msg = '<br />Error:<br />';
            $msg .= $exc->getMessage();
            $msg .= '<br />response: <br />';
            $msg .= $client->__getLastResponse();
            $mform->addElement('static', 'client_error', '', $msg);
            return false;
        }
        return $client;
    }

    public function get_courses_by_idnumber_test($client) {
        $mform =& $this->_form;

        $cidnumbers = optional_param('cidnumbers', 0, PARAM_RAW);
        $id_array =  array();
        if (empty($cidnumbers)) {
            return;
        } else {
            $idnumbers = explode("\n", $cidnumbers);
            foreach ($idnumbers as $id) {
                $id_array[] = trim($id);
            }
        }

        $msg = '';
        try {
            $result = $client->elediaservice_get_courses_by_idnumber($id_array);
        } catch (Exception $exc) {
            $msg .= $exc->getMessage();
            $mform->addElement('static', 'service_response', '', $msg);
            return;
        }

        ob_start();
        print_object($result);
        $result_str = ob_get_contents();
        ob_end_clean();
        $msg .= $result_str;
        $mform->addElement('static', 'service_response', '', $msg);
    }

    public function enrol_users_by_idnumber_test($client) {
        $mform =& $this->_form;

        $enrolment = new stdClass();
        $enrolment->roleid = optional_param('roleid', 5, PARAM_INT);
        $enrolment->courseidnumber = optional_param('cidnumber', 0, PARAM_RAW);
        $enrolment->useridnumber = optional_param('uidnumber', 0, PARAM_RAW);
        $enrolment->timestart = optional_param('timestart', time(), PARAM_INT);
        $enrolment->timeend = optional_param('timeend', 0, PARAM_INT);
        $enrolment->suspend = optional_param('suspend', 0, PARAM_BOOL);

        if (empty($enrolment->timestart)) {
            unset($enrolment->timestart);
        }
        if (empty($enrolment->timeend)) {
            unset($enrolment->timeend);
        }

        $msg = '';
        try {
            $result = $client->elediaservice_enrol_users_by_idnumber(array('enrolments' => $enrolment));
        } catch (Exception $exc) {
            $msg .= $exc->getMessage();
            $mform->addElement('static', 'service_response', '', $msg);
            return;
        }

        ob_start();
        print_object($result);
        $result_str = ob_get_contents();
        ob_end_clean();
        $msg .= 'Success '.$result_str;
        $mform->addElement('static', 'service_response', '', $msg);
    }

    public function unenrol_users_by_idnumber_test($client) {
        $mform =& $this->_form;

        $enrolment = new stdClass();
        $enrolment->courseidnumber = optional_param('cidnumber', 0, PARAM_RAW);
        $enrolment->useridnumber = optional_param('uidnumber_u', 0, PARAM_RAW);
        $enrolment->enrolname = optional_param('enrolname', 0, PARAM_RAW);

        $msg = '';
        try {
            $result = $client->elediaservice_unenrol_users_by_idnumber(array('enrolments' => $enrolment));
        } catch (Exception $exc) {
            $msg .= $exc->getMessage();
            $mform->addElement('static', 'service_response', '', $msg);
            return;
        }

        ob_start();
        print_object($result);
        $result_str = ob_get_contents();
        ob_end_clean();
        $msg .= 'Success '.$result_str;
        $mform->addElement('static', 'service_response', '', $msg);
    }

    public function update_courses_by_idnumber_test($client) {
        $mform =& $this->_form;

        $course = new stdClass();
        $course->fullname = optional_param('fullname', 0, PARAM_RAW);
        $course->shortname = optional_param('shortname', 0, PARAM_RAW);
        $course->categoryid = optional_param('categoryid', 0, PARAM_RAW);
        $course->idnumber = optional_param('cidnumber', 0, PARAM_RAW);
        $summary_array = optional_param_array('summary', 0, PARAM_RAW);
        $course->summary = $summary_array['text'];
        $course->summaryformat = optional_param('summaryformat', 0, PARAM_RAW);
        $course->format = optional_param('format', 0, PARAM_RAW);
        $course->showgrades = optional_param('showgrades', 0, PARAM_RAW);
        $course->newsitems = optional_param('newsitems', 0, PARAM_RAW);
        $course->startdate = optional_param('startdate', 0, PARAM_RAW);
        $course->numsections = optional_param('numsections', 0, PARAM_RAW);
        $course->maxbytes = optional_param('maxbytes', 0, PARAM_RAW);
        $course->showreports = optional_param('showreports', 0, PARAM_RAW);
        $course->visible = optional_param('visible', 0, PARAM_RAW);
        $course->hiddensections = optional_param('hiddensections', 0, PARAM_RAW);
        $course->groupmode = optional_param('groupmode', 0, PARAM_RAW);
        $course->groupmodeforce = optional_param('groupmodeforce', 0, PARAM_RAW);
        $course->defaultgroupingid = optional_param('defaultgroupingid', 0, PARAM_RAW);
        $course->enablecompletion = optional_param('enablecompletion', 0, PARAM_RAW);
        $course->completionstartonenrol = optional_param('completionstartonenrol', 0, PARAM_RAW);
        $course->completionnotify = optional_param('completionnotify', 0, PARAM_RAW);
        $course->lang = optional_param('lang', 0, PARAM_RAW);
        $course->forcetheme = optional_param('forcetheme', 0, PARAM_RAW);
        $course->courseformatoptions = array(array('name' => optional_param('courseformatoption_name', 0, PARAM_RAW), 'value' => optional_param('courseformatoption_value', 0, PARAM_RAW)));

        if (empty($course->idnumber)) {
            return;
        }

        foreach ($course as $key => $value) {
            if (empty($value)) {
                unset($course->$key);
            }
        }

        $msg = '';
        try {
            $result = $client->elediaservice_update_courses_by_idnumber(array($course));
        } catch (Exception $exc) {
            $msg .= $exc->getMessage();
            $mform->addElement('static', 'service_response', '', $msg);
            return;
        }

        ob_start();
        print_object($result);
        $result_str = ob_get_contents();
        ob_end_clean();
        $msg .= 'Success'.$result_str;
        $mform->addElement('static', 'service_response', '', $msg);
    }

    public function course_completion_test($client) {
        $mform =& $this->_form;

        $eledia_uidnumber = optional_param('uidnumber', 0, PARAM_RAW);
        $eledia_cidnumber = optional_param('cidnumber', 0, PARAM_RAW);

        if (empty($eledia_cidnumber) || empty($eledia_cidnumber)) {
            return;
        }

        $msg = '';
        try {
            $result = $client->elediaservice_course_completion(array(array('useridnumber' => $eledia_uidnumber , 'courseidnumber' => $eledia_cidnumber)));
        } catch (Exception $exc) {
            $msg .= $exc->getMessage();
            $mform->addElement('static', 'service_response', '', $msg);
            return;
        }

        ob_start();
        print_object($result);
        $result_str = ob_get_contents();
        ob_end_clean();
        $msg .= $result_str;
        $mform->addElement('static', 'service_response', '', $msg);
    }

    public function get_user_by_idnumber($client) {
        $mform =& $this->_form;

        $uidnumber = optional_param('uidnumber', 0, PARAM_RAW);

        if (empty($uidnumber)) {
            return;
        }

        $msg = '';
        try {
            $result = $client->elediaservice_get_user_by_idnumber($uidnumber);
        } catch (Exception $exc) {
            $msg .= $exc->getMessage();
            $mform->addElement('static', 'service_response', '', $msg);
            return;
        }

        ob_start();
        print_object($result);
        $result_str = ob_get_contents();
        ob_end_clean();
        $msg .= $result_str;
        $mform->addElement('static', 'service_response', '', $msg);
    }

    public function get_user_by_mail($client) {
        $mform =& $this->_form;

        // Build up mails array.
        $mails = optional_param('mails', 0, PARAM_RAW);
        // ToDo

        $msg = '';
        try {
            $result = $client->elediaservice_get_user_by_mail($mails);
        } catch (Exception $exc) {
            $msg .= $exc->getMessage();
            $mform->addElement('static', 'service_response', '', $msg);
            return;
        }

        ob_start();
        print_object($result);
        $result_str = ob_get_contents();
        ob_end_clean();
        $msg .= $result_str;
        $mform->addElement('static', 'service_response', '', $msg);
    }

    public function update_users_by_idnumber($client) {
        $mform =& $this->_form;

        $user = new stdClass();
        $user->idnumber = optional_param('uidnumber', '', PARAM_RAW);
        $user->username = optional_param('username', '', PARAM_USERNAME);
        $user->password = optional_param('password', '', PARAM_RAW);
        $user->firstname = optional_param('firstname', '', PARAM_RAW);
        $user->lastname = optional_param('lastname', '', PARAM_RAW);
        $user->email = optional_param('email', '', PARAM_EMAIL);
        $user->auth = optional_param('auth', '', PARAM_RAW);
        $user->lang = optional_param('lang', '', PARAM_RAW);
        $user->theme = optional_param('theme', '', PARAM_RAW);
        $user->timezone = optional_param('timezone', '', PARAM_RAW);
        $user->mailformat = optional_param('mailformat', '', PARAM_RAW);
        $user->description = optional_param_array('description', '', PARAM_RAW);
        $user->description = $user->description['text'];
        $user->city = optional_param('city', '', PARAM_RAW);
        $user->country = optional_param('country', '', PARAM_RAW);

print_object($user);
        if (empty($user->idnumber)) {
            return;
        }

        foreach ($user as $key => $value) {
            if (empty($value)) {
                unset($user->$key);
            }
        }

        $msg = '';
        try {
            $result = $client->elediaservice_update_users_by_idnumber(array($user));
        } catch (Exception $exc) {
            $msg .= $exc->getMessage();
            $mform->addElement('static', 'service_response', '', $msg);
            return;
        }

        ob_start();
        print_object($result);
        $result_str = ob_get_contents();
        ob_end_clean();
        $msg .= 'Success'.$result_str;
        $mform->addElement('static', 'service_response', '', $msg);
    }
}
