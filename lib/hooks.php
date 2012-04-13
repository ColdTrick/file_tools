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
	
	function file_tools_file_route_hook($hook, $type, $returnvalue, $params){
		$result = $returnvalue;
		
		if(!empty($params) && is_array($params)){
			$page = elgg_extract("segments", $params);
			
			switch($page[0]){
				case "view":
					if(!elgg_is_logged_in() && isset($page[1])){
						if(!get_entity($page[1])){
							gatekeeper();
						}
					}
					break;
				case "owner":
					if(elgg_get_plugin_setting("user_folder_structure", "file_tools") == "yes"){
						if(!empty($page[1])) {
							$result = false;
							
							set_input("username", $page[1]);
							include(dirname(dirname(__FILE__)) . "/pages/list.php");
						}
					}
					break;
				case "group":
					if(elgg_get_plugin_setting("user_folder_structure", "file_tools") == "yes"){
						if(!empty($page[1])) {
							$result = false;
							
							set_input("page_owner", $page[1]);
							include(dirname(dirname(__FILE__)) . "/pages/list.php");
						}
					}
					break;
			}
		}
		
		return $result;
	}
	
	function file_tools_title_menu_register_hook($hook, $type, $returnvalue, $params){
		$result = $returnvalue;
		
		if(!empty($result) && is_array($result)){
			if(!($page_owner = elgg_get_page_owner_guid())){
				$page_owner = elgg_get_logged_in_user_guid();
			}
			
			foreach($result as $index => $menu_item){
				if(($menu_item->getName() == "add") && ($menu_item->getText() == elgg_echo("file:upload"))){
					$menu_item->setHref("file_tools/file/new/" . $page_owner);
				}
			}
			
			if(elgg_in_context("file")){
				$result[] = ElggMenuItem::factory(array(
					"name" => "zip_upload",
					"href" => "file_tools/import/zip/" . $page_owner,
					"text" => elgg_echo("file_tools:upload:new"),
					"class" => "elgg-button elgg-button-action"
				));
			}
		}
		
		return $result;
	}