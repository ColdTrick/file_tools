<?php

$widget = elgg_extract('entity', $vars);

$folder_guids = $widget->folder_guids;
if (empty($folder_guids)) {
	echo elgg_echo('widgets:file_tree:no_folders');
	return;
}

$show_content = $widget->show_content;

if (!is_array($folder_guids)) {
	$folder_guids = array($folder_guids);
}

$folder_guids = array_map('sanitise_int', $folder_guids);

$folders = '';
foreach ($folder_guids as $guid) {
	$folder = get_entity($guid);
	
	if (empty($folder) || ($folder->getSubtype() !== FILE_TOOLS_SUBTYPE)) {
		continue;
	}
	
	if (!empty($show_content)) {
		// list the files
		$folders .= elgg_view_entity($folder, ['full_view' => false]);
		
		// list the content
		$sub_folders = file_tools_get_sub_folders($folder);
		if (empty($sub_folders)) {
			$sub_folders = [];
		}
		
		$files_options = [
			'type' => 'object',
			'subtype' => 'file',
			'limit' => false,
			'container_guid' => $widget->getOwnerGUID(),
			'relationship' => FILE_TOOLS_RELATIONSHIP,
			'relationship_guid' => $folder->getGUID(),
			'inverse_relationship' => false,
		];
		$files = elgg_get_entities_from_relationship($files_options);
		
		$entities = array_merge($sub_folders, $files);
		
		$folders .= elgg_format_element('div', ['class' => 'mlm'], elgg_view_entity_list($entities, [
			'full_view' => false,
			'pagination' => false,
		]));
	} else {
		$folders .= elgg_view_entity($folder);
	}
}

if (empty($folders)) {
	echo elgg_echo('notfound');
	return;
}

echo $folders;

$more_url = '';
$owner = $widget->getOwnerEntity();
if ($owner instanceof ElggUser) {
	$more_url = "file/owner/{$owner->username}";
} elseif ($owner instanceof ElggGroup) {
	$more_url = "file/group/{$owner->getGUID()}/all";
}

if (empty($more_url)) {
	return;
}

echo elgg_format_element('div', ['class' => 'elgg-widget-more'], elgg_view('output/url', [
	'href' => $more_url,
	'text' => elgg_echo('widgets:file_tree:more'),
]));
