<?php 

	$widget = $vars["entity"];
	
	if($files = file_tools_get_widget_files($widget->getOwner()))
	{
		foreach($files as $file)
		{
			$content .= elgg_view_entity($file);
		}
		
		if(!empty($content))
		{
			echo $content;
			
			echo "<div class='widget_more_wrapper'>";
			echo elgg_view("output/url", array("href" => $vars["url"] . "pg/file_tools/list/" . $widget->getOwner(), "text" => elgg_echo("widgets:file_tools:more_files")));
			echo "</div>";
		}
		else
		{
			echo elgg_view("page_elements/contentwrapper", array("body" => elgg_echo("widgets:file_tools:no_files")));
		}
	}
	else
	{
		echo elgg_view("page_elements/contentwrapper", array("body" => elgg_echo("widgets:file_tools:no_files")));
	}