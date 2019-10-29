<?php

$plugin = elgg_extract('entity', $vars);

$value = $plugin->getUserSetting('file_tools_time_display', elgg_get_page_owner_guid(), $plugin->file_tools_default_time_display);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('file_tools:usersettings:time'),
	'#help' => elgg_echo('file_tools:usersettings:time:description'),
	'name' => 'params[file_tools_time_display]',
	'options_values' => [
		'date' => elgg_echo('file_tools:usersettings:time:date'),
		'days' => elgg_echo('file_tools:usersettings:time:days'),
	],
	'value' => $value,
]);
