<?php
/*************************************************
 * Function definition for the eledia get user by mail
 * webservice
 *
 * @author Benjamin Wolf <benjamin.wolf@eledia.de>
 */

$functions = array(

    'elediaservice_get_user_by_mail' => array(
		'classname' => 'eledia_services',
		'methodname' => 'get_user_by_mail',
		'classpath' => 'local/eledia_webservicesuite/externallib.php',
		'description' => 'returns a user object according to the given mail',
		'type' => 'write',
		'capabilities' => 'local/eledia_webservicesuite:access, moodle/user:viewdetails',
	),
    'elediaservice_get_user_by_idnumber' => array(
		'classname' => 'eledia_services',
		'methodname' => 'get_user_by_idnumber',
		'classpath' => 'local/eledia_webservicesuite/externallib.php',
		'description' => 'returns a user object according to the given idnumber',
		'type' => 'write',
		'capabilities' => 'local/eledia_webservicesuite:access, moodle/user:viewdetails',
	),
    'elediaservice_update_users_by_idnumber' => array(
		'classname' => 'eledia_services',
		'methodname' => 'update_users_by_idnumber',
		'classpath' => 'local/eledia_webservicesuite/externallib.php',
		'description' => 'updates the submittet user profile',
		'type' => 'write',
		'capabilities' => 'local/eledia_webservicesuite:access, moodle/user:update',
	),
    'elediaservice_enrol_users_by_idnumber' => array(
		'classname' => 'eledia_services',
		'methodname' => 'enrol_users_by_idnumber',
		'classpath' => 'local/eledia_webservicesuite/externallib.php',
		'description' => 'enrols users in the given courses',
		'type' => 'write',
		'capabilities' => 'local/eledia_webservicesuite:access, enrol/manual:enrol',
	),
        'elediaservice_get_courses_by_idnumber' => array(
		'classname' => 'eledia_services',
		'methodname' => 'get_courses_by_idnumber',
		'classpath' => 'local/eledia_webservicesuite/externallib.php',
		'description' => 'returns a course object according to the given idnumber',
		'type' => 'write',
		'capabilities' => 'local/eledia_webservicesuite:access, moodle/course:view, moodle/course:update, moodle/course:viewhiddencourses',
	),
);

