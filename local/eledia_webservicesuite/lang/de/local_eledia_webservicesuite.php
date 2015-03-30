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
 * Lang file of the elediawebservicesuite.
 *
 * @package    local
 * @subpackage eledia_webservicesuite
 * @author     Benjamin Wolf <support@eledia.de>
 * @copyright  2014 eLeDia GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['back'] = 'zurück';

$string['eledia_webservicesuite:access'] = 'Zugriffs Recht für alle Funktionen dieses webservices';
$string['eledia_desc_header'] = 'eledia webservicesuite';
$string['eledia_desc'] = 'Fomular zum Testen der Funkionen des webservice.<br />
    Beachten sie das die Aufrufe in diesem Formular tatsächlich auf dem System ausgeführt werden.<br />
<br />
Um dieses Formular zu nutzen müssen die soap webservcies aktiviert und konfiguriert sein.<br />
Die Einstellungen zu den webservices finden sie <a href={$a}>hier</a>.<br />
Der Token für den webservice Nutzer muss in der Konfiguration dieses Plugins eingetragen werden.<br />
<br />';
$string['eledia_header'] = 'eledia webservicesuite';

$string['idnumbercourse'] = 'Course ID number';
$string['idnumberuser'] = 'User ID number';

$string['missing_token'] = 'Webservice token in der Plugin Konfiguration fehlt.';

$string['pluginname'] = 'eledia webservicesuite';

$string['start'] = 'Funktion Aufrufen';
$string['service_choose'] = 'Funktion wählen';

$string['test_form_desc'] = 'Das Formular zum Testen finden sie <a href={$a}>hier</a>.';
$string['test_header'] = 'eledia webservicesuite Funktions Test';
$string['test_token'] = 'Token für webservice Tests';

$string['wscannotenrol'] = 'Nutzer konnte nicht Eingeschrieben werden. Kurs ID: {$a->courseid}';
$string['wsnoinstance'] = 'Plugin zur Manuellen einschreibung konnte nicht gefunden werden für den Kurs: {$a->courseid}';
$string['wsusercannotassign'] = 'Sie haben nicht das Recht die Rolle ({$a->roleid}) dem Nutzer ({$a->userid}) in diesem Kurs ({$a->courseid}) zuzuordnen.';
$string['wscoursenotfound'] = 'Kurs mit der Kurs-ID {$a->idnumber} wurde nicht gefunden.';
$string['wsusernotfound'] = 'Nutzer mit der ID-Nummer {$a->idnumber} wurde nicht gefunden.';
$string['wsmultiplecoursesfound'] = 'Mehrere Kurse mit der Kurs-ID {$a->idnumber} gefunden. Kurs-ID muss eindeutig sein.';
$string['wsmultipleusersfound'] = 'Mehrere Nutzer mit der ID-Nummer {$a->idnumber} gefudnen. ID-Nummer muss eindeutig sein.';
$string['wsmultipleidnumbersfound'] = 'ID-Nummer {$a->idnumber} ist nicht eindeutig.';
$string['wsidnumbersnotfound'] = 'ID-Number {$a->idnumber} wurde nicht gefuden.';