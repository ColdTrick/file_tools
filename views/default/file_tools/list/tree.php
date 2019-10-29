<?php

$page_owner = elgg_get_page_owner_entity();
if ($page_owner->canWriteToContainer(elgg_get_logged_in_user_guid(), 'object', \FileToolsFolder::SUBTYPE)) {
	elgg_register_menu_item('title', [
		'name' => 'folder_add',
		'icon' => 'plus',
		'href' => elgg_http_add_url_query_elements('ajax/form/file_tools/folder/edit', [
			'page_owner' => $page_owner->guid,
			'folder_guid' => get_input('folder_guid'),
		]),
		'text' => elgg_echo('file_tools:new:title'),
		'link_class' => 'elgg-button elgg-button-action elgg-lightbox',
	]);
}

$menu = elgg_view_menu('file_tools_folder_sidebar_tree', [
	'container' => $page_owner,
	'sort_by' => 'priority',
	'class' => 'elgg-menu-page',
]);

if (empty($menu)) {
	return;
}

$body = elgg_format_element('div', [
	'id' => 'file-tools-folder-tree',
], $menu);

// output file tree
echo elgg_view_module('', '', $body);
