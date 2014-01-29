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
 * @category eledia_ldap_confirm
 * @copyright 2013 eLeDia GmbH {@link http://www.eledia.de}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_eledia_webservicesuite', get_string('pluginname', 'local_eledia_webservicesuite'));
    $ADMIN->add('localplugins', $settings);

    $configs = array();

    $configs[] = new admin_setting_configtext('test_token', get_string('test_token', 'local_eledia_webservicesuite'), '', '', PARAM_RAW, 40);

    foreach ($configs as $config) {
        $config->plugin = 'local_eledia_webservicesuite';
        $settings->add($config);
    }
}