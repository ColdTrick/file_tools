<?php 

	/**
	 * jQuery call to reorder a folder
	 */

	if(isloggedin())
	{
		$folder_guid = (int)get_input("folder_guid", 0);
		$parent_guid = (int)get_input("parent_guid", 0);
		$order = str_replace("file_tools_tree_element_", "", str_replace("::", ",", get_input("order")));
		
		if(!empty($folder_guid) && (!empty($parent_guid) || $parent_guid == 0))
		{
			// if parent guid, check if it is a folder
			if(!empty($parent_guid))
			{
				if($parent = get_entity($parent_guid))
				{
					if($parent->getSubtype() != FILE_TOOLS_SUBTYPE)
					{
						unset($parent_guid);
					}
				}
				else
				{
					unset($parent_guid);
				}
			}
			
			// get folder from folder_guid and check if it is a folder
			if(($folder = get_entity($folder_guid)) && !is_null($parent_guid))
			{
				if(($folder->getSubtype() == FILE_TOOLS_SUBTYPE) && $folder->canEditMetadata("parent_guid"))
				{
					// set new parent_guid
					$folder->parent_guid = $parent_guid;
					$folder->save();
				}
			}
			
			// reorder
			$order = string_to_tag_array($order);
			if(!empty($order) && !is_array($order))
			{
				$order = array($order);
			}
			
			if(!empty($order) && !is_null($parent_guid))
			{
				foreach($order as $index => $order_guid)
				{
					if($folder = get_entity($order_guid))
					{
						if(($folder->getSubtype() == FILE_TOOLS_SUBTYPE) && $folder->canEditMetadata("order"))
						{
							if($folder->parent_guid == $parent_guid)
							{
								$folder->order = $index;
								$folder->save();
							}
						}
					}
				}
			}
		}
	}