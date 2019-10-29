<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggGroup) {
	return;
}

if (!$entity->isToolEnabled('file')) {
	return;
}

if (elgg_get_plugin_setting('use_folder_structure', 'file_tools') !== 'yes') {
	return;
}

echo elgg_view_module('info', elgg_echo('file_tools:settings:sort:default'), elgg_view_form('file_tools/groups/save_sort', [], $vars));
