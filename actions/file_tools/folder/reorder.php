<?php

$folder_guid = (int) get_input('folder_guid', 0);
$parent_guid = (int) get_input('parent_guid', 0);
$order = get_input('order');

if (empty($folder_guid)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

// if parent guid, check if it is a folder
if (!empty($parent_guid)) {
	$parent = get_entity($parent_guid);
	if (!$parent instanceof FileToolsFolder) {
		unset($parent_guid);
	}
}

// get folder from folder_guid and check if it is a folder
if (!is_null($parent_guid) && ($folder_guid !== $parent_guid)) {
	$folder = get_entity($folder_guid);
	if ($folder instanceof FileToolsFolder && $folder->canEdit()) {
		// set new parent_guid
		$folder->parent_guid = $parent_guid;
		$folder->save();
	}
}

// reorder
if (!empty($order) && !is_array($order)) {
	$order = [$order];
}

if (!empty($order) && !is_null($parent_guid)) {
	foreach ($order as $index => $order_guid) {
		$folder = get_entity($order_guid);
		
		if (!$folder instanceof FileToolsFolder || !$folder->canEdit()) {
			continue;
		}
		
		if ((int) $folder->parent_guid === $parent_guid) {
			$folder->order = $index;
		}
	}
}

return elgg_ok_response('', elgg_echo('file_tools:action:folder:reorder:success'));
