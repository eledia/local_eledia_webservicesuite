<?php

require(dirname(dirname(dirname(__FILE__))).'/config.php');

// Check for valid admin user - no guest autologin.
require_login(0, false);
$context = CONTEXT_SYSTEM::instance();
require_capability('moodle/site:config', $context);
$PAGE->set_context($context);

global $DB;
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
    mtrace('<br />Error:<br />');
    mtrace($exc->getMessage());
    mtrace('<br />response: <br />');
    mtrace($client->__getLastResponse());
    exit;
}

// Create Testuser
$testuser = 'testerxforwebservice';
$user = $DB->get_record('user', array('username' => $testuser));
if (empty($user)) {
    require_once($CFG->dirroot.'/user/lib.php');
    $user = new stdClass();
    $user->username = $testuser;
    $user->password = 'Testerw_1';
    $user->firstname = 'a';
    $user->lastname = 'test';
    $user->email = 'a.test1001@eledia.de';
    $user->auth = 'manual';
    $user->idnumber = '749116';
    $user->lang = 'de';
    $user->timezone = 99;
    $user->mailformat = 0;
    $user->description = '';
    $user->city = 'testlingen';
    $user->country = 'DE';
    $user->mnethostid = $CFG->mnet_localhost_id;
    $user->preferences[0] = new stdClass();
    $user->preferences[0]->type = 'maildisplay';
    $user->preferences[0]->value = 0;
    user_create_user($user);
} else {
    $user_obj = new stdClass();
    $user_obj->username = $user->username;
    $user_obj->password = $user->password;
    $user_obj->firstname = $user->firstname;
    $user_obj->lastname = $user->lastname;
    $user_obj->email = $user->email;
    $user_obj->auth = $user->auth;
    $user_obj->idnumber = $user->idnumber;
    $user_obj->lang = $user->lang;
    $user_obj->timezone = $user->timezone;
    $user_obj->mailformat = $user->mailformat;
    $user_obj->description = $user->description;
    $user_obj->city = $user->city;
    $user_obj->country = $user->country;
    if (!empty($user->preferences)) {
        $user_obj->preferences = $user->preferences;
    }
    $user = $user_obj;
}

// Test get_user_by_mail.
mtrace('Test get_user_by_mail');
try {
    $user_by_mail = $client->elediaservice_get_user_by_mail(array($user->email));
    mtrace('...successful<br /><br />');
} catch (Exception $exc) {
    mtrace('<br />Error:<br />');
    mtrace($exc->getMessage());
    mtrace('<br />response: <br />');
    mtrace($client->__getLastResponse());
}
//ob_start();
//print_object($user_by_mail);
//$debug_out = ob_get_contents();
//ob_end_clean();
//mtrace('Result: '.$debug_out);

// Test get_users_by_idnumber.
mtrace('Test get_users_by_idnumber');
try {
    $users_by_idnumber = $client->elediaservice_get_users_by_idnumber(array($user->idnumber));
    mtrace('...successful<br /><br />');
} catch (Exception $exc) {
    mtrace('<br />Error:<br />');
    mtrace($exc->getMessage());
    mtrace('<br />response: <br />');
    mtrace($client->__getLastResponse());
}
//ob_start();
//print_object($users_by_idnumber);
//$debug_out = ob_get_contents();
//ob_end_clean();
//mtrace('Result: '.$debug_out);

// Test update_users_by_idnumber.
mtrace('Test update_users_by_idnumber -> update firstname to b');
$user->firstname = 'b';
try {
    $update_users_by_idnumber = $client->elediaservice_update_users_by_idnumber(array($user));
    mtrace('...successful<br /><br />');
} catch (Exception $exc) {
    mtrace('<br />Error:<br />');
    mtrace($exc->getMessage());
    mtrace('<br />response: <br />');
    mtrace($client->__getLastResponse());
}

// Test get_user_by_idnumber.
mtrace('Test get_user_by_idnumber');
try {
    $user_by_idnumber = $client->elediaservice_get_user_by_idnumber($user->idnumber);
    mtrace('...successful<br /><br />');
} catch (Exception $exc) {
    mtrace('<br />Error:<br />');
    mtrace($exc->getMessage());
    mtrace('<br />response: <br />');
    mtrace($client->__getLastResponse());
}
//ob_start();
//print_object($user_by_idnumber);
//$debug_out = ob_get_contents();
//ob_end_clean();
//mtrace('Result: '.$debug_out);

user_delete_user($user);
