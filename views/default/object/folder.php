<?php

	$folder = $vars['entity'];
	$context = elgg_get_context();
	
	$time_preference = "date";
	
	if($user_time_preference = elgg_get_plugin_user_setting('file_tools_time_display')){
		$time_preference = $user_time_preference;
	} elseif($site_time_preference = elgg_get_plugin_setting("file_tools_default_time_display", "file_tools")) {
		$time_preference = $site_time_preference;
	}
	
	if($time_preference == 'date')
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
	$edit_url 	= $vars["url"] . "file_tools/folder/edit/" . $folder->getGUID();
	
	if(elgg_get_context() == "search")
	{
		echo elgg_view("input/hidden", array("name" => "folder_guid", "value" => $folder->getGUID()));
	}
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
	<?php 
	
	if(elgg_get_context() != "widget"){ ?>
		<div class="file_tools_file_etc"><?php echo $friendlytime;?> <span><?php echo elgg_echo('folder');?></span></div>
		
		<?php 
		if($context != 'widget' && $folder->canEdit())
		{
		?>
		<div class="file_tools_folder_actions">
			<span><?php echo elgg_echo('file_tools:file:actions');?></span>
			<ul>
				<?php 
				echo '<li>' . elgg_view("output/url", array("href" => $edit_url, "text" => elgg_echo("edit"))) . '</li>';
				
				$onclick = "if(confirm('". elgg_echo('question:areyousure') . "')){ file_tools_remove_folder_files(this); return true;} else { return false; }"; 
				echo '<li>' . elgg_view("output/url", array("href" => $delete_url, "text" => elgg_echo("delete"), "onclick" => $onclick, "is_action" => true)) . '</li>';
				?>
			</ul>
		</div>
		<input style="float: right;" type="checkbox" name="file_tools_file_action_check" value="<?php echo $folder->getGUID(); ?>" />
	<?php 
		}
	}
	?>
</div>