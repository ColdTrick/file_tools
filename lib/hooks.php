<?php

	function file_tools_can_edit_metadata_hook($hook, $type, $returnvalue, $params)
	{
		$result = $returnvalue;
	
		if(!empty($params) && is_array($params) && $result !== true)
		{
			if(array_key_exists("user", $params) && array_key_exists("entity", $params))
			{
				$entity = $params["entity"];
				$user = $params["user"];
	
				if($entity->getSubtype() == FILE_TOOLS_SUBTYPE)
				{
					$container_entity = $entity->getContainerEntity();
						
					if(($container_entity instanceof ElggGroup) && $container_entity->isMember($user) && ($container_entity->file_tools_structure_management_enable != "no"))
					{
						$result = true;
					}
				}
			}
		}
	
		return $result;
	}
	
	function file_tools_folder_icon_hook($hook, $type, $returnvalue, $params)
	{
		global $CONFIG;
	
		$result = $returnvalue;
	
		if(array_key_exists("entity", $params) && array_key_exists("size", $params))
		{
			$entity = $params["entity"];
			$size = $params["size"];
				
			if($entity->getSubtype() == FILE_TOOLS_SUBTYPE)
			{
				switch($size) 
				{
					case "tiny":
					case "medium":
						$result = $CONFIG->wwwroot . "mod/file_tools/_graphics/folder_" . $size . ".png";
						break;
					default:
						$result = $CONFIG->wwwroot . "mod/file_tools/_graphics/folder_small.png";
					break;
				}
			}
		}
	
		return $result;
	}
	
	function file_tools_write_acl_plugin_hook($hook, $type, $returnvalue, $params)
	{
		$result = $returnvalue;
		
		if(!empty($params) && is_array($params))
		{
			
			if((get_context() == "file_tools") && ($page_owner = page_owner_entity()) && ($page_owner instanceof ElggGroup))
			{
				$result = array(
					$page_owner->group_acl => elgg_echo("groups:group") . ": " . $page_owner->name,
					ACCESS_LOGGED_IN => elgg_echo("LOGGED_IN"),
					ACCESS_PUBLIC => elgg_echo("PUBLIC")
				);
			}
		}
		
		return $result;
	}