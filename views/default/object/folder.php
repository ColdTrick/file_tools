<?php
	$folder = $vars['entity'];
	
	$friendlytime 	= elgg_view_friendly_time($folder->time_created);
	$title 			= $folder->title;
	
	if(strlen($title) > 20)
	{
		$title 	= substr($title, 0, 20) . '..';
	}
	
	$delete_url = $vars["url"] . "action/file_tools/delete?folder_guid=" . $folder->getGUID();
	$edit_url 	= $vars["url"] . "pg/file_tools/edit/" . $folder->getGUID();
?>
<div class="file_tools_folder">
	<div class="file_tools_folder_title">
		<div class="file_tools_file_icon"><img src="<?php echo $vars['url'] . 'mod/file_tools/_graphics/folder_tiny.png';?>" /></div>
		
		<a class="file_tools_load_folder" rel="<?php echo $folder->getGUID(); ?>" id="<?php echo $folder->getGUID(); ?>" href="javascript: void(0);"><?php echo $title; ?></a>
	</div>
	
	<div class="file_tools_folder_etc">
		<?php //echo $friendlytime; ?>
	</div>
	
	<div class="file_tools_folder_actions">
		<?php echo elgg_view("output/url", array("href" => $edit_url, "text" => elgg_echo("edit")));?> |
		<?php
			$js = "onclick=\"if(confirm('". elgg_echo('question:areyousure') . "')){ file_tools_remove_folder_files(this); return true;} else { return false; }\""; 
			echo elgg_view("output/url", array("href" => $delete_url, "text" => elgg_echo("delete"), "js" => $js, "is_action" => true));
		?>
	</div>
</div>