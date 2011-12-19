<?php 

	/**
	 * jQuery call to move a file in a folder
	 */

	if(isloggedin())
	{
		$file_guid = get_input("file_guid", 0);
		$folder_guid = get_input("folder_guid", 0);
		
		if(!empty($file_guid))
		{
			if($file = get_entity($file_guid))
			{
				$container_entity = $file->getContainerEntity();
				
				if(($file->canEdit() || ($container_entity instanceof ElggGroup && $container_entity->isMember())))
				{
					if($file->getSubtype() == "file")
					{
						// check if a given guid is a folder
						if(!empty($folder_guid))
						{
							if($folder = get_entity($folder_guid))
							{
								if($folder->getSubtype() != FILE_TOOLS_SUBTYPE)
								{
									unset($folder_guid);
								}
							}
							else
							{
								unset($folder_guid);
							}
						}
						
						// remove old relationships
						remove_entity_relationships($file->getGUID(), FILE_TOOLS_RELATIONSHIP, true);
						
						if(!empty($folder_guid))
						{
							add_entity_relationship($folder_guid, FILE_TOOLS_RELATIONSHIP, $file_guid);
						}
					}
					elseif($file->getSubtype() == "folder")
					{
						$file->parent_guid = $folder_guid;
					}
				}
			}
		}
	}