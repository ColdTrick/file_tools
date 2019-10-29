<?php

$page_owner = elgg_get_page_owner_entity();

echo elgg_view('output/longtext', [
	'value' => elgg_echo('file_tools:upload:form:zip:info'),
]);

echo elgg_view_field([
	'#type' => 'file',
	'#label' => elgg_echo('file_tools:upload:form:choose'),
	'name' => 'zip_file',
]);

if (elgg_get_plugin_setting('use_folder_structure', 'file_tools') == 'yes') {
	echo elgg_view_field([
		'#type' => 'folder_select',
		'#label' => elgg_echo('file_tools:forms:edit:parent'),
		'name' => 'parent_guid',
		'container_guid' => $page_owner->guid,
	]);
}

echo elgg_view_field([
	'#type' => 'access',
	'#label' => elgg_echo('access'),
	'name' => 'access_id',
]);

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'container_guid',
	'value' => $page_owner->guid,
]);

$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('upload'),
]);
elgg_set_form_footer($footer);
