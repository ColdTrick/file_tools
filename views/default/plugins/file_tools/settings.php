<?php
	
$plugin = elgg_extract('entity', $vars);

$list_length_options = [
	0 => elgg_echo('file_tools:settings:list_length:unlimited'),
];
$list_length_options += array_combine(range(10, 200, 10), range(10, 200, 10));

// Allowed extensions
echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('file_tools:settings:allowed_extensions'),
	'#help' => elgg_echo('file_tools:settings:allowed_extensions:help'),
	'name' => 'params[allowed_extensions]',
	'value' => implode(',', file_tools_allowed_extensions()),
]);

// Use folder structure
echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('file_tools:settings:use_folder_structure'),
	'name' => 'params[use_folder_structure]',
	'checked' => $plugin->use_folder_structure === 'yes',
	'switch' => true,
	'default' => 'no',
	'value' => 'yes',
]);

// default time notation
echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('file_tools:usersettings:time:default'),
	'name' => 'params[file_tools_default_time_display]',
	'options_values' => [
		'date' => elgg_echo('file_tools:usersettings:time:date'),
		'days' => elgg_echo('file_tools:usersettings:time:days'),
	],
	'value' => $plugin->file_tools_default_time_display,
]);

// default sorting options
echo elgg_view_field([
	'#type' => 'fieldset',
	'#label' => elgg_echo('file_tools:settings:sort:default'),
	'align' => 'horizontal',
	'fields' => [
		[
			'#type' => 'select',
			'name' => 'params[sort]',
			'value' => $plugin->sort,
			'options_values' => [
				'e.time_created' => elgg_echo('file_tools:list:sort:time_created'),
				'oe.title' => elgg_echo('title'),
				'simpletype' => elgg_echo('file_tools:list:sort:type'),
			],
		],
		[
			'#type' => 'select',
			'name' => 'params[sort_direction]',
			'value' => $plugin->sort_direction,
			'options_values' => [
				'asc' => elgg_echo('file_tools:list:sort:asc'),
				'desc' => elgg_echo('file_tools:list:sort:desc'),
			],
		],
	],
]);

// limit folder listing
echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('file_tools:settings:list_length'),
	'name' => 'params[list_length]',
	'value' => (int) $plugin->list_length,
	'options_values' => $list_length_options,
]);
