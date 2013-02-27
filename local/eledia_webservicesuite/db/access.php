<?php
/*************************************************
 * Capabilites for eledia_gradeexport_webservice
 *
 * Defines one all purpose capability, that is used to control
 * access to the webservice
 *
 * @author Benjamin Wolf <benjamin.wolf@eledia.de>
 */

$capabilities = array(
	'local/eledia_webservicesuite:access' => array(
		'captype' => 'write',
		'contextlevel' => CONTEXT_SYSTEM,
	),
);


