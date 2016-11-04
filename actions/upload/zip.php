<?php

$container_guid = (int) get_input('container_guid', 0);
$parent_guid = get_input('parent_guid');

set_time_limit(0);

$forward_url = REFERER;

if (empty($container_guid) || !get_uploaded_file('zip_file')) {
	register_error(elgg_echo('file:cannotload'));
	forward(REFERER);
}

$extension_array = explode('.', $_FILES['zip_file']['name']);
if (strtolower(end($extension_array)) !== 'zip') {
	register_error(elgg_echo('file:uploadfailed'));
	forward(REFERER);
}

$file = $_FILES['zip_file'];

// disable notifications of new objects
elgg_unregister_notification_event('object', 'file');

if (file_tools_unzip($file, $container_guid, $parent_guid)) {
	system_message(elgg_echo('file:saved'));
	
	$container = get_entity($container_guid);
	if ($container instanceof ElggGroup) {
		$forward_url = "file/group/{$container->getGUID()}/all#{$parent_guid}";
	} else {
		$forward_url = "file/owner/{$container->username}#{$parent_guid}";
	}
} else {
	register_error(elgg_echo('file:uploadfailed'));
}

// reenable notifications of new objects
elgg_register_notification_event('object', 'file');

forward($forward_url);
