<?php

$file_guids = (array) get_input('file_guids', []);
$folder_guids = (array) get_input('folder_guids', []);

if (empty($file_guids) && empty($folder_guids)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$success_msgs = [];
		
// remove all files
if (!empty($file_guids)) {
	$file_count = 0;
	
	$files = elgg_get_entities([
		'type' => 'object',
		'subtype' => 'file',
		'guids' => $file_guids,
		'limit' => false,
	]);
	
	foreach ($files as $file) {
		if (!$file->canDelete()) {
			continue;
		}
		
		if ($file->delete()) {
			$file_count++;
		}
	}
	
	if (empty($file_count)) {
		return elgg_error_response(elgg_echo('file_tools:action:bulk_delete:error:files'));
	} else {
		$success_msgs[] = elgg_echo('file_tools:action:bulk_delete:success:files', [$file_count]);
	}
}

// remove folders
if (!empty($folder_guids)) {
	$folder_count = 0;
	
	$folders = elgg_get_entities([
		'type' => 'object',
		'subtype' => FileToolsFolder::SUBTYPE,
		'guids' => $folder_guids,
		'limit' => false,
	]);
	
	foreach ($folders as $folder) {
		if (!$folder->canDelete()) {
			continue;
		}
		
		if ($folder->delete()) {
			$folder_count++;
		}
	}
	
	if (empty($folder_count)) {
		return elgg_error_response(elgg_echo('file_tools:action:bulk_delete:error:folders'));
	} else {
		$success_msgs[] = elgg_echo('file_tools:action:bulk_delete:success:folders', [$folder_count]);
	}
}

$msg = !empty($success_msgs) ? implode('<br />', $success_msgs) : '';

return elgg_ok_response('', $msg);
