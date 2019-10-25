<?php

$file_guid = (int) get_input('file_guid', 0);
$folder_guid = (int) get_input('folder_guid', 0);

if (empty($file_guid)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$file = get_entity($file_guid);
if (empty($file)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$container_entity = $file->getContainerEntity();
if (!$file->canEdit() && (($container_entity instanceof ElggGroup) && !$container_entity->isMember())) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

$msg = '';
if ($file instanceof ElggFile) {
	// check if a given guid is a folder
	if (!empty($folder_guid)) {
		$folder = get_entity($folder_guid);
		if (!$folder instanceof FileToolsFolder) {
			unset($folder_guid);
		}
	}
	
	// remove old relationships
	remove_entity_relationships($file->guid, FileToolsFolder::RELATIONSHIP, true);
	
	if (!empty($folder_guid)) {
		add_entity_relationship($folder_guid, FileToolsFolder::RELATIONSHIP, $file_guid);
	}
	
	$msg = elgg_echo('file_tools:action:move:success:file');
	
} elseif ($file instanceof FileToolsFolder) {
	$file->parent_guid = $folder_guid;
	
	$msg = elgg_echo('file_tools:action:move:success:folder');
}

return elgg_ok_response('', $msg);
