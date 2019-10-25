<?php
/**
 * Show a folder in the file_tree widget with contents show
 *
 * @uses $vars['entity'] the folder to show
 */

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \FileToolsFolder) {
	return;
}

// cleanup
unset($vars['items']);
unset($vars['item_view']);

// show folder
$params = $vars;
$params['full_view'] = false;
echo elgg_view_entity($entity, $params);

// show contained folder/files
echo elgg_view('object/folder/file_tree_content', $vars);
