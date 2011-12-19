<?php 

	$folder = $vars["entity"];
	
	if(get_context() == "search")
	{
		echo elgg_view("input/hidden", array("internalname" => "folder_guid", "value" => $folder->getGUID()));
	}