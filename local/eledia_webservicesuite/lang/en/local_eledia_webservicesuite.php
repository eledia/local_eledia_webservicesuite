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

$string['pluginname'] = 'eLeDia webservices';
$string['eledia_webservicesuite:access'] = 'Access right for all functions of this web service';
$string['wscannotenrol'] = 'Plugin instance cannot manually enrol a user in the course id = {$a->courseid}';
$string['wsnoinstance'] = 'Manual enrolment plugin instance doesn\'t exist or is disabled for the course (id = {$a->courseid})';
$string['wsusercannotassign'] = 'You don\'t have the permission to assign this role ({$a->roleid}) to this user ({$a->userid}) in this course({$a->courseid}).';
$string['wscoursenotfound'] = 'Course with idnumber = {$a->idnumber} not found.';
$string['wsusernotfound'] = 'User with idnumber = {$a->idnumber} not found.';
$string['wsmultiplecoursesfound'] = 'Found multiple courses with idnumber = {$a->idnumber}. Idnumber must be unique';
$string['wsmultipleusersfound'] = 'Found multiple users with idnumber = {$a->idnumber}. Idnumber must be unique';

$string['wsmultipleidnumbersfound'] = 'Idnumber {$a->idnumber} is not unique.';
$string['wsidnumbersnotfound'] = 'Idnumber {$a->idnumber} not found.';