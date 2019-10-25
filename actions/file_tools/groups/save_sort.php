<?php

$guid = get_input('guid');
$entity = get_entity($guid);

if (($entity instanceof ElggGroup) && $entity->canEdit()) {
	$sort = get_input('sort');
	$sort_direction = get_input('sort_direction');
	
	$entity->file_tools_sort = $sort;
	$entity->file_tools_sort_direction = $sort_direction;
	
	return elgg_ok_response('', '', $entity->getURL());
}

return elgg_ok_response();
