<?php
/**
 * The externallib of the elediawebservice get user by mail.
 *
 * Here you'll find the methods you can directly access through
 * the webservice.
 *
 * @author Benjamin Wolf <benjamin.wolf@eledia.de>
 */
defined('MOODLE_INTERNAL') || die;
require_once $CFG->libdir.'/externallib.php';

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
    public static function get_user_by_mail($mails){
        global $DB, $CFG;

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
            $usercontext = get_context_instance(CONTEXT_USER, $user->id);
            self::validate_context($usercontext);
            $currentuser = ($user->id == $USER->id);
            if ($userarray  = user_get_user_details($user)) {
                //fields matching permissions from /user/editadvanced.php
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
                    'username'    => new external_value(PARAM_RAW, 'Username policy is defined in Moodle security config', VALUE_OPTIONAL),
                    'firstname'   => new external_value(PARAM_NOTAGS, 'The first name(s) of the user', VALUE_OPTIONAL),
                    'lastname'    => new external_value(PARAM_NOTAGS, 'The family name of the user', VALUE_OPTIONAL),
                    'fullname'    => new external_value(PARAM_NOTAGS, 'The fullname of the user'),
                    'email'       => new external_value(PARAM_TEXT, 'An email address - allow email as root@localhost', VALUE_OPTIONAL),
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
                    'auth'        => new external_value(PARAM_PLUGIN, 'Auth plugins include manual, ldap, imap, etc', VALUE_OPTIONAL),
                    'confirmed'   => new external_value(PARAM_NUMBER, 'Active user: 1 if confirmed, 0 otherwise', VALUE_OPTIONAL),
                    'idnumber'    => new external_value(PARAM_RAW, 'An arbitrary ID code number perhaps from the institution', VALUE_OPTIONAL),
                    'lang'        => new external_value(PARAM_SAFEDIR, 'Language code such as "en", must exist on server', VALUE_OPTIONAL),
                    'theme'       => new external_value(PARAM_PLUGIN, 'Theme name such as "standard", must exist on server', VALUE_OPTIONAL),
                    'timezone'    => new external_value(PARAM_TIMEZONE, 'Timezone code such as Australia/Perth, or 99 for default', VALUE_OPTIONAL),
                    'mailformat'  => new external_value(PARAM_INTEGER, 'Mail format code is 0 for plain text, 1 for HTML etc', VALUE_OPTIONAL),
                    'description' => new external_value(PARAM_RAW, 'User profile description', VALUE_OPTIONAL),
                    'descriptionformat' => new external_value(PARAM_INT, 'User profile description format', VALUE_OPTIONAL),
                    'city'        => new external_value(PARAM_NOTAGS, 'Home city of the user', VALUE_OPTIONAL),
                    'url'         => new external_value(PARAM_URL, 'URL of the user', VALUE_OPTIONAL),
                    'country'     => new external_value(PARAM_ALPHA, 'Home country code of the user, such as AU or CZ', VALUE_OPTIONAL),
                    'profileimageurlsmall' => new external_value(PARAM_URL, 'User image profile URL - small version'),
                    'profileimageurl' => new external_value(PARAM_URL, 'User image profile URL - big version'),
                    'customfields' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'type'  => new external_value(PARAM_ALPHANUMEXT, 'The type of the custom field - text field, checkbox...'),
                                'value' => new external_value(PARAM_RAW, 'The value of the custom field'),
                                'name' => new external_value(PARAM_RAW, 'The name of the custom field'),
                                'shortname' => new external_value(PARAM_RAW, 'The shortname of the custom field - to be able to build the field class in the code'),
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
            $usercontext = get_context_instance(CONTEXT_USER, $user->id);
            self::validate_context($usercontext);
            $currentuser = ($user->id == $USER->id);

            if ($userarray  = user_get_user_details($user)) {
                //fields matching permissions from /user/editadvanced.php
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
                    'username'    => new external_value(PARAM_RAW, 'Username policy is defined in Moodle security config', VALUE_OPTIONAL),
                    'firstname'   => new external_value(PARAM_NOTAGS, 'The first name(s) of the user', VALUE_OPTIONAL),
                    'lastname'    => new external_value(PARAM_NOTAGS, 'The family name of the user', VALUE_OPTIONAL),
                    'fullname'    => new external_value(PARAM_NOTAGS, 'The fullname of the user'),
                    'email'       => new external_value(PARAM_TEXT, 'An email address - allow email as root@localhost', VALUE_OPTIONAL),
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
                    'auth'        => new external_value(PARAM_PLUGIN, 'Auth plugins include manual, ldap, imap, etc', VALUE_OPTIONAL),
                    'confirmed'   => new external_value(PARAM_NUMBER, 'Active user: 1 if confirmed, 0 otherwise', VALUE_OPTIONAL),
                    'idnumber'    => new external_value(PARAM_RAW, 'An arbitrary ID code number perhaps from the institution', VALUE_OPTIONAL),
                    'lang'        => new external_value(PARAM_SAFEDIR, 'Language code such as "en", must exist on server', VALUE_OPTIONAL),
                    'theme'       => new external_value(PARAM_PLUGIN, 'Theme name such as "standard", must exist on server', VALUE_OPTIONAL),
                    'timezone'    => new external_value(PARAM_TIMEZONE, 'Timezone code such as Australia/Perth, or 99 for default', VALUE_OPTIONAL),
                    'mailformat'  => new external_value(PARAM_INTEGER, 'Mail format code is 0 for plain text, 1 for HTML etc', VALUE_OPTIONAL),
                    'description' => new external_value(PARAM_RAW, 'User profile description', VALUE_OPTIONAL),
                    'descriptionformat' => new external_value(PARAM_INT, 'User profile description format', VALUE_OPTIONAL),
                    'city'        => new external_value(PARAM_NOTAGS, 'Home city of the user', VALUE_OPTIONAL),
                    'url'         => new external_value(PARAM_URL, 'URL of the user', VALUE_OPTIONAL),
                    'country'     => new external_value(PARAM_ALPHA, 'Home country code of the user, such as AU or CZ', VALUE_OPTIONAL),
                    'profileimageurlsmall' => new external_value(PARAM_URL, 'User image profile URL - small version'),
                    'profileimageurl' => new external_value(PARAM_URL, 'User image profile URL - big version'),
                    'customfields' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'type'  => new external_value(PARAM_ALPHANUMEXT, 'The type of the custom field - text field, checkbox...'),
                                'value' => new external_value(PARAM_RAW, 'The value of the custom field'),
                                'name' => new external_value(PARAM_RAW, 'The name of the custom field'),
                                'shortname' => new external_value(PARAM_RAW, 'The shortname of the custom field - to be able to build the field class in the code'),
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
                            'idnumber'    => new external_value(PARAM_RAW, 'An arbitrary ID code number perhaps from the institution'),
                            'username'    => new external_value(PARAM_USERNAME, 'Username policy is defined in Moodle security config. Must be lowercase.', VALUE_OPTIONAL, '',NULL_NOT_ALLOWED),
                            'password'    => new external_value(PARAM_RAW, 'Plain text password consisting of any characters', VALUE_OPTIONAL, '',NULL_NOT_ALLOWED),
                            'firstname'   => new external_value(PARAM_NOTAGS, 'The first name(s) of the user', VALUE_OPTIONAL, '',NULL_NOT_ALLOWED),
                            'lastname'    => new external_value(PARAM_NOTAGS, 'The family name of the user', VALUE_OPTIONAL),
                            'email'       => new external_value(PARAM_EMAIL, 'A valid and unique email address', VALUE_OPTIONAL, '',NULL_NOT_ALLOWED),
                            'auth'        => new external_value(PARAM_PLUGIN, 'Auth plugins include manual, ldap, imap, etc', VALUE_OPTIONAL, '', NULL_NOT_ALLOWED),
                            'lang'        => new external_value(PARAM_SAFEDIR, 'Language code such as "en", must exist on server', VALUE_OPTIONAL, '', NULL_NOT_ALLOWED),
                            'theme'       => new external_value(PARAM_PLUGIN, 'Theme name such as "standard", must exist on server', VALUE_OPTIONAL),
                            'timezone'    => new external_value(PARAM_TIMEZONE, 'Timezone code such as Australia/Perth, or 99 for default', VALUE_OPTIONAL),
                            'mailformat'  => new external_value(PARAM_INTEGER, 'Mail format code is 0 for plain text, 1 for HTML etc', VALUE_OPTIONAL),
                            'description' => new external_value(PARAM_TEXT, 'User profile description, no HTML', VALUE_OPTIONAL),
                            'city'        => new external_value(PARAM_NOTAGS, 'Home city of the user', VALUE_OPTIONAL),
                            'country'     => new external_value(PARAM_ALPHA, 'Home country code of the user, such as AU or CZ', VALUE_OPTIONAL),
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
        require_once($CFG->dirroot."/user/profile/lib.php"); //required for customfields related function
        require_once($CFG->dirroot."/local/eledia_webservicesuite/lib.php");

        // Ensure the current user is allowed to run this function
        $context = get_context_instance(CONTEXT_SYSTEM);
        require_capability('moodle/user:update', $context);
        self::validate_context($context);

        $params = self::validate_parameters(self::update_users_by_idnumber_parameters(), array('users'=>$users));

        $transaction = $DB->start_delegated_transaction();

        foreach ($params['users'] as $user) {
            //get user by idnumber & check for unique & existing idnumbers
            $local_user = get_record_by_idnumber('user', $user['idnumber'], true, true, 'wsusernotfound', 'wsmultipleusersfound');
            $user['id'] = $local_user->id;

            user_update_user($user);
            //update user custom fields
            if(!empty($user['customfields'])) {

                foreach($user['customfields'] as $customfield) {
                    $user["profile_field_".$customfield['type']] = $customfield['value']; //profile_save_data() saves profile file
                                                                                            //it's expecting a user with the correct id,
                                                                                            //and custom field to be named profile_field_"shortname"
                }
                profile_save_data((object) $user);
            }

            //preferences
            if (!empty($user['preferences'])) {
                foreach($user['preferences'] as $preference) {
                    set_user_preference($preference['type'], $preference['value'],$user['id']);
                }
            }
        }

        $transaction->allow_commit();

        return null;
    }

   /**
     * Returns description of method result value
     * @return external_description
     */
    public static function update_users_by_idnumber_returns() {
        return null;
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
                                        'useridnumber' => new external_value(PARAM_RAW, 'The user idnumber that is going to be enrolled'),
                                        'courseidnumber' => new external_value(PARAM_RAW, 'The course idnumber to enrol the user role in'),
                                        'timestart' => new external_value(PARAM_INT, 'Timestamp when the enrolment start', VALUE_OPTIONAL),
                                        'timeend' => new external_value(PARAM_INT, 'Timestamp when the enrolment end', VALUE_OPTIONAL),
                                        'suspend' => new external_value(PARAM_INT, 'set to 1 to suspend the enrolment', VALUE_OPTIONAL)
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

        $transaction = $DB->start_delegated_transaction(); //rollback all enrolment if an error occurs
                                                           //(except if the DB doesn't support it)

        //retrieve the manual enrolment plugin
        $enrol = enrol_get_plugin('manual');
        if (empty($enrol)) {
            throw new moodle_exception('manualpluginnotinstalled', 'enrol_manual');
        }

        foreach ($params['enrolments'] as $enrolment) {

            //get course and user by idnumber & check for unique & existing idnumbers
            $local_course = get_record_by_idnumber('course', $enrolment['courseidnumber'], true, true, 'wscoursenotfound', 'wsmultiplecoursessfound');
            $enrolment['courseid'] = $local_course->id;

            $local_user = get_record_by_idnumber('user', $enrolment['useridnumber'], true, true, 'wsusernotfound', 'wsmultipleusersfound');
            $enrolment['userid'] = $local_user->id;

            // Ensure the current user is allowed to run this function in the enrolment context
            $context = get_context_instance(CONTEXT_COURSE, $enrolment['courseid']);
            self::validate_context($context);

            //check that the user has the permission to manual enrol
            require_capability('enrol/manual:enrol', $context);

            //throw an exception if user is not able to assign the role
            $roles = get_assignable_roles($context);

            if (!key_exists($enrolment['roleid'], $roles)) {
                $errorparams = new stdClass();
                $errorparams->roleid = $enrolment['roleid'];
                $errorparams->courseid = $enrolment['courseid'];
                $errorparams->userid = $enrolment['userid'];
                throw new moodle_exception('wsusercannotassign', 'local_eledia_webservicesuite', '', $errorparams);
            }

            //check manual enrolment plugin instance is enabled/exist
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

            //check that the plugin accept enrolment (it should always the case, it's hard coded in the plugin)
            if (!$enrol->allow_enrol($instance)) {
                $errorparams = new stdClass();
                $errorparams->roleid = $enrolment['roleid'];
                $errorparams->courseid = $enrolment['courseid'];
                $errorparams->userid = $enrolment['userid'];
                throw new moodle_exception('wscannotenrol', 'local_eledia_webservicesuite', '', $errorparams);
            }

            //finally proceed the enrolment
            $enrolment['timestart'] = isset($enrolment['timestart']) ? $enrolment['timestart'] : 0;
            $enrolment['timeend'] = isset($enrolment['timeend']) ? $enrolment['timeend'] : 0;
            $enrolment['status'] = (isset($enrolment['suspend']) && !empty($enrolment['suspend'])) ?
                    ENROL_USER_SUSPENDED : ENROL_USER_ACTIVE;

            $enrol->enrol_user($instance, $enrolment['userid'], $enrolment['roleid'],
                    $enrolment['timestart'], $enrolment['timeend'], $enrolment['status']);

        }

        $transaction->allow_commit();

        return null;
    }

    /**
     * Returns description of method result value
     * @return null
     */
    public static function enrol_users_by_idnumber_returns() {
        return null;
//        return new external_value(PARAM_BOOL, 'Success');//enrol user function does not give us a success or not information
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.3
     */
    public static function get_courses_by_idnumber_parameters() {
        return new external_function_parameters(
                array('options' => new external_single_structure(
                            array('idnumbers' => new external_multiple_structure(
                                        new external_value(PARAM_RAW, 'Course idnumber')
                                        , 'List of course idnumbers. If empty return all courses
                                            except front page course.',
                                        VALUE_OPTIONAL)
                            ), 'options - operator OR is used', VALUE_DEFAULT, array())
                )
        );
    }

    /**
     * Get courses by idnumber
     *
     * @param array $options It contains an array (list of ids)
     * @return array
     * @since Moodle 2.2
     */
    public static function get_courses_by_idnumber($options = array()) {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/course/lib.php");
        require_once($CFG->dirroot."/local/eledia_webservicesuite/lib.php");

        //validate parameter
        $params = self::validate_parameters(self::get_courses_by_idnumber_parameters(),
                        array('options' => $options));


        //retrieve courses
        if (!array_key_exists('idnumbers', $params['options'])
                or empty($params['options']['idnumbers'])) {
            $courses = $DB->get_records('course');
        } else {
            $courses = array();
            foreach ($params['options']['idnumbers'] as $idnumber) {
                //get course by idnumber
                $course = get_record_by_idnumber('course', $idnumber, false, true, '', 'wsmultiplecoursesfound');
                if (empty($course)){
                    $courses[$idnumber] = null;
                } else {
                    $courses[$idnumber] = $course;
                }
            }
        }

        //create return value
        $coursesinfo = array();
        foreach ($courses as $idnumber => $course) {

            //if course not found, set info text
            if (!$course) {
                $msg = array();
                $msg['msg'] = 'course not found';
                $coursesinfo[$idnumber] = $msg;
                continue;
            }

            // now security checks
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
                 // For backward-compartibility
                 $courseinfo['numsections'] = $courseformatoptions['numsections'];
             }

             //some field should be returned only if the user has update permission
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
                     // For backward-compartibility
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

//ob_start();
//print_r($coursesinfo);
//$debug_out = ob_get_contents();
//ob_end_clean();
//file_put_contents($CFG->dataroot."/webservice_debug.txt", $debug_out, FILE_APPEND);

        return $coursesinfo;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.2
     */
    public static function get_courses_by_idnumber_returns() {
        return new external_multiple_structure(
                new external_single_structure(
                        array(
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
                                    '(deprecated, use courseformatoptions) How the hidden sections in the course are displayed to students',
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
     * @since Moodle 2.2
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
                                    '(deprecated, use courseformatoptions) How the hidden sections in the course are displayed to students',
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
     * @since Moodle 2.2
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
            //get course by idnumber & check for unique & existing idnumbers
            $origin_course = get_record_by_idnumber('course', $course['idnumber'], true, true, 'wscoursenotfound', 'wsmultiplecoursesfound');

            $update_course = get_object_vars($origin_course);
            foreach ($course as $field => $value) {
                $update_course[$field] =  $value;
            }

            // Ensure the current user is allowed to run this function
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

            // Make sure lang is valid
            if (!empty($update_course['lang']) && empty($availablelangs[$update_course['lang']])) {
                throw new moodle_exception('errorinvalidparam', 'webservice', '', 'lang');
            }

            // Make sure theme is valid
            if (array_key_exists('forcetheme', $update_course)) {
                if (!empty($CFG->allowcoursethemes)) {
                    if (empty($availablethemes[$update_course['forcetheme']])) {
                        throw new moodle_exception('errorinvalidparam', 'webservice', '', 'forcetheme');
                    } else {
                        $update_course['theme'] = $update_course['forcetheme'];
                    }
                }
            }

            //force visibility if ws user doesn't have the permission to set it
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

            //Note: update_course() core function check shortname, idnumber, category
            update_course((object) $update_course);

        }

        $transaction->allow_commit();

        return null;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.2
     */
    public static function update_courses_by_idnumber_returns() {
        return null;
    }


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_user_by_idnumber_parameters() {
        return new external_function_parameters(
                array(
                    'idnumber' => new external_value(PARAM_RAW, 'user idnumber'),//new external_multiple_structure()
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

        $user = get_record_by_idnumber ('user', $idnumber, true, true, 'wsusernotfound', 'wsmultipleusersfound');

        $hasuserupdatecap = has_capability('moodle/user:update', get_system_context());

        context_instance_preload($user);
        $usercontext = get_context_instance(CONTEXT_USER, $user->id);
        self::validate_context($usercontext);
        $currentuser = ($user->id == $USER->id);

        if ($userarray  = user_get_user_details($user)) {
            //fields matching permissions from /user/editadvanced.php
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
        return $userarray;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_user_by_idnumber_returns() {
        return new external_single_structure(
                array(
                    'id'    => new external_value(PARAM_NUMBER, 'ID of the user'),
                    'username'    => new external_value(PARAM_RAW, 'Username policy is defined in Moodle security config', VALUE_OPTIONAL),
                    'firstname'   => new external_value(PARAM_NOTAGS, 'The first name(s) of the user', VALUE_OPTIONAL),
                    'lastname'    => new external_value(PARAM_NOTAGS, 'The family name of the user', VALUE_OPTIONAL),
                    'fullname'    => new external_value(PARAM_NOTAGS, 'The fullname of the user'),
                    'email'       => new external_value(PARAM_TEXT, 'An email address - allow email as root@localhost', VALUE_OPTIONAL),
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
                    'auth'        => new external_value(PARAM_PLUGIN, 'Auth plugins include manual, ldap, imap, etc', VALUE_OPTIONAL),
                    'confirmed'   => new external_value(PARAM_NUMBER, 'Active user: 1 if confirmed, 0 otherwise', VALUE_OPTIONAL),
                    'idnumber'    => new external_value(PARAM_RAW, 'An arbitrary ID code number perhaps from the institution', VALUE_OPTIONAL),
                    'lang'        => new external_value(PARAM_SAFEDIR, 'Language code such as "en", must exist on server', VALUE_OPTIONAL),
                    'theme'       => new external_value(PARAM_PLUGIN, 'Theme name such as "standard", must exist on server', VALUE_OPTIONAL),
                    'timezone'    => new external_value(PARAM_TIMEZONE, 'Timezone code such as Australia/Perth, or 99 for default', VALUE_OPTIONAL),
                    'mailformat'  => new external_value(PARAM_INTEGER, 'Mail format code is 0 for plain text, 1 for HTML etc', VALUE_OPTIONAL),
                    'description' => new external_value(PARAM_RAW, 'User profile description', VALUE_OPTIONAL),
                    'descriptionformat' => new external_value(PARAM_INT, 'User profile description format', VALUE_OPTIONAL),
                    'city'        => new external_value(PARAM_NOTAGS, 'Home city of the user', VALUE_OPTIONAL),
                    'url'         => new external_value(PARAM_URL, 'URL of the user', VALUE_OPTIONAL),
                    'country'     => new external_value(PARAM_ALPHA, 'Home country code of the user, such as AU or CZ', VALUE_OPTIONAL),
                    'profileimageurlsmall' => new external_value(PARAM_URL, 'User image profile URL - small version'),
                    'profileimageurl' => new external_value(PARAM_URL, 'User image profile URL - big version'),
                    'customfields' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'type'  => new external_value(PARAM_ALPHANUMEXT, 'The type of the custom field - text field, checkbox...'),
                                'value' => new external_value(PARAM_RAW, 'The value of the custom field'),
                                'name' => new external_value(PARAM_RAW, 'The name of the custom field'),
                                'shortname' => new external_value(PARAM_RAW, 'The shortname of the custom field - to be able to build the field class in the code'),
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
            $course = get_record_by_idnumber ('course', $param['courseidnumber'], true, true, 'wscoursenotfound', 'wsmultiplecoursesfound');

            $enrolmentinstance = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => $param['enrolname']));

            $plugin = enrol_get_plugin($enrolmentinstance->enrol);
            $plugin->unenrol_user($enrolmentinstance, $user->id);
        }
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function unenrol_users_by_idnumber_returns() {
        return null;
    }

}

