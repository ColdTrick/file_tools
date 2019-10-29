<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggGroup) {
	return;
}
	
// build form
$fields = [];

$sort_value = 'e.time_created';
if ($entity->file_tools_sort) {
	$sort_value = $entity->file_tools_sort;
} elseif ($site_sort_default = elgg_get_plugin_setting('sort', 'file_tools')) {
	$sort_value = $site_sort_default;
}

$fields[] = [
	'#type' => 'select',
	'name' => 'sort',
	'value' => $sort_value,
	'options_values' => [
		'e.time_created' => elgg_echo('file_tools:list:sort:time_created'),
		'oe.title' => elgg_echo('title'),
		'simpletype' => elgg_echo('file_tools:list:sort:type'),
	],
];

$sort_direction_value = 'asc';
if ($entity->file_tools_sort_direction) {
	$sort_direction_value = $entity->file_tools_sort_direction;
} elseif ($site_direction_sort_default = elgg_get_plugin_setting('sort_direction', 'file_tools')) {
	$sort_direction_value = $site_direction_sort_default;
}

$fields[] = [
	'#type' => 'select',
	'name' => 'sort_direction',
	'value' => $sort_direction_value,
	'options_values' => [
		'asc' => elgg_echo('file_tools:list:sort:asc'),
		'desc' => elgg_echo('file_tools:list:sort:desc'),
	],
];

echo elgg_view_field([
	'#type' => 'fieldset',
	'#label' => elgg_echo('file_tools:settings:sort:default'),
	'align' => 'horizontal',
	'fields' => $fields,
]);

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'guid',
	'value' => $entity->guid,
]);

$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('save'),
]);

elgg_set_form_footer($footer);
