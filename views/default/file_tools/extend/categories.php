<?php 

	// only extend files
	if(get_context() == "file")
	{
		$page_owner = page_owner_entity();
	
		if(!empty($vars["entity"]))
		{
			$file = $vars["entity"];
			
			$options = array(
				"type" => "object",
				"subtype" => FILE_TOOLS_SUBTYPE,
				"owner_guid" => $page_owner->getGUID(),
				"relationship" => FILE_TOOLS_RELATIONSHIP,
				"relationship_guid" => $file->getGUID(),
				"inverse_relationship" => true,
				"limit" => 1
			);
			
			if($folders = elgg_get_entities_from_relationship($options))
			{
				$parent_guid = $folders[0]->getGUID();
			}
		}
		
		if(empty($parent_guid))
		{
			$parent_guid = 0;
		}
		
		echo "<div><label>" . elgg_echo("file_tools:forms:edit:parent") . "</label></div>";
		echo elgg_view("input/folder_select", array("owner_guid" => $page_owner->getGUID(), "value" => $parent_guid, "internalname" => "folder_guid"));
	}