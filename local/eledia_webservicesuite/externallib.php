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
 * The externallib of the elediawebservicesuite.
 *
 * Here you'll find the methods you can directly access through
 * the webservice.
 *
 * @package    local
 * @subpackage eledia_webservicesuite
 * @author     Benjamin Wolf <support@eledia.de>
 * @copyright  2014 eLeDia GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir.'/externallib.php');

class eledia_services extends external_api {

    /**
     * Parameterdefinition for method "get_user_by_mail"
     *
     * @return {object} external_function_parameters
     */
    public static function get_user_by_mail_parameters() {
        return new external_function_parameters(
            array(
                'mails' => new external_multiple_structure(
                    new external_value(PARAM_EMAIL, 'mail adress of a user to search for')
                )
            )
        );
    }

    /**
     * Method to get the users with the given mail adresses.
     *
     * @param {array} usermail
     * @return {array} array of user objects
     * @throws {moodle_exception}
     */
    public static function get_user_by_mail($mails) {
        global $DB, $CFG, $USER;

        require_once($CFG->dirroot . "/user/lib.php");
        self::validate_parameters(self::get_user_by_mail_parameters(), array('mails' => $mails));

        list($uselect, $ujoin) = context_instance_preload_sql('u.id', CONTEXT_USER, 'ctx');
        list($sqlmails, $params) = $DB->get_in_or_equal($mails);
        $usersql = "SELECT u.* $uselect
                      FROM {user} u $ujoin
                     WHERE u.email $sqlmails";

        $users = $DB->get_recordset_sql($usersql, $params);
        $result = array();
        $hasuserupdatecap = has_capability('moodle/user:update', get_system_context());
        foreach ($users as $user) {
            if (!empty($user->deleted)) {
                continue;
            }
            context_instance_preload($user);
            $usercontext = CONTEXT_USER::instance($user->id);
            self::validate_context($usercontext);
            $currentuser = ($user->id == $USER->id);
            if ($userarray  = user_get_user_details($user)) {
                // Fields matching permissions from /user/editadvanced.php.
                if ($currentuser or $hasuserupdatecap) {
                    $userarray['auth']       = $user->auth;
                    $userarray['confirmed']  = $user->confirmed;
                    $userarray['idnumber']   = $user->idnumber;
                    $userarray['lang']       = $user->lang;
                    $userarray['theme']      = $user->theme;
                    $userarray['timezone']   = $user->timezone;
                    $userarray['mailformat'] = $user->mailformat;
                }
                $result[] = $userarray;
            }
        }
        $users->close();
        return $result;
    }

    /**
     * Returndefinition for method "get_user_by_mail"
     *
     * @return {object} external_value
     */
    public static function get_user_by_mail_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id'    => new external_value(PARAM_NUMBER, 'ID of the user'),
                    'username'    => new external_value(PARAM_RAW,
                            'Username policy is defined in Moodle security config',
                            VALUE_OPTIONAL),
                    'firstname'   => new external_value(PARAM_NOTAGS,
                            'The first name(s) of the user',
                            VALUE_OPTIONAL),
                    'lastname'    => new external_value(PARAM_NOTAGS, 'The family name of the user', VALUE_OPTIONAL),
                    'fullname'    => new external_value(PARAM_NOTAGS, 'The fullname of the user'),
                    'email'       => new external_value(PARAM_TEXT,
                            'An email address - allow email as root@localhost', VALUE_OPTIONAL),
                    'address'     => new external_value(PARAM_MULTILANG, 'Postal address', VALUE_OPTIONAL),
                    'phone1'      => new external_value(PARAM_NOTAGS, 'Phone 1', VALUE_OPTIONAL),
                    'phone2'      => new external_value(PARAM_NOTAGS, 'Phone 2', VALUE_OPTIONAL),
                    'icq'         => new external_value(PARAM_NOTAGS, 'icq number', VALUE_OPTIONAL),
                    'skype'       => new external_value(PARAM_NOTAGS, 'skype id', VALUE_OPTIONAL),
                    'yahoo'       => new external_value(PARAM_NOTAGS, 'yahoo id', VALUE_OPTIONAL),
                    'aim'         => new external_value(PARAM_NOTAGS, 'aim id', VALUE_OPTIONAL),
                    'msn'         => new external_value(PARAM_NOTAGS, 'msn number', VALUE_OPTIONAL),
                    'department'  => new external_value(PARAM_TEXT, 'department', VALUE_OPTIONAL),
                    'institution' => new external_value(PARAM_TEXT, 'institution', VALUE_OPTIONAL),
                    'interests'   => new external_value(PARAM_TEXT, 'user interests (separated by commas)', VALUE_OPTIONAL),
                    'firstaccess' => new external_value(PARAM_INT, 'first access to the site (0 if never)', VALUE_OPTIONAL),
                    'lastaccess'  => new external_value(PARAM_INT, 'last access to the site (0 if never)', VALUE_OPTIONAL),
                    'auth'        => new external_value(PARAM_PLUGIN,
                            'Auth plugins include manual, ldap, imap, etc', VALUE_OPTIONAL),
                    'confirmed'   => new external_value(PARAM_NUMBER,
                            'Active user: 1 if confirmed, 0 otherwise', VALUE_OPTIONAL),
                    'idnumber'    => new external_value(PARAM_RAW,
                            'An arbitrary ID code number perhaps from the institution', VALUE_OPTIONAL),
                    'lang'        => new external_value(PARAM_SAFEDIR,
                            'Language code such as "en", must exist on server', VALUE_OPTIONAL),
                    'theme'       => new external_value(PARAM_PLUGIN,
                            'Theme name such as "standard", must exist on server', VALUE_OPTIONAL),
                    'timezone'    => new external_value(PARAM_TIMEZONE,
                            'Timezone code such as Australia/Perth, or 99 for default', VALUE_OPTIONAL),
                    'mailformat'  => new external_value(PARAM_INTEGER,
                            'Mail format code is 0 for plain text, 1 for HTML etc', VALUE_OPTIONAL),
                    'description' => new external_value(PARAM_RAW, 'User profile description', VALUE_OPTIONAL),
                    'descriptionformat' => new external_value(PARAM_INT, 'User profile description format', VALUE_OPTIONAL),
                    'city'        => new external_value(PARAM_NOTAGS, 'Home city of the user', VALUE_OPTIONAL),
                    'url'         => new external_value(PARAM_URL, 'URL of the user', VALUE_OPTIONAL),
                    'country'     => new external_value(PARAM_ALPHA,
                            'Home country code of the user, such as AU or CZ', VALUE_OPTIONAL),
                    'profileimageurlsmall' => new external_value(PARAM_URL, 'User image profile URL - small version'),
                    'profileimageurl' => new external_value(PARAM_URL, 'User image profile URL - big version'),
                    'customfields' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'type'  => new external_value(PARAM_ALPHANUMEXT,
                                        'The type of the custom field - text field, checkbox...'),
                                'value' => new external_value(PARAM_RAW, 'The value of the custom field'),
                                'name' => new external_value(PARAM_RAW, 'The name of the custom field'),
                                'shortname' => new external_value(PARAM_RAW,
                                        'The shortname of the custom field - to be able to build the field class in the code'),
                            )
                    ), 'User custom fields (also known as user profil fields)', VALUE_OPTIONAL),
                    'preferences' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'name'  => new external_value(PARAM_ALPHANUMEXT, 'The name of the preferences'),
                                'value' => new external_value(PARAM_RAW, 'The value of the custom field'),
                            )
                    ), 'User preferences', VALUE_OPTIONAL),
                    'enrolledcourses' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'id'  => new external_value(PARAM_INT, 'Id of the course'),
                                'fullname'  => new external_value(PARAM_RAW, 'Fullname of the course'),
                                'shortname' => new external_value(PARAM_RAW, 'Shortname of the course')
                            )
                    ), 'Courses where the user is enrolled - limited by which courses the user is able to see', VALUE_OPTIONAL)
                )
            )
        );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_users_by_idnumber_parameters() {
        return new external_function_parameters(
                array(
                    'idnumbers' => new external_multiple_structure(new external_value(PARAM_RAW, 'idnumber')),
                )
        );
    }

    /**
     * Get user information
     * - This function is matching the permissions of /user/profil.php
     * - It is also matching some permissions from /user/editadvanced.php for the following fields:
     *   auth, confirmed, idnumber, lang, theme, timezone, mailformat
     * @param array $usernumbers  array of user idnumbers
     * @return array An array of arrays describing users
     */
    public static function get_users_by_idnumber($idnumbers) {
        global $DB, $CFG;

        require_once($CFG->dirroot . "/user/lib.php");
        self::validate_parameters(self::get_users_by_idnumber_parameters(), array('idnumbers' => $idnumbers));

        list($uselect, $ujoin) = context_instance_preload_sql('u.id', CONTEXT_USER, 'ctx');
        list($sqlmails, $params) = $DB->get_in_or_equal($idnumbers);
        $usersql = "SELECT u.* $uselect
                      FROM {user} u $ujoin
                     WHERE u.idnumber $sqlmails";
        $users = $DB->get_recordset_sql($usersql, $params);

        $result = array();
        $hasuserupdatecap = has_capability('moodle/user:update', get_system_context());
        foreach ($users as $user) {
            if (!empty($user->deleted)) {
                continue;
            }
            context_instance_preload($user);
            $usercontext = CONTEXT_USER::instance($user->id);
            self::validate_context($usercontext);
            $currentuser = ($user->id == $USER->id);

            if ($userarray  = user_get_user_details($user)) {
                // Fields matching permissions from /user/editadvanced.php.
                if ($currentuser or $hasuserupdatecap) {
                    $userarray['auth']       = $user->auth;
                    $userarray['confirmed']  = $user->confirmed;
                    $userarray['idnumber']   = $user->idnumber;
                    $userarray['lang']       = $user->lang;
                    $userarray['theme']      = $user->theme;
                    $userarray['timezone']   = $user->timezone;
                    $userarray['mailformat'] = $user->mailformat;
                }
                $result[] = $userarray;
            }
        }
        $users->close();

        return $result;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_users_by_idnumber_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id'    => new external_value(PARAM_NUMBER, 'ID of the user'),
                    'username'    => new external_value(PARAM_RAW,
                            'Username policy is defined in Moodle security config', VALUE_OPTIONAL),
                    'firstname'   => new external_value(PARAM_NOTAGS, 'The first name(s) of the user', VALUE_OPTIONAL),
                    'lastname'    => new external_value(PARAM_NOTAGS, 'The family name of the user', VALUE_OPTIONAL),
                    'fullname'    => new external_value(PARAM_NOTAGS, 'The fullname of the user'),
                    'email'       => new external_value(PARAM_TEXT,
                            'An email address - allow email as root@localhost', VALUE_OPTIONAL),
                    'address'     => new external_value(PARAM_MULTILANG, 'Postal address', VALUE_OPTIONAL),
                    'phone1'      => new external_value(PARAM_NOTAGS, 'Phone 1', VALUE_OPTIONAL),
                    'phone2'      => new external_value(PARAM_NOTAGS, 'Phone 2', VALUE_OPTIONAL),
                    'icq'         => new external_value(PARAM_NOTAGS, 'icq number', VALUE_OPTIONAL),
                    'skype'       => new external_value(PARAM_NOTAGS, 'skype id', VALUE_OPTIONAL),
                    'yahoo'       => new external_value(PARAM_NOTAGS, 'yahoo id', VALUE_OPTIONAL),
                    'aim'         => new external_value(PARAM_NOTAGS, 'aim id', VALUE_OPTIONAL),
                    'msn'         => new external_value(PARAM_NOTAGS, 'msn number', VALUE_OPTIONAL),
                    'department'  => new external_value(PARAM_TEXT, 'department', VALUE_OPTIONAL),
                    'institution' => new external_value(PARAM_TEXT, 'institution', VALUE_OPTIONAL),
                    'interests'   => new external_value(PARAM_TEXT, 'user interests (separated by commas)', VALUE_OPTIONAL),
                    'firstaccess' => new external_value(PARAM_INT, 'first access to the site (0 if never)', VALUE_OPTIONAL),
                    'lastaccess'  => new external_value(PARAM_INT, 'last access to the site (0 if never)', VALUE_OPTIONAL),
                    'auth'        => new external_value(PARAM_PLUGIN,
                            'Auth plugins include manual, ldap, imap, etc', VALUE_OPTIONAL),
                    'confirmed'   => new external_value(PARAM_NUMBER, 'Active user: 1 if confirmed, 0 otherwise', VALUE_OPTIONAL),
                    'idnumber'    => new external_value(PARAM_RAW,
                            'An arbitrary ID code number perhaps from the institution', VALUE_OPTIONAL),
                    'lang'        => new external_value(PARAM_SAFEDIR,
                            'Language code such as "en", must exist on server', VALUE_OPTIONAL),
                    'theme'       => new external_value(PARAM_PLUGIN,
                            'Theme name such as "standard", must exist on server', VALUE_OPTIONAL),
                    'timezone'    => new external_value(PARAM_TIMEZONE,
                            'Timezone code such as Australia/Perth, or 99 for default', VALUE_OPTIONAL),
                    'mailformat'  => new external_value(PARAM_INTEGER,
                            'Mail format code is 0 for plain text, 1 for HTML etc', VALUE_OPTIONAL),
                    'description' => new external_value(PARAM_RAW, 'User profile description', VALUE_OPTIONAL),
                    'descriptionformat' => new external_value(PARAM_INT, 'User profile description format', VALUE_OPTIONAL),
                    'city'        => new external_value(PARAM_NOTAGS, 'Home city of the user', VALUE_OPTIONAL),
                    'url'         => new external_value(PARAM_URL, 'URL of the user', VALUE_OPTIONAL),
                    'country'     => new external_value(PARAM_ALPHA,
                            'Home country code of the user, such as AU or CZ', VALUE_OPTIONAL),
                    'profileimageurlsmall' => new external_value(PARAM_URL, 'User image profile URL - small version'),
                    'profileimageurl' => new external_value(PARAM_URL, 'User image profile URL - big version'),
                    'customfields' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'type'  => new external_value(PARAM_ALPHANUMEXT,
                                        'The type of the custom field - text field, checkbox...'),
                                'value' => new external_value(PARAM_RAW, 'The value of the custom field'),
                                'name' => new external_value(PARAM_RAW, 'The name of the custom field'),
                                'shortname' => new external_value(PARAM_RAW,
                                        'The shortname of the custom field - to be able to build the field class in the code'),
                            )
                    ), 'User custom fields (also known as user profil fields)', VALUE_OPTIONAL),
                    'preferences' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'name'  => new external_value(PARAM_ALPHANUMEXT, 'The name of the preferences'),
                                'value' => new external_value(PARAM_RAW, 'The value of the custom field'),
                            )
                    ), 'User preferences', VALUE_OPTIONAL),
                    'enrolledcourses' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'id'  => new external_value(PARAM_INT, 'Id of the course'),
                                'fullname'  => new external_value(PARAM_RAW, 'Fullname of the course'),
                                'shortname' => new external_value(PARAM_RAW, 'Shortname of the course')
                            )
                    ), 'Courses where the user is enrolled - limited by which courses the user is able to see', VALUE_OPTIONAL)
                )
            )
        );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function update_users_by_idnumber_parameters() {
        global $CFG;
        return new external_function_parameters(
            array(
                'users' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'idnumber'    => new external_value(PARAM_RAW,
                                    'An arbitrary ID code number perhaps from the institution'),
                            'username'    => new external_value(PARAM_USERNAME,
                                    'Username policy is defined in Moodle security config. Must be lowercase.',
                                    VALUE_OPTIONAL,
                                    '',
                                    NULL_NOT_ALLOWED),
                            'password'    => new external_value(PARAM_RAW,
                                    'Plain text password consisting of any characters', VALUE_OPTIONAL, '', NULL_NOT_ALLOWED),
                            'firstname'   => new external_value(PARAM_NOTAGS,
                                    'The first name(s) of the user', VALUE_OPTIONAL, '', NULL_NOT_ALLOWED),
                            'lastname'    => new external_value(PARAM_NOTAGS, 'The family name of the user', VALUE_OPTIONAL),
                            'email'       => new external_value(PARAM_EMAIL,
                                    'A valid and unique email address', VALUE_OPTIONAL, '', NULL_NOT_ALLOWED),
                            'auth'        => new external_value(PARAM_PLUGIN,
                                    'Auth plugins include manual, ldap, imap, etc', VALUE_OPTIONAL, '', NULL_NOT_ALLOWED),
                            'lang'        => new external_value(PARAM_SAFEDIR,
                                    'Language code such as "en", must exist on server', VALUE_OPTIONAL, '', NULL_NOT_ALLOWED),
                            'theme'       => new external_value(PARAM_PLUGIN,
                                    'Theme name such as "standard", must exist on server', VALUE_OPTIONAL),
                            'timezone'    => new external_value(PARAM_TIMEZONE,
                                    'Timezone code such as Australia/Perth, or 99 for default', VALUE_OPTIONAL),
                            'mailformat'  => new external_value(PARAM_INTEGER,
                                    'Mail format code is 0 for plain text, 1 for HTML etc', VALUE_OPTIONAL),
                            'description' => new external_value(PARAM_RAW, 'User profile description, no HTML', VALUE_OPTIONAL),
                            'city'        => new external_value(PARAM_NOTAGS, 'Home city of the user', VALUE_OPTIONAL),
                            'country'     => new external_value(PARAM_ALPHA,
                                    'Home country code of the user, such as AU or CZ',
                                    VALUE_OPTIONAL),
                            'customfields' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'type'  => new external_value(PARAM_ALPHANUMEXT, 'The name of the custom field'),
                                        'value' => new external_value(PARAM_RAW, 'The value of the custom field')
                                    )
                                ), 'User custom fields (also known as user profil fields)', VALUE_OPTIONAL),
                            'preferences' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'type'  => new external_value(PARAM_ALPHANUMEXT, 'The name of the preference'),
                                        'value' => new external_value(PARAM_RAW, 'The value of the preference')
                                    )
                                ), 'User preferences', VALUE_OPTIONAL),
                        )
                    )
                )
            )
        );
    }

    /**
     * Update users
     * @param array $users
     * @return null
     */
    public static function update_users_by_idnumber($users) {
        global $CFG, $DB;
        require_once($CFG->dirroot."/user/lib.php");
        require_once($CFG->dirroot."/user/profile/lib.php"); // Required for customfields related function.
        require_once($CFG->dirroot."/local/eledia_webservicesuite/lib.php");

        // Ensure the current user is allowed to run this function.
        $context = CONTEXT_SYSTEM::instance();
        require_capability('moodle/user:update', $context);
        self::validate_context($context);

        $params = self::validate_parameters(self::update_users_by_idnumber_parameters(), array('users' => $users));
        $transaction = $DB->start_delegated_transaction();
        $output = array();

        foreach ($params['users'] as $user) {
            // Get user by idnumber & check for unique & existing idnumbers.
            try {
                $local_user = get_record_by_idnumber('user', $user['idnumber'], true, true, 'wsusernotfound', 'wsmultipleusersfound');
            } catch (Exception $exc) {
                $output['result'] .= $exc->getMessage()."\n";
                continue;
            }
            $user['id'] = $local_user->id;

            try {
                user_update_user($user);
            } catch (Exception $exc) {
                $output['result'] .= $exc->getMessage()."\n";
                continue;
            }

            // Update user custom fields.
            if (!empty($user['customfields'])) {

                foreach ($user['customfields'] as $customfield) {
                    $user["profile_field_".$customfield['type']] = $customfield['value'];
                    // Profile_save_data() saves profile file.
                    // It's expecting a user with the correct id, and custom field to be named profile_field_"shortname".
                }
                try {
                    profile_save_data((object) $user);
                } catch (Exception $exc) {
                    $output['result'] .= $exc->getMessage()."\n";
                }
            }

            // Preferences.
            if (!empty($user['preferences'])) {
                foreach ($user['preferences'] as $preference) {
                    try {
                        set_user_preference($preference['type'], $preference['value'], $user['id']);
                    } catch (Exception $exc) {
                        $output['result'] .= $exc->getMessage()."\n";
                    }
                }
            }
            $output['result'] .= 'user '.$local_user->username." updated \n";
        }

        $transaction->allow_commit();

        $output['result'] .= 'success';
        $output['success'] = true;
        return array($output);
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function update_users_by_idnumber_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'success'   => new external_value(PARAM_BOOL, 'Return success of operation true or false'),
                    'result'    => new external_value(PARAM_RAW, 'Return message'),
                )
            )
        );
    }


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function enrol_users_by_idnumber_parameters() {
        return new external_function_parameters(
                array(
                    'enrolments' => new external_multiple_structure(
                            new external_single_structure(
                                    array(
                                        'roleid' => new external_value(PARAM_INT, 'Role to assign to the user'),
                                        'useridnumber' => new external_value(PARAM_RAW,
                                                'The user idnumber that is going to be enrolled'),
                                        'courseidnumber' => new external_value(PARAM_RAW,
                                                'The course idnumber to enrol the user role in'),
                                        'timestart' => new external_value(PARAM_INT,
                                                'Timestamp when the enrolment start', VALUE_OPTIONAL),
                                        'timeend' => new external_value(PARAM_INT,
                                                'Timestamp when the enrolment end', VALUE_OPTIONAL),
                                        'suspend' => new external_value(PARAM_INT,
                                                'set to 1 to suspend the enrolment', VALUE_OPTIONAL)
                                    )
                            )
                    )
                )
        );
    }

    /**
     * Enrolment of users
     * Function throw an exception at the first error encountered.
     * @param array $enrolments  An array of user enrolment
     * @return null
     */
    public static function enrol_users_by_idnumber($enrolments) {
        global $DB, $CFG;

        require_once($CFG->libdir . '/enrollib.php');
        require_once($CFG->dirroot."/local/eledia_webservicesuite/lib.php");

        $params = self::validate_parameters(self::enrol_users_by_idnumber_parameters(),
                array('enrolments' => $enrolments));

        $transaction = $DB->start_delegated_transaction(); // Rollback all enrolment if an error occurs.
                                                           // X(except if the DB doesn't support it).

        // Retrieve the manual enrolment plugin.
        $enrol = enrol_get_plugin('manual');
        if (empty($enrol)) {
            $output['result'] = get_string('manualpluginnotinstalled', 'enrol_manual');
            $output['success'] = false;
            return $output;
        }

        foreach ($params['enrolments'] as $enrolment) {

            // Get course and user by idnumber & check for unique & existing idnumbers.
            $local_course = get_record_by_idnumber('course',
                    $enrolment['courseidnumber'],
                    true,
                    true,
                    'wscoursenotfound',
                    'wsmultiplecoursessfound');
            $enrolment['courseid'] = $local_course->id;

            $local_user = get_record_by_idnumber('user',
                    $enrolment['useridnumber'],
                    true,
                    true,
                    'wsusernotfound',
                    'wsmultipleusersfound');
            $enrolment['userid'] = $local_user->id;

            // Ensure the current user is allowed to run this function in the enrolment context.
            $context = CONTEXT_COURSE::instance($enrolment['courseid']);
            self::validate_context($context);

            // Check that the user has the permission to manual enrol.
            require_capability('enrol/manual:enrol', $context);

            // Throw an exception if user is not able to assign the role.
            $roles = get_assignable_roles($context);
            if (!key_exists($enrolment['roleid'], $roles)) {
                $errorparams = new stdClass();
                $errorparams->roleid = $enrolment['roleid'];
                $errorparams->courseid = $enrolment['courseid'];
                $errorparams->userid = $enrolment['userid'];
                throw new moodle_exception('wsusercannotassign', 'local_eledia_webservicesuite', '', $errorparams);
            }

            // Check manual enrolment plugin instance is enabled/exist.
            $enrolinstances = enrol_get_instances($enrolment['courseid'], true);
            foreach ($enrolinstances as $courseenrolinstance) {
                if ($courseenrolinstance->enrol == "manual") {
                    $instance = $courseenrolinstance;
                    break;
                }
            }
            if (empty($instance)) {
                $errorparams = new stdClass();
                $errorparams->courseid = $enrolment['courseid'];
                throw new moodle_exception('wsnoinstance', 'local_eledia_webservicesuite', $errorparams);
            }

            // Check that the plugin accept enrolment (it should always the case, it's hard coded in the plugin).
            if (!$enrol->allow_enrol($instance)) {
                $errorparams = new stdClass();
                $errorparams->roleid = $enrolment['roleid'];
                $errorparams->courseid = $enrolment['courseid'];
                $errorparams->userid = $enrolment['userid'];
                throw new moodle_exception('wscannotenrol', 'local_eledia_webservicesuite', '', $errorparams);
            }

            // Finally proceed the enrolment.
            $enrolment['timestart'] = isset($enrolment['timestart']) ? $enrolment['timestart'] : 0;
            $enrolment['timeend'] = isset($enrolment['timeend']) ? $enrolment['timeend'] : 0;
            $enrolment['status'] = (isset($enrolment['suspend']) && !empty($enrolment['suspend'])) ?
                    ENROL_USER_SUSPENDED : ENROL_USER_ACTIVE;

            $enrol->enrol_user($instance, $enrolment['userid'], $enrolment['roleid'],
                    $enrolment['timestart'], $enrolment['timeend'], $enrolment['status']);
        }

        $transaction->allow_commit();
        $output['result'] = 'success';
        $output['success'] = true;
        return $output;
    }

    /**
     * Returns description of method result value
     * @return null
     */
    public static function enrol_users_by_idnumber_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'success'   => new external_value(PARAM_BOOL, 'Return success of operation true or false'),
                    'result'    => new external_value(PARAM_RAW, 'Return message'),
                )
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_courses_by_idnumber_parameters() {
        return new external_function_parameters(
                            array('idnumbers' => new external_multiple_structure(
                                        new external_value(PARAM_RAW, 'Course idnumber')
                                        , 'List of course idnumbers. If empty return all courses
                                            except front page course.')
                )
        );
    }

    /**
     * Get courses by idnumber
     *
     * @param array $options It contains an array (list of ids)
     * @return array
     */
    public static function get_courses_by_idnumber($options = array()) {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/course/lib.php");
        require_once($CFG->dirroot."/local/eledia_webservicesuite/lib.php");

        // Validate parameter.
        $params = self::validate_parameters(self::get_courses_by_idnumber_parameters(),
                        array('idnumbers' => $options));

        // Retrieve courses.
        if (empty($params['idnumbers'])) {
            $courses = $DB->get_records('course');
        } else {
            $courses = array();
            foreach ($params['idnumbers'] as $idnumber) {
                // Get course by idnumber.
                $course = get_record_by_idnumber('course', $idnumber, false, true, '', 'wsmultiplecoursesfound');
                if (empty($course)) {
                    $courses[$idnumber] = null;
                } else {
                    $courses[$idnumber] = $course;
                }
            }
        }

        // Create return value.
        $coursesinfo = array();
        $coursesinfo[0]['result'] = '';
        $coursesinfo[0]['success'] = false;
        foreach ($courses as $idnumber => $course) {

            // If course not found, set info text.
            if (!$course) {
                $msg = array();
                $msg['msg'] = 'course not found';
                $coursesinfo[$idnumber] = $msg;
                continue;
            }

            // Now security checks.
            $context = context_course::instance($course->id, IGNORE_MISSING);
            $courseformatoptions = course_get_format($course)->get_format_options();
            try {
                 self::validate_context($context);
            } catch (Exception $e) {
                 $exceptionparam = new stdClass();
                 $exceptionparam->message = $e->getMessage();
                 $exceptionparam->courseid = $course->id;
                 throw new moodle_exception('errorcoursecontextnotvalid', 'webservice', '', $exceptionparam);
            }
            require_capability('moodle/course:view', $context);

            $courseinfo = array();
            $courseinfo['id'] = $course->id;
            $courseinfo['fullname'] = $course->fullname;
            $courseinfo['shortname'] = $course->shortname;
            $courseinfo['categoryid'] = $course->category;
            list($courseinfo['summary'], $courseinfo['summaryformat']) =
                external_format_text($course->summary, $course->summaryformat, $context->id, 'course', 'summary', 0);
            $courseinfo['format'] = $course->format;
            $courseinfo['startdate'] = $course->startdate;
            if (array_key_exists('numsections', $courseformatoptions)) {
                // For backward-compartibility.
                $courseinfo['numsections'] = $courseformatoptions['numsections'];
            }

            // Some field should be returned only if the user has update permission.
            $courseadmin = has_capability('moodle/course:update', $context);
            if ($courseadmin) {
                $courseinfo['categorysortorder'] = $course->sortorder;
                $courseinfo['idnumber'] = $course->idnumber;
                $courseinfo['showgrades'] = $course->showgrades;
                $courseinfo['showreports'] = $course->showreports;
                $courseinfo['newsitems'] = $course->newsitems;
                $courseinfo['visible'] = $course->visible;
                $courseinfo['maxbytes'] = $course->maxbytes;
                if (array_key_exists('hiddensections', $courseformatoptions)) {
                    // For backward-compartibility.
                    $courseinfo['hiddensections'] = $courseformatoptions['hiddensections'];
                }
                $courseinfo['groupmode'] = $course->groupmode;
                $courseinfo['groupmodeforce'] = $course->groupmodeforce;
                $courseinfo['defaultgroupingid'] = $course->defaultgroupingid;
                $courseinfo['lang'] = $course->lang;
                $courseinfo['timecreated'] = $course->timecreated;
                $courseinfo['timemodified'] = $course->timemodified;
                $courseinfo['forcetheme'] = $course->theme;
                $courseinfo['enablecompletion'] = $course->enablecompletion;
                $courseinfo['completionstartonenrol'] = $course->completionstartonenrol;
                $courseinfo['completionnotify'] = $course->completionnotify;
                $courseinfo['courseformatoptions'] = array();
                foreach ($courseformatoptions as $key => $value) {
                    $courseinfo['courseformatoptions'][] = array(
                        'name' => $key,
                        'value' => $value
                    );
                }
            }

            if ($courseadmin or $course->visible
                    or has_capability('moodle/course:viewhiddencourses', $context)) {
                 $coursesinfo[$idnumber] = $courseinfo;
            }
        }
        $coursesinfo[0]['result'] = 'success';
        $coursesinfo[0]['success'] = true;
        return $coursesinfo;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function get_courses_by_idnumber_returns() {
        return new external_multiple_structure(
                new external_single_structure(
                        array(
                            'success'   => new external_value(PARAM_BOOL, 'Return success of operation true or false', VALUE_OPTIONAL),
                            'result'    => new external_value(PARAM_RAW, 'Return message', VALUE_OPTIONAL),
                            'msg' => new external_value(PARAM_TEXT, 'error msg', VALUE_OPTIONAL),
                            'id' => new external_value(PARAM_INT, 'course id', VALUE_OPTIONAL),
                            'shortname' => new external_value(PARAM_TEXT, 'course short name', VALUE_OPTIONAL),
                            'categoryid' => new external_value(PARAM_INT, 'category id', VALUE_OPTIONAL),
                            'categorysortorder' => new external_value(PARAM_INT,
                                    'sort order into the category', VALUE_OPTIONAL),
                            'fullname' => new external_value(PARAM_TEXT, 'full name', VALUE_OPTIONAL),
                            'idnumber' => new external_value(PARAM_RAW, 'id number', VALUE_OPTIONAL),
                            'summary' => new external_value(PARAM_RAW, 'summary', VALUE_OPTIONAL),
                            'summaryformat' => new external_format_value('summary', VALUE_OPTIONAL),
                            'format' => new external_value(PARAM_PLUGIN,
                                    'course format: weeks, topics, social, site,..', VALUE_OPTIONAL),
                            'showgrades' => new external_value(PARAM_INT,
                                    '1 if grades are shown, otherwise 0', VALUE_OPTIONAL),
                            'newsitems' => new external_value(PARAM_INT,
                                    'number of recent items appearing on the course page', VALUE_OPTIONAL),
                            'startdate' => new external_value(PARAM_INT,
                                    'timestamp when the course start', VALUE_OPTIONAL),
                            'numsections' => new external_value(PARAM_INT,
                                    '(deprecated, use courseformatoptions) number of weeks/topics',
                                    VALUE_OPTIONAL),
                            'maxbytes' => new external_value(PARAM_INT,
                                    'largest size of file that can be uploaded into the course',
                                    VALUE_OPTIONAL),
                            'showreports' => new external_value(PARAM_INT,
                                    'are activity report shown (yes = 1, no =0)', VALUE_OPTIONAL),
                            'visible' => new external_value(PARAM_INT,
                                    '1: available to student, 0:not available', VALUE_OPTIONAL),
                            'hiddensections' => new external_value(PARAM_INT,
                                    '(deprecated, use courseformatoptions)
                                        How the hidden sections in the course are displayed to students',
                                    VALUE_OPTIONAL),
                            'groupmode' => new external_value(PARAM_INT, 'no group, separate, visible',
                                    VALUE_OPTIONAL),
                            'groupmodeforce' => new external_value(PARAM_INT, '1: yes, 0: no',
                                    VALUE_OPTIONAL),
                            'defaultgroupingid' => new external_value(PARAM_INT, 'default grouping id',
                                    VALUE_OPTIONAL),
                            'timecreated' => new external_value(PARAM_INT,
                                    'timestamp when the course have been created', VALUE_OPTIONAL),
                            'timemodified' => new external_value(PARAM_INT,
                                    'timestamp when the course have been modified', VALUE_OPTIONAL),
                            'enablecompletion' => new external_value(PARAM_INT,
                                    'Enabled, control via completion and activity settings. Disbaled,
                                        not shown in activity settings.',
                                    VALUE_OPTIONAL),
                            'completionstartonenrol' => new external_value(PARAM_INT,
                                    '1: begin tracking a student\'s progress in course completion
                                        after course enrolment. 0: does not',
                                    VALUE_OPTIONAL),
                            'completionnotify' => new external_value(PARAM_INT,
                                    '1: yes 0: no', VALUE_OPTIONAL),
                            'lang' => new external_value(PARAM_SAFEDIR,
                                    'forced course language', VALUE_OPTIONAL),
                            'forcetheme' => new external_value(PARAM_PLUGIN,
                                    'name of the force theme', VALUE_OPTIONAL),
                            'courseformatoptions' => new external_multiple_structure(
                                new external_single_structure(
                                    array('name' => new external_value(PARAM_ALPHANUMEXT, 'course format option name'),
                                        'value' => new external_value(PARAM_RAW, 'course format option value')
                                )),
                                    'additional options for particular course format', VALUE_OPTIONAL
                             ),
                        ), 'course'
                )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function update_courses_by_idnumber_parameters() {
        return new external_function_parameters(
            array(
                'courses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'fullname' => new external_value(PARAM_TEXT, 'full name', VALUE_OPTIONAL),
                            'shortname' => new external_value(PARAM_TEXT, 'course short name', VALUE_OPTIONAL),
                            'categoryid' => new external_value(PARAM_INT, 'category id', VALUE_OPTIONAL),
                            'idnumber' => new external_value(PARAM_RAW, 'id number'),
                            'summary' => new external_value(PARAM_RAW, 'summary', VALUE_OPTIONAL),
                            'summaryformat' => new external_format_value('summary', VALUE_OPTIONAL),
                            'format' => new external_value(PARAM_PLUGIN,
                                    'course format: weeks, topics, social, site,..',
                                    VALUE_OPTIONAL),
                            'showgrades' => new external_value(PARAM_INT,
                                    '1 if grades are shown, otherwise 0', VALUE_OPTIONAL),
                            'newsitems' => new external_value(PARAM_INT,
                                    'number of recent items appearing on the course page',
                                    VALUE_OPTIONAL),
                            'startdate' => new external_value(PARAM_INT,
                                    'timestamp when the course start', VALUE_OPTIONAL),
                            'numsections' => new external_value(PARAM_INT,
                                    '(deprecated, use courseformatoptions) number of weeks/topics',
                                    VALUE_OPTIONAL),
                            'maxbytes' => new external_value(PARAM_INT,
                                    'largest size of file that can be uploaded into the course',
                                    VALUE_OPTIONAL),
                            'showreports' => new external_value(PARAM_INT,
                                    'are activity report shown (yes = 1, no =0)', VALUE_OPTIONAL),
                            'visible' => new external_value(PARAM_INT,
                                    '1: available to student, 0:not available', VALUE_OPTIONAL),
                            'hiddensections' => new external_value(PARAM_INT,
                                    '(deprecated, use courseformatoptions)
                                        How the hidden sections in the course are displayed to students',
                                    VALUE_OPTIONAL),
                            'groupmode' => new external_value(PARAM_INT, 'no group, separate, visible',
                                    VALUE_OPTIONAL),
                            'groupmodeforce' => new external_value(PARAM_INT, '1: yes, 0: no',
                                    VALUE_OPTIONAL),
                            'defaultgroupingid' => new external_value(PARAM_INT, 'default grouping id',
                                    VALUE_OPTIONAL),
                            'enablecompletion' => new external_value(PARAM_INT,
                                    'Enabled, control via completion and activity settings. Disabled,
                                        not shown in activity settings.',
                                    VALUE_OPTIONAL),
                            'completionstartonenrol' => new external_value(PARAM_INT,
                                    '1: begin tracking a student\'s progress in course completion after
                                        course enrolment. 0: does not',
                                    VALUE_OPTIONAL),
                            'completionnotify' => new external_value(PARAM_INT,
                                    '1: yes 0: no', VALUE_OPTIONAL),
                            'lang' => new external_value(PARAM_SAFEDIR,
                                    'forced course language', VALUE_OPTIONAL),
                            'forcetheme' => new external_value(PARAM_PLUGIN,
                                    'name of the force theme', VALUE_OPTIONAL),
                            'courseformatoptions' => new external_multiple_structure(
                                new external_single_structure(
                                    array('name' => new external_value(PARAM_ALPHANUMEXT, 'course format option name'),
                                        'value' => new external_value(PARAM_RAW, 'course format option value')
                                )),
                                    'additional options for particular course format', VALUE_OPTIONAL),
                        )
                    ), 'courses to update'
                )
            )
        );
    }

    /**
     * Update  courses by idnumber
     *
     * @param array $courses
     * @return array courses (id and shortname only)
     */
    public static function update_courses_by_idnumber($courses) {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/course/lib.php");
        require_once($CFG->libdir . '/completionlib.php');
        require_once($CFG->dirroot."/local/eledia_webservicesuite/lib.php");

        $params = self::validate_parameters(self::update_courses_by_idnumber_parameters(),
                        array('courses' => $courses));

        $availablethemes = get_plugin_list('theme');
        $availablelangs = get_string_manager()->get_list_of_translations();

        $transaction = $DB->start_delegated_transaction();

        foreach ($params['courses'] as $course) {
            // Get course by idnumber & check for unique & existing idnumbers.
            $origin_course = get_record_by_idnumber('course',
                    $course['idnumber'],
                    true,
                    true,
                    'wscoursenotfound',
                    'wsmultiplecoursesfound');

            $update_course = get_object_vars($origin_course);
            foreach ($course as $field => $value) {
                $update_course[$field] = $value;
            }

            // Ensure the current user is allowed to run this function.
            $context = context_coursecat::instance($update_course['category'], IGNORE_MISSING);
            try {
                self::validate_context($context);
            } catch (Exception $e) {
                $exceptionparam = new stdClass();
                $exceptionparam->message = $e->getMessage();
                $exceptionparam->catid = $update_course['category'];
                $exceptionparam->courseid = $update_course['category'];
                throw new moodle_exception('errorcatcontextnotvalid', 'webservice', '', $exceptionparam);
            }
            require_capability('moodle/course:update', $context);

            // Make sure lang is valid.
            if (!empty($update_course['lang']) && empty($availablelangs[$update_course['lang']])) {
                throw new moodle_exception('errorinvalidparam', 'webservice', '', 'lang');
            }

            // Make sure theme is valid.
            if (array_key_exists('forcetheme', $update_course)) {
                if (!empty($CFG->allowcoursethemes)) {
                    if (empty($availablethemes[$update_course['forcetheme']])) {
                        throw new moodle_exception('errorinvalidparam', 'webservice', '', 'forcetheme');
                    } else {
                        $update_course['theme'] = $update_course['forcetheme'];
                    }
                }
            }

            // Force visibility if ws user doesn't have the permission to set it.
            $category = $DB->get_record('course_categories', array('id' => $update_course['categoryid']));
            if (!has_capability('moodle/course:visibility', $context)) {
                $update_course['visible'] = $category->visible;
            }

            $update_course['category'] = $update_course['categoryid'];

            // Summary format.
            $update_course['summaryformat'] = external_validate_format($update_course['summaryformat']);

            if (!empty($update_course['courseformatoptions'])) {
                foreach ($update_course['courseformatoptions'] as $option) {
                    $update_course[$option['name']] = $option['value'];
                }
            }

            // Note: update_course() core function check shortname, idnumber, category.
            update_course((object) $update_course);

        }

        $transaction->allow_commit();

        $output['result'] = 'success';
        $output['success'] = true;
        return $output;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function update_courses_by_idnumber_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'success'   => new external_value(PARAM_BOOL, 'Return success of operation true or false'),
                    'result'    => new external_value(PARAM_RAW, 'Return message'),
                )
            )
        );
    }


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_user_by_idnumber_parameters() {
        return new external_function_parameters(
                array(
                    'idnumber' => new external_value(PARAM_RAW, 'user idnumber'),
                )
        );
    }

    /**
     * Get user information
     * - This function is matching the permissions of /user/profil.php
     * - It is also matching some permissions from /user/editadvanced.php for the following fields:
     *   auth, confirmed, idnumber, lang, theme, timezone, mailformat
     * @param array $usernumber  array of user idnumbers
     * @return array An array of arrays describing users
     */
    public static function get_user_by_idnumber($idnumber) {
        global $CFG;

        require_once($CFG->dirroot . "/user/lib.php");
        require_once($CFG->dirroot."/local/eledia_webservicesuite/lib.php");
        self::validate_parameters(self::get_user_by_idnumber_parameters(), array('idnumber' => $idnumber));

        try {
            $user = get_record_by_idnumber ('user', $idnumber, true, true, 'wsusernotfound', 'wsmultipleusersfound');
        } catch (Exception $exc) {
            $output['result'] = $exc->getMessage();
            $output['success'] = false;
            return $output;
        }
        $hasuserupdatecap = has_capability('moodle/user:update', get_system_context());

        context_instance_preload($user);
        $usercontext = CONTEXT_USER::instance($user->id);
        self::validate_context($usercontext);
        $currentuser = ($user->id == $USER->id);

        if ($userarray  = user_get_user_details($user)) {
            // Fields matching permissions from /user/editadvanced.php.
            if ($currentuser or $hasuserupdatecap) {
                $userarray['auth']       = $user->auth;
                $userarray['confirmed']  = $user->confirmed;
                $userarray['idnumber']   = $user->idnumber;
                $userarray['lang']       = $user->lang;
                $userarray['theme']      = $user->theme;
                $userarray['timezone']   = $user->timezone;
                $userarray['mailformat'] = $user->mailformat;
            }
        }
        $userarray['result'] = 'success';
        $userarray['success'] = true;
        return $userarray;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_user_by_idnumber_returns() {
        return new external_single_structure(
                array(
                    'success'   => new external_value(PARAM_BOOL, 'Return success of operation true or false'),
                    'result'    => new external_value(PARAM_RAW, 'Return message'),
                    'id'    => new external_value(PARAM_NUMBER, 'ID of the user', VALUE_OPTIONAL),
                    'username'    => new external_value(PARAM_RAW,
                            'Username policy is defined in Moodle security config', VALUE_OPTIONAL),
                    'firstname'   => new external_value(PARAM_NOTAGS, 'The first name(s) of the user', VALUE_OPTIONAL),
                    'lastname'    => new external_value(PARAM_NOTAGS, 'The family name of the user', VALUE_OPTIONAL),
                    'fullname'    => new external_value(PARAM_NOTAGS, 'The fullname of the user', VALUE_OPTIONAL),
                    'email'       => new external_value(PARAM_TEXT,
                            'An email address - allow email as root@localhost', VALUE_OPTIONAL),
                    'address'     => new external_value(PARAM_MULTILANG, 'Postal address', VALUE_OPTIONAL),
                    'phone1'      => new external_value(PARAM_NOTAGS, 'Phone 1', VALUE_OPTIONAL),
                    'phone2'      => new external_value(PARAM_NOTAGS, 'Phone 2', VALUE_OPTIONAL),
                    'icq'         => new external_value(PARAM_NOTAGS, 'icq number', VALUE_OPTIONAL),
                    'skype'       => new external_value(PARAM_NOTAGS, 'skype id', VALUE_OPTIONAL),
                    'yahoo'       => new external_value(PARAM_NOTAGS, 'yahoo id', VALUE_OPTIONAL),
                    'aim'         => new external_value(PARAM_NOTAGS, 'aim id', VALUE_OPTIONAL),
                    'msn'         => new external_value(PARAM_NOTAGS, 'msn number', VALUE_OPTIONAL),
                    'department'  => new external_value(PARAM_TEXT, 'department', VALUE_OPTIONAL),
                    'institution' => new external_value(PARAM_TEXT, 'institution', VALUE_OPTIONAL),
                    'interests'   => new external_value(PARAM_TEXT, 'user interests (separated by commas)', VALUE_OPTIONAL),
                    'firstaccess' => new external_value(PARAM_INT, 'first access to the site (0 if never)', VALUE_OPTIONAL),
                    'lastaccess'  => new external_value(PARAM_INT, 'last access to the site (0 if never)', VALUE_OPTIONAL),
                    'auth'        => new external_value(PARAM_PLUGIN,
                            'Auth plugins include manual, ldap, imap, etc', VALUE_OPTIONAL),
                    'confirmed'   => new external_value(PARAM_NUMBER, 'Active user: 1 if confirmed, 0 otherwise', VALUE_OPTIONAL),
                    'idnumber'    => new external_value(PARAM_RAW,
                            'An arbitrary ID code number perhaps from the institution', VALUE_OPTIONAL),
                    'lang'        => new external_value(PARAM_SAFEDIR,
                            'Language code such as "en", must exist on server', VALUE_OPTIONAL),
                    'theme'       => new external_value(PARAM_PLUGIN,
                            'Theme name such as "standard", must exist on server', VALUE_OPTIONAL),
                    'timezone'    => new external_value(PARAM_TIMEZONE,
                            'Timezone code such as Australia/Perth, or 99 for default', VALUE_OPTIONAL),
                    'mailformat'  => new external_value(PARAM_INTEGER,
                            'Mail format code is 0 for plain text, 1 for HTML etc', VALUE_OPTIONAL),
                    'description' => new external_value(PARAM_RAW, 'User profile description', VALUE_OPTIONAL),
                    'descriptionformat' => new external_value(PARAM_INT, 'User profile description format', VALUE_OPTIONAL),
                    'city'        => new external_value(PARAM_NOTAGS, 'Home city of the user', VALUE_OPTIONAL),
                    'url'         => new external_value(PARAM_URL, 'URL of the user', VALUE_OPTIONAL),
                    'country'     => new external_value(PARAM_ALPHA,
                            'Home country code of the user, such as AU or CZ', VALUE_OPTIONAL),
                    'profileimageurlsmall' => new external_value(PARAM_URL, 'User image profile URL - small version', VALUE_OPTIONAL),
                    'profileimageurl' => new external_value(PARAM_URL, 'User image profile URL - big version', VALUE_OPTIONAL),
                    'customfields' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'type'  => new external_value(PARAM_ALPHANUMEXT,
                                        'The type of the custom field - text field, checkbox...'),
                                'value' => new external_value(PARAM_RAW, 'The value of the custom field'),
                                'name' => new external_value(PARAM_RAW, 'The name of the custom field'),
                                'shortname' => new external_value(PARAM_RAW,
                                        'The shortname of the custom field - to be able to build the field class in the code'),
                            )
                    ), 'User custom fields (also known as user profil fields)', VALUE_OPTIONAL),
                    'preferences' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'name'  => new external_value(PARAM_ALPHANUMEXT, 'The name of the preferences'),
                                'value' => new external_value(PARAM_RAW, 'The value of the custom field'),
                            )
                    ), 'User preferences', VALUE_OPTIONAL),
                    'enrolledcourses' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'id'  => new external_value(PARAM_INT, 'Id of the course'),
                                'fullname'  => new external_value(PARAM_RAW, 'Fullname of the course'),
                                'shortname' => new external_value(PARAM_RAW, 'Shortname of the course')
                            )
                    ), 'Courses where the user is enrolled - limited by which courses the user is able to see', VALUE_OPTIONAL)
                )
        );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function unenrol_users_by_idnumber_parameters() {
        return new external_function_parameters(
                array(
                    'enrolments' => new external_multiple_structure(
                            new external_single_structure(
                                    array(
                                        'useridnumber' => new external_value(PARAM_RAW, 'user idnumber'),
                                        'courseidnumber' => new external_value(PARAM_RAW, 'course idnumber'),
                                        'enrolname' => new external_value(PARAM_RAW, 'enrolment'),
                                    )
                            )
                    )
                )
        );
    }

    /**
     * unenrol user from course
     * - This function unenrols a list of users from the the given enrolments in the given courses.
     * @param array $usernumber  array of user idnumbers, course idnumbers and enrolname
     */
    public static function unenrol_users_by_idnumber($params) {
        global $CFG, $DB;

        require_once($CFG->dirroot."/local/eledia_webservicesuite/lib.php");
        self::validate_parameters(self::unenrol_users_by_idnumber_parameters(), array('enrolments' => $params));

        foreach ($params as $param) {
            $user = get_record_by_idnumber ('user', $param['useridnumber'], true, true, 'wsusernotfound', 'wsmultipleusersfound');
            $course = get_record_by_idnumber ('course',
                    $param['courseidnumber'],
                    true,
                    true,
                    'wscoursenotfound',
                    'wsmultiplecoursesfound');

            $enrolmentinstance = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => $param['enrolname']));

            $plugin = enrol_get_plugin($enrolmentinstance->enrol);
            $plugin->unenrol_user($enrolmentinstance, $user->id);
        }

        $output['result'] = 'success';
        $output['success'] = true;
        return $output;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function unenrol_users_by_idnumber_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'success'   => new external_value(PARAM_BOOL, 'Return success of operation true or false'),
                    'result'    => new external_value(PARAM_RAW, 'Return message'),
                )
            )
        );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function course_completion_parameters() {
        return new external_function_parameters(
            array(
                'completion' =>
                new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'useridnumber' => new external_value(PARAM_RAW, 'user idnumber'),
                                'courseidnumber' => new external_value(PARAM_RAW, 'course idnumber'),
                            )
                        )
                )
            )
        );
    }

    /**
     * Returns course completion list
     *
     * @param array $params array contains user idnumber and course idnumber
     * @return array An array with  the completion information for the user in the specified course
     */
    public static function course_completion($params) {
        global $CFG;
        require_once($CFG->dirroot."/local/eledia_webservicesuite/lib.php");

        self::validate_parameters(self::course_completion_parameters(), array('completion' => $params));
        $params = $params[0];
        $output = array();
        require_once($CFG->dirroot.'/lib/completionlib.php');

        try {
            $user = get_record_by_idnumber ('user', $params['useridnumber'], true, true, 'wsusernotfound', 'wsmultipleusersfound');
        } catch (Exception $exc) {
            $output['result'] = $exc->getMessage();
            $output['success'] = false;
            return array($output);
        }

        try {
            $course = get_record_by_idnumber ('course',
                    $params['courseidnumber'],
                    true,
                    true,
                    'wscoursenotfound',
                    'wsmultiplecoursesfound');
        } catch (Exception $exc) {
            $output['result'] = $exc->getMessage();;
            $output['success'] = false;
            return array($output);
        }

        require_capability('report/completion:view', CONTEXT_COURSE::instance($course->id));
        $info = new completion_info($course);
        $result = $info->get_completions($user->id);

        $comp_info_formated = array();
        foreach ($result as $completion) {
            $completion_info = new stdClass();
            $completion_info->criteriaid = $completion->criteriaid;
            $completion_info->gradefinal = $completion->gradefinal;
            $completion_info->timecompleted = $completion->timecompleted;
            $criteria = $completion->get_criteria();
            $completion_info->criteriatype = $criteria->criteriatype;
            $completion_info->module = $criteria->module;
            $completion_info->moduleinstance = $criteria->moduleinstance;
            $completion_info->gradepass = $criteria->gradepass;
            $comp_info_formated[] = $completion_info;
        }

        $ccompletion = new completion_completion(array('userid' => $user->id, 'course' => $course->id));
        $course_comp = $ccompletion->is_complete();
        $output['result'] = 'success';
        $output['success'] = true;
        $output['course_completed'] = $course_comp;
        $output['criteria_list'] = $comp_info_formated;
        return array($output);
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function course_completion_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'success'   => new external_value(PARAM_BOOL, 'Return success of operation true or false'),
                    'result'    => new external_value(PARAM_RAW, 'Return message'),
                    'course_completed'    => new external_value(PARAM_BOOL, 'completion status of the user', VALUE_OPTIONAL),
                    'criteria_list'       => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'criteriaid' => new external_value(PARAM_INT, 'id of the criteria'),
                                'gradefinal' => new external_value(PARAM_FLOAT, 'final grade of the criteria', VALUE_OPTIONAL),
                                'timecompleted' => new external_value(PARAM_INT, 'timestamp of criteria completion', VALUE_OPTIONAL),
                                'criteriatype' => new external_value(PARAM_INT, 'type of the criteria'),
                                'module' => new external_value(PARAM_ALPHA, 'modul id', VALUE_OPTIONAL),
                                'moduleinstance' => new external_value(PARAM_INT, 'modul instance id', VALUE_OPTIONAL),
                                'gradepass' => new external_value(PARAM_FLOAT, 'the grade to pass', VALUE_OPTIONAL),
                            )
                        ), '', VALUE_OPTIONAL
                    )
                )
            )
        );
    }


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function course_completion_simple_parameters() {
        return new external_function_parameters(
            array(
                'useridnumber' => new external_value(PARAM_RAW, 'user idnumber'),
                'courseidnumber' => new external_value(PARAM_RAW, 'course idnumber'),
            )
        );
    }

    /**
     * Returns course completion list
     *
     * @param array $params array contains user idnumber and course idnumber
     * @return array An array with  the completion information for the user in the specified course
     */
    public static function course_completion_simple($params) {
        global $CFG;
        require_once($CFG->dirroot."/local/eledia_webservicesuite/lib.php");

        self::validate_parameters(self::course_completion_simple_parameters(), $params);

        require_once($CFG->dirroot.'/lib/completionlib.php');

        $user = get_record_by_idnumber ('user', $params['useridnumber'], true, true, 'wsusernotfound', 'wsmultipleusersfound');
        if (empty($user)) {
            $output['result'] = 'User not found for idnumber '.$params['useridnumber'];
            $output['success'] = false;
            return array($output);
        }
        $course = get_record_by_idnumber ('course',
                $params['courseidnumber'],
                true,
                true,
                'wscoursenotfound',
                'wsmultiplecoursesfound');
        if (empty($course)) {
            $output['result'] = 'Course not found for idnumber '.$params['courseidnumber'];
            $output['success'] = false;
            return array($output);
        }
        require_capability('report/completion:view', CONTEXT_COURSE::instance($course->id));
        $info = new completion_info($course);
        $result = $info->get_completions($user->id);

        $comp_info_formated = array();
        foreach ($result as $completion) {
            $completion_info = new stdClass();
            $completion_info->criteriaid = $completion->criteriaid;
            $completion_info->gradefinal = $completion->gradefinal;
            $completion_info->timecompleted = $completion->timecompleted;
            $criteria = $completion->get_criteria();
            $completion_info->criteriatype = $criteria->criteriatype;
            $completion_info->module = $criteria->module;
            $completion_info->moduleinstance = $criteria->moduleinstance;
            $completion_info->gradepass = $criteria->gradepass;
            $comp_info_formated[] = $completion_info;
        }

        $ccompletion = new completion_completion(array('userid' => $user->id, 'course' => $course->id));
        $course_comp = $ccompletion->is_complete();

        $output['result'] = 'success';
        $output['success'] = true;
        $output['course_completed'] = $course_comp;
        $output['criteria_list'] = $comp_info_formated;
        return array($output);
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function course_completion_simple_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'success'   => new external_value(PARAM_BOOL, 'Return success of operation true or false'),
                    'result'    => new external_value(PARAM_RAW, 'Return message'),
                    'course_completed'    => new external_value(PARAM_BOOL, 'completion status of the user', VALUE_OPTIONAL),
                    'criteria_list'       => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'criteriaid' => new external_value(PARAM_INT, 'id of the criteria'),
                                'gradefinal' => new external_value(PARAM_FLOAT, 'final grade of the criteria', VALUE_OPTIONAL),
                                'timecompleted' => new external_value(PARAM_INT, 'timestamp of criteria completion', VALUE_OPTIONAL),
                                'criteriatype' => new external_value(PARAM_INT, 'type of the criteria'),
                                'module' => new external_value(PARAM_ALPHA, 'modul id', VALUE_OPTIONAL),
                                'moduleinstance' => new external_value(PARAM_INT, 'modul instance id', VALUE_OPTIONAL),
                                'gradepass' => new external_value(PARAM_FLOAT, 'the grade to pass', VALUE_OPTIONAL),
                            )
                        )
                    ), '', VALUE_OPTIONAL
                )
            )
        );
    }


    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function create_users_parameters() {
        global $CFG;

        return new external_function_parameters(
            array(
                'users' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'username' =>
                                new external_value(PARAM_USERNAME, 'Username policy is defined in Moodle security config.'),
                            'password' =>
                                new external_value(PARAM_RAW, 'Plain text password consisting of any characters'),
                            'firstname' =>
                                new external_value(PARAM_NOTAGS, 'The first name(s) of the user'),
                            'lastname' =>
                                new external_value(PARAM_NOTAGS, 'The family name of the user'),
                            'update_password' =>
                                new external_value(PARAM_BOOL, 'Should user passwords be updated'),
                            'email' =>
                                new external_value(PARAM_EMAIL, 'A valid and unique email address'),
                            'auth' =>
                                new external_value(PARAM_PLUGIN, 'Auth plugins include manual, ldap, imap, etc', VALUE_DEFAULT,
                                    'manual', NULL_NOT_ALLOWED),
                            'idnumber' =>
                                new external_value(PARAM_RAW, 'An arbitrary ID code number perhaps from the institution',
                                    VALUE_DEFAULT, ''),
                            'lang' =>
                                new external_value(PARAM_SAFEDIR, 'Language code such as "en", must exist on server', VALUE_DEFAULT,
                                    $CFG->lang, NULL_NOT_ALLOWED),
                            'calendartype' =>
                                new external_value(PARAM_PLUGIN, 'Calendar type such as "gregorian", must exist on server',
                                    VALUE_DEFAULT, $CFG->calendartype, VALUE_OPTIONAL),
                            'theme' =>
                                new external_value(PARAM_PLUGIN, 'Theme name such as "standard", must exist on server',
                                    VALUE_OPTIONAL),
                            'timezone' =>
                                new external_value(PARAM_TIMEZONE, 'Timezone code such as Australia/Perth, or 99 for default',
                                    VALUE_OPTIONAL),
                            'mailformat' =>
                                new external_value(PARAM_INT, 'Mail format code is 0 for plain text, 1 for HTML etc',
                                    VALUE_OPTIONAL),
                            'description' =>
                                new external_value(PARAM_TEXT, 'User profile description, no HTML', VALUE_OPTIONAL),
                            'city' =>
                                new external_value(PARAM_NOTAGS, 'Home city of the user', VALUE_OPTIONAL),
                            'country' =>
                                new external_value(PARAM_ALPHA, 'Home country code of the user, such as AU or CZ', VALUE_OPTIONAL),
                            'firstnamephonetic' =>
                                new external_value(PARAM_NOTAGS, 'The first name(s) phonetically of the user', VALUE_OPTIONAL),
                            'lastnamephonetic' =>
                                new external_value(PARAM_NOTAGS, 'The family name phonetically of the user', VALUE_OPTIONAL),
                            'middlename' =>
                                new external_value(PARAM_NOTAGS, 'The middle name of the user', VALUE_OPTIONAL),
                            'alternatename' =>
                                new external_value(PARAM_NOTAGS, 'The alternate name of the user', VALUE_OPTIONAL),
                            'preferences' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'type'  => new external_value(PARAM_ALPHANUMEXT, 'The name of the preference'),
                                        'value' => new external_value(PARAM_RAW, 'The value of the preference')
                                    )
                                ), 'User preferences', VALUE_OPTIONAL),
                            'customfields' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'type'  => new external_value(PARAM_ALPHANUMEXT, 'The name of the custom field'),
                                        'value' => new external_value(PARAM_RAW, 'The value of the custom field')
                                    )
                                ), 'User custom fields (also known as user profil fields)', VALUE_OPTIONAL)
                        )
                    )
                )
            )

        );
    }

    /**
     * Create one or more users.
     *
     * @throws invalid_parameter_exception
     * @param array $users An array of users to create.
     * @return array An array of arrays
     * @since Moodle 2.2
     */
    public static function create_users($users) {
        global $CFG, $DB;
        require_once($CFG->dirroot."/lib/weblib.php");
        require_once($CFG->dirroot."/user/lib.php");
        require_once($CFG->dirroot."/user/profile/lib.php"); // Required for customfields related function.

        // Ensure the current user is allowed to run this function.
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('moodle/user:create', $context);

        // Do basic automatic PARAM checks on incoming data, using params description.
        // If any problems are found then exceptions are thrown with helpful error messages.
        $params = self::validate_parameters(self::create_users_parameters(), array('users' => $users));

        $availableauths  = core_component::get_plugin_list('auth');
        unset($availableauths['mnet']);       // These would need mnethostid too.
        unset($availableauths['webservice']); // We do not want new webservice users for now.

        $availablethemes = core_component::get_plugin_list('theme');
        $availablelangs  = get_string_manager()->get_list_of_translations();

        $transaction = $DB->start_delegated_transaction();

        $userids = array();

        foreach ($params['users'] as $user) {
            // Make sure that the username doesn't already exist.
            if ($DB->record_exists('user', array('username' => $user['username'], 'mnethostid' => $CFG->mnet_localhost_id))) {
                throw new invalid_parameter_exception('Username already exists: '.$user['username']);
            }

            // Make sure auth is valid.
            if (empty($availableauths[$user['auth']])) {
                throw new invalid_parameter_exception('Invalid authentication type: '.$user['auth']);
            }

            // Make sure lang is valid.
            if (empty($availablelangs[$user['lang']])) {
                throw new invalid_parameter_exception('Invalid language code: '.$user['lang']);
            }

            // Make sure lang is valid.
            if (!empty($user['theme']) && empty($availablethemes[$user['theme']])) { // Theme is VALUE_OPTIONAL,
                                                                                     // so no default value
                                                                                     // We need to test if the client sent it
                                                                                     // => !empty($user['theme']).
                throw new invalid_parameter_exception('Invalid theme: '.$user['theme']);
            }

            $user['confirmed'] = true;
            $user['mnethostid'] = $CFG->mnet_localhost_id;

            // Start of user info validation.
            // Make sure we validate current user info as handled by current GUI. See user/editadvanced_form.php func validation().
            if (!validate_email($user['email'])) {
                throw new invalid_parameter_exception('Email address is invalid: '.$user['email']);
            } else if ($DB->record_exists('user', array('email' => $user['email'], 'mnethostid' => $user['mnethostid']))) {
                throw new invalid_parameter_exception('Email address already exists: '.$user['email']);
            }
            // End of user info validation.

            // Create the user data now!
            $user['id'] = user_create_user($user, $user['update_password']);

            // Custom fields.
            if (!empty($user['customfields'])) {
                foreach ($user['customfields'] as $customfield) {
                    // Profile_save_data() saves profile file it's expecting a user with the correct id,
                    // and custom field to be named profile_field_"shortname".
                    $user["profile_field_".$customfield['type']] = $customfield['value'];
                }
                profile_save_data((object) $user);
            }

            // Trigger event.
            \core\event\user_created::create_from_userid($user['id'])->trigger();

            // Preferences.
            if (!empty($user['preferences'])) {
                foreach ($user['preferences'] as $preference) {
                    set_user_preference($preference['type'], $preference['value'], $user['id']);
                }
            }

            $userids[] = array('id' => $user['id'], 'username' => $user['username']);
        }

        $transaction->allow_commit();

        return $userids;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.2
     */
    public static function create_users_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id'       => new external_value(PARAM_INT, 'user id'),
                    'username' => new external_value(PARAM_USERNAME, 'user name'),
                )
            )
        );
    }
}
