<?php

$container_guid = (int) get_input('container_guid', 0);
$parent_guid = get_input('parent_guid');

set_time_limit(0);

if (empty($container_guid) || !get_uploaded_file('zip_file')) {
	return elgg_error_response(elgg_echo('file:cannotload'));
}

$extension_array = explode('.', $_FILES['zip_file']['name']);
if (strtolower(end($extension_array)) !== 'zip') {
	return elgg_error_response(elgg_echo('file:uploadfailed'));
}

$file = $_FILES['zip_file'];

// disable notifications of new objects
elgg_unregister_notification_event('object', 'file');

$unzip_result = file_tools_unzip($file, $container_guid, $parent_guid);

// reenable notifications of new objects
elgg_register_notification_event('object', 'file');

if (!$unzip_result) {
	return elgg_error_response(elgg_echo('file:uploadfailed'));
}

return elgg_ok_response('', elgg_echo('file:saved'));
