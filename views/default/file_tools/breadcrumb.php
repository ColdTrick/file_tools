<?php

$folder = elgg_extract('entity', $vars);

echo elgg_view_menu('file_tools_folder_breadcrumb', [
	'entity' => $folder,
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz elgg-breadcrumbs',
]);

if ($folder) {
	echo elgg_view_entity($folder);
}
