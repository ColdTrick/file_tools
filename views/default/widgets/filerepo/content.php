<?php
	
$widget = elgg_extract('entity', $vars);

// how many files to display
$num_display = (int) $widget->num_display;
if ($num_display < 1) {
	$num_display = 4;
}

$options = [
	'type' => 'object',
	'subtype' => 'file',
	'container_guid' => $widget->getOwnerGUID(),
	'limit' => $num_display,
	'pagination' => false,
	'full_view' => false,
];

// how to display the files
if ($widget->gallery_list == 2) {
	$files = elgg_get_entities($options);
	if (empty($files)) {
		echo elgg_echo('file:none');
		return;
	}
	
	$list = '<ul class="elgg-gallery">';
	
	foreach ($files as $file) {
		$list .= '<li class="elgg-item">';
		$list .= elgg_view('output/url', [
			'text' => elgg_view_entity_icon($file, 'small'),
			'href' => $file->getURL(),
			'title' => $file->getDisplayName(),
		]);
		$list .= '</li>';
	}
	$list .= '</ul>';
	
	echo $list;
	
	$more_link = '';
	$owner = $widget->getOwnerEntity();
	if ($owner instanceof ElggUser) {
		$more_link = elgg_generate_url('collection:object:file:owner', [
			'username' => $owner->username,
		]);
	} elseif ($owner instanceof ElggGroup) {
		$more_link = elgg_generate_url('collection:object:file:group', [
			'guid' => $owner->guid,
		]);
	}
	
	if (empty($more_link)) {
		return;
	}
	
	echo elgg_format_element('div', ['class' => 'elgg-widget-more'], elgg_view('output/url', [
		'text' => elgg_echo('file:more'),
		'href' => $more_link,
		'is_trusted' => true,
	]));
	
	return;
}

$list = elgg_list_entities($options);
if (empty($list)) {
	echo elgg_echo('file:none');
	return;
}

echo $list;

$more_link = '';
$owner = $widget->getOwnerEntity();
if ($owner instanceof ElggUser) {
	$more_link = elgg_generate_url('collection:object:file:owner', [
		'username' => $owner->username,
	]);
} elseif ($owner instanceof ElggGroup) {
	$more_link = elgg_generate_url('collection:object:file:group', [
		'guid' => $owner->guid,
	]);
}

if (empty($more_link)) {
	return;
}

echo elgg_format_element('div', ['class' => 'elgg-widget-more'], elgg_view('output/url', [
	'text' => elgg_echo('file:more'),
	'href' => $more_link,
	'is_trusted' => true,
]));
