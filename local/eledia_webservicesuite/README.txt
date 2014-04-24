This plugin provides new webservice functions for moodle.

Installation:
To install the plugin just copy the folder "eledia_webservicesuite" into moodle/local/.

Afterwards you have to go to http://your-moodle/admin (Site administration -> Notifications) to trigger the installation process.

Usage:
The new functions appear within the moodle webservice function list after installation.
The functions definition can be found within the API Documentation in Site Administration/Plugins/Web Services.

List of functions:
elediaservice_update_users_by_idnumber => updates the submittet user profile identified by idnumber
elediaservice_enrol_users_by_idnumber => enrols users in the given courses, users and courses are identified by idnumber
elediaservice_get_courses_by_idnumber => returns a course object according to the given idnumber
elediaservice_update_courses_by_idnumber => updates a course object according to the given idnumber
elediaservice_get_user_by_idnumber => returns a list of users object according to the given idnumbers
elediaservice_unenrol_users_by_idnumber => unenrols a list of users from the given enrolment in the given courses, user and courses identified by idnumber
elediaservice_course_completion => returns the completion information for the user with the given idnumber and the course with the given idnumber
elediaservice_get_user_by_mail => DEPRECATED: use core_user_get_users_by_field instead
elediaservice_get_users_by_idnumber => DEPRECATED: use core_user_get_users_by_field instead

copyright  2014 eLeDia GmbH http://eledia.de
license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
You can receive a copy of the GNU General Public License at <http:www.gnu.org/licenses/>.
