<?php

	global $CONFIG;

	$entity_guids = json_decode($_GET['check']);
	
	$result = array();
	$result['valid'] = false;
	
	foreach($entity_guids as $entity_guid)
	{
		if($entity = get_entity($entity_guid))
		{
			if($entity->canEdit())
			{
				$entity_subtype = $entity->getSubtype();
				if($entity_subtype == 'file')
				{
					if($entity->simpletype == 'image')
					{
						$thumbnail = $entity->thumbnail;
						$smallthumb = $entity->smallthumb;
						$largethumb = $entity->largethumb;
						
						if($thumbnail)
						{
							$delfile = new ElggFile();
							$delfile->owner_guid = $entity->owner_guid;
							$delfile->setFilename($thumbnail);
							$delfile->delete();
						}
						
						if($smallthumb)
						{
							$delfile = new ElggFile();
							$delfile->owner_guid = $entity->owner_guid;
							$delfile->setFilename($smallthumb);
							$delfile->delete();
						}
						
						if($largethumb)
						{
							$delfile = new ElggFile();
							$delfile->owner_guid = $entity->owner_guid;
							$delfile->setFilename($largethumb);
							$delfile->delete();
						}
					}
					
					$result['deleted'][] = $entity->getGUID();
					$entity->delete();
				}
				elseif($entity_subtype == 'folder')
				{
					$result['deleted'][] = $entity->getGUID();
					$entity->delete();
				}
			}
		}
	}
	
	if(count($entity_guids) == count($result['deleted']))
	{
		$result['valid'] = true;
	}
	
	echo json_encode($result);
	
	exit;