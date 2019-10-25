<?php

$action = get_input('hide');
$file_guid = (int) get_input('guid');

if (empty($file_guid)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$file = get_entity($file_guid);

if (!($file instanceof \ElggFile)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

if (!$file->canEdit()) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

if ($action === 'show') {
	$file->show_in_widget = time();
} elseif ($action === 'hide') {
	unset($file->show_in_widget);
}

if (stristr($_SERVER['HTTP_REFERER'], 'file')) {
	
	$folders = elgg_get_entities([
		'type' => 'object',
		'subtype' => FileToolsFolder::SUBTYPE,
		'container_guid' => $file->getOwnerGUID(),
		'limit' => 1,
		'relationship' => FileToolsFolder::RELATIONSHIP,
		'relationship_guid' => $file->guid,
		'inverse_relationship' => true,
	]);
	
	if (!empty($folders)) {
		$folder = $folders[0];
		
		return elgg_ok_response('', '', $folder->getURL());
	}
}

return elgg_ok_response();
