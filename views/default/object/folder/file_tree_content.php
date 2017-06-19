<?php
/**
 * Show a folder in the file_tree widget with contents show
 *
 * @uses $vars['entity'] the folder to show
 */

$entity = elgg_extract('entity', $vars);
if (!($entity instanceof ElggObject) || $entity->getSubtype() !== FILE_TOOLS_SUBTYPE) {
	return;
}

// show folder
echo elgg_view_entity($entity, ['full_view' => false]);

// get the containing folders
$sub_folders = file_tools_get_sub_folders($entity);
if (empty($sub_folders)) {
	$sub_folders = [];
}

// get the containing files
$files = elgg_get_entities_from_relationship([
	'type' => 'object',
	'subtype' => 'file',
	'limit' => false,
	'container_guid' => $entity->getContainerGUID(),
	'relationship' => FILE_TOOLS_RELATIONSHIP,
	'relationship_guid' => $entity->getGUID(),
	'inverse_relationship' => false,
]);

// merge results
$entities = array_merge($sub_folders, $files);

// list results
echo elgg_view_entity_list($entities, [
	'list_class' => 'mlm',
	'full_view' => false,
	'pagination' => false,
]);
