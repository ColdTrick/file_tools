<?php

$widget = elgg_extract('entity', $vars);

$count = (int) $widget->file_count;
if ($count < 1) {
	$count = 8;
}

echo elgg_view_field([
	'#type' => 'number',
	'#label' => elgg_echo('file:num_files'),
	'name' => 'params[file_count]',
	'value' => $count,
	'min' => 1,
	'max' => 50,
]);
