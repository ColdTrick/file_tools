<?php

$guid = (int) get_input('guid');
$title = get_input('title');
$owner_guid = (int) get_input('page_owner');
$description = get_input('description');
$parent_guid = (int) get_input('file_tools_parent_guid', 0); // 0 is top_level
$access_id = (int) get_input('access_id');
$change_children_access = get_input('change_children_access', false);
$change_files_access = get_input('change_files_access', false);

if (empty($title) || empty($owner_guid)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$owner = get_entity($owner_guid);
if (!($owner instanceof ElggUser) && !($owner instanceof ElggGroup)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

if (!empty($guid)) {
	// check if editing existing
	$folder = get_entity($guid);
	if (!$folder instanceof FileToolsFolder) {
		unset($folder);
	}
} else {
	// create a new folder
	$folder = new FileToolsFolder();
	$folder->owner_guid = elgg_get_logged_in_user_guid();
	$folder->container_guid = $owner_guid;
	$folder->access_id = $access_id;

	$order = elgg_count_entities([
		'type' => 'object',
		'subtype' => FileToolsFolder::SUBTYPE,
		'metadata_name_value_pairs' => [
			'name' => 'parent_guid',
			'value' => $parent_guid
		],
	]);

	$folder->order = $order;

	if (!$folder->save()) {
		unset($folder);
	}
}

if (empty($folder)) {
	return elgg_error_response(elgg_echo('file_tools:action:edit:error:folder'));
}

// check for the correct parent_guid
if (!empty($parent_guid) && ($parent_guid === $folder->guid)) {
	return elgg_error_response(elgg_echo('file_tools:action:edit:error:parent_guid'));
}

$folder->title = $title;
$folder->description = $description;

$folder->access_id = $access_id;

if (!empty($change_children_access)) {
	$folder->save();
	$folder->updateChildAccess(!empty($change_files_access));
} elseif (!empty($change_files_access)) {
	$folder->save();
	$folder->updateFileAccess();
}

$folder->parent_guid = $parent_guid;

if (!$folder->save()) {
	return elgg_error_response(elgg_echo('file_tools:action:edit:error:save'));
}

return elgg_ok_response('', elgg_echo('file_tools:action:edit:success'), $folder->getURL());
