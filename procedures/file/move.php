<?php 

	/**
	 * jQuery call to move a file in a folder
	 */

	if(elgg_is_logged_in())
	{
		$file_guid 		= (int)get_input("file_guid", 0);
		$folder_guid 	= (int)str_replace('file_tools_tree_element_', '', get_input("folder_guid", 0));
		
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
				else
				{
					//echo 'cant edit<br />';
				}
			}
			else
			{
				//echo 'cant get entity<br />';
			}
		}
		else
		{
			//echo 'no fileguid<br />';
		}
	}
	else
	{
		//echo 'cant login<br />';
	}