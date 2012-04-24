<?php 

	$folder = $vars["entity"];
	
	echo "<div id='file_tools_breadcrumbs'>";	
	echo "<ul>";		
	echo "<li><a href='" . $vars["url"] . "file_tools/list/" . page_owner() . "'>" . elgg_echo("file_tools:list:folder:main") . "</a></li>";
	
	if($folder)
	{	
		$parent_guid = $folder->parent_guid;
		
		$count = 0;
		while($count <= 2)
		{
			if($parent_guid && ($parent_folder = get_entity($parent_guid)))
			{
				if(strlen($parent_folder->title) > 10)
				{
					$title = substr($parent_folder->title, 0, 10).'..';
				}
				else
				{
					$title = $parent_folder->title;
				}
				$output[] = "<li><a class='file_tools_load_folder' rel='" . $parent_guid . "' href='javascript: void(0);'>" . $title . "</a></li>";
				
				$parent_guid = $parent_folder->parent_guid;
				
				$count++;
			}
			else
			{
				break;
			}
		}
		
		if($parent_guid != 0)
		{
			$output[] = "<li><a href='javascript: void(0);'>...</a></li>";
		}
		
		$output = array_reverse($output);
		
		foreach($output as $item)
		{
			echo $item;
		}
		
		echo "<li><a class='file_tools_load_folder' rel='" . $folder->getGUID() . "' href='javascript: void(0);'>" . $folder->title . "</a></li>";
	}
		
	echo "</ul>";		
	
	if($folder)
	{		
		$edit_url = $vars["url"] . "file_tools/folder/edit/" . $folder->getGUID();
		$delete_url = $vars["url"] . "action/file_tools/folder/delete?folder_guid=" . $folder->getGUID();
		
		$time_preference = "date";
		
		if($user_time_preference = elgg_get_plugin_user_setting('file_tools_time_display')){
			$time_preference = $user_time_preference;
		} elseif($site_time_preference = elgg_get_plugin_setting("file_tools_default_time_display", "file_tools")) {
			$time_preference = $site_time_preference;
		}
		
		if($time_preference == 'date')
		{
			$friendlytime 	= date('d-m-Y G:i', $folder->time_created);
		}
		else
		{
			$friendlytime 	= elgg_view_friendly_time($folder->time_created);
		}
		
		$onclick = "if(confirm('". elgg_echo('question:areyousure') . "')){ file_tools_remove_folder_files(this); return true;} else { return false; }"; 
		
		$edit_link = elgg_view("output/url", array("href" => $edit_url, "text" => elgg_echo("edit")));		
		$delete_link = elgg_view("output/url", array("href" => $delete_url, "text" => elgg_echo("delete"), "onclick" => $onclick, "is_action" => true));
				
		echo '<div id="file_tools_folder_preview">';

		echo elgg_echo('title') . ': ' . $folder->title . '<br />';
		if(!empty($folder->description)){
			echo elgg_echo('description') . ': ' . $folder->description . '<br />';
		}
		echo elgg_echo('file_tools:list:sort:time_created') . ': ' . $friendlytime . '<br />';
		
		if($folder->canEdit())
		{
			echo $edit_link . ' | ' . $delete_link;
		}
		
		echo '</div>';
	}
	
	echo "<div class='clearfloat'></div>";
	echo "</div>";
	