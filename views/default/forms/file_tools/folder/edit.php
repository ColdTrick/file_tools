<?php

$folder = elgg_extract('folder', $vars);
$page_owner = get_entity(elgg_extract('page_owner', $vars));

if (!empty($folder)) {
	$title = $folder->title;
	$desc = $folder->description;

	if (!empty($folder->parent_guid)) {
		$parent = $folder->parent_guid;
	} else {
		$parent = 0;
	}

	$access_id = $folder->access_id;

	echo elgg_view_field([
		'#type' => 'hidden',
		'name' => 'guid',
		'value' => $folder->guid,
	]);

	$submit_text = elgg_echo('update');
} else {
	$title = '';
	$desc = '';

	$parent = get_input('folder_guid', 0);

	if (!empty($parent) && ($parent_entity = get_entity($parent))) {
		$access_id = $parent_entity->access_id;
	} else {
		if ($page_owner instanceof ElggGroup) {
			$access_id = $page_owner->group_acl;
		} else {
			$access_id = ACCESS_DEFAULT;
		}
	}

	$submit_text = elgg_echo('save');
}

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'page_owner',
	'value' => $page_owner->guid,
]);

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('file_tools:forms:edit:title'),
	'name' => 'title',
	'value' => $title,
	'required' => true,
]);

echo elgg_view_field([
	'#type' => 'longtext',
	'#label' => elgg_echo('file_tools:forms:edit:description'),
	'name' => 'description',
	'value' => $desc,
]);

echo elgg_view_field([
	'#type' => 'folder_select',
	'#label' => elgg_echo('file_tools:forms:edit:parent'),
	'name' => 'file_tools_parent_guid',
	'folder' => $folder,
	'value' => $parent,
	'container_guid' => $page_owner->guid,
	'type' => 'folder',
]);

// set context to influence access
elgg_push_context('file_tools');

echo elgg_view_field([
	'#type' => 'access',
	'#label' => elgg_echo('access'),
	'name' => 'access_id',
	'value' => $access_id,
	'type' => 'object',
	'subtype' => 'folder',
	'entity' => $folder,
]);

// restore context
elgg_pop_context();

if (!empty($folder)) {
	$change_access = elgg_view_field([
		'#type' => 'checkbox',
		'#label' => elgg_echo('file_tools:forms:edit:change_children_access'),
		'name' => 'change_children_access',
		'value' => 'yes',
		'checked' => true,
	]);
	$change_access .= elgg_view_field([
		'#type' => 'checkbox',
		'#label' => elgg_echo('file_tools:forms:edit:change_files_access'),
		'name' => 'change_files_access',
		'value' => 'yes',
		'checked' => true,
	]);
	
	echo elgg_format_element('div', ['id' => 'file_tools_edit_form_access_extra'], $change_access);
}

$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => $submit_text,
]);
elgg_set_form_footer($footer);
