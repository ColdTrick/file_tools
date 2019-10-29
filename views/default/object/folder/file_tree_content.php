<?php
/**
 * Show the folder contents in the file_tree widget
 *
 * @uses $vars['entity'] the folder to show
 */

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \FileToolsFolder) {
	return;
}

// get the containing folders
$sub_folders = file_tools_get_sub_folders($entity);
if (empty($sub_folders)) {
	$sub_folders = [];
}

// get the containing files
$files = elgg_get_entities([
	'type' => 'object',
	'subtype' => 'file',
	'limit' => false,
	'container_guid' => $entity->getContainerGUID(),
	'relationship' => FileToolsFolder::RELATIONSHIP,
	'relationship_guid' => $entity->guid,
	'inverse_relationship' => false,
]);

// merge results
$entities = array_merge($sub_folders, $files);

// list results
$params = $vars;
$params['list_class'] = 'mlm';
$params['full_view'] = false;
$params['pagination'] = false;

echo elgg_view_entity_list($entities, $params);
