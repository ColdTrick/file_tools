<?php

$folder = elgg_extract('entity', $vars);
$full_view = (bool) elgg_extract('full_view', $vars, false);

$time_preference = 'date';

if ($user_time_preference = elgg_get_plugin_user_setting('file_tools_time_display', null, 'file_tools')) {
	$time_preference = $user_time_preference;
} elseif ($site_time_preference = elgg_get_plugin_setting('file_tools_default_time_display', 'file_tools')) {
	$time_preference = $site_time_preference;
}

if ($time_preference == 'date') {
	$friendlytime = date(elgg_echo('friendlytime:date_format'), $folder->time_created);
} else {
	$friendlytime = elgg_view_friendly_time($folder->time_created);
}

$title = $folder->title;
if (empty($title)) {
	$title = elgg_echo('untitled');
}
$title = elgg_view('output/url', [
	'text' => elgg_get_excerpt($title, 100),
	'href' => $folder->getURL(),
	'is_trusted' => true,
]);

if ($full_view) {
	
	echo elgg_view('object/elements/full', [
		'entity' => $folder,
		'title' => $title,
		'icon' => false,
		'show_summary' => true,
		'show_social_menu' => false,
		'responses' => false,
		'body' => elgg_view('output/longtext', ['value' => $folder->description]),
	]);
} else {
	// summary view
	$icon = elgg_view_icon('folder-open-regular');
	$icon_alt = '';
	if (!elgg_in_context('widgets')) {
		$icon_alt = elgg_view('input/checkbox', [
			'name' => 'folder_guids[]',
			'value' => $folder->guid,
			'default' => false,
		]);
	}
	
	$params = [
		'entity' => $folder,
		'subtitle' => false,
		'show_social_menu' => false,
	];
	
	$params = $params + $vars;
	$list_body = elgg_view('object/elements/summary', $params);
	
	echo elgg_view_image_block($icon, $list_body, [
		'class' => 'file-tools-folder',
		'image_alt' => $icon_alt,
	]);
}
