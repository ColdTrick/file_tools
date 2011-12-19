<?php 

	$file = $vars["entity"];
	
	if(!$vars["full"] && get_context() == "search")
	{
		echo elgg_view("input/hidden", array("internalname" => "file_guid", "value" => $file->getGUID()));
	}
	elseif($vars["full"])
	{
		//echo elgg_view("file_tools/breadcrumb", array("entity" => $file));
	}

?>