<?php

$files = elgg_extract('files', $vars, []);
$folder = get_entity(elgg_extract('folder_guid', $vars));
$show_more = (bool) elgg_extract('show_more', $vars, false);
$limit = (int) elgg_extract('limit', $vars, elgg_get_plugin_setting('list_length', 'file_tools'));
$offset = (int) elgg_extract('offset', $vars, 0);
$page_owner = elgg_extract('page_owner', $vars);

if (!elgg_get_page_owner_entity() && !empty($page_owner)) {
	elgg_set_page_owner_guid($page_owner);
}

elgg_require_js('file_tools/list/files');

if (elgg_get_page_owner_entity()->canWriteToContainer(0, 'object', 'file')) {
	elgg_require_js('file_tools/list/edit');
}

// only show the header if offset == 0
$folder_content = '';
if (empty($offset)) {
	$folder_content = elgg_view('file_tools/breadcrumb', [
		'entity' => $folder,
	]);
	
	$sub_folders = file_tools_get_sub_folders($folder);
	if (empty($sub_folders)) {
		$sub_folders = [];
	}
	
	$entities = array_merge($sub_folders, $files);
} else {
	$entities = $files;
}

$files_content = '';
if (!empty($entities)) {
	$params = [
		'full_view' => false,
		'pagination' => false,
		'file_tools_selector' => true,
		'list_class' => 'file-tools-file-list',
	];
	
	$files_content = elgg_view_entity_list($entities, $params);
}

if (empty($files_content)) {
	$files_content = elgg_echo('file_tools:list:files:none');
} else {
	if ($show_more) {
		$more = elgg_view('input/button', [
			'value' => elgg_echo('file_tools:show_more'),
			'class' => 'elgg-button-action',
			'id' => 'file-tools-show-more-files',
		]);
		$more .= elgg_view('input/hidden', [
			'name' => 'offset',
			'value' => ($limit + $offset),
		]);
		if (!empty($folder)) {
			$more .= elgg_view('input/hidden', [
				'name' => 'folder_guid',
				'value' => $folder->guid,
			]);
		} else {
			$more .= elgg_view('input/hidden', [
				'name' => 'folder_guid',
				'value' => '0',
			]);
		}
		
		$files_content .= elgg_format_element('div', [
			'id' => 'file-tools-show-more-wrapper',
			'class' => 'center',
		], $more);
	}

	// only show selectors on the first load
	if (empty($offset)) {
		$selector = '';
		
		if (elgg_get_page_owner_entity()->canWriteToContainer(0, 'object', 'file')) {
			$selector = elgg_view('output/url', [
				'id' => 'file_tools_action_bulk_delete',
				'text' => elgg_echo('file_tools:list:delete_selected'),
				'href' => false,
			]);
			$selector .= ' | ';
		}
		
		$selector .= elgg_view('output/url', [
			'id' => 'file_tools_action_bulk_download',
			'text' => elgg_echo('file_tools:list:download_selected'),
			'href' => false,
		]);
		
		$selector .= elgg_view('output/url', [
			'id' => 'file_tools_select_all',
			'text' => elgg_format_element('span', [], elgg_echo('file_tools:list:select_all')) .
				elgg_format_element('span', ['class' => 'hidden'], elgg_echo('file_tools:list:deselect_all')),
			'href' => false,
			'class' => 'float-alt',
		]);
				
		$files_content .= elgg_format_element('div', ['class' => 'clearfix mtm'], $selector);
	}
}

// show the listing
echo '<div id="file_tools_list_files">';
echo $folder_content;
echo $files_content;
echo '</div>';
