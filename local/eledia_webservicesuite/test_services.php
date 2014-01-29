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
 *
 *
 * @package local
 * @category eledia_webservicesuite
 * @copyright 2013 eLeDia GmbH {@link http://www.eledia.de}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once('test_services_form.php');

// Check for valid admin user - no guest autologin.
require_login(0, false);
$context = CONTEXT_SYSTEM::instance();
require_capability('moodle/site:config', $context);
$PAGE->set_context($context);

$myurl = new moodle_url($FULLME);
// $myurl->remove_params();

$PAGE->set_url($myurl);
$PAGE->set_pagelayout('course');

$mform = new test_services_form(null, array());

if ($mform->is_cancelled()) {
    redirect($CFG->httpswwwroot.'/index.php');
}

if ($formdata = $mform->get_data()) {

}

$PAGE->navbar->add('test webservices');

$header = get_string('test_header', 'local_eledia_webservicesuite');
$PAGE->set_heading($header);

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
