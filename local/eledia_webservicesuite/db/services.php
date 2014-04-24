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
 * Function definition for the eledia_webservicesuite functions.
 *
 * @package    local
 * @subpackage eledia_webservicesuite
 * @author     Benjamin Wolf <support@eledia.de>
 * @copyright  2014 eLeDia GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = array(
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
        'capabilities' => 'local/eledia_webservicesuite:access, enrol/manual:enrol, moodle/role:assign',
    ),
    'elediaservice_get_courses_by_idnumber' => array(
        'classname' => 'eledia_services',
        'methodname' => 'get_courses_by_idnumber',
        'classpath' => 'local/eledia_webservicesuite/externallib.php',
        'description' => 'returns a course object according to the given idnumber',
        'type' => 'write',
        'capabilities' => 'local/eledia_webservicesuite:access, moodle/course:view,
            moodle/course:update, moodle/course:viewhiddencourses',
    ),
    'elediaservice_update_courses_by_idnumber' => array(
        'classname' => 'eledia_services',
        'methodname' => 'update_courses_by_idnumber',
        'classpath' => 'local/eledia_webservicesuite/externallib.php',
        'description' => 'updates a course object according to the given idnumber',
        'type' => 'write',
        'capabilities' => 'local/eledia_webservicesuite:access, moodle/course:view, moodle/course:update,
            moodle/course:viewhiddencourses, moodle/course:visibility',
    ),
    'elediaservice_get_user_by_idnumber' => array(
        'classname' => 'eledia_services',
        'methodname' => 'get_user_by_idnumber',
        'classpath' => 'local/eledia_webservicesuite/externallib.php',
        'description' => 'returns a list of users object according to the given idnumbers',
        'type' => 'write',
        'capabilities' => 'local/eledia_webservicesuite:access, moodle/user:viewdetails',
    ),
    'elediaservice_unenrol_users_by_idnumber' => array(
        'classname' => 'eledia_services',
        'methodname' => 'unenrol_users_by_idnumber',
        'classpath' => 'local/eledia_webservicesuite/externallib.php',
        'description' => 'unenrols a list of users from the given enrolment in the given courses',
        'type' => 'write',
        'capabilities' => 'local/eledia_webservicesuite:access, enrol/manual:unenrol',
    ),
    'elediaservice_course_completion' => array(
        'classname' => 'eledia_services',
        'methodname' => 'course_completion',
        'classpath' => 'local/eledia_webservicesuite/externallib.php',
        'description' =>
            'returns the completion information for the user with the given idnumber and the course with the given idnumber',
        'type' => 'write',
        'capabilities' => 'report/completion:view',
    ),
    'elediaservice_get_user_by_mail' => array(
        'classname' => 'eledia_services',
        'methodname' => 'get_user_by_mail',
        'classpath' => 'local/eledia_webservicesuite/externallib.php',
        'description' => 'returns a user object according to the given mail
            DEPRECATED: use core_user_get_users_by_field instead',
        'type' => 'write',
        'capabilities' => 'local/eledia_webservicesuite:access, moodle/user:viewdetails',
    ),
    'elediaservice_get_users_by_idnumber' => array(
        'classname' => 'eledia_services',
        'methodname' => 'get_users_by_idnumber',
        'classpath' => 'local/eledia_webservicesuite/externallib.php',
        'description' => 'returns a list of users object according to the given idnumbers
            DEPRECATED: use core_user_get_users_by_field instead',
        'type' => 'write',
        'capabilities' => 'local/eledia_webservicesuite:access, moodle/user:viewdetails',
    ),
);

