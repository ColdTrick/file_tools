<?php

$folder_guid = (int) elgg_extract('guid', $vars);
elgg_entity_gatekeeper($folder_guid, 'object', \FileToolsFolder::SUBTYPE, true);

$folder = get_entity($folder_guid);
		
// set context and page_owner
elgg_set_context('file');
elgg_set_page_owner_guid($folder->getContainerGUID());

// build page elements
$title_text = elgg_echo('file_tools:edit:title');

$body_vars = [
	'folder' => $folder,
	'page_owner' => elgg_get_page_owner_guid(),
];

$edit = elgg_view_form('file_tools/folder/edit', [], $body_vars);

// build page
$body = elgg_view_layout('one_sidebar', [
	'title' => $title_text,
	'content' => $edit
]);

echo elgg_view_page($title_text, $body);
