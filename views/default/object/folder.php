<?php

	$folder = $vars['entity'];
	$context = get_context();
	

	if(get_plugin_usersetting('file_tools_time_display') == 'date')
	{
		$friendlytime 	= date('d-m-Y G:i', $vars['entity']->time_created);
	}
	else
	{
		$friendlytime 	= elgg_view_friendly_time($vars['entity']->time_created);
	}
	
	$title 			= $folder->title;
	
	if(strlen($title) > 20)
	{
		$title 	= substr($title, 0, 20) . '..';
	}
	
	$delete_url = $vars["url"] . "action/file_tools/folder/delete?folder_guid=" . $folder->getGUID();
	$edit_url 	= $vars["url"] . "pg/file_tools/folder/edit/" . $folder->getGUID();
?>
<div class="file_tools_folder" id="file_<?php echo $folder->getGUID(); ?>">
	<div class="file_tools_folder_title">
		<div class="file_tools_file_icon"><img src="<?php echo $vars['url'] . 'mod/file_tools/_graphics/folder_tiny.png';?>" /></div>
		
		<?php 
		if($context == 'widget')
		{
			$href = $folder->getURL();
		}
		else
		{
			$href = 'javascript: void(0);';
		}
		?>
		<a class="file_tools_load_folder" rel="<?php echo $folder->getGUID(); ?>" href="<?php echo $href; ?>"><?php echo $title; ?></a>
	</div>
	
	<div class="file_tools_file_etc"><?php echo $friendlytime;?> <span><?php echo elgg_echo('folder');?></span></div>
	
	<?php 
	if($context != 'widget' && $folder->canEdit())
	{
	?>
	<div class="file_tools_folder_actions">
		<span><?php echo elgg_echo('file_tools:file:actions');?></span>
		<ul>
			<?php 
			echo '<li>' . elgg_view("output/url", array("href" => $edit_url, "text" => elgg_echo("edit"))) . '</li>';?>
			<?php
			$js = "onclick=\"if(confirm('". elgg_echo('question:areyousure') . "')){ file_tools_remove_folder_files(this); return true;} else { return false; }\""; 
			echo '<li>' . elgg_view("output/url", array("href" => $delete_url, "text" => elgg_echo("delete"), "js" => $js, "is_action" => true)) . '</li>';
			?>
		</ul>
	</div>
	<input style="float: right;" type="checkbox" name="file_tools_file_action_check" value="<?php echo $folder->getGUID(); ?>" />
	<?php 
	}
	?>
</div>